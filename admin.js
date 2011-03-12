/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function($){

	try{convertEntities(wpCWAdminL10n);}catch(e){};

	var arr = {
		'wp-cw-maps' : wpCWAdminL10n.confirmDeleteMap,
		'wp-cw-games' : wpCWAdminL10n.confirmDeleteGame,
		'wp-cw-teams' : wpCWAdminL10n.confirmDeleteTeam,
		'wp-cw-matches' : wpCWAdminL10n.confirmDeleteMatch
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

});

