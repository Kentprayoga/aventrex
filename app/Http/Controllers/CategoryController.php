<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Request as GlobalRequest; 
use Illuminate\Support\Facades\Auth;
use App\Models\Log;
class CategoryController extends Controller
{
    /**
     * Simpan kategori baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name
        ]);
        Log::create([
            'user_id'    => Auth::user()->id, // user admin yang sedang login
            'action'     => 'category_create',
            'detail'     => "Admin menambahkan kategori dengan nama {$request->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);


        return redirect()->route('template.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Update kategori.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->route('template.index')
                ->withErrors($validator)
                ->withInput();
        }

        $category->update([
            'name' => $request->name
        ]);
        Log::create([
            'user_id'    => Auth::user()->id, // user admin yang sedang login
            'action'     => 'category_update',
            'detail'     => "Admin memperbarui kategori dengan id {$id} menjadi {$request->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('template.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Hapus kategori.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        Log::create([
            'user_id'    => Auth::user()->id, // user admin yang sedang login
            'action'     => 'category_delete',
            'detail'     => "Admin menghapus kategori dengan id {$id}",
            'ip_address' => GlobalRequest::ip(),
            'user_agent' => GlobalRequest::userAgent(),
        ]);

        return redirect()->route('template.index')->with('success', 'Kategori berhasil dihapus!');
    }
}