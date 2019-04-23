function jsUcfirst(string)
{
	return string.charAt(0).toUpperCase() + string.slice(1);
}
function changeLanguage(language) {
	document.getElementById('skrillPaymentNameEn').style.display = 'none';
	document.getElementById('skrillPaymentNameDe').style.display = 'none';
	document.getElementById('skrillPaymentName'+jsUcfirst(language)).style.display = 'block';
}

var shopClientList = document.querySelectorAll('.shop-client-item');
[].forEach.call(shopClientList, function(elm){
	$(elm).click(function() {
		$('#loader').show();
		var settingType = $(this).attr('setting-type');
		var clientId = $(this).attr('client-id');
		$(shopClientList).removeClass('active');
		$(this).addClass('active');
		$.ajax({
			url : '/skrill/configuration/'+clientId+'/'+settingType+'/',
			dataType : 'json'
		}).done(function(r){
			if (r.settings) {
				var settings = r.settings;
				if (r.settingType == 'skrill_general') {
					$('[name=logoUrl]').val(settings.logoUrl);
					$('[name=merchantAccount]').val(settings.merchantAccount);
					$('[name=merchantEmail]').val(settings.merchantEmail);
					$('[name=merchantId]').val(settings.merchantId);
					$('[name=display]').val(settings.display);
					$('[name=recipient]').val(settings.recipient);
					$('[name=shopUrl]').val(settings.shopUrl);
				} else {
					$('[name=languageEnPaymentName]').val(settings.language.en.paymentName);
					$('[name=languageDePaymentName]').val(settings.language.de.paymentName);
					if (settings.enabled == '1') {
						$("input#enabled").attr('checked', 'checked');
					}

					if (settings.showSeparately == '1') {
						$("input#showSeparately").attr('checked','checked');
					}
				}
			} else {
				resetValue(r.settingType);
			}
			$('[name=plentyId]').val(r.plentyId);
			$('#skrillSettings').show();
			$('#loader').hide();
		});
	});
});

function resetValue(settingType) {
	if (settingType == 'skrill_general') {
		$('[name=logoUrl]').val('');
		$('[name=merchantAccount]').val('');
		$('[name=merchantEmail]').val('');
		$('[name=merchantId]').val('');
		$('[name=display]').val('');
		$('[name=recipient]').val('');
		$('[name=shopUrl]').val('');
	} else {
		$('[name=languageEnPaymentName]').val('');
		$('[name=languageDePaymentName]').val('');
		$('input#enabled').removeAttr('checked');
		$('input#showSeparately').removeAttr('checked');
	}
}