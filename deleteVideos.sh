#!/usr/bin/env bash

find /var/www/webpagetest/www/results/video/* -mtime +7 -exec rm {} \;
