<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'tanggal',
        'masuk',
        'is_terlambat',
        'latitude_datang',
        'longitude_datang',
        'pulang',
        'is_pulang_cepat',
        'latitude_pulang',
        'longitude_pulang',
        'created_at'
    ];

    public function user()
    {
        // Ini memberitahu Laravel bahwa presensi ini milik seorang User
        return $this->belongsTo(User::class, 'user_id');
    }
}
