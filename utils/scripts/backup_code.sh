#!/bin/bash

echo

# Check if zip installed on system
if ! command -v zip &> /dev/null
then
    echo "zip command could not be found, please install zip"
    exit 1
fi

# Make name of script (read input, add date, save to variable)
read -p "Enter descriptive name for the backup (without extension): " USER_INPUT_NAME
CURRENT_DATE=$(date +%d_%m_%y)
ZIP_FILE="code_backup_${USER_INPUT_NAME}_${CURRENT_DATE}.zip"

# Prepare paths
ROOT_PATH=$(dirname "$(dirname "$(dirname "$(realpath $0)")")")
SAVE_PATH="$ROOT_PATH/utils/backups/code/${ZIP_FILE}"
INCLUDE_PATH="$ROOT_PATH/*"
EXCLUDE_PATH="$ROOT_PATH/utils/backups/*"

# Create zip/backup
zip -r $SAVE_PATH $INCLUDE_PATH -x $EXCLUDE_PATH

# Success message
echo
echo "Backup created as $ZIP_FILE in $SAVE_PATH"
echo 
