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

jQuery(function ($) {

    $('#wpbody').on('change', '.wp-clanwars-widget-settings .widget-setting-hide-older-than select', function () {
        var $container = $(this).closest('.wp-clanwars-widget-settings');
        var $durationWrap = $container.find('.widget-setting-custom-hide-duration');
        var isCustomDuration = $(this).val() === 'custom';

        ($durationWrap[ isCustomDuration ? 'slideDown' : 'slideUp' ])();
    });
});