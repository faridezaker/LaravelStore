<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes,HasFactory;

    protected $table = 'brands';

    protected $fillable = ['name','display_name'];

    public function getRouteKeyName()
    {
        return 'id';
    }
}
