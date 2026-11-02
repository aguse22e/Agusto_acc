<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karyawan extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'karyawan';
    protected $dates = ['deleted_at', 'tanggal_lahir', 'tanggal_masuk'];

    public function jadwalKerja()
    {
        return $this->hasMany(JadwalKerja::class, 'karyawan_id');
    }
}
