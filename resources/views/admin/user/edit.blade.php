    @extends('templates.app')

    @section('content')
    <div class="w-75 d-block mx-auto my-5 p-4">

        {{-- Breadcrumb dengan shadow --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb p-3 bg-white rounded shadow-sm">
                <li class="breadcrumb-item">Pengguna</a></li>
                <li class="breadcrumb-item">Data</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah</li>
            </ol>
        </nav>

        {{-- Judul sama seperti Buat Data Staff --}}
        <h5 class="text-center my-3">Edit Data Staff</h5>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.users.update', $users['id']) }}">
            @csrf
            @method('PUT')


            {{-- Nama --}}
            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" 
                    class="form-control @error('name') is-invalid @enderror" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $users['name']) }}">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $users['email']) }}">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    id="password" 
                    name="password">
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Tombol sama seperti form Buat --}}
            <button type="submit" class="btn btn-primary w-100 fw-bold">
                BUAT
            </button>
        </form>
    </div>
    @endsection
