@extends('templates.app')
@section('content')
    <div class="container card my-5 p-4">
        <div class="card-body">
        <div class="d-flex justify-content-end">
            <a href="{{ route('ticket.export.pdf', $ticket['id']) }}" class="btn btn-secondary">unduh (pdf.)</a>
        </div>
        <div class="d-flex flex-wrap gap-3-center">
            @foreach ($ticket['rows_of_seats'] as $kursi)
                <div class="p-2">
                    <div class="d-flex justify-content-center-end-center">
                        <b>{{ $ticket['schedule']['movie']['title'] }}</b>
                    </div>
                    <hr>
                    <b>{{ $ticket['schedule']['movie']['title'] }}</b>
                    <br>
                    <p>Tanggal :
                        {{ \carbon\carbon::parse($ticket['ticketPayment']['booket_date'])->format('d F, Y') }}
                    </p>
                    <p>Tanggal :
                        {{ \carbon\carbon::parse($ticket['hour'])->format('H:i') }}
                    </p>
                    <p>kursi : {{ $kursi }}</p>
                    <p>Harga : Rp. {{ number_format($ticket['schedule']['price'], 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>
    </div>
@endsection
