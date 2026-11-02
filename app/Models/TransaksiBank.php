<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiBank extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'transaksi_bank';
    protected $primaryKey = 'kode_transaksi_bank';
    public $incrementing = FALSE;
    protected $dates = ['deleted_at'];

    public function kodeAkun()
    {
        return $this->belongsTo(KodeAkun::class,'akun_kode');

    }
    public function detailKas()
    {
        return $this->hasMany('App\Models\TransaksiBankDetail');
    }
}
