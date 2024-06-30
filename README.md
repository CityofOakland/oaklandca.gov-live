<p align="center"><a href="https://craftcms.com/" target="_blank"><img width="312" height="90" src="https://craftcms.com/craftcms.svg" alt="Craft CMS"></a></p>

## About Craft CMS

Craft is a flexible and scalable CMS for creating bespoke digital experiences on the web and beyond.

It features:

- An intuitive Control Panel for administration tasks and content creation.
- A clean-slate approach to content modeling and [front-end development](https://docs.craftcms.com/v3/dev/).
- A built-in Plugin Store with hundreds of free and commercial [plugins](https://plugins.craftcms.com/).
- A robust framework for [module and plugin development](https://docs.craftcms.com/v3/extend/).

Learn more about it at [craftcms.com](https://craftcms.com).

## Installation

1. `git clone` this repository into your development server.
2. Run `composer install` and `npm install` to install package builders.
3. Copy and rename `.env.example` to `.env`. Make sure to fill in the correct environment variables and create any necessary databases/API keys.
4. Build assets by running `npm run build`. Make sure to check `package.json` for other handy development commands.

## Resources

#### Official Resources
- [Documentation](https://docs.craftcms.com/v3/) – Everything from usage instructions to plugin guides. 
- [Class Reference](https://docs.craftcms.com/api/v3/) – Full API and class reference for plugin and module developers.
- [Demo site](https://demo.craftcms.com/) – Quickly launch a personalized demo of a Craft site.
- [Craft Slack](https://craftcms.com/community#slack) – Join one of the most friendly and helpful Slack groups around.
- [Craft Commerce](https://craftcommerce.com/) – First-party e-commerce platform for Craft.

#### Community Resources
- [CraftQuest](https://craftquest.io/) – Unlimited access to Craft training (and more) from Mijingo.
- [Envato Tuts+](https://webdesign.tutsplus.com/categories/craft-cms/courses) – Video courses.
- [nystudio107 Blog](http://straightupcraft.com/) – Articles about Craft and modern web development.
- [Craft Link List](http://craftlinklist.com/) – Bimonthly newsletter about the Craft ecosystem.
- [Craft CMS Stack Exchange](http://craftcms.stackexchange.com/) – Community-run Q&A for Craft developers.

## Oakland Documentation

#### Update Schedule

Craft CMS Core and site plugins are scheduled to be updated on the first Tuesday of every month, 6AM PST. Typical downtime shouldn't be longer than a few minutes, however, site users should be instructed to avoid creating new content and to save outstanding edits in that thirty minute period (6:00AM to 6:30AM). During this time, we will also restore the staging database with the current live production version.

Exceptions to this cadence include but are not limited to:

- High risk security fixes.
- Critical/Urgent user-facing or admin-facing issues.

#### Deploy Schedule

Regular code updates will be pushed to production after being marked "Approved" on Asana. Deployments that require downtime or may interrupt admin workflows will be deployed on Tuesdays at 6AM PST weekly (similar to the monthly update schedule). P.S. Site users should be instructed not to create content during this time too!

#### Committing Client Changes

Depending on how tech-savvy the client is, changes can be committed various ways.

- Through the GitHub CLI, in the Mac Terminal (typically for developers).
- Inside the GitHub web application. Files can be edited and new branches can be created from saved changes (can be limiting on the number of edited pages).

This section is subject to change.

### CLI Utilities

All CLI utility scripts currently live under `scripts/` directory.

#### Craft Data Download Utility

This utility helps download Craft data from a remote server to a local environment. It can be useful for automating data synchronization across various environments (e.g. production, staging, development, etc).

**Features:**
* Downloading a DB SQL dump from a remote server containing a Craft schema.
* Searching and replacing certain strings in the SQL dump to make sure the installation works on the local / destination environment (e.g. Craft address, directory, etc).
* Interactively prompts the user to optionally run Craft CLI commands:
  * Apply YAML config from and to the imported database.
  * Run all migrations.
  * Create a new local user.
  * Retain or delete the downloaded SQL dump file.

**Software Stack:**
* Bash.
* Common UNIX utilities like: ssh, sed, tee, cat, etc.
* MySQL CLI utilities like mysql, mysqldump, etc.
* Operating System:
  * UNIX (Native)
    * GNU/Linux.
    * BSD-based (e.g. FreeBSD, MacOS, etc).
  * Windows 10+ (via WSL, containers, VMs / hypervisors, etc).

**Usage Instructions:**
1. Consult the CLI utility's usage manual by invoking `craft-download-db.sh -h`.
2. Either create an environment file to be used by the CLI utility (see `-e` option in usage manual), or make sure the current shell environment has all the required environment variables set while invoking the CLI utility. Here's an example environment file to start with:
```
# Local & remote MySQL connection info.
LOCAL_MYSQL_USER=""
REMOTE_MYSQL_USER=""
REMOTE_MYSQL_DB=""

# Local & remote Craft addresses (e.g. domain name, IP, etc)
# without the scheme prefix (e.g. http://, https://, etc).
LOCAL_CRAFT_ADDRESS=""
REMOTE_CRAFT_ADDRESS=""

# Local and remote Craft directory on the server without a trailing slash.
LOCAL_CRAFT_DIRECTORY=""
REMOTE_CRAFT_DIRECTORY=""

# SSH connection string. This could be in the format user@server-address,
# or a host in local SSH config.
REMOTE_MYSQL_SSH=""

# Additional mysqldump command line options.
# The variable is a bash array.
# Example: ADDITIONAL_MYSQLDUMP_OPTIONS=("--no-tablespaces" "--column-statistics=0")
# Depending on the mysqldump version, or MySQL server config, additional flags may be necessary.
# Consult the remote mysqldump version's manual for reference.
ADDITIONAL_MYSQLDUMP_OPTIONS=()
```
3. Make sure the destination / local Craft environment has what it needs to connect to its local database server and function properly. You do not need a full Craft schema setup, but at least a `.env` file with all the variables needed.
4. Run utility, and follow all prompts and instructions on the screen.
