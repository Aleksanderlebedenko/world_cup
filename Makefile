ifeq ($(shell uname -s),Darwin)
export OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES
IS_MAC := 1
endif

.PHONY: default
default: help

.PHONY: help
help: ## Get this help
	@echo Tasks:
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.PHONY: clean
clean: ## Stop and remove containers
	docker-compose rm -f -s -v

.PHONY: build
build:
	docker-compose build
	make upd
	docker exec php bash -c "composer install"

.PHONY: upd
upd: ## Run project containers in background
	docker-compose up -d

.PHONY: down
down: ## Stop project container
	docker-compose down

.PHONY: php
php: ## Ssh to main container
	docker exec -it php bash

.PHONY: redis
redis: ## Ssh to redis container
	docker exec -it redis redis-cli

.PHONY: test
test: ## Run unit tests
	docker exec php bash -c "bin/phpunit"

.PHONY: reset-data
reset-data: ## Run unit tests
	docker exec -it redis redis-cli -c "FLUSHALL"

