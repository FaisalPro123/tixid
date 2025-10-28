@extends('templates.app')

@section('content')
<div class="container my-5">
    <h3>Tambah Promo</h3>
    <form action="{{ route('staff.promos.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Kode Promo</label>
            <input type="text" name="promo_code" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tipe Diskon</label>
            <select name="type" class="form-control" required>
                <option value="percent">Persen (%)</option>
                <option value="nominal">Nominal (Rp)</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah Potongan</label>
            <input type="number" name="discount" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">kirim</button>
        <a href="{{ route('staff.promos.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
