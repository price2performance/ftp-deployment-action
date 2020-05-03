#!/bin/sh

cp /ftp-deployment/provide-config.php provide-config.php
deployment provide-config.php $*
rm provide-config.php