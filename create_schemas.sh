#!/bin/bash

# Script to create PostgreSQL schemas for filament project

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}===== PostgreSQL Schema Setup for Filament Project =====${NC}"

# Find .env file
ENV_FILE=".env"
if [ ! -f "$ENV_FILE" ]; then
    # Try to find .env in parent directories
    for i in {1..5}; do
        ENV_FILE="../$ENV_FILE"
        if [ -f "$ENV_FILE" ]; then
            break
        fi
    done
fi

# Load environment variables from .env file
if [ -f "$ENV_FILE" ]; then
    echo -e "${GREEN}Loading environment variables from $ENV_FILE${NC}"
    # Use grep and sed to extract environment variables without executing the file
    # (safer than sourcing the file)
    export $(grep -v '^#' "$ENV_FILE" | grep -v "^$" | xargs)
else
    echo -e "${YELLOW}Warning: .env file not found. Using default values.${NC}"
fi

# Detect OS
if [[ "$OSTYPE" == "linux-gnu"* ]]; then
    OS="linux"
elif [[ "$OSTYPE" == "darwin"* ]]; then
    OS="macos"
elif [[ "$OSTYPE" == "cygwin" || "$OSTYPE" == "msys" || "$OSTYPE" == "win32" ]]; then
    OS="windows"
else
    OS="unknown"
fi

echo -e "${YELLOW}Detected OS: $OS${NC}"

# Check if Docker is running
is_docker_running() {
    if command -v docker &> /dev/null; then
        if docker info &> /dev/null; then
            return 0
        fi
    fi
    return 1
}

# Check and install dependencies based on OS
install_dependencies() {
    # Check if using Docker for PostgreSQL
    if [[ "${USE_DOCKER:-no}" == "yes" ]]; then
        echo -e "${YELLOW}Docker mode enabled for PostgreSQL${NC}"

        if ! is_docker_running; then
            echo -e "${RED}Docker is not running. Please start Docker and try again.${NC}"
            exit 1
        fi

        echo -e "${YELLOW}Checking if PostgreSQL container is running...${NC}"
        if ! docker ps | grep -q postgres; then
            echo -e "${YELLOW}PostgreSQL container not found. Consider starting one with:${NC}"
            echo -e "${BLUE}docker run --name postgres -e POSTGRES_PASSWORD=password -p 5432:5432 -d postgres${NC}"
            echo -e "${YELLOW}Then run this script again.${NC}"

            echo -e "${YELLOW}Do you want to start a PostgreSQL container now? (y/n)${NC}"
            read -r answer
            if [[ "$answer" == "y" || "$answer" == "Y" ]]; then
                echo -e "${YELLOW}Starting PostgreSQL container...${NC}"
                docker run --name postgres -e POSTGRES_PASSWORD="${DB_PASSWORD}" -p "${DB_PORT}":5432 -d postgres
                sleep 5  # Give the container time to start
            else
                echo -e "${YELLOW}Continuing without starting a container...${NC}"
            fi
        else
            echo -e "${GREEN}PostgreSQL container is running.${NC}"
        fi
    fi

    # Install psql client tool
    if [[ "$OS" == "linux" ]]; then
        echo -e "${YELLOW}Checking for PostgreSQL client on Linux...${NC}"
        if ! command -v psql &> /dev/null; then
            echo -e "${YELLOW}PostgreSQL client not found. Installing...${NC}"
            sudo apt-get update
            sudo apt-get install -y postgresql-client
            echo -e "${GREEN}PostgreSQL client installed successfully!${NC}"
        else
            echo -e "${GREEN}PostgreSQL client already installed.${NC}"
        fi
    elif [[ "$OS" == "macos" ]]; then
        echo -e "${YELLOW}Checking for PostgreSQL client on macOS...${NC}"
        if ! command -v psql &> /dev/null; then
            echo -e "${YELLOW}PostgreSQL client not found. Installing via Homebrew...${NC}"
            if ! command -v brew &> /dev/null; then
                echo -e "${YELLOW}Homebrew not found. Installing Homebrew first...${NC}"
                /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
            fi
            brew install libpq
            brew link --force libpq
            echo -e "${GREEN}PostgreSQL client installed successfully!${NC}"
        else
            echo -e "${GREEN}PostgreSQL client already installed.${NC}"
        fi
    elif [[ "$OS" == "windows" ]]; then
        echo -e "${YELLOW}On Windows, please manually install PostgreSQL from:${NC}"
        echo -e "${BLUE}https://www.postgresql.org/download/windows/${NC}"
        echo -e "${YELLOW}After installation, ensure 'psql' is in your PATH or run this script in Git Bash/WSL.${NC}"

        if ! command -v psql &> /dev/null; then
            echo -e "${RED}PostgreSQL client not found in PATH. Please install it and add to PATH.${NC}"
            exit 1
        else
            echo -e "${GREEN}PostgreSQL client found in PATH.${NC}"
        fi
    else
        echo -e "${RED}Unsupported OS. Please install PostgreSQL client manually.${NC}"
        exit 1
    fi
}

# Extract database connection settings from environment variables
extract_db_settings() {
    # Production DB Settings
    # Try to get variables from specific DB connection variables first, then fall back to common settings
    # For LARAVEL_DB
    LARAVEL_DB_HOST="${LARAVEL_DB_HOST:-${DB_HOST:-localhost}}"
    LARAVEL_DB_PORT="${LARAVEL_DB_PORT:-${DB_PORT:-5432}}"
    LARAVEL_DB_USERNAME="${LARAVEL_DB_USERNAME:-${DB_USERNAME:-postgres}}"
    LARAVEL_DB_PASSWORD="${LARAVEL_DB_PASSWORD:-${DB_PASSWORD:-password}}"
    LARAVEL_DB_DATABASE="${LARAVEL_DB_DATABASE:-${DB_DATABASE:-scf}}"

    # For MY_DB
    MY_DB_HOST="${MY_DB_HOST:-$LARAVEL_DB_HOST}"
    MY_DB_PORT="${MY_DB_PORT:-$LARAVEL_DB_PORT}"
    MY_DB_USERNAME="${MY_DB_USERNAME:-$LARAVEL_DB_USERNAME}"
    MY_DB_PASSWORD="${MY_DB_PASSWORD:-$LARAVEL_DB_PASSWORD}"
    MY_DB_DATABASE="${MY_DB_DATABASE:-$LARAVEL_DB_DATABASE}"

    # For ADMIN_DB
    ADMIN_DB_HOST="${ADMIN_DB_HOST:-$LARAVEL_DB_HOST}"
    ADMIN_DB_PORT="${ADMIN_DB_PORT:-$LARAVEL_DB_PORT}"
    ADMIN_DB_USERNAME="${ADMIN_DB_USERNAME:-$LARAVEL_DB_USERNAME}"
    ADMIN_DB_PASSWORD="${ADMIN_DB_PASSWORD:-$LARAVEL_DB_PASSWORD}"
    ADMIN_DB_DATABASE="${ADMIN_DB_DATABASE:-$LARAVEL_DB_DATABASE}"

    # For MANAGE_DB
    MANAGE_DB_HOST="${MANAGE_DB_HOST:-$LARAVEL_DB_HOST}"
    MANAGE_DB_PORT="${MANAGE_DB_PORT:-$LARAVEL_DB_PORT}"
    MANAGE_DB_USERNAME="${MANAGE_DB_USERNAME:-$LARAVEL_DB_USERNAME}"
    MANAGE_DB_PASSWORD="${MANAGE_DB_PASSWORD:-$LARAVEL_DB_PASSWORD}"
    MANAGE_DB_DATABASE="${MANAGE_DB_DATABASE:-$LARAVEL_DB_DATABASE}"

    # Test DB Settings - If not defined, create them based on production settings
    # For TEST_LARAVEL_DB
    TEST_LARAVEL_DB_HOST="${TEST_LARAVEL_DB_HOST:-$LARAVEL_DB_HOST}"
    TEST_LARAVEL_DB_PORT="${TEST_LARAVEL_DB_PORT:-$LARAVEL_DB_PORT}"
    TEST_LARAVEL_DB_USERNAME="${TEST_LARAVEL_DB_USERNAME:-$LARAVEL_DB_USERNAME}"
    TEST_LARAVEL_DB_PASSWORD="${TEST_LARAVEL_DB_PASSWORD:-$LARAVEL_DB_PASSWORD}"
    TEST_LARAVEL_DB_DATABASE="${TEST_LARAVEL_DB_DATABASE:-scf_test}"

    # For TEST_MY_DB
    TEST_MY_DB_HOST="${TEST_MY_DB_HOST:-$MY_DB_HOST}"
    TEST_MY_DB_PORT="${TEST_MY_DB_PORT:-$MY_DB_PORT}"
    TEST_MY_DB_USERNAME="${TEST_MY_DB_USERNAME:-$MY_DB_USERNAME}"
    TEST_MY_DB_PASSWORD="${TEST_MY_DB_PASSWORD:-$MY_DB_PASSWORD}"
    TEST_MY_DB_DATABASE="${TEST_MY_DB_DATABASE:-$TEST_LARAVEL_DB_DATABASE}"

    # For TEST_ADMIN_DB
    TEST_ADMIN_DB_HOST="${TEST_ADMIN_DB_HOST:-$ADMIN_DB_HOST}"
    TEST_ADMIN_DB_PORT="${TEST_ADMIN_DB_PORT:-$ADMIN_DB_PORT}"
    TEST_ADMIN_DB_USERNAME="${TEST_ADMIN_DB_USERNAME:-$ADMIN_DB_USERNAME}"
    TEST_ADMIN_DB_PASSWORD="${TEST_ADMIN_DB_PASSWORD:-$ADMIN_DB_PASSWORD}"
    TEST_ADMIN_DB_DATABASE="${TEST_ADMIN_DB_DATABASE:-$TEST_LARAVEL_DB_DATABASE}"

    # For TEST_MANAGE_DB
    TEST_MANAGE_DB_HOST="${TEST_MANAGE_DB_HOST:-$MANAGE_DB_HOST}"
    TEST_MANAGE_DB_PORT="${TEST_MANAGE_DB_PORT:-$MANAGE_DB_PORT}"
    TEST_MANAGE_DB_USERNAME="${TEST_MANAGE_DB_USERNAME:-$MANAGE_DB_USERNAME}"
    TEST_MANAGE_DB_PASSWORD="${TEST_MANAGE_DB_PASSWORD:-$MANAGE_DB_PASSWORD}"
    TEST_MANAGE_DB_DATABASE="${TEST_MANAGE_DB_DATABASE:-$TEST_LARAVEL_DB_DATABASE}"

    # Set schemas from env variables
    LARAVEL_DB_SCHEMA="${LARAVEL_DB_SCHEMA:-laravel}"
    MY_DB_SCHEMA="${MY_DB_SCHEMA:-my}"
    ADMIN_DB_SCHEMA="${ADMIN_DB_SCHEMA:-admin}"
    MANAGE_DB_SCHEMA="${MANAGE_DB_SCHEMA:-manage}"

    TEST_LARAVEL_DB_SCHEMA="${TEST_LARAVEL_DB_SCHEMA:-$LARAVEL_DB_SCHEMA}"
    TEST_MY_DB_SCHEMA="${TEST_MY_DB_SCHEMA:-$MY_DB_SCHEMA}"
    TEST_ADMIN_DB_SCHEMA="${TEST_ADMIN_DB_SCHEMA:-$ADMIN_DB_SCHEMA}"
    TEST_MANAGE_DB_SCHEMA="${TEST_MANAGE_DB_SCHEMA:-$MANAGE_DB_SCHEMA}"

    # Set primary database for schema creation
    # We'll use the LARAVEL_DB as our main connection for creating schemas
    DB_HOST="$LARAVEL_DB_HOST"
    DB_PORT="$LARAVEL_DB_PORT"
    DB_NAME="$LARAVEL_DB_DATABASE"
    DB_USER="$LARAVEL_DB_USERNAME"
    DB_PASSWORD="$LARAVEL_DB_PASSWORD"
    DB_TEST_NAME="$TEST_LARAVEL_DB_DATABASE"

    # Set schemas to create
    schemas=("public" "$LARAVEL_DB_SCHEMA" "$MY_DB_SCHEMA" "$ADMIN_DB_SCHEMA" "$MANAGE_DB_SCHEMA")
    test_schemas=("public" "$TEST_LARAVEL_DB_SCHEMA" "$TEST_MY_DB_SCHEMA" "$TEST_ADMIN_DB_SCHEMA" "$TEST_MANAGE_DB_SCHEMA")

    # Print connection settings
    echo -e "${YELLOW}========= Database Settings =========${NC}"
    echo -e "${YELLOW}DB Host:     ${NC}$DB_HOST"
    echo -e "${YELLOW}DB Port:     ${NC}$DB_PORT"
    echo -e "${YELLOW}DB Name:     ${NC}$DB_NAME"
    echo -e "${YELLOW}DB User:     ${NC}$DB_USER"
    echo -e "${YELLOW}DB Test Name:${NC}$DB_TEST_NAME"
    echo -e "${YELLOW}Schemas:     ${NC}${schemas[*]}"
    echo -e "${YELLOW}Test Schemas:${NC}${test_schemas[*]}"
    echo -e "${YELLOW}===================================${NC}"
}

# Check PostgreSQL connection
check_postgres_connection() {
    echo -e "${YELLOW}Checking PostgreSQL connection...${NC}"
    if PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -c "SELECT 1" postgres &> /dev/null; then
        echo -e "${GREEN}PostgreSQL connection successful!${NC}"
        return 0
    else
        echo -e "${RED}Cannot connect to PostgreSQL. Please check your credentials and ensure the server is running.${NC}"

        if [[ "${USE_DOCKER:-no}" == "yes" ]]; then
            echo -e "${YELLOW}Checking Docker PostgreSQL container...${NC}"
            if ! docker ps | grep -q postgres; then
                echo -e "${YELLOW}PostgreSQL container is not running. Starting it...${NC}"
                docker start postgres
                sleep 5  # Wait for container to start

                if PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -c "SELECT 1" postgres &> /dev/null; then
                    echo -e "${GREEN}PostgreSQL connection successful after starting container!${NC}"
                    return 0
                fi
            fi
        fi

        echo -e "${RED}Could not establish connection to PostgreSQL. Please check your installation.${NC}"
        echo -e "${YELLOW}Tips:${NC}"
        echo -e "  - Check if PostgreSQL service/container is running"
        echo -e "  - Verify username and password"
        echo -e "  - Make sure you have permission to connect"
        echo -e "  - Check host and port settings"
        return 1
    fi
}

# Use Docker for PostgreSQL by default if specified
USE_DOCKER="${USE_DOCKER:-no}"

# Extract database settings from environment variables
extract_db_settings

# Install dependencies
install_dependencies

# Check PostgreSQL connection
if ! check_postgres_connection; then
    exit 1
fi

# Function to execute SQL commands
execute_sql() {
    echo -e "${YELLOW}Executing: $2${NC}"
    if PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$1" -c "$2" &> /dev/null; then
        echo -e "${GREEN}✓ SQL executed successfully${NC}"
        return 0
    else
        echo -e "${RED}✗ Error executing SQL: $2${NC}"
        return 1
    fi
}

# Create main database if it doesn't exist
echo -e "\n${YELLOW}Creating main database if it doesn't exist...${NC}"
if PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -tc "SELECT 1 FROM pg_database WHERE datname = '$DB_NAME'" postgres | grep -q 1; then
    echo -e "${GREEN}✓ Database '$DB_NAME' already exists${NC}"
else
    if PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -c "CREATE DATABASE $DB_NAME" postgres; then
        echo -e "${GREEN}✓ Database '$DB_NAME' created successfully${NC}"
    else
        echo -e "${RED}✗ Error creating database '$DB_NAME'${NC}"
        exit 1
    fi
fi

# Create test database if it doesn't exist
echo -e "\n${YELLOW}Creating test database if it doesn't exist...${NC}"
if PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -tc "SELECT 1 FROM pg_database WHERE datname = '$DB_TEST_NAME'" postgres | grep -q 1; then
    echo -e "${GREEN}✓ Database '$DB_TEST_NAME' already exists${NC}"
else
    if PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -c "CREATE DATABASE $DB_TEST_NAME" postgres; then
        echo -e "${GREEN}✓ Database '$DB_TEST_NAME' created successfully${NC}"
    else
        echo -e "${RED}✗ Error creating database '$DB_TEST_NAME'${NC}"
        exit 1
    fi
fi

# Create schemas in main database
echo -e "\n${YELLOW}Creating schemas in main database...${NC}"

for schema in "${schemas[@]}"; do
    echo -e "${YELLOW}Creating schema: $schema${NC}"
    execute_sql "$DB_NAME" "CREATE SCHEMA IF NOT EXISTS $schema"
done

# Create schemas in test database
echo -e "\n${YELLOW}Creating schemas in test database...${NC}"

for schema in "${test_schemas[@]}"; do
    echo -e "${YELLOW}Creating schema: $schema in test database${NC}"
    execute_sql "$DB_TEST_NAME" "CREATE SCHEMA IF NOT EXISTS $schema"
done

# Grant privileges to user
echo -e "\n${YELLOW}Granting privileges...${NC}"
for db in "$DB_NAME" "$DB_TEST_NAME"; do
    echo -e "${YELLOW}Granting privileges for database: $db${NC}"

    # Grant privileges on database
    execute_sql "postgres" "GRANT ALL PRIVILEGES ON DATABASE $db TO $DB_USER"

    # Connect to the database and grant privileges on schemas
    if [ "$db" == "$DB_NAME" ]; then
        for schema in "${schemas[@]}"; do
            execute_sql "$db" "GRANT ALL ON SCHEMA $schema TO $DB_USER"
            execute_sql "$db" "ALTER DEFAULT PRIVILEGES IN SCHEMA $schema GRANT ALL ON TABLES TO $DB_USER"
            execute_sql "$db" "ALTER DEFAULT PRIVILEGES IN SCHEMA $schema GRANT ALL ON SEQUENCES TO $DB_USER"
            execute_sql "$db" "ALTER DEFAULT PRIVILEGES IN SCHEMA $schema GRANT ALL ON FUNCTIONS TO $DB_USER"
        done
    else
        for schema in "${test_schemas[@]}"; do
            execute_sql "$db" "GRANT ALL ON SCHEMA $schema TO $DB_USER"
            execute_sql "$db" "ALTER DEFAULT PRIVILEGES IN SCHEMA $schema GRANT ALL ON TABLES TO $DB_USER"
            execute_sql "$db" "ALTER DEFAULT PRIVILEGES IN SCHEMA $schema GRANT ALL ON SEQUENCES TO $DB_USER"
            execute_sql "$db" "ALTER DEFAULT PRIVILEGES IN SCHEMA $schema GRANT ALL ON FUNCTIONS TO $DB_USER"
        done
    fi
done

echo -e "\n${GREEN}===== Database setup completed successfully! =====${NC}"
echo -e "${YELLOW}Main Database: $DB_NAME${NC}"
echo -e "${YELLOW}Test Database: $DB_TEST_NAME${NC}"
echo -e "${YELLOW}Schemas created: ${schemas[*]}${NC}"

# Usage instructions
echo -e "\n${YELLOW}===== Usage =====${NC}"
echo -e "Run with default values (reads from .env):"
echo -e "  ${GREEN}./create_schemas.sh${NC}"
echo -e "\nRun with Docker PostgreSQL:"
echo -e "  ${GREEN}USE_DOCKER=yes ./create_schemas.sh${NC}"
echo -e "\nOverride specific settings:"
echo -e "  ${GREEN}LARAVEL_DB_HOST=custom-host LARAVEL_DB_PASSWORD=custom-pass ./create_schemas.sh${NC}"
