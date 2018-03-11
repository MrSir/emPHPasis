#!/bin/bash

./vendor/bin/phpunit -c ./build/phpunit.xml
./vendor/bin/phpcs --report=checkstyle --report-file=./build/logs/checkstyle.xml --standard=PSR2 --extensions=php ./src ./tests
./vendor/bin/phpmd ./src xml ./build/phpmd.xml --reportfile ./build/logs/phpmd.xml
./vendor/bin/phpcpd --log-pmd ./build/logs/pmd-cpd.xml ./src
