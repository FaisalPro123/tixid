@extends('templates.app')

@section('content')
    <div class="w-75 d-block mx-auto my-5 p-4">
        <form method="POST" action="{{ route('staff.promos.update', $promo->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="promo_code" class="form-label">Kode Promo</label>
                <input type="text"
                       class="form-control @error('promo_code') is-invalid @enderror"
                       id="promo_code"
                       name="promo_code"
                       value="{{ old('promo_code', $promo->promo_code) }}">
                @error('promo_code')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Tipe Promo</label>
                <select id="type"
                        class="form-control @error('type') is-invalid @enderror"
                        name="type">
                    <option value="">Pilih Tipe</option>
                    <option value="percent" {{ old('type', $promo->type) == 'percent' ? 'selected' : '' }}>% (Persen)</option>
                    <option value="rupiah" {{ old('type', $promo->type) == 'rupiah' ? 'selected' : '' }}>Rp (Rupiah)</option>
                </select>
                @error('type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label for="discount" class="form-label">Jumlah Potongan</label>
                <input type="number"
                       class="form-control @error('discount') is-invalid @enderror"
                       id="discount"
                       name="discount"
                       value="{{ old('discount', $promo->discount) }}">
                @error('discount')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Data</button>
        </form>
    </div>
@endsection