$(document).ready(function () {
	var skrillUrl = new URL(document.URL);
	var skrillSettingType = skrillUrl.searchParams.get('action');

	var url = '/skrill/settings/'+skrillSettingType+'/';
	$('#loader').show();
	$.ajax({
		url : url
	}).done(function (r){
		$('skrill-ui').html(r);
		$('#loader').hide();
		saveSettings();
	});

	function saveSettings(){
		$('#saveSettings').submit(function(e) {
			$('#loader').show();
			e.preventDefault();
			var saveUrl = '/skrill/settings/save';
			$.ajax({
				url : saveUrl,
				type: 'post',
				data: $(this).serialize()
			}).done(function(r){
				$('#loader').hide();
				if (r == 'success') {
					$('#successMessage').css('display', 'block');
				} else {
					$('#errorMessage').css('display', 'block');
				}
				setTimeout(removeMessage, 5000);
			});
		});
	}

	function removeMessage() {
		if ($('#successMessage').is(":visible")) {
			$('#successMessage').css('display', 'none');
		}
		if ($('#errorMessage').is(":visible")) {
			$('#errorMessage').css('display', 'none');
		}
	}
})