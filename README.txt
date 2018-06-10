=== WP-ClanWars ===
Contributors: andddd
Tags: cybersport, clanwar, team, clan, cyber, sport, match, gaming, game, community
Requires at least: 4.3
Tested up to: 4.3
Stable tag: 1.7.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WP-ClanWars is a match organizer plugin for gaming communities and teams.

== Help out to translate this plugin ==

Wanna see WP-ClanWars in your language? Help out to translate it!

https://www.transifex.com/projects/p/wp-clanwars/

== Description ==

WP-ClanWars is a match organizer plugin for gaming communities and teams. It supports multiple games, sidebar widget, match browser, and allows multigamings to delegate match scheduling between multiple war arrangers using simple access control system.

Plugin creates a post for every match, all posts are published under category set in plugin settings. I suggest to create a separate category for all matches so you don't mix it with any other blog posts. Since every match is a regular post, it means that they will show up all around your website, if it's not desired behavior you will have to restrict matches category from being shown on specific pages of your website (e.g. on front page). I guess there are plenty of plugins for that or you can always do it manually by fixing your theme files. I would also suggest to avoid any changes to the content of created posts because plugin overwrites post content on match update.

By default plugin uses it's own stylesheet which can be disabled in plugin settings. If you decide to make a custom CSS for your website, as example, take a look at default styles: [site.css](https://bitbucket.org/and/wp-clanwars/raw/default/css/site.css) and [widget.css](https://bitbucket.org/and/wp-clanwars/raw/default/css/widget.css).

Plugin supports a match browser, which can be displayed for visitors using `[wp-clanwars]` shortcode, I suggest to create a separate page for it.

== Installation ==

- PHP 5.3 or newer
- MySQL 5.0 or newer

== Features ==

* __Games management__: add your own games, manage maps, export, import others.
* __Match management__: create match, there is no limit for number of maps per match or number of rounds per map, extra information like "External League URL", "Practice War" or "Official Match" can be also specified, final score is calculated automatically.
* __Teams management__: mostly informational, the only useful thing is to setup your team and mark it as home team. Home team is selected by default when you add a new match.
* __Import/Export__: you can save a pack of games that you created on your own, all games are exported with maps attached to them. Can be useful if you need to transfer a game from one website to another. Plugin comes with 7 game packs built-in which can be installed from Import menu.
* __Access Control__: grant your war arrangers an access to match management.
* __Match Browser__: site visitors can navigate through all matches.

== Screenshots ==

1. Match Browser
2. Games management
3. Match management
4. Match editor
5. Import menu
6. Plugin Settings
7. Sidebar widget settings
8. Sidebar widget

== Changelog ==

= 1.7.2 =

* Fix error in database handling routine ([issue #3](https://github.com/pronebird/wp-clanwars/issues/3))

= 1.7.1 =

* Fix issues with large images in gallery

= 1.7.0 =

* Add gallery support
* Add Spanish and Russian translations
* Lots of optimizations

= 1.6.3 =

* Add German translation

= 1.6.2 =

* Add link to translation service

= 1.6.1 =

* Use ZipArchive when available
* Improve ZIP error handling

= 1.6.0 =

* Add import/export to ZIP
* Add "Export" link next to "Maps" in Games section
* Drop old localizations

== Attributions ==

Plugin icon made by [Freepik](http://www.flaticon.com/authors/freepik "Freepik") from [www.flaticon.com](http://www.flaticon.com "Flaticon") is licensed by [CC BY 3.0](http://creativecommons.org/licenses/by/3.0/ "Creative Commons BY 3.0").
