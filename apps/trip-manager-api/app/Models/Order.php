<?php

namespace App\Models;

use Domain\Order\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'status',
        'destination_id',
        'departure_date',
        'return_date',
        'approved_at',
    ];

    /**
     * Deafult values for attributes .
     * @var list<string>
     */
    protected $attributes = [
        'status' => 'REQUESTED',
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'departure_date' => 'datetime',
            'return_date' => 'datetime',
            'approved_at' => 'datetime',
            'status' => OrderStatus::class,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
