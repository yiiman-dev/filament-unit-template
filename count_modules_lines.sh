#!/bin/bash

echo "Calculating total lines of code and comprehensive statistics for Modules folder..."
echo "==============================================================================="

# Count total lines in all files in Modules directory
echo "Total statistics for Modules folder:"
echo "----------------------------------------"

# Count lines by file extension
echo "Lines of code by file type:"
echo "==========================="
find Modules/ -type f -name "*.php" -exec wc -l {} + 2>/dev/null | grep total$ | awk '{print $1 " lines in PHP files"}'
find Modules/ -type f -name "*.js" -exec wc -l {} + 2>/dev/null | grep total$ | awk '{print $1 " lines in JS files"}'
find Modules/ -type f -name "*.css" -exec wc -l {} + 2>/dev/null | grep total$ | awk '{print $1 " lines in CSS files"}'
find Modules/ -type f -name "*.blade.php" -exec wc -l {} + 2>/dev/null | grep total$ | awk '{print $1 " lines in Blade files"}'
find Modules/ -type f -name "*.html" -exec wc -l {} + 2>/dev/null | grep total$ | awk '{print $1 " lines in HTML files"}'
find Modules/ -type f -name "*.json" -exec wc -l {} + 2>/dev/null | grep total$ | awk '{print $1 " lines in JSON files"}'
find Modules/ -type f -name "*.yaml" -exec wc -l {} + 2>/dev/null | grep total$ | awk '{print $1 " lines in YAML files"}'
find Modules/ -type f -name "*.yml" -exec wc -l {} + 2>/dev/null | grep total$ | awk '{print $1 " lines in YML files"}'
find Modules/ -type f -name "*.sql" -exec wc -l {} + 2>/dev/null | grep total$ | awk '{print $1 " lines in SQL files"}'
find Modules/ -type f -name "*.txt" -exec wc -l {} + 2>/dev/null | grep total$ | awk '{print $1 " lines in TXT files"}'

# Total lines across all file types
echo ""
echo "Overall totals:"
echo "==============="
total_lines=$(find Modules/ -type f \( -name "*.php" -o -name "*.js" -o -name "*.css" -o -name "*.blade.php" -o -name "*.html" -o -name "*.json" -o -name "*.yaml" -o -name "*.yml" -o -name "*.sql" -o -name "*.txt" -o -name "*.md" -o -name "*.xml" -o -name "*.env" -o -name "*.conf" -o -name "*.ini" \) -exec cat {} + 2>/dev/null | wc -l)
echo "Total lines of code: $total_lines"

# File count
total_files=$(find Modules/ -type f -name "*.php" -o -name "*.js" -o -name "*.css" -o -name "*.blade.php" -o -name "*.html" -o -name "*.json" -o -name "*.yaml" -o -name "*.yml" -o -name "*.sql" -o -name "*.txt" -o -name "*.md" -o -name "*.xml" -o -name "*.env" -o -name "*.conf" -o -name "*.ini" | wc -l)
echo "Total files: $total_files"

# Directory structure breakdown
echo ""
echo "Lines by module directory:"
echo "=========================="
for dir in Modules/*/; do
    if [ -d "$dir" ]; then
        dir_name=$(basename "$dir")
        dir_lines=$(find "$dir" -type f \( -name "*.php" -o -name "*.js" -o -name "*.css" -o -name "*.blade.php" -o -name "*.html" -o -name "*.json" -o -name "*.yaml" -o -name "*.yml" -o -name "*.sql" -o -name "*.txt" -o -name "*.md" -o -name "*.xml" -o -name "*.env" -o -name "*.conf" -o -name "*.ini" \) -exec cat {} + 2>/dev/null | wc -l)
        dir_files=$(find "$dir" -type f -name "*.php" -o -name "*.js" -o -name "*.css" -o -name "*.blade.php" -o -name "*.html" -o -name "*.json" -o -name "*.yaml" -o -name "*.yml" -o -name "*.sql" -o -name "*.txt" -o -name "*.md" -o -name "*.xml" -o -name "*.env" -o -name "*.conf" -o -name "*.ini" | wc -l)
        echo "$dir_name: $dir_lines lines in $dir_files files"
    fi
done

# Git statistics (if git repo exists)
if [ -d .git ]; then
    echo ""
    echo "Git change statistics (from project beginning):"
    echo "============================================="
    git log --pretty=format: --numstat --follow -- Modules/ | awk '
    BEGIN {insertions=0; deletions=0}
    {
        if ($1 != "-") insertions += $1
        if ($2 != "-") deletions += $2
    }
    END {
        print "Total insertions:", insertions
        print "Total deletions:", deletions
        print "Net lines added:", insertions - deletions
    }'

    echo ""
    echo "Most frequently changed files:"
    echo "=============================="
    git log --pretty=format: --numstat --follow -- Modules/ | awk '
    {
        if ($1 != "-") insertions[$3] += $1
        if ($2 != "-") deletions[$3] += $2
        total[$3] = (insertions[$3] + deletions[$3])
    }
    END {
        for (file in total) {
            print total[file], "changes in", file, "(+" insertions[file] "/-" deletions[file] ")"
        }
    }' | sort -nr | head -10
fi

echo ""
echo "Time-based statistics:"
echo "====================="
# Get the first and last commit dates for Modules
first_commit_date=$(git log --pretty=format:"%ad" --date=iso --follow -- Modules/ | tail -1)
last_commit_date=$(git log --pretty=format:"%ad" --date=iso --follow -- Modules/ | head -1)

if [ ! -z "$first_commit_date" ] && [ ! -z "$last_commit_date" ]; then
    echo "First commit date: $first_commit_date"
    echo "Last commit date: $last_commit_date"

    # Calculate elapsed time in days
    first_timestamp=$(date -jf "%Y-%m-%d %H:%M:%S %z" "$first_commit_date" +%s 2>/dev/null || date -d "$first_commit_date" +%s 2>/dev/null)
    last_timestamp=$(date -jf "%Y-%m-%d %H:%M:%S %z" "$last_commit_date" +%s 2>/dev/null || date -d "$last_commit_date" +%s 2>/dev/null)

    if [ ! -z "$first_timestamp" ] && [ ! -z "$last_timestamp" ]; then
        elapsed_seconds=$((last_timestamp - first_timestamp))
        elapsed_days=$((elapsed_seconds / 86400))
        elapsed_months=$((elapsed_days / 30))
        elapsed_years=$((elapsed_days / 365))

        echo "Development period: $elapsed_days days ($elapsed_months months, $elapsed_years years)"

        # Calculate average lines per day/month/year
        if [ $elapsed_days -gt 0 ]; then
            lines_per_day=$((total_lines / elapsed_days))
            echo "Average development rate: $lines_per_day lines per day"
        fi

        if [ $elapsed_months -gt 0 ]; then
            lines_per_month=$((total_lines / elapsed_months))
            echo "Average development rate: $lines_per_month lines per month"
        fi

        if [ $elapsed_years -gt 0 ]; then
            lines_per_year=$((total_lines / elapsed_years))
            echo "Average development rate: $lines_per_year lines per year"
        fi
    fi
fi

# Commit frequency statistics
echo ""
echo "Commit statistics:"
echo "================="
total_commits=$(git log --oneline --follow -- Modules/ | wc -l)
echo "Total commits affecting Modules: $total_commits"

if [ $total_commits -gt 0 ] && [ $elapsed_days -gt 0 ]; then
    commits_per_day=$(echo "scale=2; $total_commits / $elapsed_days" | bc)
    echo "Average commits per day: $commits_per_day"
fi

# Daily activity statistics
echo ""
echo "Daily development activity:"
echo "=========================="
git log --pretty=format:"%ad" --date=format:"%Y-%m-%d" --follow -- Modules/ | sort | uniq -c | sort -nr | head -10

echo ""
echo "Cost and Resource Estimation:"
echo "============================="
# Industry standard metrics for cost estimation
LINES_PER_DEVELOPER_PER_DAY=300    # Average productive lines per developer per day
LINES_PER_DEVELOPER_PER_MONTH=6000 # Average productive lines per developer per month
DEVELOPER_DAILY_RATE=500          # USD per day (adjust based on location/skill level)
DEVELOPER_MONTHLY_RATE=1000      # USD per month

# Additional team member rates
PRODUCT_OWNER_DAILY_RATE=400      # USD per day
PROJECT_MANAGER_DAILY_RATE=350    # USD per day
BUSINESS_ANALYST_DAILY_RATE=350   # USD per day
PRODUCT_DESIGNER_DAILY_RATE=300   # USD per day

# Calculate estimated developer effort
estimated_developer_days=$((total_lines / LINES_PER_DEVELOPER_PER_DAY))
estimated_developer_months=$((total_lines / LINES_PER_DEVELOPER_PER_MONTH))

# Calculate estimated costs
estimated_cost_days=$((estimated_developer_days * DEVELOPER_DAILY_RATE))
estimated_cost_months=$((estimated_developer_months * DEVELOPER_MONTHLY_RATE))

echo "Estimated development effort:"
echo "  - $estimated_developer_days developer-days required"
echo "  - $estimated_developer_months developer-months required"
echo ""
echo "Estimated costs (based on average rates):"
echo "  - Daily rate approach: \$$estimated_cost_days"
echo "  - Monthly rate approach: \$$estimated_cost_months"
echo ""
echo "Team size estimation:"
echo "  - If developed in $elapsed_days days: $((estimated_developer_days / elapsed_days)) developers needed"
echo "  - If developed in $elapsed_months months: $((estimated_developer_months / elapsed_months)) developers needed"
echo ""

# Additional team members estimation
echo "Additional team members (estimated):"
echo "  - Product Owner: $(echo "$elapsed_months * 0.5" | bc -l | cut -d. -f1) person-months (part-time)"
echo "  - Project Manager: $(echo "$elapsed_months * 0.3" | bc -l | cut -d. -f1) person-months (part-time)"
echo "  - Business Analyst: $(echo "$elapsed_months * 0.4" | bc -l | cut -d. -f1) person-months (part-time)"
echo "  - Product Designer: $(echo "$elapsed_months * 0.2" | bc -l | cut -d. -f1) person-months (part-time)"
echo ""

# Calculate additional team costs
po_cost=$(echo "$elapsed_months * 0.5 * $PRODUCT_OWNER_DAILY_RATE * 22" | bc -l | cut -d. -f1)
pm_cost=$(echo "$elapsed_months * 0.3 * $PROJECT_MANAGER_DAILY_RATE * 22" | bc -l | cut -d. -f1)
ba_cost=$(echo "$elapsed_months * 0.4 * $BUSINESS_ANALYST_DAILY_RATE * 22" | bc -l | cut -d. -f1)
pd_cost=$(echo "$elapsed_months * 0.2 * $PRODUCT_DESIGNER_DAILY_RATE * 22" | bc -l | cut -d. -f1)

total_additional_cost=$((po_cost + pm_cost + ba_cost + pd_cost))
total_project_cost=$((estimated_cost_months + total_additional_cost))

echo "Additional team costs (part-time, based on $elapsed_months-month project):"
echo "  - Product Owner: \$$po_cost (0.5 FTE @ \$$PRODUCT_OWNER_DAILY_RATE/day)"
echo " - Project Manager: \$$pm_cost (0.3 FTE @ \$$PROJECT_MANAGER_DAILY_RATE/day)"
echo " - Business Analyst: \$$ba_cost (0.4 FTE @ \$$BUSINESS_ANALYST_DAILY_RATE/day)"
echo "  - Product Designer: \$$pd_cost (0.2 FTE @ \$$PRODUCT_DESIGNER_DAILY_RATE/day)"
echo "  - Total additional costs: \$$total_additional_cost"
echo "  - Grand total project cost: \$$total_project_cost"
echo ""

echo "Industry benchmarks (for comparison):"
echo "  - Lines per developer per day (productive): $LINES_PER_DEVELOPER_PER_DAY"
echo " - Lines per developer per month (productive): $LINES_PER_DEVELOPER_PER_MONTH"
echo " - Average daily rate: \$$DEVELOPER_DAILY_RATE"
echo "  - Average monthly rate: \$$DEVELOPER_MONTHLY_RATE"

echo ""
echo "Summary:"
echo "======="
echo "Total lines of code in Modules: $total_lines"
echo "Total files in Modules: $total_files"
echo "Average lines per file: $((total_lines / total_files))"
