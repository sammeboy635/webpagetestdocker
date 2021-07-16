DOCKER_TAG   = upgrade-test#$(shell date +%Y-%m-%d)

default: build-alpine

alpine-php:
	docker build --rm -t "baqend/webpagetest-php:$(DOCKER_TAG)" -f Dockerfile-php .

alpine-nginx:
	docker build --rm -t "baqend/webpagetest-nginx:$(DOCKER_TAG)" -f Dockerfile-nginx .

apache:
	docker build --rm -t "baqend/webpagetest-server:$(DOCKER_TAG)" -f Dockerfile .

alpine-push:
	docker push baqend/webpagetest-php:$(DOCKER_TAG)
	docker push baqend/webpagetest-nginx:$(DOCKER_TAG)

apache-push:
	docker push baqend/webpagetest-server:$(DOCKER_TAG)

build-alpine: alpine-php alpine-nginx alpine-push
build-apache: apache
