$(document).ready(function() {
		
		var spanHeight,
			nav = $('.menu-navigation'),
			lis = $('li', nav),
			anchors = $('a', lis).css('padding', 0);
			
		$.each(anchors, function() {
			var a = $(this),
				val = a.text();
	
			a.html('<span>' + val + '</span> <span>' + val + '</span>')
			 .parent('li')
				.height(a.children('span:first').outerHeight())
			 .end()
			 .children('span:first')
				.css('marginTop', 0) // strange for IE
		});
		
		spanHeight = lis.eq(0).height();
		
		lis.hover(function() {
			$(this).find('span:first').animate({
				marginTop : '-' + spanHeight
			}, { duration: 200, queue : false });
		}, function() {
			$(this).find('span:first').animate({
				marginTop : 0
			}, { duration: 200, queue: false });
		});
		
	});
