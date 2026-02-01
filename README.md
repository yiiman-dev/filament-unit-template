# PHP Upload Size Configuration Scripts

This directory contains scripts to automatically increase PHP upload size limits across different operating systems and PHP installations.

## Scripts

### Linux/macOS Shell Script
- **File**: `increase_php_upload_size.sh`
- **Supports**: Linux, macOS, and Windows WSL
- **PHP Installations**: System PHP, Docker containers, various PHP versions

### Windows Batch Script  
- **File**: `increase_php_upload_size.bat`
- **Supports**: Windows operating system
- **PHP Installations**: XAMPP, native PHP installations

## Features

- Automatically detects and updates PHP configuration files
- Creates backup files before making changes
- Updates multiple related settings (upload_max_filesize, post_max_size, memory_limit)
- Cross-platform compatibility
- Command-line argument support
- Automatic service restart detection

## Usage

### Linux/macOS:
```bash
# Make script executable
chmod +x increase_php_upload_size.sh

# Run with default settings (512M)
./increase_php_upload_size.sh

# Run with custom size
./increase_php_upload_size.sh -s 1G -p 1G

# Show help
./increase_php_upload_size.sh --help
```

### Windows:
```cmd
# Run with default settings (512M)
increase_php_upload_size.bat

# Run with custom size
increase_php_upload_size.bat -s 1G -p 1G

# Show help
increase_php_upload_size.bat --help
```

## Command Line Options

- `-s, --size SIZE`: Set upload file size limit (default: 512M)
- `-p, --post-size SIZE`: Set POST data size limit (default: 512M)  
- `-h, --help`: Show help message

## Size Format

- Use format like `128M` for 128 megabytes
- Use format like `1G` for 1 gigabyte
- Supported units: M (megabytes), G (gigabytes)

## Supported PHP Installations

### Linux/macOS:
- System PHP (in `/etc/php/`, `/usr/local/etc/php/`)
- Apache, Nginx, PHP-FPM configurations
- Docker containers (Laravel Sail)
- Any PHP installation with accessible php.ini

### Windows:
- XAMPP (in `C:\xampp\php\php.ini`)
- Native PHP (in `C:\php\php.ini`, `C:\Program Files\PHP\php.ini`)
- PHP installed via PATH

## What Gets Updated

The scripts update the following PHP configuration directives:
- `upload_max_filesize`: Maximum file upload size
- `post_max_size`: Maximum POST data size
- `memory_limit`: PHP memory limit (if smaller than upload size)

## Important Notes

1. **Administrator/Root Rights**: You may need elevated privileges to modify system PHP configuration files
2. **Service Restart Required**: Web servers (Apache, Nginx) and PHP services need to be restarted for changes to take effect
3. **Backup Created**: Each script creates a backup of the original php.ini file before making changes
4. **Verification**: The script verifies changes by displaying current PHP configuration values

## Configuration File Locations

### Linux:
- `/etc/php/*/apache2/php.ini`
- `/etc/php/*/cli/php.ini` 
- `/etc/php/*/fpm/php.ini`
- `/usr/local/etc/php/php.ini`

### Windows:
- `C:\xampp\php\php.ini`
- `C:\php\php.ini`
- `C:\Program Files\PHP\php.ini`

## Troubleshooting

If the script cannot find your PHP configuration file:
1. Locate your php.ini file manually using `php -i | grep "Loaded Configuration File"`
2. Edit the file directly with the desired values
3. Restart your web server and PHP services

## Security Considerations

- Be cautious when increasing upload limits as it may impact server performance
- Consider the security implications of allowing large file uploads
- Monitor server resource usage after making changes
