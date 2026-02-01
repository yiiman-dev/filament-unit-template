#!/bin/bash

# Array of URLs to ping
urls=("https://example.com" "https://example2.com")

# Maximum allowed ping time in seconds (5 seconds)
max_time=5

# Number of allowed failures before restart
max_failures=5

# PHP service name to restart
php_service="php7.4-fpm"  # Change this to your PHP service name, e.g., php8.1-fpm

# Temporary file to hold failure counts
count_file="/tmp/ping_fail_count"

# Initialize failure counts if file doesn't exist
if [ ! -f "$count_file" ]; then
  for i in "${!urls[@]}"; do
    echo "${i}=0" >> "$count_file"
  done
fi

# Read counts into associative array
declare -A fail_counts
while IFS='=' read -r idx count; do
  fail_counts[$idx]=$count
done < "$count_file"

# Function to extract ping time in seconds from curl
get_ping_time() {
  local url=$1
  # Use curl's --write-out to get total time in seconds, with a max timeout
  time=$(curl -o /dev/null -s -w "%{time_total}" --max-time $((max_time+5)) "$url")
  echo "$time"
}

# Check each URL
for i in "${!urls[@]}"; do
  ping_time=$(get_ping_time "${urls[$i]}")

  # Check if ping_time is a valid number and greater than max_time
  if [[ $ping_time =~ ^[0-9]+([.][0-9]+)?$ ]] && (( $(echo "$ping_time > $max_time" | bc -l) )); then
    fail_counts[$i]=$((fail_counts[$i]+1))
  else
    fail_counts[$i]=0
  fi
done

# Save updated counts
> "$count_file"
for i in "${!urls[@]}"; do
  echo "$i=${fail_counts[$i]}" >> "$count_file"
done

# Check if all counts are greater than max_failures
restart_needed=true
for i in "${!urls[@]}"; do
  if [ "${fail_counts[$i]}" -le $max_failures ]; then
    restart_needed=false
    break
  fi
done

# Restart PHP service if needed
if $restart_needed; then
  systemctl restart "$php_service"
fi
