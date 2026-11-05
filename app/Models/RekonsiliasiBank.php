<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekonsiliasiBank extends Model
{
    use HasFactory;
    protected $table = 'rekonsiliasi_bank';
    protected $fillable = ['akun_kode', 'periode', 'saldo_koran'];
}
