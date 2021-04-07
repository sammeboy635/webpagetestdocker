VERSION      = $(shell git describe --tags --always --dirty)
DOCKER_TAG   = $(shell git rev-parse HEAD)
DOCKER_IMAGE =

default: all

php:
	docker build  --squash --rm -t "wpt-php:$(DOCKER_TAG)" -f Dockerfile-php .

nginx:
	docker build  --squash --rm -t "wpt-nginx:$(DOCKER_TAG)" -f Dockerfile-nginx .

apache:
	docker build  --squash --rm -t "wpt-apache:$(DOCKER_TAG)" -f Dockerfile .

all: php nginx
