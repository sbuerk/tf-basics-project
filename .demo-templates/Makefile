.PHONY: help
help: ## Displays this list of targets with descriptions
	@echo "The following commands are available:\n"
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: test-unit
test-unit: ## Run unit tests
	ddev exec vendor/bin/phpunit -c Build/phpunit/UnitTests.xml

.PHONY: test-functional
test-functional: ## Run functional tests
	ddev exec typo3DatabaseDriver=pdo_sqlite php vendor/bin/phpunit -c Build/phpunit/FunctionalTests.xml
