<?php
// app/Models/PengajuanIzinJam.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanIzinJam extends Model
{
    use HasFactory;

     protected $table = 'pengajuan_izin_jam'; 

    protected $fillable = [
        'user_id',
        'jenis_izin',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
        'file_lampiran',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}