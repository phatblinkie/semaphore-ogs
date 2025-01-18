cd web
npm run lint --fix
cd ..
sudo systemctl stop semaphore
task build:fe && task build:be && sudo cp -f bin/semaphore /usr/bin/semaphore && sudo systemctl start semaphore
#server --config /etc/semaphore.json

