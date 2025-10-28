@extends('templates.app')

@section('content')
<div class="container my-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb p-3 bg-white rounded shadow-sm">
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah User</li>
        </ol>
    </nav>

    {{-- Card Form --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="text-center mb-4">Tambah User</h5>

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                {{-- Nama User --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="" disabled selected>Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                    </select>
                    @error('role')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tombol --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary fw-bold">SIMPAN</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">KEMBALI</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
