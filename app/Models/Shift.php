<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'shift';

    public function jadwalKerja()
    {
        return $this->hasMany(JadwalKerja::class, 'shift_id');
    }
}
