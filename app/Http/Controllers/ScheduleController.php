<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Movie;
use App\Models\Cinema;
use Illuminate\Http\Request;
use App\Exports\ScheduleExport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();
        $movies = Movie::all();
        //karena cinema_id dan movie_id di db hanya berupa angka untuk mengambil detail relasi di ambil dari nama 
        $schedules = Schedule::with(['cinema', 'movie'])->get();
        return view('staff.schedule.index' , compact('cinemas', 'movies' ,'schedules'));

    }

       public function datatables()
    {
        $schedules = Schedule::query();

        return DataTables::of($schedules)
            ->addIndexColumn()   

            ->addColumn('btnActions', function ($schedules) {
                $btnEdit = '<a href="' . route('admin.schedules.edit', $schedules['id']) . '" class="btn btn-primary me-2">edit</a>';

                $btnDelete = '<form action="' . route('admin.schedules.delete', $schedules['id']) . '" method="POST">' .
                    csrf_field() .
                    method_field('DELETE') . '
                            <button class="btn btn-danger">hapus</button>
                        </form>';

                return '<div class="d-flex gap-2">'  . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['btnActions'])
            ->make(true);
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
            'cinema_id' => 'required' ,
            'movie_id' => 'required' ,
            'price' => 'required|numeric',
            'hours.*' => 'required|date_format:H:i'
        ], [
            'cinema_id.required' => 'bioskop harus diisi',
            'movie_id.required' => 'film harus dipilih',
            'price.required' => 'harga harus diisi',
            'price.numeric' => 'harga harus berupa angka',
            'hours.*.required' => 'jam harus diisi',
            'hours.*.date_format' => 'format tayang harus di isi dengan jam:menit',   
        ]
        );
        //pengecekan apakah ada bioskop dan film yang di pilih skring di db nya kalau ada ambil jamnya
        $hours = Schedule::where('cinema_id', $request->cinema_id)->where('movie_id',$request->movie_id)->value('hours');
        $hoursBefore = $hours ?? [];
        $mergeHours = array_merge($hoursBefore, $request->hours);
        $newHours = array_unique($mergeHours);

        $createData = Schedule::updateOrCreate([
            'cinema_id' => $request->cinema_id,
            'movie_id' => $request->movie_id,
        ],[
            'price' => $request->price,
            'hours' => $newHours,
        ]);
        if($createData) {
            return redirect()->route('staff.schedules.index')->with('success', 'berhasil menambahkan');
        } else {
            return redirect()->route('staff.schedules.index')->with('error', 'gagal!! coba lagi');
        }
    }
        
        /**
     * Display the specified resource.
     */
 public function show($id)
{
    $schedule = Schedule::with(['movie', 'cinema'])->findOrFail($id);
    $movie = $schedule->movie;

    return view('schedule.detail-film', compact('movie', 'schedule'));
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule,$id)
    {
        $schedule = Schedule::where('id', $id)->with(['cinema','movie'])->first();
        return view('staff.schedule.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule, $id)
    {
        $request->validate([
            'price' => 'required|numeric',
            'hours.*' => 'required|date_format:H:i'
        ], [
            'price.required' => 'harga harus diisi',
            'price.numeric' => 'harga harus diisi dengan angka',
            'hours.*.required' => 'jam tayang harus disi',
            'hours.*.date_format' => 'Jam tayang harus diisi dengan format jam:menit',
        ]);
        $updateData = Schedule::where('id' , $id)->update([
            'price' => $request->price,
            'hours' => $request->hours,
        ]);

        if($updateData) {
            return redirect()->route('staff.schedules.index')->with('success', 'berhasil mengubah');
        } else {
            return redirect()->back()->with('eror', 'gagal!! coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule , $id)
    {
        Schedule::where('id', $id)->delete();
        return redirect()->route('staff.schedules.index')->with('success', 'berhasil menghapus data!!');
    }
    public function trash() {
        $scheduleTrash = Schedule::with(['cinema','movie'])->onlyTrashed()->get();
        return view('staff.schedule.trash', compact('scheduleTrash'));
    }
    public function restore($id) {
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule->restore();
        return redirect()->route('staff.schedules.index')->with('success', 'berhasil mengembalikan data!!');
    }
    public function deletePermanent($id) {
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule->forceDelete();
        return redirect()->back()->with('success', 'berhasil menghapus data secara permanen!!');
    }
    public function export() {
        $fileName = 'jadwal-tayang.xlsx';
        return Excel::download(new ScheduleExport, $fileName);
    }
}
