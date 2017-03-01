
BACKUP_DIR=/backup/

/usr/bin/pg_dump -h 127.0.0.1 -U sherwint_sherwin -c sherwint_sms102 > $BACKUP_DIR"sherwint_sms102-"`date +\%Y-\%m-\%d-\%H-\%M`.sql

/usr/bin/psql -h 127.0.0.1 -U sherwint_sherwin sherwint_sms102 < /srv/www/sms102.dev/maintenance/maintenance.sql
