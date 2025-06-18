<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Detail;
use App\Models\Position;
use App\Models\WorkDivision;
use App\Models\Division;
use App\Models\Profile;
use App\Models\Approval;
use App\Models\Log;
use App\Models\Template;
use App\Models\LeaveBalance;
use App\Models\User;
use PhpOffice\PhpWord\IOFactory;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request as GlobalRequest; 

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:templates,id',
            'lama_hari' => 'nullable|integer|max:10',
            'alasan' => 'required|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'target_nip' => 'nullable|string|exists:profiles,nip',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // 2. Data dasar
            $user = Auth::user();
            $template = Template::with('category')->findOrFail($request->template_id);
            $year = now()->year;

            // 3. Generate nomor surat
            $count = Document::where('template_id', $template->id)
                ->whereYear('tanggal_pengajuan', $year)
                ->count();
            $newNumber = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            $documentNumber = $template->format_nomor . '/' . $newNumber;

            // 4. Cari target user (jika ada)
            $targetUser = null;
            if ($request->target_nip) {
                $profileTarget = Profile::where('nip', $request->target_nip)->first();
                $targetUser = $profileTarget?->user;
            }

            // 5. Simpan dokumen awal
            $document = Document::create([
                'user_id' => $user->id,
                'template_id' => $template->id,
                'document_number' => $documentNumber,
                'lama_hari' => $request->lama_hari,
                'alasan' => $request->alasan,
                'tanggal_pengajuan' => now(),
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'target_user_id' => $targetUser?->id,
                'file_path' => null,
            ]);

            // 6. Cek file template
            $templatePath = storage_path('app/public/' . $template->file_path);
            if (!file_exists($templatePath)) {
                return response()->json([
                    'status' => false,
                    'message' => 'File template tidak ditemukan',
                ], 404);
            }

            // 7. Ambil data pengaju
            $profile = $user->profile;
            $detail  = Detail::where('user_id', $user->id)->first();
            $position = $detail?->position;
            $workDivision = WorkDivision::where('detail_id', $detail?->id)->first();
            $division = $workDivision?->division;

            // 8. Ambil data target (jika ada)
            $targetProfile = $targetUser?->profile;
            $targetDetail = $targetUser ? Detail::where('user_id', $targetUser->id)->first() : null;
            $targetPosition = $targetDetail?->position;
            $targetDivision = WorkDivision::where('detail_id', $targetDetail?->id)->first()?->division;

            // 9. Isi template Word
            $templateProcessor = new TemplateProcessor($templatePath);
            $templateProcessor->setValue('name', $profile->name ?? '-');
            $templateProcessor->setValue('nip', $profile->nip ?? '-');
            $templateProcessor->setValue('gender', $profile->gender ?? '-');
            $templateProcessor->setValue('user_position', $position?->name ?? '-');
            $templateProcessor->setValue('user_division', $division?->name ?? '-');

            $templateProcessor->setValue('target_name', $targetProfile->name ?? '-');
            $templateProcessor->setValue('target_nip', $targetProfile->nip ?? '-');
            $templateProcessor->setValue('target_position', $targetPosition?->name ?? '-');
            $templateProcessor->setValue('target_division', $targetDivision?->name ?? '-');

            $templateProcessor->setValue('document_number', $documentNumber);
            $templateProcessor->setValue('alasan', $request->alasan ?? '-');
            $templateProcessor->setValue('tanggal_pengajuan', now()->format('d-m-Y'));
            $templateProcessor->setValue('lama_hari', $request->lama_hari ?? '-');
            $templateProcessor->setValue('tanggal_mulai', $request->tanggal_mulai ? date('d-m-Y', strtotime($request->tanggal_mulai)) : '-');
            $templateProcessor->setValue('tanggal_selesai', $request->tanggal_selesai ? date('d-m-Y', strtotime($request->tanggal_selesai)) : '-');

            // 10. Simpan file DOCX
            $timestamp = time();
            $filenameDocx = 'surat_' . $document->id . '_' . $timestamp . '.docx';
            $savePathDocx = storage_path('app/public/documents/' . $filenameDocx);
            if (!file_exists(dirname($savePathDocx))) {
                mkdir(dirname($savePathDocx), 0755, true);
            }
            $templateProcessor->saveAs($savePathDocx);

            // 11. Konversi ke PDF
            $phpWord = IOFactory::load($savePathDocx);
            $htmlPath = storage_path('app/public/documents/temp_' . $timestamp . '.html');
            IOFactory::createWriter($phpWord, 'HTML')->save($htmlPath);

            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml(file_get_contents($htmlPath));
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filenamePdf = 'surat_' . $document->id . '_' . $timestamp . '.pdf';
            $savePathPdf = storage_path('app/public/documents/' . $filenamePdf);
            file_put_contents($savePathPdf, $dompdf->output());

            // 12. Update dokumen dengan file path PDF
            $document->update(['file_path' => 'documents/' . $filenamePdf]);
            @unlink($savePathDocx);
            @unlink($htmlPath);

            // 13. Buat approval awal
            Approval::create([
                'document_id' => $document->id,
                'user_id' => $user->id,
                'status' => 'pending',
            ]);

            // 14. Log aktivitas
            Log::create([
                'user_id' => $user->id,
                'action' => 'Pengajuan Dokumen',
                'detail' => 'Mengajukan dokumen ' . $documentNumber . ' (' . optional($template->category)->name . ')',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // 15. Respon sukses
            return response()->json([
                'status' => true,
                'message' => 'Pengajuan dokumen berhasil',
                'data' => $document,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal generate dokumen',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}