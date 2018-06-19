#!/bin/sh
#
# This script intalls the i18 'tools' and 'pot.sh' into `wp-content/plugins`.
# I order to generate POT, cd to `wp-content/plugin` and run `pot.sh`
#
# Reference:
# https://codex.wordpress.org/I18n_for_WordPress_Developers
#

cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd

cp -R tools ../../tools
cp pot.sh ../../

cd ../../
ln -s ../../ src
