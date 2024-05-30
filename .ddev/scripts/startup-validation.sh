#!/bin/bash

IS_DDEV_PROJECT=${IS_DDEV_PROJECT:"false"}
if [[ "${IS_DDEV_PROJECT}" != "true" ]]; then
  echo "ERROR: $0 must be executed within a ddev container"
  exit 1
fi

BIN_PHP="/usr/bin/php -d memory_limit=-1 -d xdebug.mode=off "
BIN_COMPOSER="${BIN_PHP} /usr/local/bin/composer"
BIN_MYSQL="/usr/bin/mysql"
BIN_TYPO3="${BIN_PHP} /var/www/html/vendor/bin/typo3"

function instanceSetup() {
  local DB_TABLES="$(mysql db -e 'SHOW TABLES;')"
  if [[ -z "${DB_TABLES}" ]]; then
    # TYPO3 not installed - setup instance
    echo ">> Setup instance ..."
    ${BIN_TYPO3} setup \
      --force \
      --driver=pdoMysql \
      --host=db \
      --port=3306 \
      --dbname=db \
      --username=db \
      --password=db \
      --project-name="TF Integration Bascis Demo Project" \
      --server-type=apache \
      --no-interaction
    EXIT_CODE="$?"
    if [[ $EXIT_CODE -ne 0 ]]; then
      echo "[ERROR] TYPO3 Setup failed"
      exit 1;
    fi

    echo ">> Create maintainer admin user ..."
    ${BIN_TYPO3} backend:user:create --admin --maintainer --ansi
    if [[ $EXIT_CODE -ne 0 ]]; then
      echo "[ERROR] TYPO3 Setup failed"
      exit 1;
    fi

    echo ">> Setup styleguide frontend ..."
    ${BIN_TYPO3} styleguide:generate --create frontend
    EXIT_CODE="$?"
    if [[ $EXIT_CODE -ne 0 ]]; then
      echo "[ERROR] Creating styleguide frontend failed"
      exit 1;
    fi

    echo ">> Enable all sites ..."
    ${BIN_TYPO3} sites:state on
    EXIT_CODE="$?"
    if [[ $EXIT_CODE -ne 0 ]]; then
      echo "[ERROR] Enabling all sites failed"
      exit 1;
    fi

    ${BIN_TYPO3} cache:flush && ${BIN_TYPO3} cache:warmup
    EXIT_CODE="$?"
    if [[ $EXIT_CODE -ne 0 ]]; then
      echo "[ERROR] Enabling all sites failed"
      exit 1;
    fi

  else
    # ensure all extension table and fields are available (safe mode)
    ${BIN_TYPO3} extension:setup
    EXIT_CODE="$?"
    if [[ $EXIT_CODE -ne 0 ]]; then
      echo "[ERROR] extension setup failed"
    fi
  fi
}

instanceSetup
