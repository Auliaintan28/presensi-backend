<?php
// app/Models/PengajuanDinas.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanDinas extends Model
{
    use HasFactory;
    
    protected $table = 'pengajuan_dinas'; 

    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'tujuan',
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