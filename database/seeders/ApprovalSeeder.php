<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Approval;
use App\Models\Document;

class ApprovalSeeder extends Seeder
{
    public function run()
    {
        // Cari atau buat dokumen dummy
        $document = Document::first(); // Ambil dokumen pertama
        if (!$document) {
            // Kalau belum ada, buat dokumen dummy
            $document = Document::create([
                'user_id' => 2,
                'categorie_id' => 1023,
                'template_id' => 7,
                'document_number' => 'DOC-0001',
                'name' => 'Dokumen Dummy',
                'alasan' => 'Contoh alasan pengajuan',
                'file_path' => null,
                'tanggal_pengajuan' => now(),
            ]);
        }

        // Buat approval
        Approval::create([
            'document_id' => $document->id,
            'user_id' => 2,
            'status' => 'pending',
        ]);
    }
}