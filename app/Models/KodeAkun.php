<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KodeAkun extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'kode_akun';
    protected $primaryKey = 'kode_akun';
    protected $dates = ['deleted_at'];

    public function kodeInduk()
    {
        return $this->belongsTo('\App\Models\KodeInduk', 'induk_kode');
    }

    public function user()
    {
        return $this->belongsTo('\App\Models\User', 'deleted_by');
    }
}
