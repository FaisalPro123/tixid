<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = ['cinema_id', 'movie_id', 'hours', 'price'];

    //array :,json : {}/[]
    protected function casts(): array 
    {
        return [
            'hours' => 'array'
        ];
    }
    
    public function cinema() {
        //kana sceduke ada di posisi dua, gunakan belongsTo untuk menghubungkan ke cinema
        return $this->belongsTo(Cinema::class);
    }

    public function movie() {
        return $this->belongsTo(Movie::class);
    }

        public function tickets()
    {
        return $this->hashMany(Ticket::class);
    }
}
