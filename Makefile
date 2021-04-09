VERSION      = $(shell git describe --tags --always --dirty)
DOCKER_TAG   = $(shell git log -1 --format=%h)
DOCKER_IMAGE =

default: all

php:
	docker build --rm -t "wpt-php:$(DOCKER_TAG)" -f Dockerfile-php .

nginx:
	docker build  --squash --rm -t "wpt-nginx:$(DOCKER_TAG)" -f Dockerfile-nginx .

apache:
	docker build  --squash --rm -t "wpt-apache:$(DOCKER_TAG)" -f Dockerfile .

all: php nginx
