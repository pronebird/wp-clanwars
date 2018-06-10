/*
    WP-Clanwars
    (c) 2011 Andrej Mihajlov

    This file is part of WP-Clanwars.

    WP-Clanwars is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WP-Clanwars is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with WP-Clanwars.  If not, see <http://www.gnu.org/licenses/>.
*/

jQuery(document).ready(function ($) {
    $('.wp-clanwars-list .maplist a').each(function () {
        var href = $(this).attr('href');
        var title = $(this).attr('title');
        var p = href.indexOf('#');
        var split = [ 150, 150 ];

        if(p !== -1) {
            split = href.substr(++p).split('x', 2);
        }

        var w = split[0];
        var h = split[1];

        $(this).attr('title', '<div style="position: relative;"><img src="' + href + '" style="width: ' + w + 'px; height: ' + h + 'px;" /><div class="map-title">' + title + '</div></div>')
            .tipsy({html: true})
            .on('click', function () { return false; });
    });
});