@extends('templates.app')

@section('content')
<div class="container mt-3">
    @if (Session::get('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    @if (Session::get('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif  
    <div class="d-flex justify-content-end">
        <a href="{{route('admin.cinemas.export')}}" class="btn btn-secondary me-2">Export (.xlsx)</a>
        <a href="{{ route('admin.cinemas.trash') }}" class="btn btn-secondary me-2">data sampah</a>
        <a href="{{ route('admin.cinemas.create') }}" class="btn btn-success">Tambah Data</a>
    </div>
    <h5 class="mt-3">Data Bioskop</h5>
    <table class="table table-bordered" id="tableCinema">
        <thead>
        <tr>
            <th>#</th>
            <th>Nama Bioskop</th>
            <th>Lokasi</th>
            <th>Aksi</th>
        </tr>
    </thead>
        <!-- $cinemas dari compact -->
        <!-- foreach karena $cinemas pake ::all() datanya lebih dari satu dan berbentuk array -->
@push('script')
<script>
$(function() {
    $("#tableCinema").DataTable({   
        processing: true,
        serverSide: true,
        ajax: '{{ route('admin.cinemas.datatables') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' }, // kolom sesuai database
            { data: 'location', name: 'location' },
            { data: 'btnActions', name: 'btnActions', orderable: false, searchable: false },
        ]
    });
});
</script>
@endpush