<?php

use Domain\Auth\UseCases\AuthedUser\AuthedUser;
use Domain\Auth\Services\AuthedUserInterface;
use Domain\User\Entities\User;
use Domain\Auth\Exceptions\UserNotFoundException;

it('returns the authenticated user', function () {
    $mockService = mock(AuthedUserInterface::class);
    $user = mock(User::class);

    $mockService->shouldReceive('getUserAuthed')
        ->once()
        ->andReturn($user);

    $authedUser = new AuthedUser($mockService);

    $result = $authedUser->execute();

    expect($result)->toBe($user);
});

it('throws UserNotFoundException when no user is authenticated', function () {
    $mockService = mock(AuthedUserInterface::class);

    $mockService->shouldReceive('getUserAuthed')
        ->once()
        ->andReturn(null);

    $authedUser = new AuthedUser($mockService);

    $this->expectException(UserNotFoundException::class);

    $authedUser->execute();
});
