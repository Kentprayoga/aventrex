<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkDivision;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\Detail;
use App\Models\Position;
use App\Models\Division;
use App\Models\LeaveBalance;
use App\Models\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class ProfileController extends Controller
{
    public function updatePassword(Request $request)
{
    $user = $request->user();

    $validator = Validator::make($request->all(), [
        'old_password' => 'required',
        'new_password' => 'required|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    if (!Hash::check($request->old_password, $user->password)) {
        return response()->json(['error' => 'Password lama salah.'], 401);
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json(['message' => 'Password berhasil diperbarui.'], 200);
}
    // public function getProfile(Request $request)
    // {
    //     try {
    //         $user = $request->user(); // Mendapatkan user yang sedang login (terautentikasi)

    //         // Mengambil data profil user
    //         $profile = Profile::where('user_id', $user->id)->first();
    //         $details = Detail::where('user_id', $user->id)->first();

    //         if (!$profile || !$details) {
    //             return response()->json(['error' => 'Data profil atau detail tidak ditemukan.'], 404);
    //         }

    //         $position = Position::find($details->position_id);

    //         if (!$position) {
    //             return response()->json(['error' => 'Posisi tidak ditemukan.'], 404);
    //         }

    //         // Mengambil data divisi (bisa lebih dari satu)
    //         $divisions = WorkDivision::where('detail_id', $details->id)
    //                                 ->with('division')  // Mengambil relasi division
    //                                 ->get()
    //                                 ->map(function($workDivision) {
    //                                     return $workDivision->division->name; // Mengambil nama divisi
    //                                 })
    //                                 ->toArray();

    //         return response()->json([
    //             'user_id' => $user->id,
    //             'email' => $user->email,  // Mengambil email dari tabel user
    //             'profile' => $profile,
    //             'details' => $details,
    //             'position' => $position,
    //             'divisi' => $divisions,  // Menambahkan divisi sebagai array
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage() . ' ' . $e->getTraceAsString()], 500);
    //     }
    // }


    public function getProfile(Request $request)
{
    try {
        $user = $request->user();

        $profile = Profile::where('user_id', $user->id)->first();
        $details = Detail::where('user_id', $user->id)->first();

        if (!$profile || !$details) {
            return response()->json(['error' => 'Data profil atau detail tidak ditemukan.'], 404);
        }

        $position = Position::find($details->position_id);

        if (!$position) {
            return response()->json(['error' => 'Posisi tidak ditemukan.'], 404);
        }

        $divisions = WorkDivision::where('detail_id', $details->id)
                                ->with('division')
                                ->get()
                                ->map(fn($workDivision) => $workDivision->division->name)
                                ->toArray();

        // Ambil data leave balance
        $leaveBalance = LeaveBalance::where('user_id', $user->id)->first();

        return response()->json([
            'user_id' => $user->id,
            'email' => $user->email,
            'profile' => $profile,
            'details' => $details,
            'position' => $position,
            'divisi' => $divisions,
            'leave_balance' => [
                'total' => $leaveBalance->total_leave ?? 0,
                'used' => $leaveBalance->used_leave ?? 0,
                'remaining' => $leaveBalance->remaining_leave ?? 0,
            ],
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    }
}

    public function profile()
    {
        $user = Auth::user()->load([
            'profile',
            'detail.position',
            'detail.workDivision.division',
            'leaveBalance'
        ]);

        return response()->json([
            'email' => $user->email,
            'status' => $user->status,
            'profile' => [
                'name' => $user->profile->name ?? null,
                'gender' => $user->profile->gender ?? null,
                'tgl_lahir' => $user->profile->tgllahir ?? null,
                'alamat' => $user->profile->address ?? null,
                'nip' => $user->profile->nip ?? null,
                'phone_number' => $user->profile->phone_number ?? null,
                'tgl_masuk' => $user->profile->tglmasuk ?? null,
            ],
            'position' => $user->detail->position->name ?? null,
            'division' => $user->detail->workDivision->division->name ?? null,
            'leave_balance' => [
                'total' => $user->leaveBalance->total_leave ?? 0,
                'used' => $user->leaveBalance->used_leave ?? 0,
                'remaining' => $user->leaveBalance->remaining_leave ?? 0,
            ]
        ], 200);
    }

}