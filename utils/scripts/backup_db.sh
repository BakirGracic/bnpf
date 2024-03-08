#!/bin/bash

# Prompt user for MySQL credentials
read -p "Enter MySQL Username: " DB_USER
read -sp "Enter MySQL Password (invisible input): " DB_PASSWORD
echo # new line after invisible input
read -p "Enter Database Name: " DB_NAME
read -p "Enter MySQL Host (localhost if unsure): " DB_HOST
read -p "Enter descriptive name: " USER_INPUT_NAME
echo

# Calculate backup directory
ROOT_PATH=$(dirname "$(dirname "$(dirname "$(realpath $0)")")")
CURRENT_DATE=$(date +%d_%m_%y)
BACKUP_FILE="db_backup_${USER_INPUT_NAME}_${CURRENT_DATE}.sql"
SAVE_PATH="$ROOT_PATH/utils/backups/mysql/${BACKUP_FILE}"

# Create backup
mysqldump -u $DB_USER -p$DB_PASSWORD -h $DB_HOST $DB_NAME > $SAVE_PATH

# Check if the backup was successful
echo
if [ $? -eq 0 ]; then
    echo "Backup successful: $BACKUP_FILE"
else
    echo "Backup failed"
fi

echo
