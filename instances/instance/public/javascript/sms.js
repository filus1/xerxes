/**
 * books record page
 *
 * @author David Walker <dwalker@calstate.edu>
 */
 
 $(document).ready(prepareSMS);

function prepareSMS()
{
	if ( $('#sms') )
	{
		$('#sms').hide();
		
		$('#sms-link').click(function() {
			return showSMS()
		});
		
		$('#sms-form').submit(function() {
			return checkSMSForm();
		});
				
		return false;
	}
}

function showSMS()
{	
	$('#sms').show();

	$('#sms-link').click(function() {
		return prepareSMS()
	});

	return false;
}

function checkSMSForm()
{
	var provider = document.smsForm.provider.value;
	var phone = document.smsForm.phone.value;
	
	if ( phone == '' )
	{
		alert('Please enter a phone number');
		return false;
	}
	
	if ( provider == '' )
	{
		alert('Please choose your cell phone provider');
		return false;
	}
	
	return true;
}
