@echo off
echo Clearing Laravel caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo Done! Now try the Twitch connection again.