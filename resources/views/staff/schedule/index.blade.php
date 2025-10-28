@extends('templates.app')

@section('content')
    <div class="container my-3">
        <div class="d-flex justify-content-end">
            <a href="{{ route('staff.schedules.trash')}}" class="btn btn-secondary me-2">data sampah</a>
            <a href="{{ route('staff.schedules.export') }}" class="btn btn-secondary me-2">EXPORT DATA</a>

            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdd">tambah data</button>

        </div>

    </div>
    <h3 class="my-3">data jadwal penayangan</h3>
    @if (Session::get('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
        
@endif
    <table class="table table-bordered" id="tableschedules">
        <tr>
            <th>#</th>
            <th>Nama bioskop</th>
            <th>JUdul film</th>
            <th>Harga</th>
            <th>jam tayang</th>
            <th>aksi</th>
        </tr>
        @foreach ($schedules as $key => $schedule)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $schedule['cinema']['name'] ?? '' }}</td>
                <td>{{ $schedule['movie']['title'] ?? ''}}</td>
                <td>{{ $schedule->price }}</td>
                <td>
                    <ul>
                        @foreach ($schedule['hours'] as $hours)
                            <li>{{ $hours }}</li>
                        @endforeach
                    </ul>
                </td>

                <td class="d-flex">
                    <a href="{{ route('staff.schedules.edit',$schedule->id)}}" class="btn btn-primary">edit</a>
                    <form action="{{ route('staff.schedules.delete',$schedule->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                    <button class="btn btn-danger ms-2">hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    {{-- modal --}}
    <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalAddLabel">Tambah data</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('staff.schedules.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label">bioskop</label>
                            <select name="cinema_id" id="cinema_id"
                                class="form-select @error('cinema_id') is-invalid @enderror">
                                <option disabled hidden selected>pilih bioskop</option>
                                @foreach ($cinemas as $cinema)
                                    <option value="{{ $cinema->id }}">{{ $cinema->name }}</option>
                                @endforeach
                            </select>
                            @error('cinema_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror

                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Film</label>
                            <select name="movie_id" id="movie_id"
                                class="form-select @error('movie_id') is-invalid @enderror">
                                <option disabled hidden selected>pilih film</option>
                                @foreach ($movies as $movie)
                                    <option value="{{ $movie->id }}">{{ $movie->title }}</option>
                                @endforeach
                            </select>
                            @error('movie_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">harga</label>
                            <input type="number" name="price" id="price" class="form-control"
                                @error('price') is-invalid @enderror>
                            @error('price')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            @if ($errors->has('hours'))
                                <small class="text-id">{{ $errors->first('hours.*') }}</small>
                            @endif
                            <label for="hours" class="form-label">tayang</label>
                            <input type="time" name="hours[]" id="hours" class="form-control"
                                @if ($errors->has('hours')) is-invalid @endif>
                            <div id="additionalinput"></div>
                            <span class="text-primary mt-3" style="cursor:pointer" onclick="addInput()">+Tambah data</span>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">batal</button>
                            <button type="submit" class="btn btn-primary">kirim</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>    
        $(function() {
            $("#tableschedules").DataTable({
                processing: true,
                serverSide: true,
                ajax:"{{ route('admin.schedules.datatables') }}",
                columns: [
                    {data: 'DT_RowIndex',name: 'DT_RowIndex',orderable:false,searchable:false},
                    {data: 'cinema',name: 'cinema',orderable:true,searchable:true},
                    {data: 'movie',name: 'movie',orderable:true,searchable:false},
                    {data: 'price',name: 'price',orderable:true,searchable:false},
                    {data: 'hours',name: 'hours',orderable:true,searchable:false},
                    {data: 'btnActions',name: 'btnActions',orderable:true,searchable:false},
                    
                ]
    });
});
</script>
@endpush
@endsection
    <script>
        function addInput() {
            let content = '<input type="time" name="hours[]" class="form-control my-3">';
            let wadah = document.querySelector('#additionalinput');
            wadah.innerHTML += content;
        }
    </script>
    @if ($errors->any())
        <script>
            let modalAdd = document.querySelector('#modalAdd');
            new boostrap.Modal(modalAdd).show();
        </script>
    @endif
@endpush
