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


}); //define