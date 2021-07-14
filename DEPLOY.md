## Deployment Process

Oakland is currently on Craft CMS, and has two environments: `staging` (https://staging.oaklandca.dev/) and `production` (https://www.oaklandca.gov/). 

Deployment to `staging` is automated through GitHub Actions. If you deploy to the GitHub repository (https://github.com/CityofOakland/oaklandca.gov-live) on the staging branch, it will automatically run a few scripts to deploy the site live. You can see the script here: https://github.com/CityofOakland/oaklandca.gov-live/blob/staging/.github/workflows/manual.yml.

For production deploys, run a git pull while SSH’d into the production site. You should have a 1Password invite to the Oakland Digital Services vault. All Craft files should live in the ~/www/public_html folder.

### Upgrading

Performing Craft core updates can be done through the `./craft update craft` command. However, some issues may arise from database migrations due to some of the `config/general.php` variables.

Currently the steps for upgrading are:

1. Change `allowAdminChanges` in `config/general.php` to `true` for your specific environment.
2. Run `./craft update craft` from the project directory.
3. If it prompts you to backup the database, type `yes`. If something goes wrong you can revert the changes by typing `yes` again.
4. Once the update are complete, go back and change `allowAdminChanges` to false.
5. Celebrate!