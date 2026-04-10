<?php
// app/Models/PengajuanCuti.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanCuti extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_cuti'; 

    protected $fillable = [
        'user_id',
        'jenis_cuti_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'file_lampiran',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jenisCuti(): BelongsTo
    {
        return $this->belongsTo(JenisCuti::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}