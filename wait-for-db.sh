#!/bin/sh

# Exit if any command fails
set -e

echo "Waiting for database to be ready..."

# Loop until the database is reachable
while ! mysql -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" &> /dev/null
do
  echo "Database not ready yet, sleeping..."
  sleep 2
done

echo "Database is ready! Running migrations..."

# Run Laravel migrations
php artisan migrate --force

echo "Starting Apache..."
# Replace default Apache port with Render's $PORT
sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf
sed -i "s/80/${PORT}/g" /etc/apache2/sites-available/000-default.conf

# Start Apache in foreground
apache2-foreground
