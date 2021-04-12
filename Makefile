DOCKER_TAG   = $(shell date +%Y-%m-%d)

default: build-all

php:
	docker build --rm -t "baqend/webpagetest-php:$(DOCKER_TAG)" -f Dockerfile-php .

nginx:
	docker build --rm -t "baqend/webpagetest-nginx:$(DOCKER_TAG)" -f Dockerfile-nginx .

apache:
	docker build --rm -t "wpt-apache:$(DOCKER_TAG)" -f Dockerfile .

push:
	docker push baqend/webpagetest-php:$(DOCKER_TAG)
	docker push baqend/webpagetest-nginx:$(DOCKER_TAG)

build-all: php nginx
