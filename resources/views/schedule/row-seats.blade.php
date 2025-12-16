@extends('templates.app')
@section('content')
    <div class="container card my-5 p-4" style="margin-bottom:20% !important;">
        <b>{{ $schedule['cinema']['name'] }}</b>
        <br><b>{{ now()->format('d F, Y') }} - {{ $hour }}</b>
        <div class="alert alert-secondary">
            <i class="fa-solid fa-info text-danger"></i> remaja berusia 14 tahun sudah boleh memesan tiket film ini
        </div>
        <div class="w-100 d-block mx-auto my-4">
            <div class="row">
                <div class="col-4 d-flex">
                    <div style="width: 20px; height: 20px; background: blue; margin-right: 5px"></div>kursi di pilih
                </div>
                <div class="col-4 d-flex">
                    <div style="width: 20px; height: 20px; background: #112646; margin-right: 5px"></div>kursi tersedia
                </div>
                <div class="col-4 d-flex">
                    <div style="width: 20px; height: 20px; background: red; margin-right: 5px"></div>kursi terjual
                </div>
                @php
                    $rows = range('A', 'H');
                    $cols = range(1, 18);
                @endphp

                @foreach ($rows as $row)
                    <div style="display:flex; margin-bottom:10px;">
                        @foreach ($cols as $col)
                            @if ($col == 7)
                                <div style="width: 50px"></div>
                            @endif
                            @php
                                $seat = $row . '-' . $col;
                            @endphp
                            @if (in_array($seat,$soldSeatsFormat))
                                <div style="width:45px; height:45px; text-align:center; font-weight:bold; color:black; padding-top:10px; cursor:pointer; background: red; margin-right:5px; border-radius:8px">
                                    {{ $row }}-{{ $col }}
                                </div>
                            @else
                                <div style="width:45px; height:45px; text-align:center; font-weight:bold; color:white; padding-top:10px; cursor:pointer; background:#112646; margin-right:5px; border-radius:8px;"
                                    onclick="selectSeat('{{ $schedule['price'] }}','{{ $row }}','{{ $col }}', this)">
                                    {{ $row }}-{{ $col }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="fixed-bottom">
        <div class="p-4 bg-light text-center w-100">LAYAR BIOSKOP</div>
        <div class="row w-100 bg-light">
            <div class="col-6 py-3 text-center" style="border: 1px solid gray">
                <h5>TOTAL HARGA</h5>
                <h5 id="totalPrice">Rp. -</h5>
            </div>
            <div class="col-6 py-3 text-center" style="border: 1px solid gray">
                <h5>KURSI DI PILIH</h5>
                <h5 id="seats">--</h5>
            </div>
            {{-- --}}
            <input type="hidden" id="user_id" value="{{ Auth::user()->id }}">
            <input type="hidden" id="schedule_id" value="{{ $schedule->id }}">
            <input type="hidden" id="date" value="{{ now() }}">
            <input type="hidden" id="hour" value="{{ $hour }}">

            <div class="w-100 bg-light p-2 text-center" id="btnOrder">RINGKASAN ORDER</div>
        </div>

    </div>
@endsection

@push('script')
    <script>
        let seats = [];
        let totalPrice = 0;

        function selectSeat(price, row, col, element) {
            let seat = row + "-" + col;
            let indexSeat = seats.indexOf(seat);
            if (indexSeat == -1) {
                seats.push(seat);
                element.style.background = 'blue';
            } else {
                seats.splice(indexSeat, 1);
                element.style.background = '#112646';
            }
            totalPrice = price * seats.length;
            let totalPriceElement = document.querySelector("#totalPrice");
            totalPriceElement.innerText = totalPrice;

            let seatsElement = document.querySelector('#seats');
            seatsElement.innerText = seats.join(', ');

            let btnOrder = document.querySelector("#btnOrder");
            if (seats.length > 0) {
                btnOrder.classList.remove('bg-light');
                btnOrder.style.background = '#112646';
                btnOrder.style.color = 'white';
                btnOrder.style.cursor = 'pointer';
                btnOrder.onclick = createTicket;
            } else {
                btnOrder.classList.add('bg-light');
                btnOrder.style.background = '';
                btnOrder.style.color = '';
                btnOrder.style.cursor = '';
                btnOrder.onclick = null;

            }
        }

        function createTicket() {
            $.ajax({
                url: "{{ route('ticket.store') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id: $("#user_id").val(),
                    schedule_id: $("#schedule_id").val(),
                    date: $("#date").val(),
                    hour: $("#hour").val(),
                    rows_of_seats: seats,
                    quantity: seats.length,
                    total_price: totalPrice,
                    service_fee: 4000 * seats.length
                },
                success: function(response) {
                    let ticketId = response.data.id;
                    window.location.href = `/ticket/${ticketId}/order`;
                },
                error: function(massage) {
                    alert('gagal membuat data ticket');
                }
            })
        }
    </script>
@endpush
