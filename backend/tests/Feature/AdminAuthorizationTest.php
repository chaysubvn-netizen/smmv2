<?php

use App\Http\Middleware\AuthAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

function adminMiddlewareRequest(?User $user): Request
{
    $request = Request::create('/api/admin/dashboard', 'GET');
    $request->headers->set('Accept', 'application/json');
    $request->setUserResolver(fn () => $user);

    return $request;
}

test('admin middleware rejects unauthenticated api requests', function () {
    $response = (new AuthAdmin())->handle(
        adminMiddlewareRequest(null),
        fn () => response()->json(['ok' => true])
    );

    expect($response->getStatusCode())->toBe(401);
});

test('admin middleware forbids regular users', function () {
    $user = new User(['role' => 'user']);
    $user->id = 10;

    expect(fn () => (new AuthAdmin())->handle(
        adminMiddlewareRequest($user),
        fn () => response()->json(['ok' => true])
    ))->toThrow(HttpException::class);
});

test('admin middleware allows administrators', function () {
    $user = new User(['role' => 'admin']);
    $user->id = 1;

    $response = (new AuthAdmin())->handle(
        adminMiddlewareRequest($user),
        fn () => response()->json(['ok' => true])
    );

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getData(true))->toBe(['ok' => true]);
});