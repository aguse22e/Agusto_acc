<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'tahun',
        'bulan',
        'seq_length',
        'seq_no',
        'kode_akun'
    ];
}
