    @extends('templates.app')

    @section('content')
        <div class="container my-3">
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>

        <h3 class="my-3">Data Sampah film</h3>

        @if (Session::get('success'))
            <div class="alert alert-success text-center">{{ Session::get('success') }}</div>
        @endif

        <div class="container">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Judul Film</th>
                        <th>status aktif</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($MovieTrash as $key => $movie)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $movie->title }}</td>
                            <td>
                                @if ($movie->actived == 1)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Non Aktif</span>
                                @endif
                            </td>

                            <td class="d-flex justify-content-center">
                                {{-- Tombol Restore --}}
                                <form action="{{ route('admin.movies.restore', $movie->id) }}" method="POST"
                                    class="me-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning btn-sm">Kembalikan</button>
                                </form>

                                {{-- Tombol Hapus Permanen --}}
                                <form action="{{ route('admin.movies.delete_permanent', $movie->id) }}" method="POST">
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
