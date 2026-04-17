# Makefile for CampusFix Priority Dispatch System
# Compiler and flags
CXX = clang++
CXXFLAGS = -std=c++17 -Wall -Wextra -g
MYSQLX_FLAGS = -I/opt/homebrew/include -I/opt/homebrew/Cellar/mysql-connector-c++/9.6.0/include
MYSQLX_LIBS = -L/opt/homebrew/lib -L/opt/homebrew/Cellar/mysql-connector-c++/9.6.0/lib -lmysqlcppconnx -lmysqlclient

# Output directory
BUILD_DIR = build
BIN_DIR = bin

# Source files
SOURCES = main.cpp utility.cpp ticket_priority_engine.cpp
HEADERS = utility.h models.h ticket_priority_engine.h
OBJECTS = $(addprefix $(BUILD_DIR)/, $(SOURCES:.cpp=.o))

# Target executable
TARGET = $(BIN_DIR)/campusfix_dispatcher

# Default target
all: directories $(TARGET)

# Create necessary directories
directories:
	@mkdir -p $(BUILD_DIR)
	@mkdir -p $(BIN_DIR)

# Link object files to create executable
$(TARGET): $(OBJECTS)
	$(CXX) $(CXXFLAGS) $(MYSQLX_FLAGS) -o $@ $^ $(MYSQLX_LIBS)
	@echo "✓ Build successful! Executable: $@"

# Compile source files to object files
$(BUILD_DIR)/%.o: %.cpp $(HEADERS)
	$(CXX) $(CXXFLAGS) $(MYSQLX_FLAGS) -c $< -o $@
	@echo "Compiled: $<"

# Clean build artifacts
clean:
	rm -rf $(BUILD_DIR) $(BIN_DIR)
	@echo "✓ Clean complete"

# Run the executable
run: $(TARGET)
	./$(TARGET)

# Phony targets
.PHONY: all clean run directories
