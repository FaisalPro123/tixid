@extends('templates.app')

@section('content')
<div class="container my-3">
    <div class="d-flex justify-content-end">
        <a href="{{ route('admin.cinemas.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<h3 class="my-3">Data Sampah Bioskop</h3>

@if (Session::get('success'))
    <div class="alert alert-success text-center">{{ Session::get('success') }}</div>
@endif

<div class="container">
    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Nama Bioskop</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cinemaTrash as $key => $cinema)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $cinema->name }}</td>
                    <td>{{ $cinema->location }}</td>
                    <td class="d-flex justify-content-center">
                        {{-- Tombol Restore --}}
                        <form action="{{ route('admin.cinemas.restore', $cinema->id) }}" method="POST" class="me-2">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning btn-sm">Kembalikan</button>
                        </form>

                        {{-- Tombol Hapus Permanen --}}
                        <form action="{{ route('admin.cinemas.delete_permanent', $cinema->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus Permanen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-muted">Tidak ada data bioskop yang dihapus.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
