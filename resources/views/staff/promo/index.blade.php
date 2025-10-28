@extends('templates.app')

@section('content')
    <div class="container my-5">
        <h3>Data Promo</h3>
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('staff.promos.trash') }}" class="btn btn-secondary me-2">data sampah</a>
            <a href="{{ route('staff.promos.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('staff.promos.create') }}" class="btn btn-success">Tambah Promo</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode Promo</th>
                    <th>Tipe Diskon</th>
                    <th>Nilai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($promos as $key => $promo)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $promo->promo_code }}</td>
                        <td>{{ $promo->type }}</td>
                        <td>
                            @if ($promo->type == 'percent')
                                {{ $promo->discount }}%
                            @else
                                Rp {{ number_format($promo->discount, 0, ',', '.') }}
                            @endif
                        </td>
                <td class="d-flex">
                    <a href="{{ route('staff.promos.edit',$promo->id)}}" class="btn btn-primary">edit</a>
                    <form action="{{ route('staff.promos.destroy',$promo->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                    <button class="btn btn-danger ms-2">hapus</button>
                    </form>

                            </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
