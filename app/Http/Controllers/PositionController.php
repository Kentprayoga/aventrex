<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as GlobalRequest; 
use Illuminate\Support\Facades\Auth;
use App\Models\Log;

class PositionController extends Controller
{
    // Menampilkan seluruh posisi
    public function index()
    {
        $positions = Position::all();
        return view('user.index', compact('positions'));
    }

    // Menyimpan posisi baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id' => 'required|unique:positions,id', // Validasi ID yang dimasukkan harus unik
            'position_name' => 'required|string|max:255', // Nama posisi wajib diisi
        ]);
    

        // Simpan posisi baru dengan ID manual yang dimasukkan
        Position::create([
            'id' => $request->id, // Menggunakan ID yang dimasukkan oleh pengguna
            'name' => $request->position_name, // Nama posisi
        ]);
        Log::create([
            'user_id'    => Auth::user()->id, // user admin yang sedang login
            'action'     => 'position_create',
            'detail'     => "Admin menambahkan posisi dengan nama {$request->position_name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Posisi berhasil ditambahkan.');
    }

    // Menampilkan data posisi untuk edit
    public function edit($id)
    {
        $position = Position::findOrFail($id);
        return response()->json(['position' => $position]);
    }

    // Memperbarui posisi
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $position = Position::findOrFail($id);
        $position->update(['name' => $request->name]);

        Log::create([
            'user_id'    => Auth::user()->id, // user admin yang sedang login
            'action'     => 'position_update',
            'detail'     => "Admin memperbarui posisi dengan id {$id} menjadi {$request->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return response()->json(['success' => 'Posisi berhasil diperbarui.']);
    }

    // Menghapus posisi
    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        Log::create([
            'user_id'    => Auth::user()->id, // user admin yang sedang login
            'action'     => 'position_delete',
            'detail'     => "Admin menghapus posisi dengan id {$id}",
            'ip_address' => GlobalRequest::ip(),
            'user_agent' => GlobalRequest::userAgent(),
        ]);
        return response()->json(['success' => 'Posisi berhasil dihapus.']);
    }
    public function getPositions()
    {
        $positions = Position::all();
        return response()->json($positions);
    }
}