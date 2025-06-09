<?php


namespace Database\Factories;

use App\Models\Destination;
use App\Models\User;
use DateTimeImmutable;
use Domain\Order\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Destination>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $now = new DateTimeImmutable();
        return [
            'user_id' => User::factory()->create()->id,
            'status' => OrderStatus::REQUESTED,
            'destination_id' => Destination::factory()->create()->id,
            'departure_date' => $now->add(new \DateInterval('P7D'))->format('Y-m-d H:i:s'),
            'return_date' => $now->add(new \DateInterval('P14D'))->format('Y-m-d H:i:s'),
            'approved_at' => null,
        ];
    }
}
