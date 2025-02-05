#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

# Function to migrate a database
migrate_database() {
    local ENVIRONMENT=$1

    if [ -z "$ENVIRONMENT" ]; then
        ENV_OPTION=""
        ENV_DISPLAY="default"
    else
        ENV_OPTION="--env=$ENVIRONMENT"
        ENV_DISPLAY="$ENVIRONMENT"
    fi

    echo "-------------------------------------------"
    echo " Migrating $ENV_DISPLAY database"
    echo "-------------------------------------------"

    # Create the database if it doesn't exist
    php bin/console doctrine:database:create --if-not-exists $ENV_OPTION

    # Drop the schema
    php bin/console doctrine:schema:drop --force $ENV_OPTION

    # Clear the migration table
    php bin/console doctrine:migrations:version --delete --all --no-interaction $ENV_OPTION

    # Run migrations
    php bin/console doctrine:migrations:migrate --no-interaction $ENV_OPTION

	# dont load fixtures for test environment
	if [ -z "$ENVIRONMENT" ]; then
		# Load fixtures
		php bin/console doctrine:fixtures:load --no-interaction $ENV_OPTION
	fi
}

# Migrate the normal (default) database
migrate_database ""

# Migrate the test database
migrate_database "test"

echo "-------------------------------------------"
echo " All migrations and fixtures loading completed successfully"
echo "-------------------------------------------"
