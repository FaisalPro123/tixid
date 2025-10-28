<?php

namespace App\Exports;

use App\Models\Promo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PromoExport implements FromCollection, WithHeadings, WithMapping
{
    private $key = 0;
    
    public function collection()
    {
        return Promo::all();
    }

    public function headings(): array
    {
        return ['No', 'Kode Promo', 'Total Potongan'];
    }

    public function map($promo): array
    {
        if ($promo->type == 'percent') {
            $discount =$promo->discount . '%';
        }elseif ($promo->type ==='rupiah') {
            $discount = 'Rp.' . number_format($promo->discount, 0,'.','.');
        }
        return [
            ++$this->key,
            $promo->promo_code,     // at au $cinema->nama_bioskop
            $discount, // atau $cinema->lokasi
        ];
    }
}