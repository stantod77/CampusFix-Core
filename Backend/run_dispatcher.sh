#!/bin/bash

# CampusFix Priority Dispatcher Runner
# This script runs the priority dispatch system

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BIN_DIR="$SCRIPT_DIR/bin"
EXECUTABLE="$BIN_DIR/campusfix_dispatcher"

# Check if executable exists
if [ ! -f "$EXECUTABLE" ]; then
    echo "❌ Executable not found at $EXECUTABLE"
    echo "Building the project..."
    cd "$SCRIPT_DIR"
    make
    if [ $? -ne 0 ]; then
        echo "❌ Build failed"
        exit 1
    fi
fi

echo "🚀 Starting CampusFix Priority Dispatcher..."
echo "================================================\n"

"$EXECUTABLE"
