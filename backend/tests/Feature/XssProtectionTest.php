<?php

use App\Http\Middleware\XssMiddleware;
use Illuminate\Http\Request;

function runThroughXssMiddleware(array $input)
{
    $request = Request::create('/api/test', 'POST', $input);
    $response = (new XssMiddleware())->handle($request, function (Request $request) {
        return response()->json($request->all());
    });

    return [$response, $response->getData(true)];
}

test('plain api fields cannot retain html or event handlers', function () {
    [$response, $data] = runThroughXssMiddleware([
        'username' => '<img src=x onerror=alert(1)>alice',
    ]);

    expect($data['username'])->toBe('alice')
        ->and($response->headers->get('X-Content-Type-Options'))->toBe('nosniff')
        ->and($response->headers->get('Content-Security-Policy'))->toContain("object-src 'none'");
});

test('rich text keeps formatting but removes executable markup', function () {
    [, $data] = runThroughXssMiddleware([
        'content' => '<p onclick="alert(1)">Hello</p><script>alert(2)</script><a href="javascript:alert(3)">bad</a><a href="https://example.com">ok</a>',
    ]);

    expect($data['content'])
        ->not->toContain('onclick')
        ->not->toContain('<script')
        ->not->toContain('javascript:')
        ->toContain('<p>Hello</p>')
        ->toContain('https://example.com');
});