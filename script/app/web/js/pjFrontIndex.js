$(function(){
	var $host = window.location.origin + "/script";
	var $form = $('#foodkonnekt-form');
	
	$('#datepicker').datepicker({
		minDate: 0,
		dateFormat: 'dd-mm-yy'
	});
	
	$(window).load(function(){
		var $location_id = parseInt($('#location_id').val());
		if($.isNumeric($location_id))
		{
			params = {'id': $location_id};
			$.get([$host + '/index.php?controller=pjFront&action=pjActionGetLocation'].join(""), params).done(function(data){
				$('#fdPickupAddressLabel').html(data.address);
				$('#fdPickupAddressText').val(data.address);
				$('#time-picker').html('Please choose a date.');
			});	
		}
	});
	
	$form.on('submit', function(e){
		var $lc = $('#location_id').val();
		var $date = $('#datepicker').val();
		if($date == '') {
			$('#datepicker').addClass('error');
			e.preventDefault();
		}
		if($lc == ''){
			$('#location_id').addClass('error');
			e.preventDefault();
		}
	}).on('change', '#location_id', function(e) {
		if( e && e.preventDefault ){
			e.preventDefault();
		}
		params = {'id': $(this).val()};
		$.get([$host + '/index.php?controller=pjFront&action=pjActionGetLocation'].join(""), params).done(function(data){
			$('#fdPickupAddressLabel').html(data.address);
			$('#fdPickupAddressText').val(data.address);
			$('#time-picker').html('Please choose a date.');
		});		
	}).on('change', '#datepicker', function(e) {
		if( e && e.preventDefault ){
			e.preventDefault();
		}		
		params = {
			"date": $(this).val(),
			"location_id": $('#location_id').val(),
			"type": 'pickup',
			"index": '1234'
		};
		$.post([$host + '/index.php?controller=pjFrontLayouts&action=pjActionGetWTime'].join(""), params).done(function(data){
			$('#time-picker').html(data);
			$('#time-picker').find('select').addClass('form-control time');
		});
	});
});