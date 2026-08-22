<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterOpd extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'kategori'];
}
