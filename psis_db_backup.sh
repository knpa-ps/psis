#!/bin/bash
#Use bash. this is the directory which the bash is in.

#Time when this script executes.
NOW_TIME=`date`

#Backup date_time
BACKUP_TIME=`date +"%Y%m%d_%H%M%S"`

#Set DB directory
#DB_DIR=/usr/...

#Directory that the backup file will be stored
if [ ! -d /usr/backup/psis_db ]; then
	mkdir -p /usr/backup/psis_db
fi
BACKUP_DIR=/usr/backup/psis_db

#MySQL backup
echo "Start mysqldump to ${BACKUP_DIR}/${BACKUP_TIME}"
mysqldump -u root psis_db > ${BACKUP_DIR}/${BACKUP_TIME}.sql

#Set backup directory owner as knpa1206 and set permission to block other users.
chown -R knpa1206.knpa1206 ${BACKUP_DIR}
chmod -R 700 ${BACKUP_DIR}

#Delete old backup data (older than a week ago)
echo "Delete old backup files"
find ${BACKUP_DIR}/ -mtime +7 -exec rm -f {} \;

#Mirror backup files to NAS
echo "Start rsync to NAS - need rsa key"
rsync -avr -—delete ${BACKUP_DIR} root@10.17.42.238:/volume1/psis_gwangjw_backup

exit 0