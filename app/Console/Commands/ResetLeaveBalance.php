<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'leave:reset')]
class ResetLeaveBalance extends Command
{
    protected $description = 'Reset cuti tahunan semua karyawan berdasarkan masa kerja';

    public function handle(): void
    {
        $users = User::with(['profile', 'leaveBalance'])->get();

        foreach ($users as $user) {
            if (!$user->profile || !$user->profile->tglmasuk) {
                $this->warn("User ID {$user->id} tidak punya tanggal masuk, skip.");
                continue;
            }

            $tglMasuk = Carbon::parse($user->profile->tglmasuk);
            $now = Carbon::now();
            $masaKerjaTahun = $tglMasuk->diffInYears($now);

            // Aturan jatah cuti
            if ($masaKerjaTahun < 1) {
                $jatahCuti = 0;
            } elseif ($masaKerjaTahun < 5) {
                $jatahCuti = 12;
            } else {
                $jatahCuti = 15;
            }

            $user->leaveBalance()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'total_leave' => $jatahCuti,
                    'used_leave' => 0,
                    'remaining_leave' => $jatahCuti,
                ]
            );

            $this->info("User ID {$user->id} - Jatah cuti direset ke {$jatahCuti} hari.");
        }

        $this->info('Reset cuti berdasarkan masa kerja selesai.');
    }
}