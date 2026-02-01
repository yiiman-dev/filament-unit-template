#!/bin/sh
php artisan filament:list-modules
#php artisan vendor:publish --all &
npm run build &
if [ "$APP_ENV" = "migrate" ]; then
  echo "Running Laravel migrations..."
  exec php artisan migrate --force
elif [ "$APP_ENV" = "queue" ]; then
  echo "Running Laravel queue worker..."
  exec php artisan queue:work $RUN_PARAMETERS
else
  echo "Running Laravel server..."
  exec php artisan serve --host=0.0.0.0 --port=8000 $RUN_PARAMETERS
fi
