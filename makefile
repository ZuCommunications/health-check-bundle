##@ Help

## Source - https://www.thapaliya.com/en/writings/well-documented-makefiles/
help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Dev Tools
phpstan:  ## Run PHPStan
	tools/php-stan/vendor/bin/phpstan analyse --configuration tools/php-stan/phpstan.neon

phpcs:  ## Run PHP Code Sniffer
	tools/php-cs-fixer/vendor/bin/php-cs-fixer --config=tools/php-cs-fixer/.php-cs-fixer.php fix src tests

test:  ## Run tests
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text --coverage-html tools/php-unit/coverage --testdox