#!/bin/bash

set -e

WORKSPACE=$2
JOB=$3

COMMAND=$(echo "${JOB}" | jq -r '.command')

if [[ ! ${COMMAND} =~ phpunit ]]; then
    exit 0
fi

PHP_VERSION=$(echo "${JOB}" | jq -r '.php')

if [[ "${PHP_VERSION}" != "8.1" ]]; then
    exit 0
fi

DEPS=$(echo "${JOB}" | jq -r '.dependencies')

if [[ "${DEPS}" != "lowest" ]]; then
    exit 0
fi

cd "${WORKSPACE}"
composer require "laminas/laminas-code:^4.4" --ignore-platform-req=php
