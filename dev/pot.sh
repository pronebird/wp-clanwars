#!/bin/sh

cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd

php tools/i18n/makepot.php wp-clanwars wp-clanwars wp-clanwars/langs/wp-clanwars.pot
php tools/i18n/makepot.php wp-clanwars-countries wp-clanwars wp-clanwars/langs/wp-clanwars-countries.pot
