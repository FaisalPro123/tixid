@extends('templates.app')

@section('content')
<div class="container mt-3">
    {{-- Notifikasi sukses --}}
    @if (Session::get('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    {{-- Tombol tambah data --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.users.trash')}}" class="btn btn-secondary me-2">Data sampah</a>
        <a href="{{ route('admin.users.export')}}" class="btn btn-secondary me-2">Export (.xlsx)</a>
        <a href="{{ route('admin.users.create') }}" class="btn btn-success">Tambah Data</a>
    </div>

    <h5>Data Pengguna (Admin & Staff)</h5> 

    <table class="table table-bordered" id="tableUser">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
@push('script')
    <script>    
        $(function() {
            $("#tableUser").DataTable({
                processing: true,
                serverSide: true,
                ajax:"{{ route('admin.users.datatables') }}",
                columns: [
                    {data: 'DT_RowIndex',name: 'DT_RowIndex',orderable:false,searchable:false},
                    {data: 'name',name: 'name',orderable:true,searchable:true},
                    {data: 'email',name: 'email',orderable:true,searchable:false},
                    {data: 'role',name: 'role',orderable:true,searchable:false},
                    {data: 'btnActions',name: 'btnActions',orderable:true,searchable:false},
                    
                ]
    });
});
</script>
@endpush
@endsection
