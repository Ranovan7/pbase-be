<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Main Route

$app->group('/logger', function() {
    // home
    $this->get('[/]', function(Request $request, Response $response, $args) {
        $token_data = $request->getAttribute('decoded_token_data');
        dump($token_data);

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

    // change to post on production env
    $this->get('/{sn}', function(Request $request, Response $response, $args) {
        $logger_sn = $request->getAttribute('sn');
        $token_data = $request->getAttribute('decoded_token_data');
        dump($token_data);

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

});
