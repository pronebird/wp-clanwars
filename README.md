## WP-ClanWars plugin for WordPress

WP-ClanWars is a match organizer plugin for cyber sport teams. It supports multiple games, sidebar widget, match browser, and allows multigamings to delegate match scheduling between multiple war arrangers using simple access control system.

Plugin creates a post for every match, all posts are published under category set in plugin settings. I suggest to create a separate category for all matches so you don't mix it with any other blog posts. Since every match is a regular post, it means that they will show up all around your website, if it's not desired behavior you will have to restrict matches category from being shown on specific pages of your website (e.g. on front page). I guess there are plenty of plugins for that or you can always do it manually by fixing your theme files. I would also suggest to avoid any changes to the content of created posts because plugin overwrites post content on match update.

By default plugin uses it's own stylesheet which can be disabled in plugin settings. If you decide to make a custom CSS for your website, as example, take a look at default styles: [site.css](https://bitbucket.org/and/wp-clanwars/raw/default/css/site.css) and [widget.css](https://bitbucket.org/and/wp-clanwars/raw/default/css/widget.css).

Plugin supports a match browser, which can be displayed for visitors using `[wp-clanwars]` shortcode, I suggest to create a separate page for it.

### Help out to translate this plugin

Wanna see WP-ClanWars in your language? Help out to translate it!

https://www.transifex.com/projects/p/wp-clanwars/

### Installation

I published plugin on WordPress.org, so you can install it thru admin now.

https://wordpress.org/plugins/wp-clanwars/

Make sure your MySQL server supports InnoDB, otherwise this plugin won't work.

### Features

* __Games management__: add your own games, manage maps, export, import others.
* __Match management__: create match, there is no limit for number of maps per match or number of rounds per map, extra information like "External League URL", "Practice War" or "Official Match" can be also specified, final score is calculated automatically.
* __Teams management__: mostly informational, the only useful thing is to setup your team and mark it as home team. Home team is selected by default when you add a new match.
* __Import/Export__: you can save a pack of games that you created on your own, all games are exported with maps attached to them. Can be useful if you need to transfer a game from one website to another. Plugin comes with 7 game packs built-in which can be installed from Import menu.
* __Access Control__: grant your war arrangers an access to match management.
* __Match Browser__: site visitors can navigate through all matches.

### Screenshots

#### Match Browser
![Match browser](https://bitbucket.org/and/wp-clanwars/raw/default/screenshot-1.jpg)

#### Games management
![Games management](https://bitbucket.org/and/wp-clanwars/raw/default/screenshot-2.jpg)

#### Match management
![Match management](https://bitbucket.org/and/wp-clanwars/raw/default/screenshot-3.jpg)

#### Match editor
![Match editor](https://bitbucket.org/and/wp-clanwars/raw/default/screenshot-4.jpg)

#### Import menu
![Import menu](https://bitbucket.org/and/wp-clanwars/raw/default/screenshot-5.jpg)

#### Settings
![Settings](https://bitbucket.org/and/wp-clanwars/raw/default/screenshot-6.jpg)

#### Sidebar widget settings
![Sidebar widget settings](https://bitbucket.org/and/wp-clanwars/raw/default/screenshot-7.jpg)

#### Sidebar widget
![Sidebar widget](https://bitbucket.org/and/wp-clanwars/raw/default/screenshot-8.jpg)