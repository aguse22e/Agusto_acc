<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Memorial extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'memorial';
    protected $primaryKey = 'kode_memorial';
    public $incrementing = FALSE;

    public function kodeRekening()
    {
        return $this->belongsTo(KodeAkun::class,'akun_kode');

    }
}
