<?php

use App\Models\Order;
use App\Models\User;
use App\Models\Destination;
use Domain\Order\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

uses(RefreshDatabase::class);


beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = JWTAuth::fromUser($this->user);
});

describe('Create Order', function () {

    it('creates a new order successfully', function () {
        $destination = Destination::factory()->create();

        $data = [
            'user_id' => $this->user->id,
            'destination_id' => $destination->id,
            'departure_date' => '2024-08-01 00:00:00',
            'return_date' => '2024-08-10 00:00:00',
        ];
        $response = $this->withHeaders([
                'Authorization' => "Bearer  $this->token",
                'Accept' => 'application/json',
            ])->postJson('/api/orders', $data);

        $response->assertStatus(204);
        $this->assertDatabaseHas('orders', $data);
    });

    it('returns 422 if validation fails', function () {

        $data = [
            'user_id' => $this->user->id,
            'destination_id' => null,
            'departure_date' => '2024-08-01',
            'return_date' => '2024-08-10',
        ];
        $response = $this->withHeaders([
                'Authorization' => "Bearer  $this->token",
                'Accept' => 'application/json',
            ])->postJson('/api/orders', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['destination_id']);
    });

    it('returns 403 if user is not authorized', function () {
        $user = User::factory()->create(['type' => 'MANAGER']);
        $token = JWTAuth::fromUser($user);
        $destination = Destination::factory()->create();

        $data = [
            'destination_id' => $destination->id,
            'departure_date' => '2024-08-01',
            'return_date' => '2024-08-10',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $token",
            'Accept' => 'application/json',
        ])->postJson('/api/orders', $data);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'user_not_authorized']);
    });

    it('returns 422 if destination is not found', function () {

        Destination::factory()->create();

        $data = [
            'user_id' => $this->user->id,
            'destination_id' => 999,
            'departure_date' => '2024-08-01',
            'return_date' => '2024-08-10',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->postJson('/api/orders', $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'destination_not_found']);
    });

    it('returns 422 if return_date is not after departure_date', function () {

        $destination = Destination::factory()->create();

        $data = [
            'user_id' => $this->user->id,
            'destination_id' => $destination->id,
            'departure_date' => '2024-08-10',
            'return_date' => '2024-08-01',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->postJson('/api/orders', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['return_date']);
    });
});

describe('Update Order', function () {

    it('updates an order successfully', function () {

        $destination = Destination::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $data = [
            'destination_id' => $destination->id,
            'departure_date' => '2024-08-01 00:00:00',
            'return_date' => '2024-08-10 00:00:00',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->putJson("/api/orders/{$order->id}", $data);


        $response->assertStatus(204);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            ...$data,
        ]);
    });

    it('returns 422 if update validation fails', function () {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
        ]);


        $data = [
            'destination_id' => null,
            'departure_date' => '2024-08-01',
            'return_date' => '2024-08-10',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->putJson("/api/orders/{$order->id}", $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['destination_id']);
    });

    it('returns 404 if order is not found', function () {

        $user = User::factory()->create();
        Auth::setUser($user);

        $data = [
            'destination_id' => Destination::factory()->create()->id,
            'departure_date' => '2024-08-01',
            'return_date' => '2024-08-10',
        ];


        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->putJson("/api/orders/999", $data);

        $response->assertStatus(404);
    });

    it('returns 403 if user is not authorized to update the order', function () {
        $otherUser = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $data = [
            'destination_id' => Destination::factory()->create()->id,
            'departure_date' => '2024-08-01',
            'return_date' => '2024-08-10',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->putJson("/api/orders/{$order->id}", $data);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'user_not_authorized']);
    });

    it('returns 422 if destination is not found in update', function () {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $data = [
            'destination_id' => 999,
            'departure_date' => '2024-08-01',
            'return_date' => '2024-08-10',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->putJson("/api/orders/{$order->id}", $data);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'destination_not_found']);
    });

    it('returns 422 if return_date is not after departure_date in update', function () {
        $destination = Destination::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $data = [
            'destination_id' => $destination->id,
            'departure_date' => '2024-08-10',
            'return_date' => '2024-08-01',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->putJson("/api/orders/{$order->id}", $data);

        $response->assertStatus(422);
    });
});

describe('Update Order status', function () {
    it('updates order status to approved successfully', function () {
        $user = User::factory()->create(['type' => "MANAGER"]);
        $order = Order::factory()->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'status' => 'APPROVED',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $token",
            'Accept' => 'application/json',
        ])->patchJson("/api/orders/{$order->id}", $data);

        $response->assertStatus(204);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::APPROVED->value,
        ]);
    });

    it('updates order status to canceled successfully', function () {
        $user = User::factory()->create(['type' => "MANAGER"]);
        $order = Order::factory()->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'status' => 'CANCELED',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $token",
            'Accept' => 'application/json',
        ])->patchJson("/api/orders/{$order->id}", $data);

        $response->assertStatus(204);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::CANCELED->value,
        ]);
    });

    it('returns 422 if validation fails', function () {
        $user = User::factory()->create(['type' => "MANAGER"]);
        $order = Order::factory()->create();

        $token = JWTAuth::fromUser($user);

        $data = [
            'status' => 'DELETED',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $token",
            'Accept' => 'application/json',
        ])->patchJson("/api/orders/{$order->id}", $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    });

    it('returns 404 if order is not found', function () {
        $user = User::factory()->create(['type' => "MANAGER"]);
        $token = JWTAuth::fromUser($user);

        $data = [
            'status' => 'APPROVED',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $token",
            'Accept' => 'application/json',
        ])->patchJson("/api/orders/9", $data);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'order_not_found']);
    });

    it('returns 403 if user is not authorized to approve orders', function () {
        $user = User::factory()->create(['type' => "SOLICITOR"]);
        $token = JWTAuth::fromUser($user);
        $order = Order::factory()->create();

        $data = [
            'status' => 'APPROVED',
        ];


        $response = $this->withHeaders([
            'Authorization' => "Bearer  $token",
            'Accept' => 'application/json',
        ])->patchJson("/api/orders/{$order->id}", $data);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'user_not_authorized']);
    });

    it('handles ApproveException and returns 204', function () {
        $user = User::factory()->create(['type' => "MANAGER"]);
        $token = JWTAuth::fromUser($user);
        $order = Order::factory()->create(['status' => OrderStatus::APPROVED]);


        $data = [
            'status' => 'APPROVED',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $token",
            'Accept' => 'application/json',
        ])->patchJson("/api/orders/{$order->id}", $data);


        $response->assertStatus(204);
    });

    it('handles CancelException and returns 204', function () {
        $user = User::factory()->create(['type' => "MANAGER"]);
        $token = JWTAuth::fromUser($user);
        $order = Order::factory()->create(['status' => OrderStatus::CANCELED]);

        $data = [
            'status' => 'CANCELED',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $token",
            'Accept' => 'application/json',
        ])->patchJson("/api/orders/{$order->id}", $data);

        $response->assertStatus(422);
    });
});

describe('Show Order', function () {
    it('shows an order successfully', function () {
        $destination = Destination::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'destination_id' => $destination->id,
            'departure_date' => '2024-08-01',
            'return_date' => '2024-08-10',
            'status' => OrderStatus::REQUESTED,
        ]);
        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $order->id,
                'departure_date' => '2024-08-01 00:00:00',
                'return_date' => '2024-08-10 00:00:00',
                'status' => OrderStatus::REQUESTED->value,
                'approved_at' => null,
                'destination' => [
                    'city' => $destination->city,
                    'iata_code' => $destination->iata_code,
                    'country' => $destination->country,
                ],
                'user' => [
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ],
            ]);
    });

    it('shows an order successfully for user manager', function () {
        $userManager = User::factory()->create(['type' => "MANAGER"]);
        $token = JWTAuth::fromUser($userManager);

        $destination = Destination::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'destination_id' => $destination->id,
            'departure_date' => '2024-08-01',
            'return_date' => '2024-08-10',
            'status' => OrderStatus::REQUESTED,
        ]);
        $response = $this->withHeaders([
            'Authorization' => "Bearer  $token",
            'Accept' => 'application/json',
        ])->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $order->id,
                'departure_date' => '2024-08-01 00:00:00',
                'return_date' => '2024-08-10 00:00:00',
                'status' => OrderStatus::REQUESTED->value,
                'approved_at' => null,
                'destination' => [
                    'city' => $destination->city,
                    'iata_code' => $destination->iata_code,
                    'country' => $destination->country,
                ],
                'user' => [
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ],
            ]);
    });

    it('returns 404 if order is not found', function () {

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->getJson("/api/orders/2");

        $response->assertStatus(404);
        $response->assertJson(['message' => 'order_not_found']);
    });

    it('returns 403 if user is not authorized to view the order', function () {
        $otherUser = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->getJson("/api/orders/{$order->id}");

        $response->assertStatus(403);
        $response->assertJson(['message' => 'user_not_authorized']);
    });
});

describe('list all orders', function () {
    it('lists orders successfully', function () {

        $destination1 = Destination::factory()->create();
        $destination2 = Destination::factory()->create();

        Order::factory()->create(['user_id' => $this->user->id, 'destination_id' => $destination1->id, 'status' => OrderStatus::REQUESTED]);
        Order::factory()->create(['user_id' => $this->user->id, 'destination_id' => $destination2->id, 'status' => OrderStatus::APPROVED]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->getJson("/api/orders");

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'departure_date',
                    'return_date',
                    'status',
                    'approved_at',
                    'destination' => [
                        'city',
                        'iata_code',
                        'country',
                    ],
                    'user' => [
                        'name',
                        'email',
                    ],
                ],
            ])
            ->assertJsonCount(2);
    });

    it('lists orders successfully with status filter', function () {

        $destination1 = Destination::factory()->create();
        $destination2 = Destination::factory()->create();

        $order1 = Order::factory()->create(['user_id' => $this->user->id, 'destination_id' => $destination1->id, 'status' => OrderStatus::REQUESTED]);
        $order2 = Order::factory()->create(['user_id' => $this->user->id, 'destination_id' => $destination2->id, 'status' => OrderStatus::APPROVED]);


        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->getJson("/api/orders?status=REQUESTED");

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([
                [
                    'id' => $order1->id,
                    'status' => OrderStatus::REQUESTED->value,
                ],
            ]);
    });

    it('lists orders successfully with destination_id filter', function () {
        $destination1 = Destination::factory()->create();
        $destination2 = Destination::factory()->create();

        $order1 = Order::factory()->create(['user_id' => $this->user->id, 'destination_id' => $destination1->id, 'status' => OrderStatus::REQUESTED]);
        $order2 = Order::factory()->create(['user_id' => $this->user->id, 'destination_id' => $destination2->id, 'status' => OrderStatus::APPROVED]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->getJson("/api/orders?destination_id={$destination1->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([
                [
                    'id' => $order1->id,
                    'destination' => [
                        'city' => $destination1->city,
                        'iata_code' => $destination1->iata_code,
                        'country' => $destination1->country,
                    ],
                ],
            ]);
    });

    it('lists orders successfully with start_date and end_date filters', function () {
        $destination1 = Destination::factory()->create();

        $order1 = Order::factory()->create(['user_id' => $this->user->id, 'destination_id' => $destination1->id, 'departure_date' => '2024-08-01', 'return_date' => '2024-08-04' , 'status' => OrderStatus::REQUESTED]);
        $order2 = Order::factory()->create(['user_id' => $this->user->id, 'destination_id' => $destination1->id, 'departure_date' => '2024-08-10', 'return_date' => '2024-08-15' ,'status' => OrderStatus::APPROVED]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->getJson("/api/orders?start_date=2024-08-05&end_date=2024-08-15");

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJson([
                    [
                        'id' => $order2->id,
                        'departure_date' => '2024-08-10 00:00:00',
                    ],
            ]);
    });
    it('returns 422 if validation fails for date range', function () {
        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->getJson("/api/orders?start_date=2024-08-05&end_date=ssss");

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_date']);
    });

    it('returns 422 if validation fails for status', function () {
        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->getJson("/api/orders?status=INVALID_STATUS");

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    });

    it('returns 422 if validation fails for destination_id', function () {
        $response = $this->withHeaders([
            'Authorization' => "Bearer  $this->token",
            'Accept' => 'application/json',
        ])->getJson("/api/orders?destination_id=INVALID");

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['destination_id']);
    });
});
