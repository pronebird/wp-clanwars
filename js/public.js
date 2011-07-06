
jQuery(document).ready(function($){

	$('.wp-clanwars-list .maplist a').each(function(){
		var href = $(this).attr('href');
		var title = $(this).attr('title');

		var w, h;

		var p = href.indexOf('#'), split = [150,150];

		if(p != -1) {
			split = href.substr(p+1).split('x', 2);
		}

		w = split[0];
		h = split[1];

		$(this).attr('title', '<div style="position: relative;"><img src="' + href + '" style="width: ' + w + 'px; height: ' + h + 'px;" /><div class="map-title">' + title + '</div></div>')
			.tipsy({html: true})
			.click(function(){ return false; });
	});

});
