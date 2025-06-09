<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *  Destination Model
 */
class Destination extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var list<string>
     */
    protected $fillable = [
        'city',
        'iata_code',
        'country',
    ];

    /**
     * The attributes that are mass assignable.
     * @var boolean
     */
    public $timestamps = false;
}
