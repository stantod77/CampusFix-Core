/*______________________________________________________________________________
____    Written by: Mohamad Al Kary                                         ____
____    Date Written: 01/28/2026                                            ____
____    Title: Configuration System                                         ____
______________________________________________________________________________*/
#pragma once

#include <string>
#include <map>
#include <fstream>
#include <sstream>
#include <iostream>

using namespace std;

class Config {
private:
    map<string, string> settings;
    
public:
    // Default constructor with hardcoded defaults
    Config() {
        // Set reasonable defaults
        settings["host"] = "localhost";
        settings["port"] = "33060";
        settings["user"] = "root";
        settings["password"] = "";
        settings["database"] = "campusfix_db";
        settings["demo_mode"] = "false";
        settings["verbose"] = "true";
    }
    
    // Load configuration from file
    bool loadFromFile(const string& filename) {
        ifstream file(filename);
        if (!file.is_open()) {
            cerr << "Warning: Could not open config file: " << filename << endl;
            cerr << "Using defaults or command-line arguments." << endl;
            return false;
        }
        
        string line;
        int line_num = 0;
        
        while (getline(file, line)) {
            line_num++;
            
            // Skip empty lines and comments
            if (line.empty() || line[0] == '#') {
                continue;
            }
            
            // Parse key=value pairs
            size_t delimiter = line.find('=');
            if (delimiter == string::npos) {
                cerr << "Warning: Invalid config line " << line_num << ": " << line << endl;
                continue;
            }
            
            string key = line.substr(0, delimiter);
            string value = line.substr(delimiter + 1);
            
            // Trim whitespace
            key = trim(key);
            value = trim(value);
            
            if (!key.empty()) {
                settings[key] = value;
            }
        }
        
        file.close();
        cout << "✓ Configuration loaded from: " << filename << endl;
        return true;
    }
    
    // Set a configuration value
    void set(const string& key, const string& value) {
        settings[key] = value;
    }
    
    // Get a configuration value
    string get(const string& key, const string& default_value = "") const {
        auto it = settings.find(key);
        if (it != settings.end()) {
            return it->second;
        }
        return default_value;
    }
    
    // Get as integer
    int getInt(const string& key, int default_value = 0) const {
        string value = get(key);
        if (value.empty()) {
            return default_value;
        }
        try {
            return stoi(value);
        } catch (...) {
            cerr << "Warning: Invalid integer value for " << key << ": " << value << endl;
            return default_value;
        }
    }
    
    // Get as boolean
    bool getBool(const string& key, bool default_value = false) const {
        string value = get(key);
        if (value.empty()) {
            return default_value;
        }
        return (value == "true" || value == "1" || value == "yes");
    }
    
    // Print all settings
    void printSettings() const {
        cout << "\n--- Configuration Settings ---" << endl;
        for (const auto& pair : settings) {
            string display_value = pair.second;
            // Mask password
            if (pair.first == "password" && !display_value.empty()) {
                display_value = "****";
            }
            cout << "  " << pair.first << " = " << display_value << endl;
        }
        cout << "------------------------------\n" << endl;
    }
    
private:
    // Trim whitespace from string
    static string trim(const string& str) {
        size_t first = str.find_first_not_of(" \t\n\r");
        if (string::npos == first) {
            return str;
        }
        size_t last = str.find_last_not_of(" \t\n\r");
        return str.substr(first, (last - first + 1));
    }
};

// Helper to print usage information
inline void printUsage(const char* program_name) {
    cout << "\nUsage: " << program_name << " [OPTIONS]\n" << endl;
    cout << "Options:" << endl;
    cout << "  --host HOST              Database host (default: localhost)" << endl;
    cout << "  --port PORT              Database port (default: 33060)" << endl;
    cout << "  --user USER              Database user (default: root)" << endl;
    cout << "  --password PASS          Database password (default: blank)" << endl;
    cout << "  --database DB            Database name (default: campusfix_db)" << endl;
    cout << "  --config FILE            Load configuration from file" << endl;
    cout << "  --demo-only              Run in demo mode (no database)" << endl;
    cout << "  --verbose                Enable verbose output (default)" << endl;
    cout << "  --quiet                  Disable verbose output" << endl;
    cout << "  --help                   Show this help message" << endl;
    cout << "\nExamples:" << endl;
    cout << "  " << program_name << " --host db.example.com --database mydb" << endl;
    cout << "  " << program_name << " --config /etc/campusfix/database.conf" << endl;
    cout << "  " << program_name << " --demo-only" << endl;
    cout << endl;
}
