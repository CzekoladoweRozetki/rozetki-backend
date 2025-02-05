#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

# Check if the script is being run from the Symfony project root
if [ ! -f "bin/console" ]; then
    echo "Error: 'bin/console' not found. Please run this script from the root of your Symfony project."
    exit 1
fi

# Set the environment to 'test'
ENVIRONMENT=test

echo "-------------------------------------------"
echo " Starting test database and schema creation"
echo "-------------------------------------------"

# Drop the existing test database if it exists
echo "Dropping existing test database (if it exists)..."
php bin/console doctrine:database:drop --env=$ENVIRONMENT --force --if-exists

# Create the test database
echo "Creating test database..."
php bin/console doctrine:database:create --env=$ENVIRONMENT

# Create the schema in the test database
echo "Creating schema in the test database..."
php bin/console doctrine:schema:create --env=$ENVIRONMENT

echo "-------------------------------------------"
echo " Test database and schema have been created"
echo "-------------------------------------------"
