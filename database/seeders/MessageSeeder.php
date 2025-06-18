<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                // Mengirimkan pesan pertama dari admin ke user1

        // Mengirimkan pesan antara user1 dan admin
        Message::create([
            'sender_id' => 2, // User 1
            'receiver_id' => 1, // Admin
            'message' => 'Thank you for the message, Admin.',
        ]);

        // Mengirimkan pesan antara user2 dan admin

    
    }
}