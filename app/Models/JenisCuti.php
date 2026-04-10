<?php
// app/Models/JenisCuti.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisCuti extends Model
{
    use HasFactory;
     protected $table = 'jenis_cuti'; 
    
    // Tidak perlu $fillable karena kita isi via seeder/migration
}