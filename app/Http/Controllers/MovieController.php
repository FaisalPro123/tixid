<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MovieExport;
use App\Models\Schedule;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::all();

        // perbaikan: variable yg dikirim harus sesuai, bukan $movie tunggal
        return view('admin.movie.index', compact('movies'));
    }

public function datatables()
{
    $movie = Movie::query();

    return DataTables::of($movie)
        ->addIndexColumn()
        ->addColumn('imgPoster', function($movie) {
            $imgUrl = asset('storage/' . $movie['poster']);
            return '<img src="' . $imgUrl . '" width="128">';
        })
        ->addColumn('activeBadge', function($movie) {
            if ($movie['actived'] == 1) {
                return '<span class="badge badge-success">Aktif</span>';
            } else {
                return '<span class="badge badge-secondary">Non-aktif</span>';
            }
        })
        ->addColumn('btnActions', function($movie) {
            $btnDetail = '<button class="btn btn-secondary me-2" onclick=\'showmodal('. json_encode($movie) .')\'>detail</button>';

            $btnEdit = '<a href="'. route('admin.movies.edit', $movie['id']) .'" class="btn btn-primary me-2">edit</a>';

            $btnDelete = '<form action="'. route('admin.movies.delete', $movie['id']) .'" method="POST">' .
                        csrf_field() .
                        method_field('DELETE') . '
                            <button class="btn btn-danger">hapus</button>
                        </form>';

            if ($movie['actived'] == 1) {
                $btnNonAktif = '<form action="'. route('admin.movies.actived', $movie['id']) .'" method="POST">' .
                                csrf_field() .
                                method_field('PATCH') . '
                                    <button class="btn btn-danger">non-aktif</button>
                                </form>';
            } else {
                $btnNonAktif = '';
            }

             return '<div class="d-flex gap-2">' . $btnDetail . $btnEdit . $btnDelete . $btnNonAktif . '</div>';
            $btnNonAktif . '</div>';
        })
        ->rawColumns(['imgPoster','activeBadge','btnActions'])
        ->make(true);
}

    public function home()
    {
        $movies = Movie::where('actived', 1)->orderBy('created_at', 'desc')->limit(4)->get();

        return view('home', compact('movies'));
    }

    public function homeMovies(Request $request)
    {
        // perbaikan: kolomnya actived, bukan activated
        $namaMovie = $request->search_movie;
        if ($namaMovie != "") {
            //like : mencari data yang miri/mengandung teks dimita
            $movies = Movie::where('title', 'LIKE', '%' . $namaMovie . '%')->where('actived', 1)->orderBy('created_at', 'DESC')->get();
        } else {
            $movies = Movie::where('actived', 1)->orderBy('created_at', 'DESC')->get();
        }

        return view('movies', compact('movies'));
    }

    public function movieSchedules($movie_id, Request $request)
    {
        $sortirHarga = $request->sortirHarga;
        if ($sortirHarga) {
            $movie = Movie::where('id', $movie_id)
                ->with([
                    'schedules' => function ($q) use ($sortirHarga) {
                        $q->orderBy('price', $sortirHarga);
                    },
                    'schedules.cinema'
                ])->first();
        } else {
            $movie = Movie::where('id', $movie_id)->with('schedules', 'schedules.cinema')->first();
        }

        $sortirAlfabet = $request->sortirAlfabet;
        if($sortirAlfabet == 'ASC') {
            $movie->schedules = $movie->schedules->sortBy(function($schedule){
                return $schedule->cinema->name;
            })->values();
        } elseif ($sortirAlfabet == 'DESC') {
            $movie->schedules = $movie->schedules->sortByDesc(function($schedule){
                return $schedule->cinema->name;
            })->values();
        }
        return view('schedule.detail-film', compact('movie'));
    }

    public function actived($id)
    {
        $movie = Movie::find($id);
        $movie->actived = !$movie->actived;
        $movie->save();

        return redirect()->route('admin.movies.index')->with('success', 'Status film berhasil diubah!');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.movie.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'poster' => 'required|mimes:jpg,jpeg,png,svg,webp',
            'description' => 'required|min:10',
        ], [
            'title.required' => 'Judul film harus diisi',
            'duration.required' => 'Durasi film harus diisi',
            'genre.required' => 'Genre film harus diisi',
            'director.required' => 'Sutradara film harus diisi',
            'poster.required' => 'Poster harus diupload',
            'poster.mimes' => 'Poster harus berbentuk JPG/JPEG/PNG/SVG/WEBP',
            'description.required' => 'Sinopsis harus diisi',
            'description.min' => 'Sinopsis diisi minimal 10 karakter',
        ]);

        $poster = $request->file('poster');
        $namaFile = rand(1, 10) . '-poster.' . $poster->getClientOriginalExtension();
        $path = $poster->storeAs('posters', $namaFile, 'public');

        $createData = Movie::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            'poster' => $path,
            'description' => $request->description,
            'actived' => 1,
        ]);

        if ($createData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil membuat data');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan data!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $movie = Movie::findOrFail($id);

        return view('admin.movie.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'poster' => 'nullable|mimes:jpg,jpeg,png,svg,webp',
            'description' => 'required|min:10',
        ], [
            'title.required' => 'Judul film harus diisi',
            'duration.required' => 'Durasi film harus diisi',
            'genre.required' => 'Genre film harus diisi',
            'director.required' => 'Sutradara film harus diisi',
            'poster.mimes' => 'Poster harus berbentuk JPG/JPEG/PNG/SVG/WEBP',
            'description.required' => 'Sinopsis harus diisi',
            'description.min' => 'Sinopsis diisi minimal 10 karakter',
        ]);

        $movie = Movie::findOrFail($id);

        // cek jika ada poster baru
        if ($request->file('poster')) {
            $posterSebelumnya = storage_path('app/public/' . $movie['poster']);
            if (file_exists($posterSebelumnya)) {
                unlink($posterSebelumnya);
            }

            $poster = $request->file('poster');
            $namaFile = rand(1, 10) . '-poster.' . $poster->getClientOriginalExtension();
            $path = $poster->storeAs('posters', $namaFile, 'public');
        }

        $updateData = $movie->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'age_rating' => $request->age_rating,
            'director' => $request->director,
            'poster' => $path ?? $movie['poster'],
            'description' => $request->description,
            'actived' => 1,
        ]);

        if ($updateData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil mengubah data');
        } else {
            return redirect()->back()->with('error', 'Gagal mengubah data!');
        }
    }

    /*
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $schedules = Schedule::where('movie_id', $id)->count();
        if ($schedules) {
            return redirect()->route('admin.movies.index')->with('error', 'Tidak dapat menghapus data film, karena masih ada jadwal film.');
        }

        $movie = Movie::findOrFail($id);

        $movie->delete();

        return redirect()->route('admin.movies.index')->with('success', 'Berhasil menghapus data (tersimpan di data sampah)');
    }
    public function trash()
    {
        // ambil data bioskop yang sudah dihapus sementara (soft delete)
        $MovieTrash = \App\Models\Movie::onlyTrashed()->get();

        // arahkan ke view 'admin.Movie.trash' (atau sesuaikan nama folder view-mu)
        return view('admin.Movie.trash', compact('MovieTrash'));
    }

    public function restore($id)
    {
        $movie = Movie::onlyTrashed()->findOrFail($id);
        $movie->restore();

        return redirect()->route('admin.movies.index')->with('success', 'Data film berhasil dikembalikan!');
    }

    public function deletePermanent($id)
    {
        $movie = Movie::onlyTrashed()->findOrFail($id);

        // 🧹 Hapus file posternya baru di sini
        if ($movie->poster && Storage::disk('public')->exists($movie->poster)) {
            Storage::disk('public')->delete($movie->poster);
        }

        $movie->forceDelete();

        return redirect()->back()->with('success', 'Data film berhasil dihapus permanen!');
    }

    public function exportExcel()
    {
        $fileName = 'data-film.xlsx';
        return Excel::download(new MovieExport, $fileName);
    }
}
