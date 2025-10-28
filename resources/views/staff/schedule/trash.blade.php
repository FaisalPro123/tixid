@extends('templates.app')

@section('content')
    <div class="container my-3">
        <div class="d-flex justify-content-end">
            <a href="{{ route('staff.schedules.index')}}" class="btn btn-secondary">kembali</a>

        </div>

    </div>
    <h3 class="my-3">jadwal sampah</h3>    
    @if (Session::get('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    <table class="table table-bordered">
        <tr>
            <th>#</th>
            <th>Nama bioskop</th>
            <th>JUdul film</th>
            <th>aksi</th>
        </tr>
        @foreach ($scheduleTrash as $key => $schedule)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $schedule['cinema']['name'] ?? '' }}</td>
                <td>{{ $schedule['movie']['title'] ?? ''}}</td>
                <td class="d-flex">
                    <form action=" {{ route('staff.schedules.restore', $schedule->id)}}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-danger ms-2" >kembalikan</button>
                    </form>
                    <form action=" {{ route('staff.schedules.delete_permanent',  $schedule->id)}}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger ms-2">hapus permanen</button>
                    </form>
                </td>

                <td class="d-flex">
        @endforeach
    </table>
</div>
@endsection