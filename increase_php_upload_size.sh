#!/bin/bash

# Script to increase PHP upload size across different OS and PHP installations
# Supports Linux, Windows (XAMPP and native PHP)

set -e

# Default values
DEFAULT_UPLOAD_SIZE="512M"
DEFAULT_POST_SIZE="512M"

# Function to print usage
print_usage() {
    echo "Usage: $0 [OPTIONS]"
    echo "Options:"
    echo "  -s, --size SIZE     Set upload size (default: 512M)"
    echo "  -p, --post-size SIZE  Set post max size (default: 512M)"
    echo "  -h, --help         Show this help message"
    echo ""
    echo "Examples:"
    echo " $0                    # Use default sizes (512M)"
    echo "  $0 -s 1G -p 1G      # Set 1GB upload and post size"
    echo "  $0 --size 256M      # Set 256M upload size"
}

# Function to detect OS
detect_os() {
    if [[ "$OSTYPE" == "linux-gnu"* ]]; then
        echo "linux"
    elif [[ "$OSTYPE" == "msys" ]] || [[ "$OSTYPE" == "cygwin" ]]; then
        echo "windows"
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        echo "macos"
    else
        echo "unknown"
    fi
}

# Function to find PHP configuration files on Linux/Mac
find_linux_php_configs() {
    local configs=()

    # Common locations for PHP configuration files
    local locations=(
        "/etc/php/*/apache2/php.ini"
        "/etc/php/*/cli/php.ini"
        "/etc/php/*/fpm/php.ini"
        "/etc/php.ini"
        "/usr/local/etc/php/php.ini"
        "$(php -r 'echo php_ini_loaded_file();')"
    )

    for location in "${locations[@]}"; do
        if [[ "$location" == *"php -r"* ]]; then
            local loaded_file=$(php -r 'echo php_ini_loaded_file();')
            if [[ -f "$loaded_file" ]]; then
                configs+=("$loaded_file")
            fi
        else
            for file in $location; do
                if [[ -f "$file" ]]; then
                    configs+=("$file")
                fi
            done
        fi
    done

    # Remove duplicates
    printf '%s\n' "${configs[@]}" | sort -u
}

# Function to find XAMPP PHP configuration on Windows (using WSL or native)
find_xampp_configs() {
    local configs=()
    local xampp_paths=(
        "/mnt/c/xampp/php/php.ini"
        "C:/xampp/php/php.ini"
        "/c/xampp/php/php.ini"
    )

    for path in "${xampp_paths[@]}"; do
        if [[ -f "$path" ]]; then
            configs+=("$path")
        fi
    done

    printf '%s\n' "${configs[@]}"
}

# Function to find native PHP configuration on Windows
find_native_php_configs() {
    local configs=()
    local php_paths=(
        "/mnt/c/php/php.ini"
        "C:/php/php.ini"
        "/c/php/php.ini"
        "/mnt/c/PHP/php.ini"
        "C:/PHP/php.ini"
    )

    for path in "${php_paths[@]}"; do
        if [[ -f "$path" ]]; then
            configs+=("$path")
        fi
    done

    printf '%s\n' "${configs[@]}"
}

# Function to update PHP configuration file
update_php_config() {
    local config_file="$1"
    local upload_size="$2"
    local post_size="$3"

    echo "Updating PHP configuration: $config_file"

    # Backup the original file
    cp "$config_file" "${config_file}.backup.$(date +%Y%m%d_%H%M%S)"
    echo "  Backup created: ${config_file}.backup.$(date +%Y%m%d_%H%M%S)"

    # Update upload_max_filesize
    if grep -q "^upload_max_filesize" "$config_file"; then
        sed -i.bak "s/^upload_max_filesize.*/upload_max_filesize = $upload_size/" "$config_file" && rm "$config_file.bak"
        echo "  Updated upload_max_filesize to $upload_size"
    else
        echo "upload_max_filesize = $upload_size" >> "$config_file"
        echo "  Added upload_max_filesize = $upload_size"
    fi

    # Update post_max_size
    if grep -q "^post_max_size" "$config_file"; then
        sed -i.bak "s/^post_max_size.*/post_max_size = $post_size/" "$config_file" && rm "$config_file.bak"
        echo "  Updated post_max_size to $post_size"
    else
        echo "post_max_size = $post_size" >> "$config_file"
        echo "  Added post_max_size = $post_size"
    fi

    # Update memory_limit if it's smaller than upload size
    local current_memory=$(grep -E "^memory_limit" "$config_file" | cut -d'=' -f2 | tr -d ' ')
    if [[ -n "$current_memory" ]] && [[ "$current_memory" != *"M"* ]] && [[ "$current_memory" != *"G"* ]]; then
        current_memory="${current_memory}M"
    fi

    # Compare sizes (simplified comparison)
    if [[ "$current_memory" == *"M"* ]] && [[ "$upload_size" == *"M"* ]]; then
        local current_num=$(echo "$current_memory" | sed 's/M//')
        local upload_num=$(echo "$upload_size" | sed 's/M//')
        if [[ "$current_num" -lt "$upload_num" ]]; then
            local new_memory=$((upload_num + 128))
            sed -i.bak "s/^memory_limit.*/memory_limit = ${new_memory}M/" "$config_file" && rm "$config_file.bak"
            echo "  Updated memory_limit to ${new_memory}M"
        fi
    elif [[ "$current_memory" == *"G"* ]] && [[ "$upload_size" == *"M"* ]]; then
        local current_num=$(echo "$current_memory" | sed 's/G//')
        local upload_num=$(echo "$upload_size" | sed 's/M//')
        if [[ $((current_num * 1024)) -lt "$upload_num" ]]; then
            local new_memory=$((upload_num / 1024 + 1))
            sed -i.bak "s/^memory_limit.*/memory_limit = ${new_memory}G/" "$config_file" && rm "$config_file.bak"
            echo "  Updated memory_limit to ${new_memory}G"
        fi
    elif [[ "$current_memory" == *"M"* ]] && [[ "$upload_size" == *"G"* ]]; then
        local current_num=$(echo "$current_memory" | sed 's/M//')
        local upload_num=$(echo "$upload_size" | sed 's/G//')
        local upload_mb=$((upload_num * 1024))
        if [[ "$current_num" -lt "$upload_mb" ]]; then
            sed -i.bak "s/^memory_limit.*/memory_limit = $upload_size/" "$config_file" && rm "$config_file.bak"
            echo "  Updated memory_limit to $upload_size"
        fi
    fi
}

# Function to restart services after configuration change
restart_services() {
    local os_type="$1"

    echo "Restarting services..."

    case "$os_type" in
        "linux")
            # Try common service restart commands
            if command -v systemctl &> /dev/null; then
                for service in apache2 httpd nginx php*-fpm; do
                    if systemctl is-active --quiet "$service" 2>/dev/null; then
                        echo "Restarting $service..."
                        sudo systemctl restart "$service" || echo "Failed to restart $service"
                    fi
                done
            fi
            ;;
        "macos")
            # Try common macOS service restarts
            if command -v brew &> /dev/null; then
                if brew services list | grep -q "httpd.*started"; then
                    brew services restart httpd
                fi
                if brew services list | grep -q "nginx.*started"; then
                    brew services restart nginx
                fi
            fi
            ;;
        "windows")
            # On Windows, services need to be restarted manually or through XAMPP Control Panel
            echo "Please restart Apache/Nginx and PHP services manually or through XAMPP Control Panel"
            ;;
    esac
}

# Function to validate size format
validate_size() {
    local size="$1"
    if [[ ! "$size" =~ ^[0-9]+[MG]$ ]]; then
        echo "Error: Invalid size format '$size'. Use format like 128M, 1G"
        exit 1
    fi
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -s|--size)
            UPLOAD_SIZE="$2"
            validate_size "$UPLOAD_SIZE"
            shift 2
            ;;
        -p|--post-size)
            POST_SIZE="$2"
            validate_size "$POST_SIZE"
            shift 2
            ;;
        -h|--help)
            print_usage
            exit 0
            ;;
        *)
            echo "Unknown option: $1"
            print_usage
            exit 1
            ;;
    esac
done

# Set default values if not provided
UPLOAD_SIZE="${UPLOAD_SIZE:-$DEFAULT_UPLOAD_SIZE}"
POST_SIZE="${POST_SIZE:-$DEFAULT_POST_SIZE}"

# Validate sizes
validate_size "$UPLOAD_SIZE"
validate_size "$POST_SIZE"

echo "Increasing PHP upload size..."
echo "Upload size: $UPLOAD_SIZE"
echo "Post size: $POST_SIZE"

OS_TYPE=$(detect_os)
echo "Detected OS: $OS_TYPE"

case "$OS_TYPE" in
    "linux"|"macos")
        # Find and update Linux/Mac PHP configurations
        configs=$(find_linux_php_configs)
        if [[ -n "$configs" ]]; then
            while IFS= read -r config; do
                if [[ -n "$config" ]]; then
                    update_php_config "$config" "$UPLOAD_SIZE" "$POST_SIZE"
                fi
            done <<< "$configs"
            restart_services "$OS_TYPE"
        else
            echo "Warning: Could not find PHP configuration files"
            echo "Please locate your php.ini file manually and update the settings:"
            echo "  upload_max_filesize = $UPLOAD_SIZE"
            echo "  post_max_size = $POST_SIZE"
        fi
        ;;
    "windows")
        # Check for XAMPP first
        xampp_configs=$(find_xampp_configs)
        if [[ -n "$xampp_configs" ]]; then
            echo "Found XAMPP installation"
            while IFS= read -r config; do
                if [[ -n "$config" ]]; then
                    update_php_config "$config" "$UPLOAD_SIZE" "$POST_SIZE"
                fi
            done <<< "$xampp_configs"
        else
            echo "XAMPP not found"
        fi

        # Check for native PHP
        native_configs=$(find_native_php_configs)
        if [[ -n "$native_configs" ]]; then
            echo "Found native PHP installation"
            while IFS= read -r config; do
                if [[ -n "$config" ]]; then
                    update_php_config "$config" "$UPLOAD_SIZE" "$POST_SIZE"
                fi
            done <<< "$native_configs"
        else
            echo "Native PHP installation not found"
        fi

        # For Windows, also check if running in WSL
        if grep -q Microsoft /proc/version 2>/dev/null; then
            echo "Running in WSL, you may need to restart Windows services manually"
        fi
        ;;
    *)
        echo "Unsupported OS: $OS_TYPE"
        exit 1
        ;;
esac

echo ""
echo "PHP upload size configuration updated successfully!"
echo "Upload size: $UPLOAD_SIZE"
echo "Post size: $POST_SIZE"
echo ""
echo "Please restart your web server (Apache/Nginx) and PHP services for changes to take effect."

# Verify the changes
echo ""
echo "Verifying changes..."
php -i | grep -E "(upload_max_filesize|post_max_size|memory_limit)"
