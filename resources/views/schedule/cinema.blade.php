@extends('templates.app')

@section('content')
    <div class="container my-5">
        <h5>daftar bioskop</h5>
        @foreach ($cinemas as $cinema)
        <a href="{{ route('cinema.schedule',$cinema->id)}}" class="card mt-3">
            <div class="card-body d-flex justify-content-between align-item-center">
                <div>
                    <i class="fa-solid fa-star text-secondary me-3"></i>
                    <b>{{ $cinema['name'] }}</b>

                    </div>
                    <div>
                        <i class="fa-solid fa-arrow-right text-secondary"></i>
                    </div>
            </div>
        </a>
        @endforeach
    </div>
    @endsection