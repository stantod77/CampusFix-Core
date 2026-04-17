# CampusFix Priority Dispatcher v2.1 - Portable & Configurable

## What Changed

The system is now **fully portable** and works with any database configuration:

**Command-line Arguments** - Configure via CLI flags  
**Configuration File Support** - Load settings from database.conf  
**Demo Mode** - Run without any database  
**Graceful Fallback** - Works even if database is unavailable  
**Customizable Database** - Works with any MySQL database name/host  

## Usage

### Basic Usage
```bash
./bin/campusfix_dispatcher
```
Uses default settings (localhost:33060, campusfix_db)

### Demo Mode (No Database Required)
```bash
./bin/campusfix_dispatcher --demo-only
```
Runs with sample tickets, no database connection needed.

### Command-Line Arguments
```bash
./bin/campusfix_dispatcher --host db.example.com --port 33060 --user admin --password secret --database mydb
```

**Available Arguments:**
```
--host HOST              Database host (default: localhost)
--port PORT              Database port (default: 33060)
--user USER              Database user (default: root)
--password PASS          Database password (default: blank)
--database DB            Database name (default: campusfix_db)
--config FILE            Load configuration from file
--demo-only              Run in demo mode (no database)
--verbose                Enable verbose output (default)
--quiet                  Disable verbose output
--help                   Show help message
```

### Using Configuration File
```bash
./bin/campusfix_dispatcher --config database.conf
```

Edit `database.conf` to change settings:
```ini
host=db.example.com
port=33060
user=admin
password=secure_password
database=production_db
demo_mode=false
verbose=true
```

## Portability

### For Different Machines

1. **Clone/Download source code** (not just the binary)
2. **Install dependencies on their machine:**
   ```bash
   # macOS with Homebrew
   brew install mysql-connector-c++ mysql
   
   # Linux (Ubuntu/Debian)
   apt-get install libmysqlclient-dev mysql-connector-c++
   
   # Or compile from source
   ```

3. **Build on their machine:**
   ```bash
   make clean
   make
   ```

4. **Configure for their database:**
   ```bash
   ./bin/campusfix_dispatcher --config /path/to/their/database.conf
   ```

### For Different Database Names

If the database isn't named `campusfix_db`:

**Option 1:** Command-line
```bash
./bin/campusfix_dispatcher --database their_db_name
```

**Option 2:** Config file
```bash
echo "database=their_db_name" >> database.conf
./bin/campusfix_dispatcher --config database.conf
```

**Option 3:** Create custom config
```bash
cp database.conf production.conf
# Edit production.conf as needed
./bin/campusfix_dispatcher --config production.conf
```

## Example Scenarios

### Scenario 1: Local Development
```bash
./bin/campusfix_dispatcher --database campusfix_dev --port 33060
```

### Scenario 2: Remote Production Server
```bash
./bin/campusfix_dispatcher \
  --host prod-db.example.com \
  --port 33060 \
  --user campusfix_user \
  --password $DB_PASSWORD \
  --database campusfix_prod
```

### Scenario 3: Testing Without Database
```bash
./bin/campusfix_dispatcher --demo-only --quiet
```

### Scenario 4: Batch Processing (Silent Mode)
```bash
./bin/campusfix_dispatcher --quiet --config batch.conf
```

### Scenario 5: Debugging
```bash
./bin/campusfix_dispatcher --host localhost --database test_db --verbose
```

## Configuration Priority

Settings are applied in this order (later overrides earlier):

1. **Hardcoded defaults** (config.h)
2. **Config file** (if specified with --config)
3. **Command-line arguments** (highest priority)

Example:
```bash
# database.conf has: database=old_db
./bin/campusfix_dispatcher --config database.conf --database new_db
# Result: Uses new_db (command-line overrides file)
```

## Error Handling

The system gracefully handles:
- Database connection failures → Falls back to demo mode
- Wrong database name → Falls back to demo mode  
- Wrong credentials → Falls back to demo mode
- No database available → Works in demo mode
- All modes show clear error messages

## What Gets Stored/Used

**Database Queries:**
- Reads: buildings, tickets, assets tables
- Writes: Updates tickets with priority_score and status

**Demo Mode:**
- No database access
- Uses hardcoded sample tickets
- Results not persisted

## Security Notes

1. **Passwords in CLI** - Avoid: `./dispatcher --password secret` in shared shells
2. **Config Files** - Keep `database.conf` in secure directory (mode 600)
3. **Production** - Use environment variables for credentials:
   ```bash
   export DB_PASSWORD=secret
   ./bin/campusfix_dispatcher --password $DB_PASSWORD
   ```

## Distribution to Others

Share with:
```
src/
├── main.cpp
├── utility.cpp
├── utility.h
├── ticket_priority_engine.cpp
├── ticket_priority_engine.h
├── models.h
├── config.h
├── Makefile
├── database.conf (template)
└── BUILD_README.md
```

They can then:
```bash
make
./bin/campusfix_dispatcher --help
```

---

**Version:** 2.1  
**Date:** 01/28/2026  
**Status:** Fully Portable & Configurable
