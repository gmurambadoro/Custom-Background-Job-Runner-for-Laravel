@echo off
REM Load environment variables from docker.env
FOR /F "usebackq tokens=* delims=" %%A IN (docker.env) DO SET %%A

docker exec -it %DC_APP_NAME% php artisan queue:work --queue=high,medium,default
