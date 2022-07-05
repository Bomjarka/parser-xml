<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'mark',
        'model',
        'generation',
        'year',
        'run',
        'color',
        'body-type',
        'engine-type',
        'transmission',
        'gear-type',
        'generation_id'
    ];
}
