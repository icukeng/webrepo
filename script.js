function doDraggable(ctx) {
	$( ".draggable", ctx ).each(function () {
		var tr = $(this).closest('tr').prevAll('.package:first').andSelf().filter('.package').last();
		$( this ).draggable({
			helper: "clone",
			cursor: "move",
			scope: $('.package-label', tr).data('name')
		});
	});
}

function doDroppable(ctx) {
	$( ".droppable", ctx ).each(function () {
		$( this ).droppable({
			activeClass: "ui-state-active",
			hoverClass: "ui-state-hover",
			scope: $(this).closest('tr').find('.package-label').data('name'),
			drop: function( event, ui ) {
				var rcv = this.cellIndex;
				var trx = ui.draggable.closest('td').get(0).cellIndex;

				var tr = $(this).closest('tr').prevAll('.package:first').andSelf().filter('.package').last();

				var pkg  = tr.find('.package-label').data('name');
				var vers = ui.draggable.data('version');
				var th = $('table.webrepo-table-striped th');
				$.post('/copy/', {
					rx: $(th[rcv]).data('repo'),
					tx: $(th[trx]).data('repo'),
					pkg: pkg,
					vers: vers
				}, function(data, status) {
					window.location.reload();
				})
			}
		});
	});
}

require(['jquery', 'jquery-ui'], function ($) {

var getExecution = function(el) {
	$.ajax({
		url: '/execution',
		method: 'POST',
		dataType: 'JSON',
		data: {name: $(el).data('name')}
	}).done(function() {
		getStatus(el);
	}).fail(function() {
		console.log('fail');
	});
};

var getStatus = function (el) {
	$.ajax({
		url: '/status',
		method: 'GET',
		dataType: 'JSON',
		data: {name: $(el).data('name')}
	}).done(function(data) {
		// check for finishing command execution
		if(data === 'true') {
			$('.cssload-container[data-name=' + $(el).data('name') + ']').hide();
			$('.build[data-name=' + $(el).data('name') + ']').show();
		} else {
			$('.build[data-name=' + $(el).data('name') + ']').hide();
			$('.cssload-container[data-name=' + $(el).data('name') + ']').show();
		}
		// specify shell-body by id
		$console = $(document.getElementById($(el).data('name')));
		// retrieve shell-body with required name
		$console.empty();
		$.each(data, function(k, v) {
			$console.append("<li>" + v + "</li>");
		});
		$console.show();
		// status polling
		setTimeout(getStatus.bind(null, el), 2000);
	});
};

$(function(){
	$('.build').on('click', function(e) {
		var el = e.target;
		// specify element by id
		$('.shell-body').attr('id', $(el).data('name'));
		$('#modal-execution').on('shown.bs.modal', function () {
			$('.modal-title').html($(el).data('name'));
			getExecution(this);
		}.apply(this, el));
	});
	$('.cssload-container').on('click', function(e) {
		// specify element by id
		$('.shell-body').attr('id', $(this).data('name'))
	});
	$(".popover-run").popover({
		html : true,
		placement: 'bottom',
		trigger:'click',
		content: function() {
			return $(this).siblings('.popover').html();
		},
		title: function() { return ''; }
	});
	$(".popover-run").on('shown.bs.popover', function () {
		var id = $(this).attr('aria-describedby');
		doDraggable( $(this).siblings('#'+id) );
	})
});

$(function(){
	// folding package versions
	$(".package-label").on('click', function(e) {
		var pv = $(this).closest('tr').nextUntil('tr.package, tr.alert-info');
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

$(function() {
	doDraggable(document);
	doDroppable(document);
});

}); //define