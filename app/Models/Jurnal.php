<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jurnal extends Model
{
    // PERBAIKAN SAK: jurnal ikut soft-delete mengikuti header transaksinya,
    // agar transaksi yang dipindah ke sampah tidak terhitung di laporan.
    use HasFactory, SoftDeletes;
    protected $table = 'jurnal';
}
