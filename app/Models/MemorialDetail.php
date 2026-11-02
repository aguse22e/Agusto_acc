<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemorialDetail extends Model
{
    use HasFactory;
    protected $table = 'memorial_detail';
    protected $fillable = ['kode_memorial', 'keterangan', 'kode','lawan','subtotal'];

}
