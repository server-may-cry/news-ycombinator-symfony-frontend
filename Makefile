vendor: composer.json composer.lock
	composer install

.PHONY: web-server
web-server: vendor
	php -S 127.0.0.1:8000 -t public

.PHONY: fix-cs
fix-cs: vendor
	vendor/bin/php-cs-fixer fix

.PHONY: test
test: vendor
	vendor/bin/phpstan analyze src --level=max
	bin/console lint:yaml config
	bin/console lint:twig templates
