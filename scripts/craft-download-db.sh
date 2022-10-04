#!/bin/bash

# Exit handler and traps.
set -e -o pipefail

# This is where premature termination of the script gets a chance to cleanup,
# or do anything necessary to offer a graceful exit for an abrupt termination.
function exit_handler() {
    rm -f "$MYSQL_DUMP_FILE" &> /dev/null
}
trap "exit_handler" ERR # Invoke exit handler when an error happens anywhere in the script.
trap "exit_handler" INT # Invoke exit handler when the user issues a keyboard interrupt (CTRL + C).

# Array of required environment variables.
required_environment_variables=( \
    'MYSQL_DB' \
    'LOCAL_MYSQL_USER' \
    'REMOTE_MYSQL_USER' \
    'LOCAL_CRAFT_ADDRESS' \
    'REMOTE_CRAFT_ADDRESS' \
    'LOCAL_CRAFT_DIRECTORY' \
    'REMOTE_CRAFT_DIRECTORY' \
    'REMOTE_MYSQL_SSH' \
)

# Displays help text.
function display_help() {
    # Format required environment variables array to be displayed within the usage manual.
    required_environment_variables_formatted_text=""
    for required_environment_variable in "${required_environment_variables[@]}"; do
        required_environment_variables_formatted_text="${required_environment_variables_formatted_text}
        ${required_environment_variable}"
    done

    # Print usage manual.
    cat << EOT

Usage: $(basename $0) [OPTIONS]

ENVIRONMENT VARIABLES

    Required: ${required_environment_variables_formatted_text}

    Optional (with defaults):
        LOCAL_MYSQL_ADDRESS="127.0.0.1"
        REMOTE_MYSQL_ADDRESS="127.0.0.1"
        MYSQL_DUMP_FILE="<REMOTE_CRAFT_ADDRESS>-db-dump-<timestamp>.sql"
        MYSQL_NET_BUFFER_LENGTH="8388608"
        MYSQL_MAX_ALLOWED_PACKET="33554432"

OPTIONS

    -e, --environment-file    Specifies an environment file to load.

    -h, --help                Displays help manual.

EOT
}

# Parse command line arguments.
positional_parameters=""
while (( "$#" )); do
    case "$1" in
        -e|--environment-file)
            if [ -z "$2" ] || [ ${2:0:1} == "-" ]; then
                echo "$1 option is missing an environment file as an argument." >&2
                exit 1
            else
                source "$2"
                shift 2
            fi
            ;;
        -h|--help)
            display_help
            exit
            ;;
        -*) # unsupported flags
            echo "Error: Unsupported flag $1. You may use -h or --help to see the usage manual." >&2
            exit 1
            ;;
        *) # preserve positional arguments
            positional_parameters="$positional_parameters $1"
            shift
            ;;
    esac
done
set -- "$positional_parameters"

# Check required environment variables are set.
for required_environment_variable in "${required_environment_variables[@]}"; do
    if [ -z "${!required_environment_variable}" ]; then
        cat << EOT >&2

'${required_environment_variable}' is a required environment variable.
You may consult the usage manual using -h for a list of required environment variables.

EOT
        exit 2
    fi
done

# Optional environment variables can have default values if not set.
[ -n "$LOCAL_MYSQL_ADDRESS" ] || LOCAL_MYSQL_ADDRESS="127.0.0.1"
[ -n "$REMOTE_MYSQL_ADDRESS" ] || REMOTE_MYSQL_ADDRESS="127.0.0.1"
[ -n "$MYSQL_DUMP_FILE" ] || MYSQL_DUMP_FILE="${REMOTE_CRAFT_ADDRESS}-db-dump-$(date +"%s").sql"
[ -n "$MYSQL_NET_BUFFER_LENGTH" ] || MYSQL_NET_BUFFER_LENGTH="8388608"
[ -n "$MYSQL_MAX_ALLOWED_PACKET" ] || MYSQL_MAX_ALLOWED_PACKET="33554432"

# MySQL dump command line arguments.
# Consult the official "mysqldump" command documentation.
mysqldump_cli_args=( \
    "-h ${REMOTE_MYSQL_ADDRESS}" \
    "-u ${REMOTE_MYSQL_USER}" \
    "-p" \
    "--add-drop-table" \
    "--add-locks" \
    "--skip-lock-tables" \
    "--single-transaction" \
    "--no-tablespaces" \
    "--column-statistics=0" \
    "--routines" \
    "--triggers" \
    "--events" \
    "--hex-blob" \
    "--extended-insert" \
    "--net-buffer-length=${MYSQL_NET_BUFFER_LENGTH}" \
    "--max-allowed-packet=${MYSQL_MAX_ALLOWED_PACKET}" \
    "--databases ${MYSQL_DB}" \
)

# Search & replace for incoming MySQL dump stream via "sed" command.
# The search array can have simple strings, or regular expressions.
search=( \
    "$REMOTE_CRAFT_DIRECTORY" \
    "$REMOTE_CRAFT_ADDRESS" \
    "https://${LOCAL_CRAFT_ADDRESS}" \
)

# The replace array must be of the same size as the search array.
# Replacement strings must align in the same order / index as their
# search counterparts.
replace=( \
    "$LOCAL_CRAFT_DIRECTORY" \
    "$LOCAL_CRAFT_ADDRESS" \
    "http://${LOCAL_CRAFT_ADDRESS}" \
)

# Build sed script using search and replace arrays.
sed_script=""
for i in "${!search[@]}"; do
    sed_script="${sed_script} s@${search[$i]}@${replace[$i]}@g;"
done

# Initialize the local MySQL dump file.
cat << EOS > "$MYSQL_DUMP_FILE"
DROP SCHEMA IF EXISTS \`${MYSQL_DB}\`;
CREATE SCHEMA \`${MYSQL_DB}\`;

EOS

# Download MySQL dump from remote server to local.
ssh -t "$REMOTE_MYSQL_SSH" "echo 'Enter remote MySQL password:'; mysqldump ${mysqldump_cli_args[@]}" \
    | tee >(head -n 1; cat > /dev/null) \
          >(tail -n +3 | sed "$sed_script" >> "$MYSQL_DUMP_FILE") \
          > /dev/null

# Prompt the user before dropping the local MySQL schema, and loading the MySQL dump file downloaded.
cat << 'EOT'

ATTENTION!
----------
The local database will be dropped, then reloaded from the MySQL dump file downloaded from the remote server.
When ready, proceed by entering the local MySQL password below.

EOT

mysql -h "${LOCAL_MYSQL_ADDRESS}" -u "${LOCAL_MYSQL_USER}" -p -A -N -B < "$MYSQL_DUMP_FILE"

# Change to Craft local directory in order to run Craft commands.
pushd "$LOCAL_CRAFT_DIRECTORY"

# Craft CMS config synchronization against imported data.
cat << 'EOT'

Choose one of the following Craft CMS config synchronization options:

    1. Apply YAML config files to database.
    2. Rebuild YAML config files from database.
    3. Do nothing. This is also the default if left blank, or any unrecognized input is entered.

EOT

read -p 'Please enter an option number from the list above: ' craft_config_sync_choice
case $craft_config_sync_choice in
    1)
        php craft project-config/apply
        ;;
    2)
        php craft project-config/rebuild
        ;;
esac

# Craft CMS migrations.
read -p 'Run Craft CMS migrations? [y/N]: ' craft_migrations_choice
case $craft_migrations_choice in
    y|Y)
        php craft migrate/all
        ;;
esac

# Leave Craft local directory back to the previous directory when done running Craft commands.
popd

# Prompt the user whether to keep or delete the downloaded MySQL dump file.
read -p 'Delete the downloaded MySQL dump file? [y/N]: ' delete_sql_dump_file_choice
case $delete_sql_dump_file_choice in
    y|Y)
        rm "$MYSQL_DUMP_FILE"
        ;;
esac

# Print final confirmation of successful database download.
cat << 'EOT'

Database download completed successfully!

EOT
