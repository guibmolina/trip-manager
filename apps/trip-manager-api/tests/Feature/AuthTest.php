<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

uses(RefreshDatabase::class);

it('log in a user and returns a token', function () {
   User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'user@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['token']);
});

it('return 401 invalid credential when log in a user with wrong password', function () {
   User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'user@example.com',
        'password' => 'Wrongpassword',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'invalid_credentials']);
});

it('return 401 unauthorized when accessing protected route without token', function () {
    $response = $this->getJson('/api/auth/user');

    $response->assertStatus(401)
        ->assertJson(['error' => 'Unauthorized']);
});

it('returns the authenticated user', function () {

    $user = User::factory()->create();

    $token = JWTAuth::fromUser($user);

    $response = $this->withHeaders([
            'Authorization' => "Bearer  $token",
            'Accept' => 'application/json',
        ])->getJson('/api/auth/user');

    $response->assertStatus(200)
        ->assertJson([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'type'  => $user->type->value,
        ]);
});
