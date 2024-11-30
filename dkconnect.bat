@echo off
REM Load environment variables from docker.env
FOR /F "usebackq tokens=* delims=" %%A IN (docker.env) DO SET %%A

echo Web:             http://localhost:%DC_APP_PORT%
echo phpMyAdmin:      http://localhost:%DC_PMA_PORT%

docker exec -it %DC_APP_NAME% bash
