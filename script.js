require(['jquery'], function ($) {

$(function(){
	$("[data-toggle=popover]").popover({
		html : true, 
		placement: 'bottom',
		trigger:'click',
		content: function() {
			return $('div', this).html();
		},
		title: function() { return ''; }
	});
});

$(function(){
	// folding package versions
	$(".package-label").on('click', function(e) {
		var pv = $(this).closest('tr').nextUntil('tr.package, tr.alert-info');
		console.log(pv);
		if( !pv.is(':visible') ) { //show
			$('.glyphicon', this)
				.removeClass('glyphicon-chevron-right')
				.addClass('glyphicon-chevron-down');
			pv.show();
		} else { // hide
			$('.glyphicon', this)
				.removeClass('glyphicon-chevron-down')
				.addClass('glyphicon-chevron-right');
			pv.hide();
		}
	});
});


}); //define