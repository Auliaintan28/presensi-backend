<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;



class User extends Authenticatable

{

    use HasApiTokens, HasFactory, Notifiable;



    /**

     * The attributes that are mass assignable.

     *

     * @var array<int, string>

     */

    protected $fillable = [

        'name',

        'email',

        'password',

        'role',

        'nip',

        'jabatan',

        'no_hp',

        'foto_profil',

    ];



    /**

     * The attributes that should be hidden for serialization.

     *

     * @var array<int, string>

     */

    protected $hidden = [

        'password',

        'remember_token',

    ];



    /**

     * The attributes that should be cast.

     *

     * @var array<string, string>

     */

    protected $casts = [

        'email_verified_at' => 'datetime',

        'jabatan' => 'array',

    ];



    // app/Models/User.php



    public function getFotoProfilUrlAttribute()

{

    if (empty($this->foto_profil)) {

        return null;

    }



    $path = $this->foto_profil;



    // Jika sudah ada domain (http), bersihkan dari duplikasi

    if (str_contains($path, 'http')) {

        $segments = explode('/storage/', $path);

        $path = end($segments);

    }



    // Bersihkan path agar tidak ada storage/ ganda

    $path = ltrim($path, '/');

    $path = str_replace('storage/', '', $path);

    $path = ltrim($path, '/');



    // Return URL bersih: http://10.0.2.2:8000/storage/foto_profil/xxx.jpg

    return asset('storage/' . $path);

}



public function toArray()

{

    $attributes = parent::toArray();

   

    // PERBAIKAN JABATAN: Ubah array ke string agar Flutter tidak error

    if (isset($attributes['jabatan'])) {

        $attributes['jabatan'] = is_array($this->jabatan)

            ? implode(', ', $this->jabatan)

            : $this->jabatan;

    }



    // Gunakan URL foto yang sudah dibersihkan

    $attributes['foto_profil'] = $this->foto_profil_url;

   

    return $attributes;

}

}