@extends('templates.app')
@section('content')
    <div class="container mt-5">
        <h5 class="mb-5">Seluruh Film Sedang Tayang</h5>
        <form action="" method="GET">
            @csrf
            <div class="row">
                <div class="col-10">
                    <input type="text" name="search_movie" placeholder="cari judul film" class="form-control">
                </div>
                <div class="col-2">
                    <button type="submit" class="btn btn-primary">cari</button>

                </div>
            </div>
        </form>
        <div class="d-flex justify-content-center flex-wrap gap-2 my-3">
            @foreach ($movies as $movie)
                <div class="card" style="width: 13rem;">
                    <img src={{ asset('storage/' . $movie->poster) }} class="card-img-top" alt="{{ $movie->title }}"
                        style="height: 300px; object-fit: cover;" />
                    <div class="card-body" style="padding: 0 !important">
                        <p class="card-text text-center bg-primary py-2"><a
                                href="{{ route('schedules.detail', $movie->id) }}" class="text-warning">beli tiket</a>

                    </div>

                </div>
            @endforeach
        </div>
    @endsection
