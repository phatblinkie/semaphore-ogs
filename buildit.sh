cd web
npm run lint --fix
cd ..
task build:fe && task build:be && bin/semaphore server --config config.json

