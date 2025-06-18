<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Category;
use App\Models\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as GlobalRequest; 
class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::with('category')->latest()->get();
        $categories = Category::all();
        return view('template.index', compact('templates', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categorie_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'file_path' => 'required|file|mimes:doc,docx,pdf'
        ]);

        $kategori = Category::find($request->categorie_id)->name;
        $category = Category::findOrFail($request->categorie_id);
        $year = now()->year;

        $lastTemplate = Template::where('categorie_id', $category->id)
                                ->whereYear('created_at', $year)
                                ->latest()
                                ->first();

        $lastNumber = $lastTemplate ? (int) substr($lastTemplate->format_nomor, -4) : 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        $formatNomor = 'HR' . '/' . strtoupper($kategori) . '/' . $year . '/' . $newNumber;
        $safeName = str_replace(['/', '\\', ' '], '_', $formatNomor);

        $file = $request->file('file_path');
        $filename = $safeName . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('templates', $filename, 'public');

        Template::create([
            'categorie_id' => $category->id,
            'name' => $request->name,
            'format_nomor' => $formatNomor,
            'file_path' => $path,
        ]);
        Log::create([
            'user_id'    => Auth::user()->id, // user admin yang sedang login
            'action'     => 'template_create',
            'detail'     => "Admin mebuat template dengan nama " . $request->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return redirect()->back()->with('success', 'Template berhasil ditambahkan.');
    }
    public function edit($id)
    {
        $template = Template::findOrFail($id);
        $categories = Category::all();
        return view('template.edit', compact('template', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $template = Template::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'categorie_id' => 'required|exists:categories,id',
            'file_path' => 'nullable|file|mimes:doc,docx,pdf',
        ]);

        $template->name = $request->name;
        $template->categorie_id = $request->categorie_id;
        $template->updated_at = now();
        

        // Buat ulang format nomor
        $tahun = now()->year;
        $kategori = Category::find($request->categorie_id)->name;
        //        $formatNomor = 'HR' . '/' . strtoupper($request->name) . '/' . $year . '/' . $newNumber;
        $format_nomor ='HR' . '/'. strtoupper(str_replace(' ', '_', $kategori)) . '/' . $tahun . '/' . str_pad($template->id, 3, '0', STR_PAD_LEFT);
        $template->format_nomor = $format_nomor;

        // Cek jika ada file baru diupload
        if ($request->hasFile('file_path')) {
            // Hapus file lama jika ada
            if ($template->file_path && Storage::disk('public')->exists($template->file_path)) {
                Storage::disk('public')->delete($template->file_path);
            }

            // Simpan file baru
            $extension = $request->file('file_path')->getClientOriginalExtension();
            $fileName = str_replace('/', '_', $format_nomor) . '_' . time() . '.' . $extension;
            $path = $request->file('file_path')->storeAs('templates', $fileName, 'public');
            $template->file_path = $path;
        }

        $template->save();
        Log::create([
            'user_id'    => Auth::user()->id, // user admin yang sedang login
            'action'     => 'update_template',
            'detail'     => "Admin meberbarui template dengan id {$request->id}  dengan nama " . $request->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return redirect()->route('template.index')->with('success', 'Template berhasil diperbarui.');
    }


    public function destroy($id, Request $request)
    {
        $template = Template::findOrFail($id);
        
        $nama = Template::findOrFail($id)->name;
        // Cek apakah file ada dan hapus
            
        if ($template->file_path && Storage::disk('public')->exists($template->file_path)) {
            Storage::disk('public')->delete($template->file_path);
        }

        $template->delete();
                Log::create([
            'user_id'    => Auth::user()->id, // user admin yang sedang login
            'action'     => 'delete_template',
            'detail'     => "Admin menghapus template dengan id {$request->id} ".$nama ,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return back()->with('success', 'Template berhasil dihapus.');
    }


    // Tambahkan method edit, update, destroy jika perlu
}