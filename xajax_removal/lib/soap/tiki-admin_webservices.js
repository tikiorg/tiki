
/**
 * Switch between SOAP and REST
 */
function switchSoapRest() {
	switch ($(this).val()) {
		case 'SOAP':
			$('#ws_operation').show();
			$('#ws_postbody').hide();
			break;
		case 'REST':
			$('#ws_operation').hide();
			$('#ws_postbody').show();
			break;
	}
}

var wsType = $('select[name=wstype]');

wsType.live('change', switchSoapRest);
wsType.ready(function() {
	wsType.trigger('change');
});