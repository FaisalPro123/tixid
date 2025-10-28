<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use App\Exports\PromoExport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::all();
        return view('staff.promo.index', compact('promos'));
    }

    public function create()
    {
        return view('staff.promo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'promo_code' => 'required',
            'discount'   => 'required',
            'type'       => 'required',
        ], [
            'promo_code.required' => 'kode promo harus diisi',
            'discount.required'   => 'diskon harus diisi',
            'type.required'       => 'tipe harus diisi',
        ]);

        if ($request->type == 'percent' && $request->discount > 100) {
            return back()->withErrors([
                'discount' => 'Diskon persen tidak boleh lebih dari 100'
            ])->withInput();
        } elseif ($request->type == 'rupiah' && $request->discount < 1000) {
            return back()->withErrors([
                'discount' => 'Diskon rupiah tidak boleh kurang dari 1000'
            ])->withInput();
        }

        $createData = Promo::create([
            'promo_code' => $request->promo_code,
            'discount'   => $request->discount,
            'type'       => $request->type,
            'actived'    => 1,
        ]);

        if ($createData) {
            return redirect()->route('staff.promos.index')->with('success', 'berhasil tambah data promo!');
        } else {
            return redirect()->back()->with('error', 'gagal silahkan coba lagi');
        }
    }

    public function edit($id)
    {
        $promo = Promo::findOrFail($id);
        return view('staff.promo.edit', compact('promo'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'promo_code' => 'required|string|max:50|unique:promos,promo_code,' . $id,
            'type'       => 'required|in:percent,rupiah',
            'discount'   => 'required|numeric|min:1',
        ]);

        $promo = Promo::findOrFail($id);
        $promo->update($request->only([
            'promo_code', 'type', 'discount'
        ]));

        return redirect()->route('staff.promos.index')->with('success', 'Promo berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->delete();

        return redirect()->route('staff.promos.index')->with('success', 'Promo berhasil dihapus!');
    }
 public function trash() {
    $promoTrash = Promo::onlyTrashed()->get();
    return view('staff.promo.trash', compact('promoTrash'));
}

public function restore($id) {
    $promo = Promo::onlyTrashed()->find($id);
    $promo->restore();
    return redirect()->route('staff.promos.index')->with('success', 'berhasil mengembalikan data!!');
}

public function deletePermanent($id) {
    $promo = Promo::onlyTrashed()->find($id);
    $promo->forceDelete();
    return redirect()->back()->with('success', 'berhasil menghapus data secara permanen!!');
}


    public function export()
    {
        $filePromo = 'data-diskon.xlsx';
        return Excel::download(new PromoExport, $filePromo); 
    }
}
