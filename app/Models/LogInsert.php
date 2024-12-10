<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogInsert extends Model
{
    protected $table = 'log_autoinsert';
    public $timestamps = false;
    protected $fillable = [
        'cabang', 
        'status', 
        'deskripsi', 
        'type', 
        'tgl_insert'
    ];
}
