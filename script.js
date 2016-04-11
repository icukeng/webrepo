require(['jquery'], function ($) {

var getExecution = function(el) {
	console.log(el);
	$.ajax({
		url: $(el).data('url') + '/execution',
		method: 'POST',
		dataType: 'JSON',
		data: {name: $(el).data('name')}
	}).done(function() {
		console.log('2nd ajax');
		getStatus(el);
	}).fail(function() {
		console.log('fail');
	});
};

var getStatus = function (el) {
	$.ajax({
		url: $(el).data('url') + '/status',
		method: 'GET',
		dataType: 'JSON'
	}).done(function(data) {
		console.log('done');
		$console = $('.shell-body');
		$console.empty();
		$.each(data, function(k, v) {
			$console.append("<li>" + v + "</li>");
		});
		$console.show();
		// stop making requests, when modal is closed
		if($('#modal-execution').is(":visible")) {
			setTimeout(getStatus.bind(null, el), 2000);
		}
	});
};

$(function(){
	$('.build').on('click', function(e) {
		var el = e.target;
		$('#modal-execution').on('shown.bs.modal', function () {
			$('.shell-top-bar').html($(el).data('name'));
			getExecution(this);
		}.apply(this, el));
	});
	/*$("[data-toggle=popover]").popover({
		html : true, 
		placement: 'bottom',
		trigger:'click',
		content: function() {
			return $('div', this).html();
		},
		title: function() { return ''; }
	});*/
});


}); //define