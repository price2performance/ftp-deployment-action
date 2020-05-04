#!/bin/sh

set -e

if [ ${INPUT_CONFIG_FILE} ]; then
  cp /ftp-deployment/provide-config.php provide-config.php
  deployment provide-config.php ${INPUT_PARAMETERS}
  rm provide-config.php
else
  deployment $*
fi