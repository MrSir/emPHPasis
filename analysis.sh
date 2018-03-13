#!/bin/bash

./vendor/bin/phpunit -c ./build/phpunit.xml
./vendor/bin/phpcs --report=checkstyle --report-file=./build/logs/checkstyle.xml --standard=PSR2 --extensions=php ./src ./tests
./vendor/bin/phpmd ./src xml ./build/phpmd.xml --reportfile ./build/logs/pmd.xml
./vendor/bin/phpcpd --log-pmd ./build/logs/pmd-cpd.xml ./src
./vendor/bin/phploc --count-tests ./src/ ./tests/ --log-xml=./build/logs/phploc.xml
./vendor/bin/phpdox -f ./build/phpdox.xml
