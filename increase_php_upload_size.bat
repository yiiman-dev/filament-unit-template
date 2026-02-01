@echo off
REM Script to increase PHP upload size on Windows for XAMPP and native PHP installations

setlocal enabledelayedexpansion

REM Default values
set DEFAULT_UPLOAD_SIZE=512M
set DEFAULT_POST_SIZE=512M

REM Initialize variables
set UPLOAD_SIZE=%DEFAULT_UPLOAD_SIZE%
set POST_SIZE=%DEFAULT_POST_SIZE%

REM Parse command line arguments
:parse_args
if "%~1"=="" goto check_args
if "%~1"=="-s" set UPLOAD_SIZE=%~2
if "%~1"=="--size" set UPLOAD_SIZE=%~2
if "%~1"=="-p" set POST_SIZE=%~2
if "%~1"=="--post-size" set POST_SIZE=%~2
if "%~1"=="-h" goto show_help
if "%~1"=="--help" goto show_help
shift
goto parse_args

:check_args
REM Validate size format (simple validation)
echo %UPLOAD_SIZE% | findstr /R "^[0-9]*[MG]$" >nul
if errorlevel 1 (
    echo Error: Invalid upload size format '%UPLOAD_SIZE%'. Use format like 128M, 1G
    goto show_help
)

echo %POST_SIZE% | findstr /R "^[0-9]*[MG]$" >nul
if errorlevel 1 (
    echo Error: Invalid post size format '%POST_SIZE%'. Use format like 128M, 1G
    goto show_help
)

goto main

:show_help
echo Usage: %~nx0 [OPTIONS]
echo Options:
echo   -s, --size SIZE     Set upload size (default: 512M)
echo   -p, --post-size SIZE  Set post max size (default: 512M)
echo   -h, --help         Show this help message
echo.
echo Examples:
echo   %~nx0                    Use default sizes (512M)
echo   %~nx0 -s 1G -p 1G      Set 1GB upload and post size
echo   %~nx0 --size 256M      Set 256M upload size
exit /b 1

:main
echo Increasing PHP upload size...
echo Upload size: %UPLOAD_SIZE%
echo Post size: %POST_SIZE%

REM Find and update XAMPP PHP configuration
call :update_xampp_config
if errorlevel 1 (
    echo Warning: Could not update XAMPP configuration
)

REM Find and update native PHP configuration
call :update_native_php_config
if errorlevel 1 (
    echo Warning: Could not update native PHP configuration
)

echo.
echo PHP upload size configuration updated successfully!
echo Upload size: %UPLOAD_SIZE%
echo Post size: %POST_SIZE%
echo.
echo Please restart your web server and PHP services for changes to take effect.
goto verify_changes

:update_xampp_config
echo Looking for XAMPP installation...

REM Common XAMPP locations
set XAMPP_PATHS=C:\xampp\php\php.ini C:\XAMPP\php\php.ini D:\xampp\php\php.ini E:\xampp\php\php.ini F:\xampp\php\php.ini

for %%p in (%XAMPP_PATHS%) do (
    if exist "%%p" (
        echo Found XAMPP php.ini: %%p
        call :backup_and_update "%%p"
        set XAMPP_FOUND=1
        goto :eof
    )
)

REM Check if XAMPP is installed in Program Files
for /f "tokens=*" %%i in ('dir "C:\Program Files\*" /ad /b 2^>nul') do (
    if exist "C:\Program Files\%%i\xampp\php\php.ini" (
        echo Found XAMPP php.ini: C:\Program Files\%%i\xampp\php\php.ini
        call :backup_and_update "C:\Program Files\%%i\xampp\php\php.ini"
        set XAMPP_FOUND=1
        goto :eof
    )
)

if not defined XAMPP_FOUND (
    echo XAMPP installation not found
)
goto :eof

:update_native_php_config
echo Looking for native PHP installation...

REM Common native PHP locations
set PHP_PATHS=C:\php\php.ini C:\PHP\php.ini C:\Program Files\PHP\php.ini C:\Program Files (x86)\PHP\php.ini

for %%p in (%PHP_PATHS%) do (
    if exist "%%p" (
        echo Found native PHP php.ini: %%p
        call :backup_and_update "%%p"
        set PHP_FOUND=1
        goto :eof
    )
)

REM Check if PHP is in PATH and find its php.ini
php -r "echo php_ini_loaded_file();" > temp_php_ini.txt 2>nul
if exist temp_php_ini.txt (
    set /p PHP_INI_PATH=<temp_php_ini.txt
    if exist "!PHP_INI_PATH!" (
        echo Found PHP configuration: !PHP_INI_PATH!
        call :backup_and_update "!PHP_INI_PATH!"
        set PHP_FOUND=1
    )
    del temp_php_ini.txt
)

if not defined PHP_FOUND (
    echo Native PHP installation not found
)
goto :eof

:backup_and_update
set PHP_INI_FILE=%~1

REM Check if file is writable
attrib +w "%PHP_INI_FILE%" >nul 2>&1
if errorlevel 1 (
    echo Error: Cannot write to %PHP_INI_FILE%. Please run as administrator.
    exit /b 1
)

REM Create backup
set BACKUP_FILE=%PHP_INI_FILE%.backup.%date:~10,4%%date:~4,2%%date:~7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
copy "%PHP_INI_FILE%" "%BACKUP_FILE%" >nul
if errorlevel 1 (
    echo Error: Cannot create backup of %PHP_INI_FILE%
    exit /b 1
)
echo   Backup created: %BACKUP_FILE%

REM Update upload_max_filesize
findstr /C:"upload_max_filesize" "%PHP_INI_FILE%" >nul
if errorlevel 1 (
    REM Line doesn't exist, add it
    echo upload_max_filesize = %UPLOAD_SIZE% >> "%PHP_INI_FILE%"
    echo   Added upload_max_filesize = %UPLOAD_SIZE%
) else (
    REM Line exists, replace it
    powershell -Command "(Get-Content '%PHP_INI_FILE%') -replace '^upload_max_filesize.*', 'upload_max_filesize = %UPLOAD_SIZE%' | Set-Content '%PHP_INI_FILE%'"
    echo   Updated upload_max_filesize to %UPLOAD_SIZE%
)

REM Update post_max_size
findstr /C:"post_max_size" "%PHP_INI_FILE%" >nul
if errorlevel 1 (
    REM Line doesn't exist, add it
    echo post_max_size = %POST_SIZE% >> "%PHP_INI_FILE%"
    echo   Added post_max_size = %POST_SIZE%
) else (
    REM Line exists, replace it
    powershell -Command "(Get-Content '%PHP_INI_FILE%') -replace '^post_max_size.*', 'post_max_size = %POST_SIZE%' | Set-Content '%PHP_INI_FILE%'"
    echo   Updated post_max_size to %POST_SIZE%
)

REM Update memory_limit if needed
for /f "tokens=2 delims==" %%a in ('findstr /C:"memory_limit" "%PHP_INI_FILE%" 2^>nul') do (
    set CURRENT_MEMORY=%%a
    set CURRENT_MEMORY=!CURRENT_MEMORY: =!
)

if defined CURRENT_MEMORY (
    REM Extract number and unit from current memory
    set CURRENT_NUM=!CURRENT_MEMORY:~0,-1!
    set CURRENT_UNIT=!CURRENT_MEMORY:~-1!

    REM Extract number and unit from upload size
    set UPLOAD_NUM=%UPLOAD_SIZE:~0,-1%
    set UPLOAD_UNIT=%UPLOAD_SIZE:~-1%

    REM Convert to comparable format (MB)
    if /i "!CURRENT_UNIT!"=="G" (
        set /a CURRENT_MB=!CURRENT_NUM! * 1024
    ) else (
        set CURRENT_MB=!CURRENT_NUM!
    )

    if /i "%UPLOAD_UNIT%"=="G" (
        set /a UPLOAD_MB=%UPLOAD_NUM% * 1024
    ) else (
        set UPLOAD_MB=%UPLOAD_NUM%
    )

    REM Compare and update if needed
    if !CURRENT_MB! lss !UPLOAD_MB! (
        set /a NEW_MEMORY=!UPLOAD_MB! + 128
        powershell -Command "(Get-Content '%PHP_INI_FILE%') -replace '^memory_limit.*', 'memory_limit = !NEW_MEMORY!M' | Set-Content '%PHP_INI_FILE%'"
        echo   Updated memory_limit to !NEW_MEMORY!M
    )
) else (
    REM memory_limit doesn't exist, add it
    echo memory_limit = %UPLOAD_SIZE% >> "%PHP_INI_FILE%"
    echo   Added memory_limit = %UPLOAD_SIZE%
)

exit /b 0

:verify_changes
echo.
echo Verifying changes...
php -i | findstr /C:"upload_max_filesize\|post_max_size\|memory_limit"
if errorlevel 1 (
    echo Could not verify changes via command line. Please check your php.ini file manually.
)
goto :eof
