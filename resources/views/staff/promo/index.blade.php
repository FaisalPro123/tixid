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

        <table class="table table-bordered" id="tablePromo">
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
                
            </tbody>
        </table>
    </div>
@endsection
@push('script')
<script>
$(function() {
    $("#tablePromo").DataTable({   
        processing: true,
        serverSide: true,
        ajax: '{{ route('staff.promos.datatables') }}',
        columns: [
                    {data: 'DT_RowIndex',name: 'DT_RowIndex',orderable:false,searchable:false},
                    {data: 'promo_code',name: 'promo_code',orderable:true,searchable:true},
                    {data: 'type',name: 'type',orderable:true,searchable:true},
                    {data: 'discount',name: 'discount',orderable:true,searchable:true},
                    {data: 'btnActions',name: 'btnActions',orderable:true,searchable:false},
                ]
    });
});
</script>
@endpush
