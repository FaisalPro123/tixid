@extends('templates.app')
@section('content')
    <div class="container card my-5 p-4">
        <div class="card-body">
            <h5 class="text-center">Selesaikan pembayaran</h5>
            <img src="{{ asset('storage/'. $ticket['ticketPayment']['qrcode'] )}}" alt="Black and white QR code representing the ticket payment for this booking; meant to be scanned to complete payment. Centered on a plain light background. No readable text is present. Neutral and functional tone" class="d-block mx-auto">
            <div class="w-25 d-block mx-auto mb-4">
                <table class="w-100">
                    <tr>
                        <td>{{ $ticket['quantity'] }} tiket</td>
                        <td><b>{{ implode(',', $ticket['rows_of_seats']) }}</b></td>
                    </tr>
                    <tr>
                        <td>kursi reguler</td>
                        <td><b>Rp. {{ number_format($ticket['schedule']['price'],0,',', '.')}} <span class="text-secondary">x{{ $ticket['quantity']}}</span></b></td>
                    </tr>
                    <tr>
                        <td>biaya layanan</td>
                        <td><b>Rp. 4.000<span class="text-secondary">x{{ $ticket['quantity']}}</span></b></td>
                    </tr>
                    <tr>
                        <td>Promo</td>

                        @php
                            if ($ticket['promo']) { 
                                $promo = $ticket['promo']['type'] == 'percent' ?
                                $ticket['promo']['discount'] . '%': 'Rp. ' . number_format($ticket['promos']['discount'], 0,',','.');
                            } else {
                                $promo = 'Rp. ';
                            }
                        @endphp
                        <td><b>{{ $promo}}</b></td>
                    </tr>
                </table>
                <hr>
                @php
                    $price = $ticket['total_price'] + ($ticket['service_fee'] *
                    $ticket['quantity']);
                @endphp
                <div class="d-flex justify-content-end">
                    <b>Rp. {{ number_format($price, 0, ',',',')}}</b>
                </div>
            </div>
            <div>
                <form action="{{ route('ticket.payment.update', $ticket['id']) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-primary btn-block btn-lg">sudah Dibayar</button>
                </form>
            </div>
        </div>

    </div>
@endsection