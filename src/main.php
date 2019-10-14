<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Main Route

$app->group('/api', function() {
    // home
    $this->get('[/]', function(Request $request, Response $response, $args) {
        return $response->withJson([
            "status" => "200",
            "description" => "main api",
            "data" => [
                "key" => "VALUE"
            ]
        ], 200);
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
            "description" => "login success",
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

    $this->get('/logout', function(Request $request, Response $response, $args) {
        // $this->flash->addMessage('messages', 'Berhasil Logout');
        $this->session->destroy();
        return $this->response->withRedirect('/login');
    });

    $this->get('/forbidden', function(Request $request, Response $response, $args) {

        return $response->withJson([
            "status" => "403",
            "description" => "forbidden",
            "data" => [
                "key" => "VALUE"
            ]
        ], 200);
    });
});
