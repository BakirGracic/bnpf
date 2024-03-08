#!/bin/bash

# Info message
echo
echo "Removing worker from cron jobs schedule."

# Prompt the user for the full path to the worker file
echo "Please enter the full path to your worker file: "
read WORKER_PATH
echo

# Validate if the file exists
if [ ! -f "$WORKER_PATH" ]; then
    echo "The file does not exist. Please check the path and try again."
    exit 1
fi

# Remove the cron job that executes the worker file
(crontab -l | grep -v "/usr/bin/php $WORKER_PATH") | crontab -

# Check if the cron job was successfully removed
if (crontab -l | grep -q "/usr/bin/php $WORKER_PATH"); then
    echo "Failed to remove the worker file from cron jobs."
else
    echo "Worker file removed from cron jobs successfully."
fi

# Restart cron service
if systemctl restart cron; then
    echo "Restarted cron service!"
else
    echo "Failed restarting cron service. Please do it manually or reboot the system."
fi

echo
