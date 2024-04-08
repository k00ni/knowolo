default:
	@echo ""
	@echo ""
	@echo "Knowolo - Makefile"
	@echo ""

prepare:
	cd scripts && vendor/bin/php-cs-fixer fix && vendor/bin/phpunit && vendor/bin/phpstan