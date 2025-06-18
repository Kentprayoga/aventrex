@extends('layouts.app')

{{-- ✅ Tambahkan CSS responsif dan tema --}}
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .chat-container {
        display: flex;
        flex-direction: row;
        height: 80vh;
        gap: 20px;
    }

    .chat-sidebar {
        width: 30%;
        background-color: #1f2937; /* dark blue-gray */
        border-radius: 10px;
        overflow-y: auto;
        color: white;
    }

    .chat-sidebar a {
        display: block;
        padding: 12px 16px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        text-decoration: none;
        color: white;
    }

    .chat-sidebar a:hover {
        background-color: #374151;
    }

    .chat-sidebar a.active {
        background-color: #3b82f6; /* blue */
    }

    .chat-content {
        flex-grow: 1;
        background-color: #f3f4f6;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
    }

    .chat-message {
        max-width: 60%;
        padding: 10px 15px;
        margin-bottom: 10px;
        border-radius: 20px;
        word-wrap: break-word;
    }

    .chat-admin {
        background-color: #a7f3d0; /* green */
        margin-left: auto;
        border-bottom-right-radius: 0;
    }

    .chat-user {
        background-color: #e5e7eb; /* gray */
        margin-right: auto;
        border-bottom-left-radius: 0;
    }

    .chat-footer textarea {
        resize: none;
    }

    @media (max-width: 768px) {
        .chat-container {
            flex-direction: column;
        }

        .chat-sidebar {
            width: 100%;
        }

        .chat-content {
            display: none;
        }

        .chat-content.active {
            display: flex;
        }

        .chat-sidebar.hidden-mobile {
            display: none;
        }
    }
</style>

@section('content')
<div class="container chat-container">

    {{-- ✅ Sidebar Chat --}}
    <div class="chat-sidebar {{ $selectedUser ? 'hidden-mobile' : '' }}" id="chatSidebar">
        <div class="p-3 border-bottom" style="background-color: #111827;">
            <h5 style="margin: 0;">📋 Daftar Chat</h5>
        </div>

        {{-- 🔍 Pencarian --}}
        <div class="p-3">
            <form method="GET" action="{{ route('admin.chat') }}">
                <input type="text" name="search" class="form-control" placeholder="Cari nama..." value="{{ request('search') }}">
            </form>
        </div>

        {{-- 👥 Daftar Pengguna --}}
        @foreach($users as $user)
            <a href="{{ route('admin.chat', ['user_id' => $user->id]) }}"
               class="{{ $selectedUser && $selectedUser->id == $user->id ? 'active' : '' }}">
                <strong>{{ $user->profile->name ?? $user->email }}</strong>
            </a>
        @endforeach
    </div>

    {{-- 💬 Area Chat --}}
    <div class="chat-content {{ $selectedUser ? 'active' : '' }}" id="chatContent">
        @if($selectedUser)
            {{-- 🔙 Tombol Kembali (Mobile) --}}
            <div class="d-block d-md-none p-2 border-bottom bg-white">
                <button class="btn btn-sm btn-outline-secondary" onclick="goBackToSidebar()">← Kembali</button>
            </div>

            {{-- Header --}}
            <div style="padding: 15px; border-bottom: 1px solid #ccc; background-color: white;">
                <h5>Chat dengan: <strong>{{ $selectedUser->profile->name ?? $selectedUser->email }}</strong></h5>
            </div>

            {{-- 🗨️ Chat --}}
            <div style="flex-grow: 1; padding: 15px; overflow-y: auto;">
                @foreach($messages as $msg)
                    <div class="chat-message {{ $msg->sender_id == auth()->id() ? 'chat-admin' : 'chat-user' }}">
                        <strong>{{ $msg->sender_id == auth()->id() ? 'Admin' : $selectedUser->profile->name ?? $selectedUser->email }}</strong>
                        <p style="margin: 5px 0;">{{ $msg->message }}</p>
                        <small style="font-size: 0.8rem; color: #555;">{{ $msg->created_at->format('d M Y H:i') }}</small>

                        @if($msg->sender_id == auth()->id())
                            <form action="{{ route('admin.chat.delete', ['id' => $msg->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-sm mt-1" onclick="return confirm('Hapus pesan ini?')">Hapus</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- ✉️ Kirim Pesan --}}
            <form method="POST" action="{{ route('admin.chat', ['user_id' => $selectedUser->id]) }}" class="chat-footer p-3 border-top bg-white">
                @csrf
                <textarea name="message" class="form-control mb-2" rows="2" placeholder="Tulis balasan..." required></textarea>
                <button type="submit" class="btn btn-primary w-100">Kirim Balasan</button>
            </form>

            {{-- 🗑️ Hapus Semua --}}
            <form action="{{ route('admin.chat.clear', ['userId' => $selectedUser->id]) }}" method="POST" class="p-3 bg-white border-top">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Hapus semua pesan dengan pengguna ini?')">Hapus Semua Pesan</button>
            </form>

        @else
            <div style="flex-grow: 1; display: flex; align-items: center; justify-content: center; color: #777;">
                <p>Pilih pengguna dari daftar untuk melihat percakapan.</p>
            </div>
        @endif
    </div>
</div>

{{-- ✅ Script untuk mobile toggle --}}
<script>
    function goBackToSidebar() {
        document.getElementById('chatContent').classList.remove('active');
        document.getElementById('chatSidebar').classList.remove('hidden-mobile');
    }

    document.querySelectorAll('.chat-sidebar a').forEach(link => {
        link.addEventListener('click', function () {
            setTimeout(() => {
                document.getElementById('chatContent').classList.add('active');
                document.getElementById('chatSidebar').classList.add('hidden-mobile');
            }, 100);
        });
    });
</script>
@endsection
