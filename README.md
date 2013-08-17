## WP-ClanWars plugin for WordPress

I made this plugin for [Team Yes! Multigaming team](http://team-yes.ru/) and never thought about publishing it on WordPress Directory since I am not capable to maintain it anymore, so use at your own risk. However I appreciate and encourage others to improve the plugin, I am sure there is a plenty of room for improvement.

For every match the plugin creates a post, all post are created in a category set in plugin settings. I suggest to create a separate category for all matches. Since every match is a regular post, it means that they will show up all around the website, if it's not desired behavior you will have to restrict matches category from being shown on specific pages of your website (e.g. on front page), I guess there are plenty of plugins for that or you can always do it manually by fixing your theme files. I would also suggest to avoid any changes to the content of created posts because plugin overwrites post content on match update.

Plugin supports a match browser, which can be displayed for visitors using `[wp-clanwars]` shortcode, I suggest to create a separate page for it.

### Features

* __Games management__: add your own games, manage maps, export, import others.
* __Match management__: create match, there is no limit for number of maps per match or number of rounds per map, extra information like "External League URL", "Practice War" or "Official Match" can be also specified, final score is calculated automatically.
* __Teams management__: mostly informational, the only useful thing is to setup your team and mark it as home team. Home team is selected by default when you add a new match.
* __Import/Export__: you can save a pack of games that you created on your own, all games are exported with maps attached to them. Can be useful if you need to transfer a game from one website to another. Plugin comes with 7 game packs built-in which can be installed from Import menu.
* __Access Control__: grant your war arrangers an access to match management.
* __Match Browser__: site visitors can navigate through all matches.

### Screenshots

#### Match Browser
![Match browser](https://bitbucket.org/and/wp-clanwars/raw/default/readme/matchbrowser.jpg)

#### Games management
![Games management](https://bitbucket.org/and/wp-clanwars/raw/default/readme/games.jpg)

#### Match management
![Match management](https://bitbucket.org/and/wp-clanwars/raw/default/readme/matches.jpg)

#### Match editor
![Match editor](https://bitbucket.org/and/wp-clanwars/raw/default/readme/match_editor.jpg)

#### Import menu
![Import menu](https://bitbucket.org/and/wp-clanwars/raw/default/readme/import.jpg)

#### Settings
![Settings](https://bitbucket.org/and/wp-clanwars/raw/default/readme/settings.jpg)

#### Sidebar widget settings
![Sidebar widget settings](https://bitbucket.org/and/wp-clanwars/raw/default/readme/widget_setup.jpg)

#### Sidebar widget
![Sidebar widget](https://bitbucket.org/and/wp-clanwars/raw/default/readme/sidebar_widget.jpg)