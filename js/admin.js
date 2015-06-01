
jQuery(document).ready(function ($) {
	var arr = {
		'wp-cw-maps': wpCWAdminL10n.confirmDeleteMap,
		'wp-cw-games': wpCWAdminL10n.confirmDeleteGame,
		'wp-cw-teams': wpCWAdminL10n.confirmDeleteTeam,
		'wp-cw-matches': wpCWAdminL10n.confirmDeleteMatch
	};

	for(var i in arr) {
		$('.' + i + ' span.delete a').each(function(){
			var data = {
				link : $(this).attr('href'),
				message : arr[i]
			};

			$(this).bind('click', data,
				function(evt){
					if(confirm(evt.data.message))
						location.href = evt.data.link;

					return false;
				});
				
		});
	}

	$('select.select2').select2();

});

