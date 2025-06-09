<?php

use Domain\Auth\Exceptions\InvalidCredentialsException;
use Domain\Auth\UseCases\Login\Login;
use Domain\Auth\UseCases\Login\LoginDTO;
use Domain\Auth\UseCases\Login\LoginOutputDTO;
use Domain\Auth\Services\LoginInterface;

it('executes login and returns token', function () {
    $mockService = mock(LoginInterface::class);
    $dto = new LoginDTO(
        email: 'user@example.com',
        password: 'password'
    );
    $expectedToken = 'test-token';

    $mockService->shouldReceive('login')
        ->once()
        ->with($dto)
        ->andReturn($expectedToken);

    $login = new Login($mockService);

    $result = $login->execute($dto);

    expect($result)->toBeInstanceOf(LoginOutputDTO::class)
        ->and($result->token)->toBe($expectedToken);
});

it('throws InvalidCredentialsException when credentials are invalid', function () {
    $mockService = mock(LoginInterface::class);
    $dto = new LoginDTO(
        email: 'user@example.com',
        password: 'passwordWrong'
    );

    $mockService->shouldReceive('login')
        ->once()
        ->with($dto)
        ->andReturn(false);

    $login = new Login($mockService);

    $this->expectException(InvalidCredentialsException::class);

    $login->execute($dto);
});
