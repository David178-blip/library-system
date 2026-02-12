@echo off
echo Starting Library System...
echo ----------------------------------------
echo This will run:
echo 1. PHP Artisan Serve (on port 8000)
echo 2. Node Server (on port 3000)
echo 3. Vite (npm run dev --host)
echo ----------------------------------------
echo To access from other PCs, find your IP address (run 'ipconfig')
echo and use: http://YOUR_IP_ADDRESS:8000
echo ----------------------------------------
npm run dev:all
pause
