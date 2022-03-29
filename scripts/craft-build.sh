php craft update/composer-install --interactive=0
php craft migrate/all --no-content --interactive=0
php craft project-config/apply
php craft migrate --track=content --interactive=0
