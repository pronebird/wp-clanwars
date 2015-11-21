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
	var arr = {
		'wp-clanwars-maps': wpCWAdminL10n.confirmDeleteMap,
		'wp-clanwars-games': wpCWAdminL10n.confirmDeleteGame,
		'wp-clanwars-teams': wpCWAdminL10n.confirmDeleteTeam,
		'wp-clanwars-matches': wpCWAdminL10n.confirmDeleteMatch
	};

	for(var i in arr) {
		$('.' + i + ' span.delete a').each(function () {
			var data = {
				link : $(this).attr('href'),
				message : arr[i]
			};

			$(this).bind('click', data,
				function (evt) {
					if(confirm(evt.data.message)) {
						location.href = evt.data.link;
                    }
					return false;
				});
				
		});
	}

	$('select.select2').select2();

});

