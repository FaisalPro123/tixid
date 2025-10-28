<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CinemaExport;
use App\Models\Schedule;
use Yajra\DataTables\Facades\DataTables;

class CinemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();
        // Cinema::all() -> mengambil semua data pada model Cinema (table cinemas)
        // mengirim data dari controller ke blade -> compact()
        // isi compact sama dengan nama variabel
        // di model all() disebutnya eloquent
        return view('admin.cinema.index', compact('cinemas'));
    }

    public function datatables()
    {
        $cinema = Cinema::query();

        return DataTables::of($cinema)
            ->addIndexColumn()   

            ->addColumn('btnActions', function ($cinema) {
                $btnEdit = '<a href="' . route('admin.cinemas.edit', $cinema['id']) . '" class="btn btn-primary me-2">edit</a>';

                $btnDelete = '<form action="' . route('admin.cinemas.delete', $cinema['id']) . '" method="POST">' .
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
        return view('admin.cinema.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required|min:10'
        ], [
            'name.required' => 'Nama Bioskop harus diisi',
            'location.required' => 'Lokasi Bioskop harus diisi',
            'location.min' => 'Lokasi Bioskop minimal harus diisi minimal 10 karakter'
        ]);
        $createData = Cinema::create([
            'name' => $request->name,
            'location' => $request->location
        ]);
        if ($createData) {
            return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil tambah data bioskop!');
        } else {
            return redirect()->back()->with('error', 'Gagal silahkan coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cinema $cinema)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // edit($id) -> $id diambil dari route {id}
        $cinemas = Cinema::find($id);
        // find() : mencari berdasarkan id
        return view('admin.cinema.edit', compact('cinemas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required|min:10'
        ], [
            'name.required' => 'Nama Bioskop harus diisi',
            'location.required' => 'Lokasi Bioskop harus diisi',
            'location.min' => 'Lokasi bioskop harus diisi minimal 10 karakter'
        ]);
        // where() -> mencari data. format : where('nama_column', value)
        // sebelum update() wajib ada where() untuk mencari data yang akan diupdatenya
        $updateData = Cinema::where('id', $id)->update([
            'name' => $request->name,
            'location' => $request->location
        ]);
        if ($updateData) {
            return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil mengubah data!');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $schedules = Schedule::where('cinema_id', $id)->count();
        if ($schedules) {
            return redirect()->route('admin.cinemas.index')->with('error', 'tidak dapat menghapus data, karena masih ada jadwal film');
        }
        // sebelum dihapus, dicari dulu datanya pake where
        Cinema::where('id', $id)->delete();
        return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil hapus data!');
    }
    public function trash()
    {
        // ambil data bioskop yang sudah dihapus sementara (soft delete)
        $cinemaTrash = \App\Models\Cinema::onlyTrashed()->get();

        // arahkan ke view 'admin.cinema.trash' (atau sesuaikan nama folder view-mu)
        return view('admin.cinema.trash', compact('cinemaTrash'));
    }

    public function restore($id)
    {
        $cinema = \App\Models\Cinema::onlyTrashed()->findOrFail($id);
        $cinema->restore();

        return redirect()->route('admin.cinemas.index')->with('success', 'Data bioskop berhasil dikembalikan!');
    }

    public function deletePermanent($id)
    {
        $cinema = \App\Models\Cinema::onlyTrashed()->findOrFail($id);
        $cinema->forceDelete();

        return redirect()->back()->with('success', 'Data bioskop berhasil dihapus permanen!');
    }


    public function export()
    {
        return Excel::download(new CinemaExport, 'cinema.xlsx'); // Pastikan new CinemaExport
    }
}
