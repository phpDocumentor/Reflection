ARGS ?=

.PHONY: install-phive
install-phive:
	wget -O tools/phive.phar https://phar.io/releases/phive.phar; \
	wget -O tools/phive.phar.asc https://phar.io/releases/phive.phar.asc; \
	gpg --keyserver pool.sks-keyservers.net --recv-keys 0x9D8A98B29B2D5D79; \
	gpg --verify tools/phive.phar.asc tools/phive.phar; \
	chmod +x tools/phive.phar

.PHONY: setup
setup: install-phive
	docker run -it --rm -v${CURDIR}:/opt/phpdoc -w /opt/phpdoc phpdoc/dev tools/phive.phar install --force-accept-unsigned

.PHONY: test
test:
	docker-compose run --rm phpunit ${ARGS}
	docker-compose run --entrypoint=/usr/local/bin/php --rm phpunit tests/coverage-checker.php 69

.PHONY: pre-commit-test
pre-commit-test: test
