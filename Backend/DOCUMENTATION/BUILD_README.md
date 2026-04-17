# CampusFix Priority Dispatcher - Build Instructions

## Overview
This directory contains the C++ Priority Dispatch System for CampusFix, which automatically calculates priority scores for facility tickets and assigns them to appropriate trades.

## Build Artifacts

### Executable
- **Location**: `bin/campusfix_dispatcher`
- **Size**: ~422 KB
- **Platform**: macOS arm64 (Apple Silicon)
- **Status**: Ready to run

## Quick Start

### Option 1: Run the dispatcher directly
```bash
./bin/campusfix_dispatcher
```

### Option 2: Use the convenience script
```bash
./run_dispatcher.sh
```
This script automatically builds if the executable is missing.

### Option 3: Build from scratch
```bash
make clean
make
```

## Available Make Commands

| Command | Description |
|---------|-------------|
| `make` | Build the project |
| `make clean` | Remove all build artifacts and executable |
| `make run` | Build and run the dispatcher |
| `make directories` | Create necessary build directories |

## What Gets Built

The build process compiles:
- **main.cpp** - Entry point and database integration
- **utility.cpp** - Database helper functions
- **ticket_priority_engine.cpp** - Priority calculation engine
- **models.h** - Data structures (Ticket, Building, Assignment)

All object files are stored in `build/` directory.

## Requirements

- C++17 or later
- MySQL 9.6.0 (with dev headers)
- MySQL Connector C++ 9.6.0
- Installed via Homebrew on macOS

## Database Connection

The dispatcher connects to:
- **Host**: localhost
- **Port**: 33060 (MySQL X Protocol)
- **Database**: campusfix_db
- **User**: root
- **Password**: (blank)

Update these in `main.cpp` if your setup differs.

## Output

When run, the dispatcher:
1. Connects to the CampusFix database
2. Fetches all open tickets
3. Calculates priority scores (Severity × Building Criticality)
4. Assigns tickets to appropriate trades
5. Updates the database with results
6. Prints a summary sorted by priority

## Troubleshooting

### Build fails with library errors
- Ensure MySQL and MySQL Connector C++ are installed via Homebrew
- Update the library paths in `Makefile` if installed elsewhere

### Connection errors when running
- Verify MySQL server is running: `mysql --version`
- Check database exists: `mysql -u root campusfix_db`
- Confirm user/password credentials in main.cpp

### Executable not found
- Run `make` to build
- Check `bin/campusfix_dispatcher` exists

## File Structure

```
Backend/
├── Makefile                          # Build configuration
├── main.cpp                          # Entry point
├── utility.cpp / utility.h           # Database functions
├── ticket_priority_engine.cpp/.h     # Priority calculation
├── models.h                          # Data structures
├── run_dispatcher.sh                 # Convenience runner script
├── bin/                              # Build output
│   └── campusfix_dispatcher          # Compiled executable
└── build/                            # Object files (.o)
    ├── main.o
    ├── utility.o
    └── ticket_priority_engine.o
```

---

**Last Updated**: 01/28/2026  
**Author**: Mohamad Al Kary
