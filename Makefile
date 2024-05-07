build:
	docker compose build
install:
	docker compose exec api_server_php composer install
up:
	docker compose up -d
down:
	docker compose down
rr:
	make down && make build && make up
migrations:
	docker compose exec api_server_php bin/console d:m:m -n
fixtures:
	docker compose exec api_server_php bin/console d:f:l -n
phpcs:
	docker compose exec api_server_php vendor/bin/php-cs-fixer fix src --dry-run -v
phpstan:
	docker compose exec api_server_php vendor/bin/phpstan
.PHONY: migrations