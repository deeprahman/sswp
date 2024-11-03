#!/bin/bash
# Continuous sync between source and target dir with exclusions

# Check for required commands
if ! command -v inotifywait >/dev/null 2>&1; then
    echo "Error: inotifywait not found. Please install inotify-tools package."
    echo "On Debian/Ubuntu: sudo apt-get install inotify-tools"
    echo "On RHEL/CentOS: sudo yum install inotify-tools"
    echo "On MacOS: brew install inotify-tools"
    exit 1
fi

if ! command -v rsync >/dev/null 2>&1; then
    echo "Error: rsync not found. Please install rsync package."
    echo "On Debian/Ubuntu: sudo apt-get install rsync"
    echo "On RHEL/CentOS: sudo yum install rsync"
    echo "On MacOS: brew install rsync"
    exit 1
fi

# Define source and target directories
SOURCE_DIR="${1:-/home/deep/wsl.deeprahman.lo/wp-content/plugins/wp-securing-setup}"
TARGET_DIR="${2:-/var/www/stage.deeprahman.lo/wp/wp-content/plugins/wp-securing-setup}"

# Check if source and target directories exist
if [ ! -d "$SOURCE_DIR" ]; then
    echo "Error: Source directory '$SOURCE_DIR' does not exist."
    echo "Usage: $0 [source_dir] [target_dir]"
    exit 1
fi

if [ ! -d "$TARGET_DIR" ]; then
    echo "Error: Target directory '$TARGET_DIR' does not exist."
    echo "Usage: $0 [source_dir] [target_dir]"
    exit 1
fi

# Define exclusions - create exclude file in the same directory as the script
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
EXCLUDE_LIST="$SCRIPT_DIR/exclude.txt"

# Create exclude file if it doesn't exist
if [ ! -f "$EXCLUDE_LIST" ]; then
    echo "Creating exclude file at $EXCLUDE_LIST"
    cat > "$EXCLUDE_LIST" << EOL
# Add one pattern per line
.git/
node_modules/
*.tmp
*.log
.DS_Store
# Add more exclusions as needed
EOL
fi

# Function to build rsync exclude parameters
build_exclude_params() {
    local exclude_params=""
    while IFS= read -r line || [ -n "$line" ]; do
        # Skip empty lines and comments
        [[ -z "$line" || "$line" =~ ^[[:space:]]*# ]] && continue
        exclude_params+=" --exclude='$line'"
    done < "$EXCLUDE_LIST"
    echo "$exclude_params"
}

# Get exclude parameters
EXCLUDE_PARAMS=$(build_exclude_params)

echo "Starting sync from '$SOURCE_DIR' to '$TARGET_DIR'"
echo "Using exclude list from '$EXCLUDE_LIST'"
echo "Press Ctrl+C to stop"

# Start inotifywait to watch the source directory and sync changes to the target directory
inotifywait -mr -e modify,create,delete,move "$SOURCE_DIR" | while read -r path action file; do
    echo "Change detected: $action $path$file"
    eval "rsync -avz --delete $EXCLUDE_PARAMS \"$SOURCE_DIR/\" \"$TARGET_DIR/\""
done