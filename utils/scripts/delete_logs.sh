#!/bin/bash

echo

# Array of directories to search for log files
directories=(
    "../logs/api/"
    "../logs/errors/"
    "../logs/jobs/"
)

# Iterate through each directory
for dir in "${directories[@]}"; do
    # Check if the directory exists
    if [ -d "$dir" ]; then
        # Use 'find' to delete .json files safely
        find "$dir" -name "*.json" -type f -delete
    else
        echo "Directory $dir does not exist." 
    fi
done

# Print success message
timestamp=$(date +"%Y-%m-%d %H:%M:%S")
echo
echo "[$timestamp] Successfully deleted all log files in /utils/logs/"
echo
