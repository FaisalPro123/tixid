<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Exports\UserExport;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function register(Request $request)
    {
        //Request $request : mengambil value request/input
        // dd() : debugging/ cek data sebelum diproses
        // dd($request->all());

        //validasi
        $request->validate([
            //format : 'name_input' => 'validasi'
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            //email : dns memastikan email valid
            'email' => 'required',
            'password' => 'required'
        ], [
            //custom pesan 
            //format : 'name_input.validasi' => 'pesan error'
            'first_name.required' => 'Nama depan wajib diisi',
            'first_name.min' => 'Nama depan diisi minimal 3 karakter',
            'last_name.required' => 'Nama belakang wajib diisi',
            'last_name.min' => 'nama belakang diisi minimal 3 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email diisi dengan data valid',
            'password.required' => 'Password wajib diisi'
        ]);

        //eloquent (fungsi model) tambah data baru : create([])
        $createData = User::create([
            //'column' => $request->name_input
            'name' => $request->firts_name . " " . $request->last_name,
            'email' => $request->email,
            //enkripsi data : merubah menjadi karakter acak, tidak ada yang bisa tau isi datanya : Hash::make()
            'password' => Hash::make($request->password),
            //role diisi langsung sebagai user agar tidak bisa menjadi admin/staff bagi pendaftar akun
            'role' => 'user'
        ]);

        if ($createData) {
            // redirect() perpindahan halaman, route() name route yg akan dipanggil
            // with() mengirim data session, biasanya untuk notif
            return redirect()->route('login')->with('success', 'Berhasil membuat akun. Silahkan login!');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    public function loginAuth(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ], [
            'email.required' => 'Email harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        //menyimpan data yang akan diverifikasi
        $data = $request->only(['email', 'password']);
        // Auth::attempt() -> verifikasi kecocokan email-pw atau username-pw
        if (Auth::attempt($data)) {
            // Setelah berhasil login, dicek lagi role nya untuk menentukan perpindahan halaman
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil login!');
            } elseif (Auth::user()->role == 'staff') {
                return redirect()->route('staff.dashboard')->with('success', 'Berhasil login!');
            } else {
                return redirect()->route('home')->with('success', 'Berhasil login!');
            }
        } else {
            return redirect()->back()->with('error', 'Gagal! pastikan email dan password sesuai');
        }
    }

    public function logout()
    {
        // Auth::logout()
        Auth::logout();
        return redirect()->route('home')->with('logout', 'Anda sudah logout! silahkan login kembali untuk akses lengkap');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::all();
        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.create');
    }

    public function datatables()
    {
        $user = User::query();

        return DataTables::of($user)
            ->addIndexColumn()

            ->addColumn('btnActions', function ($user) {
                $btnEdit = '<a href="' . route('admin.users.edit', $user['id']) . '" class="btn btn-primary me-2">edit</a>';

                $btnDelete = '<form action="' . route('admin.users.delete', $user['id']) . '" method="POST">' .
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);
        $createData = user::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff'
        ]);
        if ($createData) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil tambah data bioskop!');
        } else {
            return redirect()->back()->with('error', 'Gagal silahkan coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // edit($id) -> $id diambil dari route {id}
        $users = User::find($id);
        // find() : mencari berdasarkan id
        return view('admin.user.edit', compact('users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;

        // kalau password diisi, baru update
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diperbarui!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // sebelum dihapus, dicari dulu datanya pake where
        User::where('id', $id)->delete();
        return redirect()->route('admin.users.index')->with('success', 'Berhasil hapus data!');
    }
    public function trash()
    {
        // ambil data bioskop yang sudah dihapus sementara (soft delete)
        $userTrash = \App\Models\user::onlyTrashed()->get();

        // arahkan ke view 'admin.user.trash' (atau sesuaikan nama folder view-mu)
        return view('admin.user.trash', compact('userTrash'));
    }

    public function restore($id)
    {
        $user = \App\Models\user::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users.index')->with('success', 'Data bioskop berhasil dikembalikan!');
    }

    public function deletePermanent($id)
    {
        $user = \App\Models\user::onlyTrashed()->findOrFail($id);
        $user->forceDelete();

        return redirect()->back()->with('success', 'Data bioskop berhasil dihapus permanen!');
    }

    public function exportExcel()
    {
        $fileUser = 'pengguna.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UserExport, $fileUser);
    }
}
