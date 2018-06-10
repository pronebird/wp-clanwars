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
    $('.widget_clanwars').each(function () {
        var cookiename = 'wp-' + $(this).attr('id') + '-tabs',
            tabs = $(this).find('.tabs'),
            initial_tab = $.cookie(cookiename);

        tabs.find('a').bind('click', function (evt) {
            var c = $(this).attr('href').substr(1), show = '.' + c,
                l = tabs.parent().parent();

            tabs.find('li').removeClass('selected');
            $(this).parent().addClass('selected');

            if(c == 'all') {
                show = '.clanwar-item';
            }

            l.find('.clanwar-item').hide().end()
             .find(show).show().end()
             .find(show + ':odd').addClass('alt').end()
             .find(show + ':even').removeClass('alt').end();

            $.cookie(cookiename, c, {expires: 365, path: '/'});

            return false;
        });

        if(initial_tab) {
            tabs.find('a[href$=' + initial_tab + ']').click();
        }
    });
});
