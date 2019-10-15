# PBase Backend

1. Duplicate .env.template to .env
2. Masukkan detail database & APP_URL (localhost:8888 jika menggunakan built in php server)
3. `composer install` && `composer start` (built in php server)

# Testing
- token generation : /tokentest?username=<username>&password=<username>
- testing generated token on development server : using curl command below
- curl --header "Authorization: Bearer <generated_token>" http://localhost:8888/api
