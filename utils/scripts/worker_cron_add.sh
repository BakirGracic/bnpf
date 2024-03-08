#!/bin/bash

# Info message
echo
echo "Adds worker to cron jobs schedule with PHP as executor for every minute!"

# Prompt the user for the full path to the worker file
echo "Please enter the full path to your worker file: "
read WORKER_PATH
echo

# Validate if the file exists
if [ ! -f "$WORKER_PATH" ]; then
    echo "The file does not exist. Please check the path and try again."
    exit 1
fi

# Add the cron job to execute the worker file every second
(crontab -l 2>/dev/null; echo "1 * * * * /usr/bin/php $WORKER_PATH") | crontab -

# Success message
echo "Worker file added to cron jobs successfully."

# Restart cron service
if systemctl restart cron; then
    echo "Restarted cron service!"
else
    echo "Failed restarting cron service. Please do it manually or reboot the system."
fi

echo
