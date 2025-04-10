<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListParameter extends Model
{
    use HasFactory;
    protected $table ="tbl_listparameter";
    protected $fillable = ["parameter","script"];
}
