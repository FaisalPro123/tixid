@extends('templates.app')

@section('content')
<div class="container my-3">
    <div class="d-flex justify-content-end">
        <a href="{{ route('staff.promos.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<h3 class="my-3">Data Sampah Promo</h3>

@if (Session::get('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
@endif

<div class="container">
    <table class="table table-bordered text-center align-middle">
        <tr>
            <th>#</th>
            <th>Kode Promo</th>
            <th>Tipe Diskon</th>
            <th>Nilai</th>
            <th>Aksi</th>
        </tr>

        @foreach ($promoTrash as $key => $promo)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $promo->promo_code }}</td>
                <td>{{ $promo->type }}</td>
                <td>
                    @if ($promo->type === 'percent')
                        {{ $promo->discount }}%
                    @else
                        Rp {{ number_format($promo->discount, 0, ',', '.') }}
                    @endif
                </td>
                <td class="d-flex justify-content-center">
                    {{-- Restore --}}
                    <form action="{{ route('staff.promos.restore', $promo->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-warning btn-sm me-2">Kembalikan</button>
                    </form>

                    {{-- Hapus Permanen --}}
                    <form action="{{ route('staff.promos.delete_permanent', $promo->id) }}" method="POST">
                    @csrf
                        @method('DELETE')
                        <button class="btn btn-danger ms-2">hapus permanen</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endsection
