@echo off
REM Load environment variables from docker.env
FOR /F "usebackq tokens=* delims=" %%A IN (docker.env) DO SET %%A

docker compose --env-file docker.env down --volumes --remove-orphans
