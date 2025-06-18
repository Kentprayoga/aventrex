<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Log;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    /**
     * Menampilkan semua template beserta kategori dan file URL.
     */
    public function index(Request $request)
    {
        // Ambil data template dengan relasi kategori
        $rawTemplates = Template::with('category')->get();

        // Ubah ke bentuk array rapi
        $templates = $rawTemplates->map(function ($template) {
            return [
                'id' => $template->id,
                'categorie_id' => $template->categorie_id,
                'name' => $template->name,
                'category_name' => $template->category->name ?? null,
                'format_nomor' => $template->format_nomor,
                'file_path' => $template->file_path,
                'file_url' => $template->file_path ? asset('storage/' . $template->file_path) : null,
            ];
        });

        // Simpan log jika user terautentikasi
        if ($request->user()) {
            // Gabungkan ID dan nama kategori untuk log
            $templateLogList = $rawTemplates->map(function ($t) {
                return 'ID: ' . $t->id . ' (' . ($t->category->name ?? '-') . ')';
            })->implode(', ');

            Log::create([
                'user_id'    => $request->user()->id,
                'action'     => 'Akses daftar template',
                'detail'     => 'User mengakses daftar template: ' . $templateLogList,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        // Kembalikan response
        return response()->json([
            'success' => true,
            'data' => $templates
        ], 200);
    }

    /**
     * Versi ringkas list template tanpa file_url.
     */
    public function listTemplates()
    {
        $templates = Template::with('category')->get()->map(function ($template) {
            return [
                'id' => $template->id,
                'categorie_id' => $template->categorie_id,
                'format_nomor' => $template->format_nomor,
                'category_name' => $template->category->name ?? null,
                'name' => $template->name,
                // file_url tidak disertakan
            ];
        });

        return response()->json(['success' => true, 'data' => $templates], 200);
    }
}