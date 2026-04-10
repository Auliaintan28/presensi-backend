<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shifts';
    protected $guarded = ['id'];

    protected $fillable = [
        'nama_shift',
        'jam_masuk',
        'jam_pulang',
    ];

    public function jadwalKerja()
    {
        return $this->hasMany(JadwalKerja::class, 'shift_id');
    }
}
