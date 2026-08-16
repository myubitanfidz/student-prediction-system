@extends('layouts.app')
@section('title', 'Ubah Akun')

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="font-display font-bold text-2xl mb-6">Ubah Akun</h1>

    <form method="POST" action="{{ route('profil.update') }}" class="card p-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="text-sm font-medium block mb-1.5">Nama</label>
            <input type="text" name="nama" value="{{ $santri['nama'] }}"
                   class="w-full rounded-lg border border-line p-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue">
        </div>
        <div>
            <label class="text-sm font-medium block mb-1.5">Email</label>
            <input type="email" name="email" value="{{ $santri['email'] }}"
                   class="w-full rounded-lg border border-line p-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue">
        </div>
        <div>
            <label class="text-sm font-medium block mb-1.5">Kata Sandi Baru</label>
            <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                   class="w-full rounded-lg border border-line p-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue">
        </div>
        <div>
            <label class="text-sm font-medium block mb-1.5">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation"
                   class="w-full rounded-lg border border-line p-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/40 focus:border-brand-blue">
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('beranda') }}" class="btn-ghost">Batal</a>
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
