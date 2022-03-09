bash ./scripts/craft-backup.sh
git pull
bash ./scripts/craft-cache-flush.sh
bash ./scripts/craft-cache-clear.sh
bash ./scripts/craft-build.sh
bash ./scripts/node-build.sh