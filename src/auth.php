<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

// Main Route

$app->group('/auth', function() {
    // home
    $this->get('[/]', function(Request $request, Response $response, $args) {
        $decoded = $request->getAttribute('decoded_token_data');
        // dump($decoded);

        $data = [
            "status" => "200",
            "message" => "welcome to main api",
            "data" => $decoded
        ];

        return $response
            ->withHeader("Content-Type", "application/json")
            ->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    });

    // change to post on production env
    $this->get('/login', function(Request $request, Response $response, $args) {
    // $this->post('/login', function(Request $request, Response $response, $args) {
        // check credentials
        $headers = $request->getHeaders();
        $username = $headers["PHP_AUTH_USER"][0];
        $user = $this->db->query("SELECT * FROM users WHERE username='{$username}'")->fetch();
        if (!$user || !password_verify($headers["PHP_AUTH_PW"][0], $user['password'])) {
            return $response->withJson([
                "status" => "failed",
                "message" => "authentication failed"
            ], 200, JSON_PRETTY_PRINT);
        }

        $data = [
            "username" => $headers["PHP_AUTH_USER"][0],
            "tenant_id" => $user['tenant_id']
        ];

        // generate token
        $settings = $this->get('settings');
        $secret = $settings['jwt']['secret'];
        $token = JWT::encode($data, $secret, "HS256");

        $result = [
            "status" => "200",
            "message" => "authentication success",
            "data" => [
                "token" => $token,
                "user" => $headers["PHP_AUTH_USER"][0]
            ]
        ];

        return $response->withJson($result, 200, JSON_PRETTY_PRINT);
    });

    $this->get('/logout', function(Request $request, Response $response, $args) {

        return $response->withJson([
            "status" => "200",
            "message" => "this is logout endpoint",
            "data" => [
                "key" => "VALUE"
            ]
        ], 200);
    });

    $this->get('/refresh', function(Request $request, Response $response, $args) {

        return $response->withJson([
            "status" => "200",
            "message" => "this is refresh endpoint",
            "data" => [
                "key" => "VALUE"
            ]
        ], 200);
    });

    $this->get('/update', function(Request $request, Response $response, $args) {

        return $response->withJson([
            "status" => "200",
            "message" => "this is update endpoint",
            "data" => [
                "key" => "VALUE"
            ]
        ], 200);
    });

    $this->get('/user', function(Request $request, Response $response, $args) {

        return $response->withJson([
            "status" => "200",
            "message" => "this is user endpoint",
            "data" => [
                "key" => "VALUE"
            ]
        ], 200);
    });
});
