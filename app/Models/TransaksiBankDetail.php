<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiBankDetail extends Model
{
    use HasFactory;
    protected $table = 'transaksi_bank_detail';
    protected $fillable = ['kode_transaksi_bank', 'kode_lawan', 'subtotal','keterangan'];

}
