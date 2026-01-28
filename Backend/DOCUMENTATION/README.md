# CampusFix Priority Dispatcher - v2.1 Complete

## What You Now Have

A **fully portable, production-ready** C++ priority dispatch system that:

### Core Features
- **Automatic Priority Calculation** - Formula: Severity x Building Criticality
-  **Auto Trade Assignment** - HVAC Tech, Plumber, Electrician, SysAdmin
-  **Database Integration** - Reads from MySQL, writes results back
-  **Sample Data** - 6 demo tickets included for testing

###  Portability Features (NEW!)
-  **Command-Line Configuration** - No recompilation needed to change database
-  **Config File Support** - Load settings from `database.conf`
-  **Demo Mode** - Works without any database (--demo-only)
-  **Graceful Fallback** - Works even if DB unavailable
-  **Custom Database Names** - Works with any database name
-  **Remote Servers** - Configure host, port, credentials via CLI

###  Project Structure

```
Backend/
├──  main.cpp                          # Entry point, CLI parsing
├──  utility.cpp / utility.h           # Database operations
├──  ticket_priority_engine.cpp/.h     # Priority calculation engine
├──  models.h                          # Data structures
├──  config.h                          # Configuration system
├──  Makefile                          # Build system
├──  database.conf                     # Configuration template
├──  BUILD_README.md                   # Build instructions
├──  PORTABILITY.md                    # Portability guide (NEW!)
├──  bin/campusfix_dispatcher          # Compiled executable (483 KB)
└──  build/                            # Object files
```

##  Quick Start Guide

###  Default Mode (Localhost Database)
```bash
./bin/campusfix_dispatcher
```
Connects to: `localhost:33060`, database `campusfix_db`, user `root`

###  Demo Mode (No Database)
```bash
./bin/campusfix_dispatcher --demo-only
```
No database needed, uses 6 sample tickets

###  Custom Database
```bash
./bin/campusfix_dispatcher --database my_database_name
```

###  Remote Server
```bash
./bin/campusfix_dispatcher \
  --host prod-db.example.com \
  --user admin \
  --password secret \
  --database production_db
```

###  Configuration File
```bash
./bin/campusfix_dispatcher --config database.conf
```

###  Quiet Mode (Batch Processing)
```bash
./bin/campusfix_dispatcher --demo-only --quiet
```
Output only: Priority table, no verbose output

##  What It Does

**Input:**
- Reads tickets from database (or uses demo data)
- Gets building criticality scores
- Identifies ticket categories

**Processing:**
```
Priority Score = Severity (1-10) × Building Criticality (1-10)
Range: 1-100
```

**Output:**
- Each ticket: Priority score + assigned trade
- Summary: Table sorted by priority (highest first)
- Database: Updates tickets with priority_score and status

##  Sharing With Others

**What to Share:**
- All `.cpp`, `.h` files
- `Makefile`, `database.conf`
- Documentation files

**What They Do:**
```bash
make
./bin/campusfix_dispatcher --config their_config.conf
```

**They Don't Need:**
- The compiled binary (different for each OS/architecture)
- MySQL installed on same machine (can use remote database)

##  Example Use Cases

### Development Team
```bash
./bin/campusfix_dispatcher --database dev_db --verbose
```

### Production
```bash
./bin/campusfix_dispatcher \
  --config /etc/campusfix/prod.conf \
  --quiet
```

### Batch Cron Job
```bash
0 * * * * /path/to/campusfix_dispatcher --config /etc/campusfix/prod.conf --quiet
```

### Testing
```bash
./bin/campusfix_dispatcher --demo-only --quiet
```

##  Priority Examples

| Severity | Building | Building Criticality | Priority Score | Assignment |
|----------|----------|----------------------|-----------------|------------|
| 10 (Fire Alarm) | Server Room | 10 | **100** | Electrician  |
| 9 (Network Down) | Server Room | 10 | **90** | SysAdmin  |
| 8 (Water Leak) | Server Room | 10 | **80** | Plumber  |
| 5 (AC Problem) | Student Center | 5 | **25** | HVAC Tech  |
| 2 (Broken Chair) | Library | 4 | **8** | Maintenance  |

##  Command Reference

```bash
# Help
./bin/campusfix_dispatcher --help

# Configuration options
--host HOST              # Database host
--port PORT              # Database port
--user USER              # Database user
--password PASS          # Database password
--database DB            # Database name
--config FILE            # Load config file
--demo-only              # No database mode
--verbose                # Show all details
--quiet                  # Minimal output

# Examples
./bin/campusfix_dispatcher                    # Default
./bin/campusfix_dispatcher --demo-only        # Demo mode
./bin/campusfix_dispatcher --help             # Show help
./bin/campusfix_dispatcher --database mydb    # Custom DB
./bin/campusfix_dispatcher --config prod.conf --quiet
```

## ✨ Key Improvements (v2.1)

| Feature | v2.0 | v2.1 |
|---------|------|------|
| Priority Calculation |  |  |
| Database Integration |  |  |
| Command-line Config |  |  NEW |
| Config File Support |  |  NEW |
| Demo Mode |  Fallback Only |  Native |
| Custom Database Names |  Hardcoded |  Dynamic |
| Portability |  Limited |  Full |
| Error Handling |  Crashes |  Graceful |

## 🛠️ Build Information

- **Compiler:** clang++ (C++17)
- **Libraries:** MySQL Connector C++, libmysqlclient
- **Build Time:** ~2 seconds
- **Executable Size:** 483 KB
- **Dependencies:** MySQL/libmysqlcppconnx only at runtime

## 📚 Documentation

- **BUILD_README.md** - Build instructions and troubleshooting
- **PORTABILITY.md** - Detailed portability and configuration guide
- **This file** - Overview and quick reference

## 🔐 Security Best Practices

1. Don't pass passwords on command line in shared environments
2. Use config files with restricted permissions (chmod 600)
3. Use environment variables for sensitive data
4. Change default credentials if using in production

## 📝 Notes

-  Fully backward compatible with v2.0
-  No database changes needed
-  Works on macOS, Linux, Windows (with proper build)
-  Can run on different machines with different configs
-  Production-ready with proper error handling

---

**Version:** 2.1  
**Release Date:** January 28, 2026  
**Status:**  **PRODUCTION READY**

**Made by Mohamad Al Kary**
