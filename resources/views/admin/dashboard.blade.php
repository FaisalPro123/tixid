@extends('templates.app')

@section('content')
    <div class="container">
        <h5 class="my-3">Grafik Pembelian Tiket</h5>
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}
                <b>Selamat Datang, {{ Auth::user()->name }}</b>
            </div>
        @endif
        <div class="row mt-5">
            <div class="col-6">
                <canvas id="chartBar"></canvas>
            </div>
            <div class="col-6">
                <canvas id="chartPie"></canvas>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        let labels = null;
        let data = null;
        let datapie = null;

        $(function() {
            $.ajax({
                url: "{{ route('admin.ticket.chart') }}",
                method: "GET",

                success: function(response) {
                    labels = response.labels;
                    data = response.data;
                    chartBar();
                },
                error: function(err) {
                    alert('gagal mengambil data untuk grafik')
                }
            })
        });
        $.ajax({
            url: "{{ route('admin.movies.chart') }}",
            method: "GET",

            success: function(response) {
                datapie = response.data;
                chartPie();
            },
            error: function(err) {
                alert('gagal mengambil data untuk grafik')
            }
        });

        const ctx = document.getElementById('chartBar');

        function chartBar() {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'penjualan Ticket bulan ini',
                        data: data,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        const ctx2 = document.getElementById('chartPie');

        function chartPie() {
            new Chart(ctx2, {
                    type: 'pie',
                    data: {
                        labels: [
                            'film aktif',
                            'film non aktif',
                        ],
                        datasets: [{
                            label: 'perbandingan film aktif dan film tidak aktif',
                            data: datapie,
                            backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                            ],
                            hoverOffset: 4
                        }]

                    }
            })
        };
        
    </script>
@endpush
