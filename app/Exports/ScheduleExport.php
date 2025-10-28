<?php

namespace App\Exports;

use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ScheduleExport implements FromCollection, WithHeadings
{
public function collection()
{
    return \App\Models\Schedule::with(['cinema', 'movie'])
        ->get()
        ->map(function ($schedule) {
            // Tangani dua kemungkinan: string JSON atau array
            if (is_string($schedule->hours)) {
                $hours = json_decode($schedule->hours, true);
            } elseif (is_array($schedule->hours)) {
                $hours = $schedule->hours;
            } else {
                $hours = [];
            }

            return [
                'Nama Bioskop' => $schedule->cinema->name ?? '-',
                'Judul Film'   => $schedule->movie->title ?? '-',
                'Jam Tayang'   => $hours ? implode(', ', $hours) : '-',
            ];
        });
}

    public function headings(): array
    {
        return [
            'Nama Bioskop',
            'Judul Film',
            'Jam Tayang',
        ];
    }
}
