
jQuery(document).ready(function($){

	$('.widget_clanwars').each(function(){

		var cookiename = 'wp-' + $(this).attr('id') + '-tabs';
		var tabs = $(this).find('.tabs');
		var onload = $.cookie(cookiename);
		var data = {
			'tabs' : tabs,
			'cookiename': cookiename
		};

		tabs.find('a').bind('click', data, function(evt){

			var tabs = evt.data.tabs;
			var cookie = evt.data.cookiename;

			tabs.find('li').removeClass('selected');
			$(this).parent().addClass('selected');

			var c = $(this).attr('href').substr(1);
			var show = '.' + c;

			if(c == 'all')
				show = '.clanwar-item';

			var l = tabs.parent().parent();
			l.find('.clanwar-item').hide();
			l.find(show).show();

			l.find(show + ':odd').addClass('alt');
			l.find(show + ':even').removeClass('alt');

			$.cookie(cookie, c, {expires: 365, path: '/'});

			return false;

		});

		if(onload != null)
			tabs.find("a[href$=" + onload + "]").click();

	});

});
