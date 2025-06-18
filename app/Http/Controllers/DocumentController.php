<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as GlobalRequest; 
class DocumentController extends Controller
{
public function index(Request $request)
{
    $categories = Category::all();

    $query = Document::with(['category', 'template', 'approval', 'user']);

    if ($request->filled('categorie_id')) {
        $query->whereHas('template', function ($q) use ($request) {
            $q->where('categorie_id', $request->categorie_id);
        });
    }

    if ($request->filled('status')) {
        $query->whereHas('approval', function ($q) use ($request) {
            $q->where('status', $request->status);
        });
    }

    if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
        $query->whereBetween('submission_date', [$request->tanggal_mulai, $request->tanggal_selesai]);
    }

    $documents = $query->get();

    return view('history.index', compact('documents', 'categories'));
}


    public function cetakHistory(Request $request)
    {
        $query = Document::with(['template.category', 'approval', 'user']);

        if ($request->filled('category_id')) {
            $query->whereHas('template', function ($q) use ($request) {
                $q->where('categore_id', $request->category_id);
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('approval', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('submission_date', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        $documents = $query->get();

        $pdf = PDF::loadView('history.cetak-history', compact('documents'));
        return $pdf->download('history-dokumen.pdf');
    }

    public function cetakSatu($id)
    {
        $document = Document::with(['template.category', 'approval', 'user'])->findOrFail($id);

        $pdf = PDF::loadView('history.cetak-satu', compact('document'));
        return $pdf->download("history-{$document->id}.pdf");
    }


}