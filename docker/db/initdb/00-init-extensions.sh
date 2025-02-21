#!/usr/bin/env bash
set -e

# 1) Create the "app_test" database if you haven't already
psql --username "$POSTGRES_USER" <<-EOSQL
    CREATE DATABASE app_test;
EOSQL

# 2) Enable extensions for the "app" database
psql --username "$POSTGRES_USER" --dbname "app" <<-EOSQL
    CREATE EXTENSION IF NOT EXISTS pg_trgm;
EOSQL

# 3) Enable extensions for the "app_test" database
psql --username "$POSTGRES_USER" --dbname "app_test" <<-EOSQL
    CREATE EXTENSION IF NOT EXISTS pg_trgm;
EOSQL
