<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

// Main Route

$app->group('/api', function() {
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

    // token generation testing
    $this->get('/tokentest', function(Request $request, Response $response, $args) {
        $params = $request->getParams();
        $settings = $this->get('settings');
        $secret = $settings['jwt']['secret'];
        $token = JWT::encode($params, $secret, "HS256");

        $data = [
            "status" => "200",
            "message" => "authentication success",
            "data" => [
                "token" => $token
            ]
        ];

        return $response->withJson($data, 200, JSON_PRETTY_PRINT);
    });

    $this->post('/token', function(Request $request, Response $response, $args) {
        $params = $request->getParams();
        // verify params data

        // generate token
        $settings = $this->get('settings');
        $secret = $settings['jwt']['secret'];
        $token = JWT::encode($params, $secret, "HS256");

        $data = [
            "status" => "200",
            "message" => "authentication success",
            "data" => [
                "token" => $token
            ]
        ];

        return $response->withJson($data, 200, JSON_PRETTY_PRINT);
    });

    // Auth User
    // dummy login flow, bisa di uncomment ke POST
    // $app->get('/lg', function(Request $request, Response $response, $args) {
    $this->post('/login', function(Request $request, Response $response, $args) {
        $credentials = $request->getParams();
        if (empty($credentials['username']) || empty($credentials['password'])) {
            die("Masukkan username dan password");
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE username=:username");
        $stmt->execute([':username' => $credentials['username']]);
        $user = $stmt->fetch();
        if (!$user) {
            die("Username tidak terdaftar!");
        } elseif (!password_verify($credentials['password'], $user['password'])) {
            // check password using md5 before returning false
            if (MD5($credentials['password']) == $user['password']) {
                // if md5 encrypt is correct, update password to use bcrypt
                $update_pass = $this->db->prepare("UPDATE users SET password=:password WHERE id=:id");
                $update_pass->execute([
                    ':password' => password_hash($credentials['password'], PASSWORD_DEFAULT),
                    ':id' => $user['id']
                ]);
            } else {
                die("Password salah!");
            }
        }

        $this->session->user_id = $user['id'];
        $this->session->user_refresh_time = strtotime("+12hour");

        // die("Welcommmen {$user['username']}!");
        // $this->flash->addMessage('messages', 'Berhasil Login');
        return $response->withJson([
            "status" => "200",
            "message" => "login success",
            "data" => [
                "key" => "VALUE"
            ]
        ], 200);
    });

    // generate admin, warning!
    $this->get('/gen', function(Request $request, Response $response, $args) {
        $credentials = $request->getParams();
        if (empty($credentials['username']) || empty($credentials['password']) || empty($credentials['role'])) {
            die("Masukkan username, password dan role");
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE username=:username");
        $stmt->execute([':username' => $credentials['username']]);
        $user = $stmt->fetch();

        // jika belum ada di DB, tambahkan
        if (!$user) {
            $stmt = $this->db->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
            $stmt->execute([
                ':username' => $credentials['username'],
                ':password' => password_hash($credentials['password'], PASSWORD_DEFAULT),
                ':role' => $credentials['role'],
            ]);
            die("Username {$credentials['username']} ditambahkan!");
        } else { // else update password
            $stmt = $this->db->prepare("UPDATE users SET password=:password WHERE id=:id");
            $stmt->execute([
                ':password' => password_hash($credentials['password'], PASSWORD_DEFAULT),
                ':id' => $user['id']
            ]);
            die("Password {$user['username']} diubah!");
        }
    });

    $this->get('/forbidden', function(Request $request, Response $response, $args) {

        return $response->withJson([
            "status" => "403",
            "message" => "forbidden",
            "data" => [
                "key" => "VALUE"
            ]
        ], 200);
    });
});
