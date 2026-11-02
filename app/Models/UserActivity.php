<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;
    protected $table = 'user_activity';

    // PERBAIKAN: relasi ke user pelaku aktivitas (dipakai halaman detail log)
    public function user()
    {
        return $this->belongsTo('\App\Models\User', 'id_user');
    }
}
