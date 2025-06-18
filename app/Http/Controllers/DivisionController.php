<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as GlobalRequest; 
use Illuminate\Support\Facades\Auth;
use App\Models\Log;

class DivisionController extends Controller
{
    // Menampilkan seluruh divisi
    public function index()
    {
        $divisions = Division::all();
        return view('user.index', compact('divisions'));
    }

    // Menyimpan divisi baru
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|unique:divisions,id',
            'name' => 'required'
        ]);

        Division::create([
            'id' => $request->id,
            'name' => $request->name,
        ]);
        Log::create([
            'user_id'    => Auth::user()->id, // user admin yang sedang login
            'action'     => 'division_create',
            'detail'     => "Admin menambahkan divisi dengan nama {$request->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Divisi berhasil ditambahkan!');
    }

    // Menampilkan form untuk edit divisi
    public function edit($id)
    {
        $division = Division::findOrFail($id);
        return response()->json(['division' => $division]);
    }

    // Memperbarui divisi
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $division = Division::findOrFail($id);
        $division->update(['name' => $request->name]);

        Log::create([
            'user_id'    => Auth::user()->id, // user admin yang sedang login
            'action'     => 'division_update',
            'detail'     => "Admin memperbarui divisi dengan id {$id} menjadi {$request->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return response()->json(['success' => 'Divisi berhasil diperbarui.']);
    }

    // Menghapus divisi
    public function destroy($id)
    {
        $division = Division::findOrFail($id);
        $division->delete();
        Log::create([
            'user_id'    => Auth::user()->id, // user admin yang sedang login
            'action'     => 'division_delete',
            'detail'     => "Admin menghapus divisi dengan id {$id}",
            'ip_address' => GlobalRequest::ip(),
            'user_agent' => GlobalRequest::userAgent(),
        ]);

        return response()->json(['success' => 'Divisi berhasil dihapus.']);
    }
    public function getDivisions()
    {
        $divisions = Division::all();
        return response()->json($divisions);
    }
}