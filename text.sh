#!/bin/bash

# Function to copy files and directories recursively and append .txt to filenames
copy_and_append_txt() {
    local source_dir=$1
    local dest_dir=$2

    # Create the destination directory if it doesn't exist
    mkdir -p "$dest_dir"

    # Find all files and directories in the source directory except the destination directory
    find "$source_dir" -type f ! -path "$dest_dir/*" | while read -r file; do
        # Get the relative path of the file
        relative_path="${file#$source_dir/}"
        
        # Create the directory structure in the destination directory
        mkdir -p "$dest_dir/$(dirname "$relative_path")"
        
        # Copy the file to the destination directory with .txt appended
        cp "$file" "$dest_dir/$relative_path.txt"
    done

    find "$source_dir" -type d ! -path "$dest_dir" ! -path "$dest_dir/*" | while read -r dir; do
        # Get the relative path of the directory
        relative_path="${dir#$source_dir/}"
        
        # Create the directory structure in the destination directory
        mkdir -p "$dest_dir/$relative_path"
    done
}

# Get the current working directory
pwd=$(pwd)

# Define the source and destination directories
source_dir="$pwd"
dest_dir="$pwd/txt"

# Call the function to copy files and directories, and append .txt to filenames
copy_and_append_txt "$source_dir" "$dest_dir"

echo "Files and directories copied successfully with .txt appended to filenames!"

