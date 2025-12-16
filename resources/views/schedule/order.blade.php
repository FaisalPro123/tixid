@extends('templates.app')

@section('content')
    <div class="container card p-4 my-5">
        <div class="card-body">
            <b class="text-center">RINGKASAN ORDER</b>
            <div class="d-flex">
                <img src="{{ asset('storage/' . $ticket['schedule']['movie']['poster']) }}" width="128" alt="">
                <div>
                    <b class="text-warning">{{ $ticket['schedule']['cinema']['name'] }}</b>
                    <br><b>{{ $ticket['schedule']['movie']['title'] }}</b>
                    <table>
                        <tr>
                            <td><b>lokasi</b></td>
                            <td>:</td>
                            <td>{{ $ticket['schedule']['cinema']['location'] }}</td>
                        </tr>
                        <tr>
                            <td><b>genre</b></td>
                            <td>:</td>
                            <td>{{ $ticket['schedule']['movie']['genre'] }}</td>
                        </tr>
                        <tr>
                            <td><b>durasi</b></td>
                            <td>:</td>
                            <td>{{ $ticket['schedule']['movie']['duration'] }}</td>
                        </tr>
                        <tr>
                            <td><b>sutradara</b></td>
                            <td>:</td>
                            <td>{{ $ticket['schedule']['movie']['director'] }}</td>
                        </tr>
                        <tr>
                            <td><b>usia minimal</b></td>
                            <td>:</td>
                            <td><span class="badge badge-danger">{{ $ticket['schedule']['movie']['age_rating'] }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <hr>
            <b class="text-secondary">NOMOR PESANAN : {{ $ticket['id'] }}</b>
            <br><b>DETAIL PESANAN</b>
            <table>
                <tr>
                    <td>{{ $ticket['quantity'] }} Tiket:</td>
                    <td style="pandding: 0 20px"></td>
                    <td><b>{{ implode(',', $ticket['rows_of_seats']) }}</b></td>
                </tr>
                <tr>
                    <td>Harga Tiket:</td>
                    <td style="pandding: 0 20px"></td>
                    <td><b>Rp. {{ number_format($ticket['schedule']['price'], 0, ',', '.') }} <span
                                class="text-secondary">{{ $ticket['quantity'] }}</span></b></td>
                </tr>
                <tr>
                    <td>biaya layanan:</td>
                    <td style="pandding: 0 20px"></td>
                    <td><b>Rp. 4.000 <span class="text-secondary">x{{ $ticket['quantity'] }}</span></b></td>
                </tr>
            </table>
            <b>Gunakan Promo :</b>
            <select id="promo_id" class="form-select" name="promo_id" onchange="selectPromo(this)">
                @if (count($promos) < 1)
                    <option disabed hidden selected>Tidak ada promo tersedia</option>
                @else
                    <option disabed hidden selected>Pilih Promo</option>
                    @foreach ($promos as $promo)
                        <option value="{{ $promo['id'] }}">{{ $promo['promo_code'] }} -
                            {{ $promo['type'] == 'percent' ? $promo['discount'] . '%' : 'Rp.' . number_format($promo['discount'], 0, ',', ',') }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <input type="hidden" name="ticket_id" value="{{ $ticket['id'] }}" id="ticket_id">
    <div class="w-100 p-2 text-center fixed-bottom" style="background: #112646; color:white; cursor:pointer"
        onclick="createQR()">
        <i class="fa-solid fa-ticket"></i> BAYAR SEKARANG
    </div>
@endsection 

@push('script')
    <script>
        let promoId = null;

        function selectPromo(element) {
            promoId = element.value;
        }

        function createQR() {
            let data = {
                _token: '{{ csrf_token() }}',
                ticket_id: $("#ticket_id").val()
            }
            $.ajax({
                url: "{{ route('ticket.barcode') }}",
                type: "POST",
                data: data,
                success: function(response) {
                    const ticketId = response.data.ticket_id;
                    window.location.href ='/ticket/' + ticketId + '/payment';
                },
                error: function(xhr) {
                    alert("terjadi kesalahan saat mengirim data");
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
@endpush
