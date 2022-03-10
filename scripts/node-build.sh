# [NODE BUILD] ===================================================== 
# This script is used to build the website assets (CSS, JS, etc.)
# You can run this script manually to build the assets.

# [NVM INSTALLATION REQUIRED] ======================================
# You can install NVM by running either of the following commands:
# curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash
# wget -qO- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash
# 
# For more information, see: https://github.com/nvm-sh/nvm#installing-and-updating

# [NOTE] ===========================================================
# Be sure to modify your shell configuration with the correct config 
# found at https://github.com/nvm-sh/nvm#deeper-shell-integration

#  Sources the location of the NVM script after it has been installed
source ~/.nvm/nvm.sh

# Uses and/or installs the version of Node.js specified in ./nvmrc
nvm use

# Installs the packages specified in ./package.json using the clean slate installer: 
# https://docs.npmjs.com/cli/v8/commands/npm-ci
npm ci

# Builds the website assets and minifies them with Laravel Mix
npx mix --production
 