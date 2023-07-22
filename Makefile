include .makenv
export

RELEASE_PATH ?=./release
WORDPRESS_VERSION ?=latest

help: ## Display help
	@awk -F ':|##' \
		'/^[^\t].+?:.*?##/ {\
			printf "\033[36m%-30s\033[0m %s\n", $$1, $$NF \
}' $(MAKEFILE_LIST) | sort

setup-tests: ## Setup unit test framework.
	/bin/bash tests/bin/install-wp-tests.sh $(TEST_DB_NAME) $(TEST_DB_USER) $(TEST_DB_PASSWORD) $(TEST_DB_HOST) $(WORDPRESS_VERSION) $(WC_VERSION)

build-release: ## Build the plugin zip file for release.
	/bin/bash build-release.sh $(PLUGIN_DIR_NAME) ./ $(RELEASE_PATH) $(VERSION)
