#!/bin/sh
psql -h 127.0.0.1 -U sherwint_sherwin sherwint_tntmobile < /srv/www/tnt.dev/maintenance/purge.sql

