#!/bin/bash

php ./vendor/bin/phpstan --memory-limit=512M
php ./vendor/bin/php-cs-fixer fix
