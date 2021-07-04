$(document).ready(function () {
	var skrillUrl = new URL(document.URL);
	var skrillSettingType = skrillUrl.searchParams.get('action');

	var url = '/skrill/settings/'+skrillSettingType+'/';
	$.ajax({
		url : url
	}).done(function (r){
		$('skrill-ui').html(r);
		saveSettings();
	});

	function saveSettings(){
		$('#saveSettings').submit(function(e) {
			e.preventDefault();
			var saveUrl = '/skrill/settings/save';
			$.ajax({
				url : saveUrl,
				type: 'post',
				data: $(this).serialize()
			}).done(function(r){
				if (r == 'success') {
					$('#successMessage').css('display', 'block');
				} else if (r == 'error') {
					$('#errorMessage').css('display', 'block');
				} else {
					$('#errorMessageInvalidCredentials').css('display', 'block');
				}
			});
		});
	}
})