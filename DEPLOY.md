## Deployment Process

Oakland is currently on Craft CMS, and has two environments: `staging` (https://staging.oaklandca.dev/) and `production` (https://www.oaklandca.gov/). 

For both `staging` and `production` deploys, run a git pull while SSH’d into the staging/production site. You should have a 1Password invite to the Oakland Digital Services vault. All Craft files should live in the ~/www/public_html folder.

Someimtes you may need to run composer install before you can apply any project-config changes (`./craft project-config/apply`). In which case, please use `composer.phar` for any composer related installs/updates. You may check and validate the potential changes by first running `./composer.phar validate`. Once confirmed, run `./composer.phar install` to start the actual install. Besides backing up the database (`./craft db/backup`), it might be a good idea to backup `vendor/` in the codebase as well. 

### Upgrading

Performing Craft core updates can be done through the `./craft update craft` command. However, some issues may arise from database migrations due to some of the `config/general.php` variables.

Currently the steps for upgrading are:

1. Change `allowAdminChanges` in `config/general.php` to `true` for your specific environment.
2. Run `./craft update craft` from the project directory.
3. If it prompts you to backup the database, type `yes`. If something goes wrong you can revert the changes by typing `yes` again.
4. Once the update are complete, go back and change `allowAdminChanges` to false.
5. Celebrate!