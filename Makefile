setup:
	cp .env.example .env
	docker run --rm \
		-u "$$(id -u):$$(id -g)" \
		-v $$(pwd):/var/www/html \
		-w /var/www/html \
		laravelsail/php81-composer:latest \
		composer install --ignore-platform-reqs
	@make install
	@make npm-install
up:
	./vendor/bin/sail up -d
build:
	./vendor/bin/sail up -d --build
install:
	./vendor/bin/sail up -d --build
	./vendor/bin/sail composer install
	./vendor/bin/sail php artisan key:generate
	./vendor/bin/sail php artisan migrate
stop:
	./vendor/bin/sail stop
restart:
	./vendor/bin/sail down
	./vendor/bin/sail up -d
down:
	./vendor/bin/sail down
ps:
	./vendor/bin/sail ps
shell:
	./vendor/bin/sail bash
fresh:
	./vendor/bin/sail php artisan migrate:fresh --seed
seed:
	./vendor/bin/sail php artisan db:seed
tinker:
	./vendor/bin/sail tinker
dump:
	./vendor/bin/sail php artisan dump-server
test:
	./vendor/bin/sail test
cache:
	./vendor/bin/sail composer dump-autoload -o
	./vendor/bin/sail php artisan optimize:clear
	./vendor/bin/sail php artisan optimize
clear:
	./vendor/bin/sail php artisan optimize:clear
log-clear:
	./vendor/bin/sail php artisan logs:clear
db:
	docker-compose exec db bash
sql:
	docker-compose exec db bash -c 'mysql -u $$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE'
npm-install:
	./vendor/bin/sail npm install
npm-run-dev:
	./vendor/bin/sail npm run dev
yarn:
	./vendor/bin/sail yarn
	./vendor/bin/sail yarn dev
ide-helper:
	./vendor/bin/sail php artisan clear-compiled
	./vendor/bin/sail php artisan ide-helper:generate
	./vendor/bin/sail php artisan ide-helper:models --nowrite
	./vendor/bin/sail php artisan ide-helper:meta
