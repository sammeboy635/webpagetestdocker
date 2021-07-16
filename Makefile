VERSION      = $(shell git describe --tags --always --dirty)
DOCKER_TAG   = upgrade#$(shell git rev-parse HEAD)
DOCKER_IMAGE =

default: all

php:
	docker build --rm -t "baqend/webpagetest-php:$(DOCKER_TAG)" -f Dockerfile-php .

nginx:
	docker build --rm -t "baqend/webpagetest-nginx:$(DOCKER_TAG)" -f Dockerfile-nginx .

apache:
	docker build --rm -t "wpt-apache:$(DOCKER_TAG)" -f Dockerfile .

all: php nginx
