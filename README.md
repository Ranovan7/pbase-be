# PBase Backend

1. Duplicate .env.template to .env
2. Masukkan detail database & APP_URL (localhost:8888 jika menggunakan built in php server)
3. `composer install` && `composer start` (built in php server)

# Use Case

## Authentikasi `/api/auth`
1. `/login` POST minta token pakai BASIC Auth, https://github.com/tuupola/slim-basic-auth
2. selanjutnya pakai BEARER

## Ambil data

# Testing
- *jangan pakai ini* ~token generation : /tokentest?username=<username>&password=<username>~
- testing generated token on development server : using curl command below
- curl --header "Authorization: Bearer <generated_token>" http://localhost:8888/api
