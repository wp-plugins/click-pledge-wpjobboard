jQuery(document).ready(function($) {
	limitText(jQuery('#wpjobboard_clickandpledge_OrganizationInformation'),jQuery('#OrganizationInformation_countdown'),1500);	
	limitText(jQuery('#wpjobboard_clickandpledge_TermsCondition'),jQuery('#TermsCondition_countdown'),1500);
	displaycheck();
	recurringdisplay();
	
	jQuery('#wpjobboard_clickandpledge_usdaccount-USD').click(function(){
		displaycheck();
	});
	jQuery('#wpjobboard_clickandpledge_euraccount-EUR').click(function(){
		displaycheck();
	});
	jQuery('#wpjobboard_clickandpledge_cadaccount-CAD').click(function(){
		displaycheck();
	});
	jQuery('#wpjobboard_clickandpledge_gbpaccount-GBP').click(function(){
		displaycheck();
	});
	jQuery('#wpjobboard_clickandpledge_hkdaccount-HKD').click(function(){
		displaycheck();
	});
	function displaycheck() {
		//Accounts Display Check
		var account_enabled = 0;
		//USD
		if(jQuery('#wpjobboard_clickandpledge_usdaccount-USD').is(':checked')) {
			jQuery('#wpjobboard_clickandpledge_USD_AccountID').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_USD_AccountGuid').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_USD_OrderMode').closest('tr').show();
			account_enabled++;
		} else {
			jQuery('#wpjobboard_clickandpledge_USD_AccountID').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_USD_AccountGuid').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_USD_OrderMode').closest('tr').hide();
		}
		//EUR
		if(jQuery('#wpjobboard_clickandpledge_euraccount-EUR').is(':checked')) {
			jQuery('#wpjobboard_clickandpledge_EUR_AccountID').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_EUR_AccountGuid').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_EUR_OrderMode').closest('tr').show();
			account_enabled++;
		} else {
			jQuery('#wpjobboard_clickandpledge_EUR_AccountID').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_EUR_AccountGuid').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_EUR_OrderMode').closest('tr').hide();
		}
		//CAD
		if(jQuery('#wpjobboard_clickandpledge_cadaccount-CAD').is(':checked')) {
			jQuery('#wpjobboard_clickandpledge_CAD_AccountID').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_CAD_AccountGuid').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_CAD_OrderMode').closest('tr').show();
			account_enabled++;
		} else {
			jQuery('#wpjobboard_clickandpledge_CAD_AccountID').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_CAD_AccountGuid').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_CAD_OrderMode').closest('tr').hide();
		}
		//GBP
		if(jQuery('#wpjobboard_clickandpledge_gbpaccount-GBP').is(':checked')) {
			jQuery('#wpjobboard_clickandpledge_GBP_AccountID').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_GBP_AccountGuid').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_GBP_OrderMode').closest('tr').show();
			account_enabled++;
		} else {
			jQuery('#wpjobboard_clickandpledge_GBP_AccountID').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_GBP_AccountGuid').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_GBP_OrderMode').closest('tr').hide();
		}
		//HKD
		if(jQuery('#wpjobboard_clickandpledge_hkdaccount-HKD').is(':checked')) {
			jQuery('#wpjobboard_clickandpledge_HKD_AccountID').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_HKD_AccountGuid').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_HKD_OrderMode').closest('tr').show();
			account_enabled++;
		} else {
			jQuery('#wpjobboard_clickandpledge_HKD_AccountID').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_HKD_AccountGuid').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_HKD_OrderMode').closest('tr').hide();
		}
		if(account_enabled == 0) {
			jQuery('#wpjobboard_clickandpledge_usdaccount-USD').prop('checked',true);
			jQuery('#wpjobboard_clickandpledge_USD_AccountID').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_USD_AccountGuid').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_USD_OrderMode').closest('tr').show();
		}
		
		if(!jQuery('#wpjobboard_clickandpledge_Paymentmethods-CreditCard').is(':checked') && !jQuery('#wpjobboard_clickandpledge_Paymentmethods-eCheck').is(':checked')) {
			jQuery('.CredicardSection').next('table').hide();
			jQuery('.CredicardSection').hide();
				
			jQuery('.RecurringSection').next('table').hide();
			jQuery('.RecurringSection').hide();
		} else {
			if(jQuery('#wpjobboard_clickandpledge_Paymentmethods-CreditCard').is(':checked')) {
				jQuery('.CredicardSection').next('table').show();
				jQuery('.CredicardSection').show();
			} else {
				jQuery('.CredicardSection').next('table').hide();
				jQuery('.CredicardSection').hide();
			}
			
			if(jQuery('#wpjobboard_clickandpledge_Paymentmethods-CreditCard').is(':checked') || jQuery('#wpjobboard_clickandpledge_Paymentmethods-eCheck').is(':checked')) {
				jQuery('.RecurringSection').next('table').show();
				jQuery('.RecurringSection').show();
			}
		}
		defaultpayment();
	}
	function defaultpayment() {
		var paymethods = [];
		var paymethods_titles = [];
		var str = '';
		var defaultval = jQuery('#wpjobboard_clickandpledge_DefaultpaymentMethod').val();
		if(jQuery('#wpjobboard_clickandpledge_Paymentmethods-CreditCard').is(':checked')) {
			paymethods.push('CreditCard');
			paymethods_titles.push('Credit Card');
		}
		if(jQuery('#wpjobboard_clickandpledge_Paymentmethods-eCheck').is(':checked')) {
			paymethods.push('eCheck');
			paymethods_titles.push('eCheck');
		}
		if(jQuery('#wpjobboard_clickandpledge_Paymentmethods-Invoice').is(':checked')) {
			paymethods.push('Invoice');
			paymethods_titles.push('Invoice');
		}
		if(jQuery('#wpjobboard_clickandpledge_Paymentmethods-PurchaseOrder').is(':checked')) {
			paymethods.push('PurchaseOrder');
			paymethods_titles.push('Purchase Order');
		}
		if(paymethods.length > 0) {
			for(var i = 0; i < paymethods.length; i++) {
				if(paymethods[i] == defaultval) {
				str += '<option value="'+paymethods[i]+'" selected>'+paymethods_titles[i]+'</option>';
				} else {
				str += '<option value="'+paymethods[i]+'">'+paymethods_titles[i]+'</option>';
				}
			}
		} else {
		 str = '<option selected="selected" value="">Please select</option>';
		}
		jQuery('#wpjobboard_clickandpledge_DefaultpaymentMethod').html(str);
	}
	function limitText(limitField, limitCount, limitNum) {
		if (limitField.val().length > limitNum) {
			limitField.val( limitField.val().substring(0, limitNum) );
		} else {
			limitCount.html (limitNum - limitField.val().length);
		}
	}
	jQuery('#wpjobboard_clickandpledge_OrganizationInformation').keydown(function(){
		limitText(jQuery('#wpjobboard_clickandpledge_OrganizationInformation'),jQuery('#OrganizationInformation_countdown'),1500);
	});
	jQuery('#wpjobboard_clickandpledge_OrganizationInformation').keyup(function(){
		limitText(jQuery('#wpjobboard_clickandpledge_OrganizationInformation'),jQuery('#OrganizationInformation_countdown'),1500);
	});
	
	jQuery('#wpjobboard_clickandpledge_Paymentmethods-CreditCard').click(function(){
		if(jQuery('#wpjobboard_clickandpledge_Paymentmethods-CreditCard').is(':checked')) {
			jQuery('.CredicardSection').next('table').show();
			jQuery('.CredicardSection').show();
		} else {
			jQuery('.CredicardSection').next('table').hide();
			jQuery('.CredicardSection').hide();
		}
		
		if(!jQuery('#wpjobboard_clickandpledge_Paymentmethods-CreditCard').is(':checked') && !jQuery('#wpjobboard_clickandpledge_Paymentmethods-eCheck').is(':checked')) {
			jQuery('.RecurringSection').next('table').hide();
			jQuery('.RecurringSection').hide();
		} else {
			if(jQuery('#wpjobboard_clickandpledge_Paymentmethods-CreditCard').is(':checked') || jQuery('#wpjobboard_clickandpledge_Paymentmethods-eCheck').is(':checked')) {
				jQuery('.RecurringSection').next('table').show();
				jQuery('.RecurringSection').show();
			}
		}
		defaultpayment();
	});
	jQuery('#wpjobboard_clickandpledge_Paymentmethods-eCheck').click(function(){
		if(!jQuery('#wpjobboard_clickandpledge_Paymentmethods-CreditCard').is(':checked') && !jQuery('#wpjobboard_clickandpledge_Paymentmethods-eCheck').is(':checked')) {
			jQuery('.RecurringSection').next('table').hide();
			jQuery('.RecurringSection').hide();
		} else {
			if(jQuery('#wpjobboard_clickandpledge_Paymentmethods-eCheck').is(':checked') || jQuery('#wpjobboard_clickandpledge_Paymentmethods-eCheck').is(':checked')) {
				jQuery('.RecurringSection').next('table').show();
				jQuery('.RecurringSection').show();
			} else {
				jQuery('.RecurringSection').next('table').hide();
				jQuery('.RecurringSection').hide();
			}
		}
		defaultpayment();
	});				
	jQuery('#wpjobboard_clickandpledge_Paymentmethods-Invoice').click(function(){
		defaultpayment();
	});
	jQuery('#wpjobboard_clickandpledge_Paymentmethods-PurchaseOrder').click(function(){
		defaultpayment();
	});
	//TermsCondition
	jQuery('#wpjobboard_clickandpledge_TermsCondition').keydown(function(){
		limitText(jQuery('#wpjobboard_clickandpledge_TermsCondition'),jQuery('#TermsCondition_countdown'),1500);
	});
	jQuery('#wpjobboard_clickandpledge_TermsCondition').keyup(function(){
		limitText(jQuery('#wpjobboard_clickandpledge_TermsCondition'),jQuery('#TermsCondition_countdown'),1500);
	});
	function recurringdisplay() {		
		if(jQuery('#wpjobboard_clickandpledge_isRecurring').val() == 1) {
			jQuery('#wpjobboard_clickandpledge_Periodicity').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_RecurringLabel').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_Periodicity-Week').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_Periodicity-2 Weeks').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_Periodicity-Month').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_Periodicity-2 Months').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_Periodicity-Quarter').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_Periodicity-6 Months').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_Periodicity-Year').closest('tr').show();
				
			jQuery('#wpjobboard_clickandpledge_RecurringMethod').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_RecurringMethod_Installment-Installment').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_RecurringMethod_Subscription-Subscription').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').closest('tr').show();							
			jQuery('#wpjobboard_clickandpledge_indefinite-on').closest('tr').show();
			
			if(jQuery('#wpjobboard_clickandpledge_RecurringMethod_Installment-Installment').is(':checked')) {
				jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').closest('tr').show();
				jQuery('#wpjobboard_clickandpledge_indefinite-on').closest('tr').hide();
			} else {
				jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').closest('tr').hide();
			}
			
			if(jQuery('#wpjobboard_clickandpledge_RecurringMethod_Subscription-Subscription').is(':checked')) {
				jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').closest('tr').show();
				jQuery('#wpjobboard_clickandpledge_indefinite-on').closest('tr').show();				
			} else {
				jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').closest('tr').hide();
			}
		} else {
			jQuery('#wpjobboard_clickandpledge_Periodicity').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_RecurringLabel').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_Periodicity-Week').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_Periodicity-2 Weeks').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_Periodicity-Month').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_Periodicity-2 Months').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_Periodicity-Quarter').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_Periodicity-6 Months').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_Periodicity-Year').closest('tr').hide();
				
			jQuery('#wpjobboard_clickandpledge_RecurringMethod').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_RecurringMethod_Installment-Installment').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_RecurringMethod_Subscription-Subscription').closest('tr').hide();
			jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').closest('tr').hide();							
			jQuery('#wpjobboard_clickandpledge_indefinite-on').closest('tr').hide();						
		}
	}


	jQuery('#wpjobboard_clickandpledge_RecurringMethod_Installment-Installment').click(function(){
		if(jQuery('#wpjobboard_clickandpledge_RecurringMethod_Installment-Installment').is(':checked')) {
			jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').closest('tr').show();
			jQuery('#wpjobboard_clickandpledge_indefinite-on').attr('checked', false);
		} else {
			jQuery('#wpjobboard_clickandpledge_indefinite-on').attr('checked', false);
			jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').closest('tr').hide();			
		}
		indefinite_display();
	});


	jQuery('#wpjobboard_clickandpledge_RecurringMethod_Subscription-Subscription').click(function(){
		if(jQuery('#wpjobboard_clickandpledge_RecurringMethod_Subscription-Subscription').is(':checked')) {
			jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').closest('tr').show();
		} else {
		jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').closest('tr').hide();
		}
		indefinite_display();
	});
	function indefinite_display() {
		if(jQuery('#wpjobboard_clickandpledge_RecurringMethod_Subscription-Subscription').is(':checked')) {
			jQuery('#wpjobboard_clickandpledge_indefinite-on').closest('tr').show();
		} else {
			jQuery('#wpjobboard_clickandpledge_indefinite-on').attr('checked', false);
			jQuery('#wpjobboard_clickandpledge_indefinite-on').closest('tr').hide();			
		}
	}
	jQuery('#wpjobboard_clickandpledge_isRecurring').change(function(){
		recurringdisplay();
	});
	
	function isInt(n) {
		return n % 1 === 0;
	}
	
	$("form").submit(function(event) {        
		var enabledisable = 0;
		if(jQuery('#disabled-0').is(':checked'))
		enabledisable++;
		if(jQuery('#disabled-1').is(':checked'))
		enabledisable++;
		if(enabledisable == 0) {
			alert('Please select Availability');
			jQuery('#disabled-1').focus();
			return false;
		}
		if(jQuery('#title').val() == '')
		{
			alert('Please enter title');
			jQuery('#title').focus();
			return false;
		}
		
		if(jQuery('#wpjobboard_clickandpledge_usdaccount-USD').is(':checked')) {
			if(jQuery('#wpjobboard_clickandpledge_USD_AccountID').val() == '')
			{
				alert('Please enter USD Account ID');
				jQuery('#wpjobboard_clickandpledge_USD_AccountID').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_USD_AccountID').val().length > 10)
			{
				alert('Please enter only 10 digits for USD Account ID');
				jQuery('#wpjobboard_clickandpledge_USD_AccountID').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_USD_AccountGuid').val() == '')
			{
				alert('Please enter USD Account GUID');
				jQuery('#wpjobboard_clickandpledge_USD_AccountGuid').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_USD_AccountGuid').val().length != 36)
			{
				alert('Account GUID should be 36 characters USD Account');
				jQuery('#wpjobboard_clickandpledge_USD_AccountGuid').focus();
				return false;
			}
		}
		
		//EUR
		if(jQuery('#wpjobboard_clickandpledge_euraccount-EUR').is(':checked')) {
			if(jQuery('#wpjobboard_clickandpledge_EUR_AccountID').val() == '')
			{
				alert('Please enter EUR Account ID');
				jQuery('#wpjobboard_clickandpledge_EUR_AccountID').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_EUR_AccountID').val().length > 10)
			{
				alert('Please enter only 10 digits for EUR Account ID');
				jQuery('#wpjobboard_clickandpledge_EUR_AccountID').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_EUR_AccountGuid').val() == '')
			{
				alert('Please enter EUR Account GUID');
				jQuery('#wpjobboard_clickandpledge_EUR_AccountGuid').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_EUR_AccountGuid').val().length != 36)
			{
				alert('Account GUID should be 36 characters EUR Account');
				jQuery('#wpjobboard_clickandpledge_EUR_AccountGuid').focus();
				return false;
			}
		}
		
		//CAD
		if(jQuery('#wpjobboard_clickandpledge_cadaccount-CAD').is(':checked')) {
			if(jQuery('#wpjobboard_clickandpledge_CAD_AccountID').val() == '')
			{
				alert('Please enter CAD Account ID');
				jQuery('#wpjobboard_clickandpledge_CAD_AccountID').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_CAD_AccountID').val().length > 10)
			{
				alert('Please enter only 10 digits for CAD Account ID');
				jQuery('#wpjobboard_clickandpledge_CAD_AccountID').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_CAD_AccountGuid').val() == '')
			{
				alert('Please enter CAD Account GUID');
				jQuery('#wpjobboard_clickandpledge_CAD_AccountGuid').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_CAD_AccountGuid').val().length != 36)
			{
				alert('Account GUID should be 36 characters CAD Account');
				jQuery('#wpjobboard_clickandpledge_CAD_AccountGuid').focus();
				return false;
			}
		}

		//GBP
		if(jQuery('#wpjobboard_clickandpledge_gbpaccount-GBP').is(':checked')) {
			if(jQuery('#wpjobboard_clickandpledge_GBP_AccountID').val() == '')
			{
				alert('Please enter GBP Account ID');
				jQuery('#wpjobboard_clickandpledge_GBP_AccountID').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_GBP_AccountID').val().length > 10)
			{
				alert('Please enter only 10 digits for GBP Account ID');
				jQuery('#wpjobboard_clickandpledge_GBP_AccountID').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_GBP_AccountGuid').val() == '')
			{
				alert('Please enter GBP Account GUID');
				jQuery('#wpjobboard_clickandpledge_GBP_AccountGuid').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_GBP_AccountGuid').val().length != 36)
			{
				alert('Account GUID should be 36 characters for GBP Account');
				jQuery('#wpjobboard_clickandpledge_GBP_AccountGuid').focus();
				return false;
			}
		}

		//HKD
		if(jQuery('#wpjobboard_clickandpledge_hkdaccount-HKD').is(':checked')) {
			if(jQuery('#wpjobboard_clickandpledge_HKD_AccountID').val() == '')
			{
				alert('Please enter HKD Account ID');
				jQuery('#wpjobboard_clickandpledge_HKD_AccountID').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_HKD_AccountID').val().length > 10)
			{
				alert('Please enter only 10 digits for HKD Account');
				jQuery('#wpjobboard_clickandpledge_HKD_AccountID').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_HKD_AccountGuid').val() == '')
			{
				alert('Please enter Account GUID for HKD Account');
				jQuery('#wpjobboard_clickandpledge_HKD_AccountGuid').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_HKD_AccountGuid').val().length != 36)
			{
				alert('Account GUID should be 36 characters for HKD Account');
				jQuery('#wpjobboard_clickandpledge_HKD_AccountGuid').focus();
				return false;
			}
		}		
		
		
		var paymethods = 0;
		if(jQuery('#wpjobboard_clickandpledge_Paymentmethods-CreditCard').is(':checked'))
		{
			paymethods++;
		}
		if(jQuery('#wpjobboard_clickandpledge_Paymentmethods-eCheck').is(':checked'))
		{
			paymethods++;
		}
		if(jQuery('#wpjobboard_clickandpledge_Paymentmethods-Invoice').is(':checked'))
		{
			paymethods++;
		}
		if(jQuery('#wpjobboard_clickandpledge_Paymentmethods-PurchaseOrder').is(':checked'))
		{
			paymethods++;
		}
		
		if(paymethods == 0) {
			alert('Please select at least  one payment method');
			jQuery('#wpjobboard_clickandpledge_Paymentmethods-CreditCard').focus();
			return false;
		}	
		
		if(jQuery('#wpjobboard_clickandpledge_isRecurring').val() == 1)
		{
			if(jQuery('#wpjobboard_clickandpledge_RecurringLabel').val() == '')
			{
			alert('Please enter Label');
			jQuery('#wpjobboard_clickandpledge_RecurringLabel').focus();
			return false;
			}
		}
		
		if(jQuery('#wpjobboard_clickandpledge_RecurringMethod_Installment-Installment').is(':checked') && jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').val() != '')
		{
			if(!jQuery.isNumeric((jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').val())))
			{
				alert('Please enter valid number. It will allow numbers only');
				jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').focus();
				return false;
			}
			if(!isInt(jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').val()))
			{
				alert('Please enter integer values only');
				jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').val() < 2)
			{
				alert('Please enter value greater than 1');
				jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').val() > 998)
			{
				alert('Please enter value between 2-998');
				jQuery('#wpjobboard_clickandpledge_maxrecurrings_Installment').focus();
				return false;
			}
		}
		
		if(jQuery('#wpjobboard_clickandpledge_RecurringMethod_Subscription-Subscription').is(':checked') && jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').val() != '')
		{
			if(!jQuery.isNumeric((jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').val())))
			{
			alert('Please enter valid number. It will allow numbers only');
			jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').focus();
			return false;
			}
			
			if(!isInt(jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').val()))
			{
				alert('Please enter integer values only');
				jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').focus();
				return false;
			}
			
			if(jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').val() < 2)
			{
				alert('Please enter Subscription Max. Recurrings Allowed greater than 1');
				jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').focus();
				return false;
			}
			if(jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').val() > 999)
			{
				alert('Please enter Subscription Max. Recurrings Allowed between 2-999');
				jQuery('#wpjobboard_clickandpledge_maxrecurrings_Subscription').focus();
				return false;
			}
		}
    });
});