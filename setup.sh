echo "Admin Setup Starting ..."
docker compose exec admin bash -c "
  set -e
  echo '📜 Running create_schemas.sh...'
  ./create_schemas.sh
  echo '📂 Running migrations...'
  php artisan migrate
  php artisan dev:
  echo '🔑 Generating app key...'
  php artisan key:generate
  echo '📋 Listing Filament modules...'
  php artisan filament:list-modules
"

echo "My Setup Starting ..."
docker compose exec my bash -c "
  set -e
  echo '🔑 Generating app key...'
  php artisan key:generate
  echo '📋 Listing Filament modules...'
  php artisan filament:list-modules
"

echo "Manage Setup Starting ..."
docker compose exec manage bash -c "
  set -e
  echo '🔑 Generating app key...'
  php artisan key:generate
  echo '📋 Listing Filament modules...'
  php artisan filament:list-modules
"