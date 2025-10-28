<?php

namespace App\Exports;

use App\Models\Cinema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CinemaExport implements FromCollection, WithHeadings, WithMapping
{
    private $key = 0;
    
    public function collection()
    {
        return Cinema::all();
    }

    public function headings(): array
    {
        return ['No', 'Nama Bioskop', 'Lokasi'];
    }

    public function map($cinema): array
    {
        return [
            ++$this->key,
            $cinema->name,     // atau $cinema->nama_bioskop
            $cinema->location, // atau $cinema->lokasi
        ];
    }
}