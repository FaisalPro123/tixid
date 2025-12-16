<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\Promo;
use App\Models\TicketPayment;
use Illuminate\Support\Facades\Redirect;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function showSeats($scheduleId, $hourId)
    {
        $schedule = Schedule::find($scheduleId);
        $hour = $schedule['hours'][$hourId] ?? '';
        $soldSeats = Ticket::where('schedule_id', $scheduleId)->where('actived', 1)->where('date', now()->format('Y-m-d'))->pluck('rows_of_seats');

        $soldSeatsFormat = [];
        foreach ($soldSeats as $seat) {
            foreach ($seat as $item)
                array_push($soldSeatsFormat, $item);
        }
        // dd($soldSeatsFormat);
        return view('schedule.row-seats', compact('schedule', 'hour', 'soldSeatsFormat'));
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $ticketActive = Ticket::where('user_id', $userId)->where('actived', 1)->where('date', now()->format('Y-m-d'))->get();
        $ticketNonActive = Ticket::where('user_id', $userId)->where('date','<>',now()->format('Y-m-d'))->get();
        return view('ticket.index',compact('ticketActive','ticketNonActive'));
    }

    /**
     * Show the form for creating a new resource.
     */ 
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'schedule_id' => 'required',
            'date' => 'required',
            'hour' => 'required',
            'rows_of_seats' => 'required',
            'quantity' => 'required',
            'total_price' => 'required',
            'service_fee' => 'required',
        ]);
        $createData = Ticket::create([
            'user_id' => $request->user_id,
            'schedule_id' => $request->schedule_id,
            'date' => $request->date,
            'hour' => $request->hour,
            'rows_of_seats' => $request->rows_of_seats,
            'quantity' => $request->quantity,
            'total_price' => $request->total_price,
            'service_fee' => $request->service_fee,
            'actived' => 0,
        ]);
        return response()->json([
            'massage' => 'berhasil membuat data tiket',
            'data' => $createData
        ]);
    }

    /**
     * Display the specified resource.
     */
    public  function ticketOrderPage($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie'])->first();
        $promo = Promo::where('actived', 1)->get();
        $promos = Promo::where('actived', 1)->get();
        return view('schedule.order', compact('ticket', 'promos'));
    }

    public function createBarcode(Request $request)
    {
        $kodeBarcode = 'TICKET' . $request->ticket_id;
        $qrImage = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($kodeBarcode);

        $filename = $kodeBarcode . '.svg';
        $path = 'barcode/' . $filename;

        Storage::disk('public')->put($path, $qrImage);

        $createData = TicketPayment::create([
            'ticket_id' => $request->ticket_id,
            'status' => 'process',
            'qrcode' => $path,
            'booked_date' => now(),
        ]);

        $ticket = Ticket::find($request->ticket_id);
        $totalPrice = $ticket['total_price'];
        if ($request->promo_id != null) {
            $promo = Promo::find($request->promo_id);
            if ($promo['type'] == 'percent') {
                $discount = $ticket['total_price'] * $promo['discount'] / 100;
            } else {
                $discount = $promo['discount'];
            }
            $totalPrice = $ticket['total_price'] - $discount;
        }
        $updateTicket = Ticket::where('id', $request->ticket_id)->update([
            'promo_id' => $request->promo_id,
            'total_price' => $totalPrice,
        ]);
        return response()->json([
            'massage' => 'berhasil membuat pesanan tiket sementara',
            'data' => $createData
        ]);
    }

    public function ticketPaymentPage($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'promo', 'ticketPayment'])->first();
        // dd($ticket); 

        return view('schedule.payment', compact('ticket'));
    }
    public function updateStatusTicket($ticketId)
    {
        $updatePayment = TicketPayment::where('ticket_id', $ticketId)->update(['paid_date' => now()]);
        $updatePayment = Ticket::where('id', $ticketId)->update(['actived' => 1]);
        //diarahkan ke halaman route web php ticket.show untuk munculin tiket
        return redirect()->route('ticket.show', $ticketId);
    }
    /**
     * Display the specified resource.
     */
    public function show($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.movie', 'schedule.cinema', 'ticketPayment'])->first();
        return view('schedule.ticket', compact('ticket'));
    }
    public function exportPdf($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.movie', 'schedule.cinema', 'ticketPayment'])->first()->toArray();
        view()->share('ticket', $ticket);
        $pdf = Pdf::loadView('schedule.export-pdf', $ticket);
        $filename = 'TICKET' . $ticketId . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function dataChart()
    {
        // ambil bulan ini
        $month = now()->format('m');
        //hasil collection (get), di kelompookan
        $ticket = Ticket::where('actived',1)->whereHas('ticketPayment',function($q) use ($month) {
            $q->whereMonth('booked_date', $month);
        })->get()->groupBy(function($ticket){
            return Carbon::parse($ticket->ticketPayment->booked_date)->format('Y-m-d');
        })->toArray();
        // dd($ticket);
        $labels = array_keys($ticket);
        //siapin wadah buat arraynya
        $data = [];
        foreach ($ticket as $ticketGroup) {
            array_push($data,count($ticketGroup));
        }
        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
