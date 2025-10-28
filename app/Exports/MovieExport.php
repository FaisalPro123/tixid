<?php

namespace App\Exports;

use App\Models\Movie;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class MovieExport implements FromCollection, WithHeadings, WithMapping
// FromCollection : mengambil data yang bakal ditampilkan di excel
//WithHeadings : membuat header d excel
//WithMapping : untuk mengisi datanya di excel
{
    //membuat property untuk no urutan data
    private $key = 0;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //memanggil data yang bakal dimunculkan diexcel
        return Movie::all();
    }

    //menentukan header data
    public function headings(): array
    {
        return ['No', 'Judul Film', 'Durasi', 'Genre', 'Sutradara', 'Usia Minimal', 'Poster', 'Sinopsis'];
    }

    public function map($movie): array
    {
        return [
            //menambahkan sebanyak 1 setiap data dari $key = 0 diatas
            ++$this->key,
            $movie->title,
            //format 1 jam 30 menit, data asal : 01.00.00
            //parse() : mengambil data tanggal/jam yang akan dimanipulasi
            //format () : menentukan format tanggal/jam
            Carbon::parse($movie->duration)->format('H') . "Jam" . Carbon::parse($movie->duration)->format('i') . "Menit",
            $movie->genre,
            $movie->director,
            //format : usia => 17+
            $movie->age_rating . "+",
            //asset() : link buat liat gambarnya
            asset('storage/') . "/" . $movie->poster,
            $movie->description,
        ];
    }
}