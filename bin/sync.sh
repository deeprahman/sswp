#!/bin/bash

# Continuous sync between source and target dir

# Define source and target directories
SOURCE_DIR="/path/to/source_directory"
TARGET_DIR="/path/to/target_directory"

# Start inotifywait to watch the source directory and sync changes to the target directory
inotifywait -mr -e modify,create,delete,move "$SOURCE_DIR" | while read -r path action file; do
    rsync -avz --delete "$SOURCE_DIR/" "$TARGET_DIR/"
done
