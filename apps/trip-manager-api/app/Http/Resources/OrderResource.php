<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Create a list of OrderResource instances from an array of orders.
     *
     * @param array $orders
     * @return array
     */
    public static function list(array $orders)
    {
        return array_map(function ($order) {
            return new self($order);
        }, $orders);
    }


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'destination_id' => $this->getDestinationId(),
            'status' => $this->getStatus(),
            'departure_date' => $this->getDepartureDate()->format('Y-m-d H:i:s'),
            'return_date' => $this->getReturnDate()->format('Y-m-d H:i:s'),
            'approved_at' => $this->getApprovedAt() ? $this->getApprovedAt()->format('Y-m-d H:i:s') : null,
            'destination' => new DestinationResource($this->getDestination()),
            'user' => new UserResource($this->getUser())
        ];
    }
}
