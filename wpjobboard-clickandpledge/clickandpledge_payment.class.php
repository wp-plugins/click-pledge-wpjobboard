<?php

//include_once dirname(__FILE__).'/clickandpledge_common.inc';
require_once dirname(__FILE__).'/clickandpledge_admin_controll.php';
class Payment_ClickandPledge extends Wpjb_Payment_Abstract 
{
	 var $responsecodes = array();
     public function __construct(Wpjb_Model_Payment $data = null) 
     {
       $this->_data = $data;
	   add_action( 'wp_ajax_clickandpledge_transaction', array( __CLASS__, 'clickandpledge_transaction' ) );
	   add_action( 'wp_ajax_nopriv_clickandpledge_transaction', array( __CLASS__, 'clickandpledge_transaction' ) );
	   
	   $this->responsecodes = array(2054=>'Total amount is wrong',2055=>'AccountGuid is not valid',2056=>'AccountId is not valid',2057=>'Username is not valid',2058=>'Password is not valid',2059=>'Invalid recurring parameters',2060=>'Account is disabled',2101=>'Cardholder information is null',2102=>'Cardholder information is null',2103=>'Cardholder information is null',2104=>'Invalid billing country',2105=>'Credit Card number is not valid',2106=>'Cvv2 is blank',2107=>'Cvv2 length error',2108=>'Invalid currency code',2109=>'CreditCard object is null',2110=>'Invalid card type ',2111=>'Card type not currently accepted',2112=>'Card type not currently accepted',2210=>'Order item list is empty',2212=>'CurrentTotals is null',2213=>'CurrentTotals is invalid',2214=>'TicketList lenght is not equal to quantity',2215=>'NameBadge lenght is not equal to quantity',2216=>'Invalid textonticketbody',2217=>'Invalid textonticketsidebar',2218=>'Invalid NameBadgeFooter',2304=>'Shipping CountryCode is invalid',2305=>'Shipping address missed',2401=>'IP address is null',2402=>'Invalid operation',2501=>'WID is invalid',2502=>'Production transaction is not allowed. Contact support for activation.',2601=>'Invalid character in a Base-64 string',2701=>'ReferenceTransaction Information Cannot be NULL',2702=>'Invalid Refrence Transaction Information',2703=>'Expired credit card',2805=>'eCheck Account number is invalid',2807=>'Invalid payment method',2809=>'Invalid payment method',2811=>'eCheck payment type is currently not accepted',2812=>'Invalid check number',1001=>'Internal error. Retry transaction',1002=>'Error occurred on external gateway please try again',2001=>'Invalid account information',2002=>'Transaction total is not correct',2003=>'Invalid parameters',2004=>'Document is not a valid xml file',2005=>'OrderList can not be empty',3001=>'Invalid RefrenceTransactionID',3002=>'Invalid operation for this transaction',4001=>'Fraud transaction',4002=>'Duplicate transaction',5001=>'Declined (general)',5002=>'Declined (lost or stolen card)',5003=>'Declined (fraud)',5004=>'Declined (Card expired)',5005=>'Declined (Cvv2 is not valid)',5006=>'Declined (Insufficient fund)',5007=>'Declined (Invalid credit card number)');
     }
	 function get_user_ip() {
		$ipaddress = '';
		 if (isset($_SERVER['HTTP_CLIENT_IP']))
			 $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		 else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			 $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		 else if(isset($_SERVER['HTTP_X_FORWARDED']))
			 $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		 else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			 $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		 else if(isset($_SERVER['HTTP_FORWARDED']))
			 $ipaddress = $_SERVER['HTTP_FORWARDED'];
		 else
			 $ipaddress = $_SERVER['REMOTE_ADDR'];		
		$parts = explode(',', $ipaddress);
        if(count($parts) > 1) $ipaddress = $parts[0];
		 return $ipaddress; 
	}
	
	function safeString( $str,  $length=1, $start=0 )
	{
		$str = preg_replace('/\x03/', '', $str); //Remove new line characters
		return substr( htmlspecialchars( ( $str ) ), $start, $length );
	}
	
	  public function clickandpledge_transaction(){
		$post = $_POST;
		global $wpdb;
		$orderplacedcheck = $wpdb->get_row( 'SELECT * FROM '.$wpdb->prefix.'wpjb_payment WHERE id = '.$post['id'], OBJECT );
		if($orderplacedcheck) {
		$post['object_id'] = $orderplacedcheck->object_id;
		$post['object_type'] = $orderplacedcheck->object_type;
		
		$classObject = new Payment_ClickandPledge();		
		$strParam =  $classObject->buildXML($post);
		$connect = array('soap_version' => SOAP_1_1, 'trace' => 1, 'exceptions' => 0);
		 $client = new SoapClient('https://paas.cloud.clickandpledge.com/paymentservice.svc?wsdl', $connect);
		 $soapParams = array('instruction'=>$strParam);		 
		 $response = $client->Operation($soapParams);
		 $params = array();
		 if (($response === FALSE)) {			
			$params['error'] = 'Connection to payment gateway failed - no data returned.';
			$params['ResultCode'] = '99999';
			$params['status'] = 'Fail';
		} else {	
			$ResultCode=$response->OperationResult->ResultCode;
			$transation_number=$response->OperationResult->TransactionNumber;
			$VaultGUID=$response->OperationResult->VaultGUID;
			
			if($ResultCode=='0')
			{
				$response_message = $response->OperationResult->ResultData;
				//Success
				$params['TransactionNumber'] = $VaultGUID;
				$params['trxn_result_code'] = $response_message;
				$params['status'] = 'Success';
				$params['ResultCode'] = $ResultCode;				
			}
			else
			{
				if( in_array( $ResultCode, array( 2051,2052,2053 ) ) )
				{
					$AdditionalInfo = $response->OperationResult->AdditionalInfo;
				}
				else
				{
					if( isset( $classObject->responsecodes[$ResultCode] ) )
					{
						$AdditionalInfo = $classObject->responsecodes[$ResultCode];
					}
					else
					{
						$AdditionalInfo = 'Unknown error';
					}
				}
				$params['error'] = $AdditionalInfo;
				$params['ResultCode'] = $ResultCode;
				$params['status'] = 'Fail';			
			}
			}
			echo json_encode($params);
		die();
		}		
	  }
	  
	  public function buildXML( $post ) {
		$params = $post;
		
		$dom = new DOMDocument('1.0', 'UTF-8');
		$root = $dom->createElement('CnPAPI', '');
		$root->setAttribute("xmlns","urn:APISchema.xsd");
		$root = $dom->appendChild($root);

		$version=$dom->createElement("Version","1.5");
		$version=$root->appendChild($version);

		$engine = $dom->createElement('Engine', '');
		$engine = $root->appendChild($engine);

		$application = $dom->createElement('Application','');
		$application = $engine->appendChild($application);

		$applicationid=$dom->createElement('ID','CnP_WPJobBoard_WordPress');
		$applicationid=$application->appendChild($applicationid);

		$applicationname=$dom->createElement('Name','CnP_WPJobBoard_WordPress');
		$applicationid=$application->appendChild($applicationname);

		$applicationversion=$dom->createElement('Version','1.0.0.20150604');
		$applicationversion=$application->appendChild($applicationversion);

		$request = $dom->createElement('Request', '');
		$request = $engine->appendChild($request);

		$operation=$dom->createElement('Operation','');
		$operation=$request->appendChild( $operation );

		$operationtype=$dom->createElement('OperationType','Transaction');
		$operationtype=$operation->appendChild($operationtype);
		
		if($this->get_user_ip() != '') {
		$ipaddress=$dom->createElement('IPAddress',$this->get_user_ip());
		$ipaddress=$operation->appendChild($ipaddress);
		}
		
		$httpreferrer=$dom->createElement('UrlReferrer',htmlentities($_SERVER['HTTP_REFERER']));
		$httpreferrer=$operation->appendChild($httpreferrer);
		
		$authentication=$dom->createElement('Authentication','');
		$authentication=$request->appendChild($authentication);

		$accounttype=$dom->createElement('AccountGuid',$params['clickandpledge_AccountGuid'] ); 
		$accounttype=$authentication->appendChild($accounttype);
		
		$accountid=$dom->createElement('AccountID',$params['clickandpledge_AccountID'] );
		$accountid=$authentication->appendChild($accountid);
				 
		$order=$dom->createElement('Order','');
		$order=$request->appendChild($order);

		if( $params['clickandpledge_OrderMode'] == 'test' ){
		$orderMode = 'Test';
		}else{		
		$orderMode = 'Production';
		}
		$ordermode=$dom->createElement('OrderMode',$orderMode);
		$ordermode=$order->appendChild($ordermode);
				
		global $wpdb;
		$paymentdetails = $wpdb->get_row( 'SELECT * FROM '.$wpdb->prefix.'wpjb_payment WHERE id = '.$params['id'], OBJECT );
		//Object Type:1-wpjb_job, 2 - wpjb_resume, 3 - wpjb_membership
		$orderdetails = array();
		$orderdetails['BillingEmail'] = $paymentdetails->email;
		$orderdetails['CustomFields'] = array();
		$order_custom_fields = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'wpjb_meta meta INNER JOIN  '.$wpdb->prefix.'wpjb_meta_value meta_value ON meta.id=meta_value.meta_id WHERE meta.meta_type=3 AND meta_value.object_id = '.$params['object_id'].' ORDER BY meta_value.meta_id ASC', OBJECT );
		if(count($order_custom_fields) > 0) {
			$oldname = $strval = '';
			$fieldindex = 0;
			foreach($order_custom_fields as $single_row) {				
				$fieldindex++;
				if($oldname == '') $oldname = $single_row->name;
				if($oldname != $single_row->name) {
					$orderdetails['CustomFields'][$oldname] = substr($strval,0,-1);
					$strval = '';
				} 
				if(count($order_custom_fields) == $fieldindex) {
					$orderdetails['CustomFields'][$single_row->name] = $single_row->value;
				}				
				$strval .= $single_row->value.',';
				$oldname = $single_row->name;								
			}
		}
		if(isset($post['clickandpledge_listing_id']) && $post['clickandpledge_listing_id'] != '') {
			$parts = explode('_', $post['clickandpledge_listing_id']);
			if(count($parts) == 3) {
				$listid = $parts[2];
				if($listid != '') {
					$listrow = $wpdb->get_row( 'SELECT * FROM '.$wpdb->prefix.'wpjb_pricing WHERE id = '.$listid, OBJECT );
					if($listrow != '') {
						$orderdetails['CustomFields']['Listing Type'] = $listrow->title;
					}
				}
			}
		}
		if($params['object_type'] == 1) {
			$orderplaced = $wpdb->get_row( 'SELECT * FROM '.$wpdb->prefix.'wpjb_job WHERE id = '.$params['object_id'], OBJECT );
			$orderdetails['ItemName'] = 'Job: '.$orderplaced->job_title;
			if($orderplaced->job_description != '')
			$orderdetails['CustomFields']['Description'] = $orderplaced->job_description;
			if($orderplaced->job_country != '') {
				$countries = Wpjb_List_Country::getAll();
				if(count($countries) > 0) {
					foreach($countries as $country) {
						if($country['code'] == $orderplaced->job_country)
						$orderdetails['CustomFields']['Job Country'] = $country['name'];
					}
				}
				
			}
			if($orderplaced->job_state != '')
			$orderdetails['CustomFields']['Job State'] = $orderplaced->job_state;
			if($orderplaced->job_zip_code != '')
			$orderdetails['CustomFields']['Job Zip-Code'] = $orderplaced->job_zip_code;
			if($orderplaced->job_city != '')
			$orderdetails['CustomFields']['Job City'] = $orderplaced->job_city;
			if($orderplaced->company_name != '')
			$orderdetails['CustomFields']['Company Name'] = $orderplaced->company_name;
			if($orderplaced->company_email != '')
			$orderdetails['CustomFields']['Contact Email'] = $orderplaced->company_email;
			if($orderplaced->company_url != '')
			$orderdetails['CustomFields']['Website'] = $orderplaced->company_url;		

			$job = new Wpjb_Model_Job($params['object_id']);			
			if(isset($job->getTag()->type) && is_array($job->getTag()->type)) {
				foreach($job->getTag()->type as $type) {
					$orderdetails['CustomFields']['Job Type'] = $type->title;
				}
			}			
			if(isset($job->tag->category) && is_array($job->tag->category)) {
				foreach($job->tag->category as $cat) {
					$orderdetails['CustomFields']['Category'] = $cat->title;
				}
			}						
			
		} else if($params['object_type'] == 2) { //Resume
			$orderplaced = $wpdb->get_row( 'SELECT * FROM '.$wpdb->prefix.'wpjb_resume r INNER JOIN '.$wpdb->prefix.'posts p ON r.post_id = p.id WHERE r.id = '.$params['object_id'], OBJECT );			
			if($orderplaced) {
				if($orderplaced->post_title != '') {
					$orderdetails['ItemName'] = 'Resume: '.$orderplaced->post_title;
				}else {
					$orderdetails['ItemName'] = 'Single Resume Access';
				}
			}
			else {
				$orderdetails['ItemName'] = 'Single Resume Access';
			}			
		} else if($params['object_type'] == 3) { //Membership
			$orderplaced = $wpdb->get_row( 'SELECT * FROM '.$wpdb->prefix.'wpjb_membership WHERE id = '.$params['object_id'], OBJECT );
			if($orderplaced->package_id != '') {
				$package = $wpdb->get_row( 'SELECT * FROM '.$wpdb->prefix.'wpjb_pricing WHERE id = '.$orderplaced->package_id, OBJECT );
				$orderdetails['ItemName'] = 'Membership: '.$package->title;
			} else {
				$orderdetails['ItemName'] = 'Employer Membership Package';
			}						
		}
		$UnitPriceCalculate = $TotalDiscountCalculate = 0;		
		
		$cardholder=$dom->createElement('CardHolder','');
		$cardholder=$order->appendChild($cardholder);

		$billinginfo=$dom->createElement('BillingInformation','');
		$billinginfo=$cardholder->appendChild($billinginfo);
		
		if(isset($post['clickandpledge_selectedPaymentMethod']) && $post['clickandpledge_selectedPaymentMethod'] == 'CreditCard') {
			$billfirst_name=$dom->createElement('BillingFirstName',$this->safeString($params['clickandpledge_FirstName_CreditCard'],50));
			$billfirst_name=$billinginfo->appendChild($billfirst_name);					
			$billlast_name=$dom->createElement('BillingLastName',$this->safeString($params['clickandpledge_LastName_CreditCard'],50));
			$billlast_name=$billinginfo->appendChild($billlast_name);
		} else 	if(isset($post['clickandpledge_selectedPaymentMethod']) && $post['clickandpledge_selectedPaymentMethod'] == 'eCheck') {
			$billfirst_name=$dom->createElement('BillingFirstName',$this->safeString($params['clickandpledge_FirstName_eCheck'],50));
			$billfirst_name=$billinginfo->appendChild($billfirst_name);					
			$billlast_name=$dom->createElement('BillingLastName',$this->safeString($params['clickandpledge_LastName_eCheck'],50));
			$billlast_name=$billinginfo->appendChild($billlast_name);
		} else if(isset($post['clickandpledge_selectedPaymentMethod']) && $post['clickandpledge_selectedPaymentMethod'] == 'Invoice') {
			$billfirst_name=$dom->createElement('BillingFirstName',$this->safeString($params['clickandpledge_FirstName_Invoice'],50));
			$billfirst_name=$billinginfo->appendChild($billfirst_name);					
			$billlast_name=$dom->createElement('BillingLastName',$this->safeString($params['clickandpledge_LastName_Invoice'],50));
			$billlast_name=$billinginfo->appendChild($billlast_name);
		} else if(isset($post['clickandpledge_selectedPaymentMethod']) && $post['clickandpledge_selectedPaymentMethod'] == 'PurchaseOrder') {
			$billfirst_name=$dom->createElement('BillingFirstName',$this->safeString($params['clickandpledge_FirstName_PO'],50));
			$billfirst_name=$billinginfo->appendChild($billfirst_name);					
			$billlast_name=$dom->createElement('BillingLastName',$this->safeString($params['clickandpledge_LastName_PO'],50));
			$billlast_name=$billinginfo->appendChild($billlast_name);
		}

		if (isset($orderdetails['BillingEmail']) && $orderdetails['BillingEmail'] != '') {
			$bill_email=$dom->createElement('BillingEmail',$orderdetails['BillingEmail']);
			$bill_email=$billinginfo->appendChild($bill_email);
		}
		//echo 'SELECT * FROM '.$wpdb->prefix.'wpjb_job WHERE post_id = '.$params['id'];
		//echo '<pre>';
		//print_r($orderplaced);
//die('fffffffffff');		
		$billingaddress=$dom->createElement('BillingAddress','');
		$billingaddress=$cardholder->appendChild($billingaddress);			
		
		if(isset($orderdetails['CustomFields']) && count($orderdetails['CustomFields']) > 0) {
			$customfieldlist = $dom->createElement('CustomFieldList','');
			$customfieldlist = $cardholder->appendChild($customfieldlist);
			
			foreach($orderdetails['CustomFields'] as $key => $val) {
				$customfield = $dom->createElement('CustomField','');
				$customfield = $customfieldlist->appendChild($customfield);
					
				$fieldname = $dom->createElement('FieldName',$key);
				$fieldname = $customfield->appendChild($fieldname);
					
				$fieldvalue = $dom->createElement('FieldValue',$this->safeString($val, 500));
				$fieldvalue = $customfield->appendChild($fieldvalue);
			}			
		}
		
		$paymentmethod=$dom->createElement('PaymentMethod','');
		$paymentmethod=$cardholder->appendChild($paymentmethod);
				
		if(isset($post['clickandpledge_selectedPaymentMethod']) && $post['clickandpledge_selectedPaymentMethod'] == 'CreditCard') {
			$payment_type=$dom->createElement('PaymentType','CreditCard');
			$payment_type=$paymentmethod->appendChild($payment_type);
		
			$creditcard=$dom->createElement('CreditCard','');
			$creditcard=$paymentmethod->appendChild($creditcard);
			$credit_card_name = '';			
			if (isset($params['clickandpledge_nameOnCard']) && $params['clickandpledge_nameOnCard'] != '') {
				$credit_card_name .= ' '.$params['clickandpledge_nameOnCard'];
			}
						
			$credit_name=$dom->createElement('NameOnCard',$this->safeString( $credit_card_name, 50));
			$credit_name=$creditcard->appendChild($credit_name);
					
			$credit_number=$dom->createElement('CardNumber',$this->safeString( str_replace(' ', '', $params['clickandpledge_cardNumber']), 17));
			$credit_number=$creditcard->appendChild($credit_number);

			$credit_cvv=$dom->createElement('Cvv2',$params['clickandpledge_cvc']);
			$credit_cvv=$creditcard->appendChild($credit_cvv);

			$credit_expdate=$dom->createElement('ExpirationDate',$params['clickandpledge_cardExpMonth'] . "/" . substr($params['clickandpledge_cardExpYear'],2,2));
			$credit_expdate=$creditcard->appendChild($credit_expdate);
		}
		elseif(isset($post['clickandpledge_selectedPaymentMethod']) && $post['clickandpledge_selectedPaymentMethod'] == 'eCheck') {
			$payment_type=$dom->createElement('PaymentType','Check');
			$payment_type=$paymentmethod->appendChild($payment_type);
			
			$echeck=$dom->createElement('Check','');
			$echeck=$paymentmethod->appendChild($echeck);
			if(!empty($post['clickandpledge_echeck_AccountNumber'])) {
			$ecAccount=$dom->createElement('AccountNumber',$this->safeString( $post['clickandpledge_echeck_AccountNumber'], 17));
			$ecAccount=$echeck->appendChild($ecAccount);
			}
			if(!empty($post['clickandpledge_echeck_AccountType'])) {
			$ecAccount_type=$dom->createElement('AccountType',$post['clickandpledge_echeck_AccountType']);
			$ecAccount_type=$echeck->appendChild($ecAccount_type);
			}
			if(!empty($post['clickandpledge_echeck_RoutingNumber'])) {
			$ecRouting=$dom->createElement('RoutingNumber',$this->safeString( $post['clickandpledge_echeck_RoutingNumber'], 9));
			$ecRouting=$echeck->appendChild($ecRouting);
			}
			if(!empty($post['clickandpledge_echeck_CheckNumber'])) {
			$ecCheck=$dom->createElement('CheckNumber',$this->safeString( $post['clickandpledge_echeck_CheckNumber'], 10));
			$ecCheck=$echeck->appendChild($ecCheck);
			}
			if(!empty($post['clickandpledge_echeck_CheckType'])) {
			$ecChecktype=$dom->createElement('CheckType',$post['clickandpledge_echeck_CheckType']);
			$ecChecktype=$echeck->appendChild($ecChecktype);
			}
			if(!empty($post['clickandpledge_echeck_NameOnAccount'])) {
			$ecName=$dom->createElement('NameOnAccount',$this->safeString( $post['clickandpledge_echeck_NameOnAccount'], 100));
			$ecName=$echeck->appendChild($ecName);
			}
			if(!empty($post['clickandpledge_echeck_IdType'])) {
			$ecIdtype=$dom->createElement('IdType',$post['clickandpledge_echeck_IdType']);
			$ecIdtype=$echeck->appendChild($ecIdtype);
			}			
			if(!empty($post['clickandpledge_echeck_IdNumber'])) {
			$IdNumber=$dom->createElement('IdNumber',$this->safeString( $post['clickandpledge_echeck_IdNumber'], 30));
			$IdNumber=$creditcard->appendChild($IdNumber);
			}
			if(!empty($post['clickandpledge_echeck_IdStateCode'])) {
			$IdStateCode=$dom->createElement('IdStateCode', $post['clickandpledge_echeck_IdStateCode']);
			$IdStateCode=$creditcard->appendChild($IdStateCode);
			}			
		}
		elseif(isset($post['clickandpledge_selectedPaymentMethod']) && $post['clickandpledge_selectedPaymentMethod'] == 'Invoice') {
			$payment_type=$dom->createElement('PaymentType','Invoice');
			$payment_type=$paymentmethod->appendChild($payment_type);
			$invoice=$dom->createElement('Invoice','');
			$invoice=$paymentmethod->appendChild($invoice);			 
			$CheckNumber=$dom->createElement('InvoiceCheckNumber',$post['InvoiceCheckNumber']);
			$CheckNumber=$invoice->appendChild($CheckNumber);
		}
		elseif(isset($post['clickandpledge_selectedPaymentMethod']) && $post['clickandpledge_selectedPaymentMethod'] == 'PurchaseOrder') {
			$payment_type=$dom->createElement('PaymentType','PurchaseOrder');
			$payment_type=$paymentmethod->appendChild($payment_type);
			$PurchaseOrder=$dom->createElement('PurchaseOrder','');
			$PurchaseOrder=$paymentmethod->appendChild($PurchaseOrder);			 
			$CheckNumber=$dom->createElement('PurchaseOrderNumber',$post['PurchaseOrderNumber']);
			$CheckNumber=$PurchaseOrder->appendChild($CheckNumber);
		} else {
			$payment_type=$dom->createElement('PaymentType','CreditCard');
			$payment_type=$paymentmethod->appendChild($payment_type);
		
			$creditcard=$dom->createElement('CreditCard','');
			$creditcard=$paymentmethod->appendChild($creditcard);
				
			$credit_card_name = '';	
			if (isset($params['clickandpledge_nameOnCard_First']) && $params['clickandpledge_nameOnCard_First'] != '') {
				$credit_card_name .= $params['clickandpledge_nameOnCard_First'];
			}
			if (isset($params['clickandpledge_nameOnCard_Last']) && $params['clickandpledge_nameOnCard_Last'] != '') {
				$credit_card_name .= ' '.$params['clickandpledge_nameOnCard_Last'];
			}
						
			$credit_name=$dom->createElement('NameOnCard',$this->safeString( $credit_card_name, 50));
			$credit_name=$creditcard->appendChild($credit_name);
					
			$credit_number=$dom->createElement('CardNumber',$this->safeString( str_replace(' ', '', $params['clickandpledge_cardNumber']), 17));
			$credit_number=$creditcard->appendChild($credit_number);

			$credit_cvv=$dom->createElement('Cvv2',$params['clickandpledge_cvc']);
			$credit_cvv=$creditcard->appendChild($credit_cvv);

			$credit_expdate=$dom->createElement('ExpirationDate',$params['clickandpledge_cardExpMonth'] . "/" . substr($params['clickandpledge_cardExpYear'],2,2));
			$credit_expdate=$creditcard->appendChild($credit_expdate);
		}
		
		$orderitemlist=$dom->createElement('OrderItemList','');
		$orderitemlist=$order->appendChild($orderitemlist);				
						
		$orderitem=$dom->createElement('OrderItem','');
		$orderitem=$orderitemlist->appendChild($orderitem);

		$itemid=$dom->createElement('ItemID',1);
		$itemid=$orderitem->appendChild($itemid);

		$itemname=$dom->createElement('ItemName',$this->safeString(trim($orderdetails['ItemName']), 100));
		$itemname=$orderitem->appendChild($itemname);

		$quntity=$dom->createElement('Quantity',1);
		$quntity=$orderitem->appendChild($quntity);
		$line_subtotal = $params['clickandpledge_Amount'];
		if( isset($params['clickandpledge_isRecurring']) &&  $params['clickandpledge_isRecurring'] == 'true' ) {
			if($params['clickandpledge_RecurringMethod'] == 'Installment') {
				if(isset($params['clickandpledge_indefinite']) && $params['clickandpledge_indefinite'] == 'true') {
				$UnitPriceDecimal = ($this->number_format(($line_subtotal/999),2,'.','')*100);
				$UnitPriceCalculate += ($this->number_format(($line_subtotal/999),2,'.','')*1);
				} else {
				$UnitPriceDecimal = ($this->number_format(($line_subtotal/$params['clickandpledge_Installment']),2,'.','')*100);
				$UnitPriceCalculate += ($this->number_format(($line_subtotal/$params['clickandpledge_Installment']),2,'.','')*1);
				}
				$unitprice=$dom->createElement('UnitPrice', $UnitPriceDecimal);
				$unitprice=$orderitem->appendChild($unitprice);
			} else {				
			$unitprice=$dom->createElement('UnitPrice',($line_subtotal*100));
			$unitprice=$orderitem->appendChild($unitprice);
			//New Fix
			$UnitPriceCalculate += ($line_subtotal*1);
			}
		} else {			
		$unitprice=$dom->createElement('UnitPrice',($line_subtotal*100));
		$unitprice=$orderitem->appendChild($unitprice);
		$UnitPriceCalculate += ($line_subtotal*1);
		}
		
		//Discount
		if($params['clickandpledge_Discount'] > 0) {
			$order_discount = $params['clickandpledge_Discount'];
			if( isset($params['clickandpledge_isRecurring']) &&  $params['clickandpledge_isRecurring'] == 'true' ) {
				if($params['clickandpledge_RecurringMethod'] == 'Installment') {
					$TotalDiscount = ($order_discount)/$params['clickandpledge_Installment'];
					$TotalDiscount = $this->number_format($TotalDiscount, 2, '.', '')*100;
				} else {
					$TotalDiscount = $this->number_format($order_discount, 2, '.', '')*100;		
				}
			} else {
			$TotalDiscount = $this->number_format($order_discount, 2, '.', '')*100;
			}
			if($TotalDiscount > 0) {		
			$unit_disc=$dom->createElement('UnitDiscount', $TotalDiscount);
			$unit_disc=$orderitem->appendChild($unit_disc);
			$TotalDiscountCalculate = $TotalDiscount;
			}
		}	
	
		$receipt=$dom->createElement('Receipt','');
		$receipt=$order->appendChild($receipt);

		$recipt_lang=$dom->createElement('Language','ENG');
		$recipt_lang=$receipt->appendChild($recipt_lang);
		
		if( isset($params['clickandpledge_OrganizationInformation']) && $params['clickandpledge_OrganizationInformation'] != '')
		{
			$recipt_org=$dom->createElement('OrganizationInformation',$this->safeString($params['clickandpledge_OrganizationInformation'], 1500));
			$recipt_org=$receipt->appendChild($recipt_org);
		}		
				
		if( isset($params['clickandpledge_TermsCondition']) && $params['clickandpledge_TermsCondition'] != '')
		{
			$recipt_terms=$dom->createElement('TermsCondition',$this->safeString($params['clickandpledge_TermsCondition'], 1500));
			$recipt_terms=$receipt->appendChild($recipt_terms);
		}

		if( isset($params['clickandpledge_email_customer']) && $params['clickandpledge_email_customer'] == 'yes' ) { //Sending the email based on admin settings
			$recipt_email=$dom->createElement('EmailNotificationList','');
			$recipt_email=$receipt->appendChild($recipt_email);			
			
			$email_notification = '';		
			if (isset($params['billing_email']) && $params['billing_email'] != '') {
				$email_notification = $params['billing_email'];
			}
								
			$email_note=$dom->createElement('NotificationEmail',$email_notification);
			$email_note=$recipt_email->appendChild($email_note);
		}
		$transation=$dom->createElement('Transaction','');
		$transation=$order->appendChild($transation);

		$trans_type=$dom->createElement('TransactionType','Payment');
		$trans_type=$transation->appendChild($trans_type);

		$trans_desc=$dom->createElement('DynamicDescriptor','DynamicDescriptor');
		$trans_desc=$transation->appendChild($trans_desc); 
		
		
		if( isset($params['clickandpledge_isRecurring']) &&  $params['clickandpledge_isRecurring'] == 'true' )
		{
			$trans_recurr=$dom->createElement('Recurring','');
			$trans_recurr=$transation->appendChild($trans_recurr);
			if  (isset($params['clickandpledge_indefinite']) &&  $params['clickandpledge_indefinite'] == 'true' )
			{
				$total_installment=$dom->createElement('Installment',999);
				$total_installment=$trans_recurr->appendChild($total_installment);
			}
			else
			{
				if($params['clickandpledge_Installment'] != '') {
					$total_installment=$dom->createElement('Installment',$params['clickandpledge_Installment']);
					$total_installment=$trans_recurr->appendChild($total_installment);
				} else {
					$total_installment=$dom->createElement('Installment',1);
					$total_installment=$trans_recurr->appendChild($total_installment);
				}
			}			
			$total_periodicity=$dom->createElement('Periodicity',$params['clickandpledge_Periodicity']);
			$total_periodicity=$trans_recurr->appendChild($total_periodicity);
			
			if( isset($params['clickandpledge_RecurringMethod']) ) {
				$RecurringMethod=$dom->createElement('RecurringMethod',$params['clickandpledge_RecurringMethod']);
				$RecurringMethod=$trans_recurr->appendChild($RecurringMethod);
			} else {
				$RecurringMethod=$dom->createElement('RecurringMethod','Subscription');
				$RecurringMethod=$trans_recurr->appendChild($RecurringMethod);
			}	
		}
		
		$trans_totals=$dom->createElement('CurrentTotals','');
		$trans_totals=$transation->appendChild($trans_totals);		
		if($TotalDiscountCalculate > 0) {
			$total_discount=$dom->createElement('TotalDiscount',$TotalDiscountCalculate);
			$total_discount=$trans_totals->appendChild($total_discount);
		}
		
		if( isset($params['clickandpledge_isRecurring']) &&  $params['clickandpledge_isRecurring'] == 'true' ) {
			if($params['clickandpledge_RecurringMethod'] == 'Installment') {			
			$Total = $this->number_format($UnitPriceCalculate, 2, '.', '')*100 - $TotalDiscountCalculate;			
			$total_amount=$dom->createElement('Total', $Total);
			$total_amount=$trans_totals->appendChild($total_amount);
			} else {
			$Total = $this->number_format($UnitPriceCalculate, 2, '.', '')*100 - $TotalDiscountCalculate;;
			$total_amount=$dom->createElement('Total',$Total);
			$total_amount=$trans_totals->appendChild($total_amount);
			}
		} else {
		$Total = $this->number_format($UnitPriceCalculate, 2, '.', '')*100 - $TotalDiscountCalculate;;
		$total_amount=$dom->createElement('Total',$Total);
		$total_amount=$trans_totals->appendChild($total_amount);
		}
		
		if($TotalDiscountCalculate > 0) {
			if(isset($post['clickandpledge_coupon_code']) && $post['clickandpledge_coupon_code'] != '') {
				$trans_coupon=$dom->createElement('CouponCode',$post['clickandpledge_coupon_code']);
				$trans_coupon=$transation->appendChild($trans_coupon);
			}
		}
			
		$strParam =$dom->saveXML();
		//die();
		return $strParam;
	  }
	  
     public function getEngine() {
       return "clickandpledge_payment";
     }     
     public function getTitle() {
       return "Click & Pledge"; 
     }

     public function getForm()
     {        
        return "Config_ClickandPledge";
     }  

    

     public function processTransaction() {
	   return array(
                            "external_id" => $this->_post['token'],
                            "paid" => $this->_post['clickandpledge_Amount']-$this->_post['clickandpledge_Discount'],
                        );		
     }
	 public function bind(array $post, array $get) {
	   // this is a good place to set $this->data
       $this->setObject(new Wpjb_Model_Payment($post["id"]));
       parent::bind($post, $get);
     }
	 private function getMerchant() {
		$merchant = array();
		//USD Account
		$merchant['wpjobboard_clickandpledge_usdaccount'] = $this->conf('wpjobboard_clickandpledge_usdaccount');
		$merchant['wpjobboard_clickandpledge_USD_AccountID'] = $this->conf('wpjobboard_clickandpledge_USD_AccountID');
		$merchant['wpjobboard_clickandpledge_USD_AccountGuid'] = $this->conf('wpjobboard_clickandpledge_USD_AccountGuid');		
		$merchant['wpjobboard_clickandpledge_USD_OrderMode'] = $this->conf('wpjobboard_clickandpledge_USD_OrderMode');
		
		//EUR Account
		$merchant['wpjobboard_clickandpledge_euraccount'] = $this->conf('wpjobboard_clickandpledge_euraccount');
		$merchant['wpjobboard_clickandpledge_EUR_AccountID'] = $this->conf('wpjobboard_clickandpledge_EUR_AccountID');
		$merchant['wpjobboard_clickandpledge_EUR_AccountGuid'] = $this->conf('wpjobboard_clickandpledge_EUR_AccountGuid');		
		$merchant['wpjobboard_clickandpledge_EUR_OrderMode'] = $this->conf('wpjobboard_clickandpledge_EUR_OrderMode');
		
		//CAD Account
		$merchant['wpjobboard_clickandpledge_cadaccount'] = $this->conf('wpjobboard_clickandpledge_cadaccount');
		$merchant['wpjobboard_clickandpledge_CAD_AccountID'] = $this->conf('wpjobboard_clickandpledge_CAD_AccountID');
		$merchant['wpjobboard_clickandpledge_CAD_AccountGuid'] = $this->conf('wpjobboard_clickandpledge_CAD_AccountGuid');		
		$merchant['wpjobboard_clickandpledge_CAD_OrderMode'] = $this->conf('wpjobboard_clickandpledge_CAD_OrderMode');
		
		//GBP Account
		$merchant['wpjobboard_clickandpledge_gbpaccount'] = $this->conf('wpjobboard_clickandpledge_gbpaccount');
		$merchant['wpjobboard_clickandpledge_GBP_AccountID'] = $this->conf('wpjobboard_clickandpledge_GBP_AccountID');
		$merchant['wpjobboard_clickandpledge_GBP_AccountGuid'] = $this->conf('wpjobboard_clickandpledge_GBP_AccountGuid');		
		$merchant['wpjobboard_clickandpledge_GBP_OrderMode'] = $this->conf('wpjobboard_clickandpledge_GBP_OrderMode');
		
		//HKD Account
		$merchant['wpjobboard_clickandpledge_hkdaccount'] = $this->conf('wpjobboard_clickandpledge_hkdaccount');
		$merchant['wpjobboard_clickandpledge_HKD_AccountID'] = $this->conf('wpjobboard_clickandpledge_HKD_AccountID');
		$merchant['wpjobboard_clickandpledge_HKD_AccountGuid'] = $this->conf('wpjobboard_clickandpledge_HKD_AccountGuid');		
		$merchant['wpjobboard_clickandpledge_HKD_OrderMode'] = $this->conf('wpjobboard_clickandpledge_HKD_OrderMode');
		
		//Payment Methods
		$merchant['wpjobboard_clickandpledge_Paymentmethods'] = $this->conf('wpjobboard_clickandpledge_Paymentmethods');
		//Default Payment Method
		$merchant['wpjobboard_clickandpledge_DefaultpaymentMethod'] = $this->conf('wpjobboard_clickandpledge_DefaultpaymentMethod');
		
		//Receipt Settings
		$merchant['wpjobboard_clickandpledge_emailcustomer'] = $this->conf('wpjobboard_clickandpledge_emailcustomer');
		$merchant['wpjobboard_clickandpledge_OrganizationInformation'] = $this->conf('wpjobboard_clickandpledge_OrganizationInformation');
		$merchant['wpjobboard_clickandpledge_TermsCondition'] = $this->conf('wpjobboard_clickandpledge_TermsCondition');
		
		//Recurring Settings
		$merchant['wpjobboard_clickandpledge_isRecurring'] = $this->conf('wpjobboard_clickandpledge_isRecurring');
		$merchant['wpjobboard_clickandpledge_RecurringLabel'] = $this->conf('wpjobboard_clickandpledge_RecurringLabel');
		$merchant['wpjobboard_clickandpledge_Periodicity'] = $this->conf('wpjobboard_clickandpledge_Periodicity');
		$merchant['wpjobboard_clickandpledge_RecurringMethod_Subscription'] = $this->conf('wpjobboard_clickandpledge_RecurringMethod_Subscription');
		$merchant['wpjobboard_clickandpledge_maxrecurrings_Subscription'] = $this->conf('wpjobboard_clickandpledge_maxrecurrings_Subscription');
		$merchant['wpjobboard_clickandpledge_RecurringMethod_Installment'] = $this->conf('wpjobboard_clickandpledge_RecurringMethod_Installment');
		$merchant['wpjobboard_clickandpledge_maxrecurrings_Installment'] = $this->conf('wpjobboard_clickandpledge_maxrecurrings_Installment');
		$merchant['wpjobboard_clickandpledge_indefinite'] = $this->conf('wpjobboard_clickandpledge_indefinite');
		return $merchant;
	 }
     public function render() {
      $data = $this->_data;
	  $id = $this->_data->id;
	  $availableCurrencies = array();
	  $paymentMethods = array();
	  $merchant = $this->getMerchant(); 
	 
	 //echo '<pre>';
	 //print_r($_POST);
	  if(isset($merchant['wpjobboard_clickandpledge_usdaccount']) && $merchant['wpjobboard_clickandpledge_usdaccount'] != '')
	  array_push($availableCurrencies, 'USD');
	  if(isset($merchant['wpjobboard_clickandpledge_euraccount']) && $merchant['wpjobboard_clickandpledge_euraccount'] != '')
	  array_push($availableCurrencies, 'EUR');
	  if(isset($merchant['wpjobboard_clickandpledge_cadaccount']) && $merchant['wpjobboard_clickandpledge_cadaccount'] != '')
	  array_push($availableCurrencies, 'CAD');
	  if(isset($merchant['wpjobboard_clickandpledge_gbpaccount']) && $merchant['wpjobboard_clickandpledge_gbpaccount'] != '')
	  array_push($availableCurrencies, 'GBP');
	  if(isset($merchant['wpjobboard_clickandpledge_hkdaccount']) && $merchant['wpjobboard_clickandpledge_hkdaccount'] != '')
	  array_push($availableCurrencies, 'HKD');
	
	
	  $selectedCurrency = $this->_data->payment_currency;
	  if(in_array($selectedCurrency,$availableCurrencies)) {
		  if(isset($merchant['wpjobboard_clickandpledge_Paymentmethods']) && count($merchant['wpjobboard_clickandpledge_Paymentmethods']) > 0) {
				foreach($merchant['wpjobboard_clickandpledge_Paymentmethods'] as $method) {
					if($method == 'CreditCard')
					$paymentMethods[$method] = 'Credit Card';
					if($method == 'eCheck')
					$paymentMethods[$method] = 'eCheck';
					if($method == 'PurchaseOrder')
					$paymentMethods[$method] = 'Purchase Order';
					if($method == 'Invoice')
					$paymentMethods[$method] = 'Invoice';
				}
			}
			else {
				$paymentMethods['CreditCard'] = 'Credit Card';
				$paymentMethods['eCheck'] = 'eCheck';
				$paymentMethods['PurchaseOrder'] = 'Purchase Order';
				$paymentMethods['Invoice'] = 'Invoice';
			}
	
	  $defaultpayment = 'CreditCard';
	  if($merchant['wpjobboard_clickandpledge_DefaultpaymentMethod'] != '')
	  $defaultpayment = $merchant['wpjobboard_clickandpledge_DefaultpaymentMethod'];
	  
	  

	  $ajaxurl = admin_url("admin-ajax.php");	
	 
	 wp_register_script( 'clickandpledge-plugin-script', plugins_url( '/clickandpledge.js', __FILE__ ) );
	 wp_enqueue_script( 'clickandpledge-plugin-script' );
	 
	 wp_register_script( 'jquery.validate.min-script', plugins_url( '/jquery.validate.min.js', __FILE__ ) );
	 wp_enqueue_script( 'jquery.validate.min-script' );
	 
	 wp_register_script( 'clickandpledge_validations-script', plugins_url( '/clickandpledge_validations.js', __FILE__ ) );
	 wp_enqueue_script( 'clickandpledge_validations-script' );
	   $html = '
		<style type="text/css">
		  .form-row > label > span {
			display: block;
			width: 200px;
			float: left;
			line-height: 2em;
		  }
		  .form-row label.error {
				color: red;
				font-style: italic;
			}
		</style>
<script type="text/javascript">
  var WPJB_PAYMENT_ID = '.$id.';
  if (typeof ajaxurl === "undefined") {
    ajaxurl = "'.$ajaxurl.'";
  }
</script>
		<form action="" method="POST" id="payment-form">
		  <h3>'.__("Click & Pledge", "wpjobboard").'</h3>
		  <div class="payment-errors"></div>
		  <div class="htmlholder">
		  ';
			
			if(count($paymentMethods) > 0) {
				$html .= '<span style="width:980px" id="payment_methods">';
				foreach($paymentMethods as $pkey => $pval) {
					if($pkey == $defaultpayment) {
					$html .= '<input type="radio" id="cnp_payment_method_selection_'.$pkey.'" name="cnp_payment_method_selection" class="cnp_payment_method_selection" style="margin: 0 0 0 0;" value="'.$pkey.'" checked>&nbsp<b>'.$pval.'</b>&nbsp;&nbsp;&nbsp;';
					} else {
					$html .= '<input type="radio" id="cnp_payment_method_selection_'.$pkey.'" name="cnp_payment_method_selection" class="cnp_payment_method_selection" style="margin: 0 0 0 0;" value="'.$pkey.'">&nbsp;<b>'.$pval.'</b>&nbsp;&nbsp;&nbsp;';
					}
				}
				$html .= '</span>';
			}
		
		$html .= '<script>
				jQuery("#cnp_payment_method_selection_CreditCard").click(function(){
					jQuery("#cnp_CreditCard_div").show();					
					jQuery("#cnp_eCheck_div").hide();
					jQuery("#cnp_Invoice_div").hide();
					jQuery("#cnp_PurchaseOrder_div").hide();
					
				});
				jQuery("#cnp_payment_method_selection_eCheck").click(function(){
					jQuery("#cnp_CreditCard_div").hide();					
					jQuery("#cnp_eCheck_div").show();
					jQuery("#cnp_Invoice_div").hide();
					jQuery("#cnp_PurchaseOrder_div").hide();
					
				});
				jQuery("#cnp_payment_method_selection_Invoice").click(function(){
					jQuery("#cnp_CreditCard_div").hide();					
					jQuery("#cnp_eCheck_div").hide();
					jQuery("#cnp_Invoice_div").show();
					jQuery("#cnp_PurchaseOrder_div").hide();
					
				});
				jQuery("#cnp_payment_method_selection_PurchaseOrder").click(function(){
					jQuery("#cnp_CreditCard_div").hide();					
					jQuery("#cnp_eCheck_div").hide();
					jQuery("#cnp_Invoice_div").hide();
					jQuery("#cnp_PurchaseOrder_div").show();
					
				});
			</script>';
		 $cdivdisplay = ($defaultpayment == 'CreditCard') ? 'block' : 'none';
		 $recurringhtml = '';
		 //echo $merchant['wpjobboard_clickandpledge_isRecurring'].'############';
		 //die();
		 if(isset($merchant['wpjobboard_clickandpledge_isRecurring']) && $merchant['wpjobboard_clickandpledge_isRecurring'] == 1) {
			if($merchant['wpjobboard_clickandpledge_RecurringLabel'] != '') {
				$recurringhtml .= '<br><div class="form-row" id="clickandpledge_isRecurring_div">
				<label>
				  <span>'.__($merchant['wpjobboard_clickandpledge_RecurringLabel'], "wpjobboard").'</span>
				  <input type="checkbox" name="clickandpledge_isRecurring" id="clickandpledge_isRecurring"/>
				</label>
			  </div>';
			} else {
			$recurringhtml .= '<br><div class="form-row" id="clickandpledge_isRecurring_div">
				<label>
				  <span>'.__('Is this Recurring Payment?', "wpjobboard").'</span>
				  <input type="checkbox" name="clickandpledge_isRecurring" id="clickandpledge_isRecurring"/>
				</label>
			  </div>';
			}
			$RecurringMethod = array();
			if(isset($merchant['wpjobboard_clickandpledge_RecurringMethod_Subscription']) && is_array($merchant['wpjobboard_clickandpledge_RecurringMethod_Subscription']) && count($merchant['wpjobboard_clickandpledge_RecurringMethod_Subscription']) > 0) {
				array_push($RecurringMethod, 'Subscription');
			}
			if(isset($merchant['wpjobboard_clickandpledge_RecurringMethod_Installment']) && is_array($merchant['wpjobboard_clickandpledge_RecurringMethod_Installment']) && count($merchant['wpjobboard_clickandpledge_RecurringMethod_Installment']) > 0) {
				array_push($RecurringMethod, 'Installment');
			}
			if(count($RecurringMethod) == 0) {
				array_push($RecurringMethod, 'Subscription');
				array_push($RecurringMethod, 'Installment');
			}
			if(count($RecurringMethod) > 0) {
				if(count($RecurringMethod) == 1) {
					$recurringhtml .= '<input type="hidden" name="clickandpledge_RecurringMethod" id="clickandpledge_RecurringMethod" value="'.$RecurringMethod[0].'">';
				} else {
				$recurringhtml .= '<br><div class="form-row" id="clickandpledge_RecurringMethod_div">
				<label>
				  <span>'.__("Recurring Method", "wpjobboard").'</span>
				  <select id="clickandpledge_RecurringMethod" name="clickandpledge_RecurringMethod">';
				foreach ($RecurringMethod as $r) {
					$recurringhtml .= '<option value="'.$r.'">'.$r.'</option>';
				}  
				$recurringhtml .= '</select>
				</label>
			  </div>';
			  }
			} else {
				$recurringhtml .= '<div class="form-row" id="clickandpledge_RecurringMethod_div">
				<label>
				  <span>'.__("Recurring Method", "wpjobboard").'</span>
				  <select id="clickandpledge_RecurringMethod" name="clickandpledge_RecurringMethod">
					<option value="Subscription">Subscription</option>
					<option value="Installment">Installment</option>
				  </select>
				</label>
			  </div>';
			}
		
		if(isset($merchant['wpjobboard_clickandpledge_indefinite']) && is_array($merchant['wpjobboard_clickandpledge_indefinite']) && count($merchant['wpjobboard_clickandpledge_indefinite']) > 0) {
		$recurringhtml .= '<br><div class="form-row" id="clickandpledge_indefinite_div">
			<label>
			  <span>'.__("Indefinite Recurring", "wpjobboard").'</span>
			  <input type="checkbox" name="clickandpledge_indefinite" id="clickandpledge_indefinite"/>
			</label>
		  </div>';	
		}
		
		if(isset($merchant['wpjobboard_clickandpledge_Periodicity']) && is_array($merchant['wpjobboard_clickandpledge_Periodicity']) && count($merchant['wpjobboard_clickandpledge_Periodicity']) > 0) {
				$recurringhtml .= '<br><div class="form-row" id="clickandpledge_Periodicity_div">
				<label>
				  <span>'.__("Every", "wpjobboard").'</span>
				  <select id="clickandpledge_Periodicity" name="clickandpledge_Periodicity">';
				foreach ($merchant['wpjobboard_clickandpledge_Periodicity'] as $r) {
					$recurringhtml .= '<option value="'.$r.'">'.$r.'</option>';
				}  
				$recurringhtml .= '</select>
				</label>&nbsp;
				<span id="clickandpledge_Installment_div"><input type="text" name="clickandpledge_Installment" id="clickandpledge_Installment" class="required" title="Installments" style="width:100px;" maxlength="3"/>&nbsp;<font color="#FF0000">*</font> payments</span>
			  </div>
			  <script>
				jQuery("#clickandpledge_Installment").keypress(function(e) {
					var a = [];
					var k = e.which;

					for (i = 48; i < 58; i++)
						a.push(i);

					if (!(a.indexOf(k)>=0))
						e.preventDefault();
				});
				</script>';
		} else {
			$Periodicity = array();
			$Periodicity['Week']		= 'Week';
			$Periodicity['2 Weeks']	= '2 Weeks';
			$Periodicity['Month']		= 'Month';
			$Periodicity['2 Months']	= '2 Months';
			$Periodicity['Quarter']	= 'Quarter';
			$Periodicity['6 Months']	= '6 Months';
			$Periodicity['Year']		= 'Year';			
			$recurringhtml .= '<br><div class="form-row" id="clickandpledge_Periodicity_div">
				<label>
				  <span>'.__("Every", "wpjobboard").'</span>
				  <select id="clickandpledge_Periodicity" name="clickandpledge_Periodicity">';
				foreach ($Periodicity as $k => $v) {
					$recurringhtml .= '<option value="'.$k.'">'.$v.'</option>';
				}  
				$recurringhtml .= '</select>
				</label>&nbsp;
				<span id="clickandpledge_Installment_div"><input type="text" name="clickandpledge_Installment" id="clickandpledge_Installment" class="required" title="Installments" style="width:100px;" maxlength="3"/>&nbsp;<font color="#FF0000">*</font> payments</span>
			  </div>
			  <script>
				jQuery("#clickandpledge_Installment").keypress(function(e) {
					var a = [];
					var k = e.which;

					for (i = 48; i < 58; i++)
						a.push(i);

					if (!(a.indexOf(k)>=0))
						e.preventDefault();
				});
				</script>';
		}		
		
		 }
		 //echo $recurringhtml;
		 //die('333333333333');		 
		 $clickandpledgename_CreditCard = '<div class="form-row cnp_payment_style" >
			<label>
			  <span>'.__("Name&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <input type="text" size="20" data-clickandpledge="number" name="clickandpledge_FirstName_CreditCard" id="clickandpledge_FirstName_CreditCard" class="required NameOnCard" placeholder="First Name"/>
			  <input type="text" size="20" data-clickandpledge="number" name="clickandpledge_LastName_CreditCard" id="clickandpledge_LastName_CreditCard" class="required NameOnCard" placeholder="Last Name"/>
			</label>
		  </div>
		  <style>
		  .cnp_payment_style label span{
			width: 100%;
			}
			#clickandpledge_FirstName_CreditCard, #clickandpledge_LastName_CreditCard {
			width: 48%;
			display: inline-block;
			}
		  </style>
		  ';
		  $clickandpledgename_eCheck = '<div class="form-row cnp_payment_style" >
			<label>
			  <span>'.__("Name&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <input type="text" size="20" data-clickandpledge="number" name="clickandpledge_FirstName_eCheck" id="clickandpledge_FirstName_eCheck" class="required NameOnCard" placeholder="First Name"/>
			  <input type="text" size="20" data-clickandpledge="number" name="clickandpledge_LastName_eCheck" id="clickandpledge_LastName_eCheck" class="required NameOnCard" placeholder="Last Name"/>
			</label>
		  </div>
		  <style>
		  .cnp_payment_style label span{
			width: 100%;
			}
			#clickandpledge_FirstName_eCheck, #clickandpledge_LastName_eCheck {
			width: 48%;
			display: inline-block;
			}
		  </style>
		  ';
		  $clickandpledgename_Invoice = '<div class="form-row cnp_payment_style" >
			<label>
			  <span>'.__("Name&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <input type="text" size="20" data-clickandpledge="number" name="clickandpledge_FirstName_Invoice" id="clickandpledge_FirstName_Invoice" class="required NameOnCard"  placeholder="First Name"/>
			  <input type="text" size="20" data-clickandpledge="number" name="clickandpledge_LastName_Invoice" id="clickandpledge_LastName_Invoice" class="required NameOnCard"  placeholder="Last Name"/>
			</label>
		  </div>
		  <style>
		  .cnp_payment_style label span{
			width: 100%;
			}
			#clickandpledge_FirstName_Invoice, #clickandpledge_LastName_Invoice {
			width: 48%;
			display: inline-block;
			}
		  </style> ';
		  $clickandpledgename_PO = '<div class="form-row cnp_payment_style" >
			<label>
			  <span>'.__("Name&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <input type="text" size="20" data-clickandpledge="number" name="clickandpledge_FirstName_PO" id="clickandpledge_FirstName_PO" class="required NameOnCard"  placeholder="First Name"/>
			  <input type="text" size="20" data-clickandpledge="number" name="clickandpledge_LastName_PO" id="clickandpledge_LastName_PO" class="required NameOnCard"  placeholder="Last Name"/>
			</label>
		  </div>
		  <style>
		  .cnp_payment_style label span{
			width: 100%;
			}
			#clickandpledge_FirstName_PO, #clickandpledge_LastName_PO {
			width: 48%;
			display: inline-block;
			}
		  </style> ';
		  
		 $html .= '<div style="display:'.$cdivdisplay.'" id="cnp_CreditCard_div">';
		 $html .= $clickandpledgename_CreditCard;
		  $html .= '<div class="form-row">
			<label>
			  <span>'.__("Name On Card&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <input type="text" size="20" data-clickandpledge="number" name="clickandpledge_nameOnCard" id="clickandpledge_nameOnCard" class="required NameOnCard"  placeholder="Name On Card"/>
			</label>
		  </div>
		  
		  <div class="form-row">
			<label>
			  <span>'.__("Card Number&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <input type="text" size="20" data-clickandpledge="number" name="clickandpledge_cardNumber" id="clickandpledge_cardNumber" class="required creditcard" placeholder="Card Number"/>
			</label>
		  </div>

		  <div class="form-row">
			<label>
			  <span>'.__("CVV&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>			  
			  <input type="text" size="4" data-clickandpledge="cvc" maxlength="4" name="clickandpledge_cvc" id="clickandpledge_cvc" class="required Cvv2" placeholder="CVV"/>
			</label>
		  </div>
		  <div class="form-row">
			<label><span>'.__("Expiration (MM/YYYY)&nbsp;<font color='red'>*</font>", "wpjobboard").'</span></label>
			<select name="clickandpledge_cardExpMonth" id="clickandpledge_cardExpMonth" class="required">'.$this->getMonths().'</select>
			<span> / </span>
			<select name="clickandpledge_cardExpYear" id="clickandpledge_cardExpYear" class="required" data-clickandpledge="exp-year">'.$this->getYears().'</select>
		  </div>';
		$html .= $recurringhtml;
		$html .= '</div>'; //CreditCard Div End
			
		$eCheckdivdisplay = ($defaultpayment == 'eCheck') ? 'block' : 'none';
		 $recurringhtml_eCheck = '';

		 if(isset($merchant['wpjobboard_clickandpledge_isRecurring']) && $merchant['wpjobboard_clickandpledge_isRecurring'] == 1) {
			if($merchant['wpjobboard_clickandpledge_RecurringLabel'] != '') {
				$recurringhtml_eCheck .= '<br><div class="form-row" id="clickandpledge_isRecurring_div_eCheck">
				<label>
				  <span>'.__($merchant['wpjobboard_clickandpledge_RecurringLabel'], "wpjobboard").'</span>
				  <input type="checkbox" name="clickandpledge_isRecurring_eCheck" id="clickandpledge_isRecurring_eCheck"/>
				</label>
			  </div>';
			} else {
			$recurringhtml_eCheck .= '<br><div class="form-row" id="clickandpledge_isRecurring_div_eCheck">
				<label>
				  <span>'.__('Is this Recurring Payment?', "wpjobboard").'</span>
				  <input type="checkbox" name="clickandpledge_isRecurring_eCheck" id="clickandpledge_isRecurring_eCheck"/>
				</label>
			  </div>';
			}
			$RecurringMethod = array();
			if(isset($merchant['wpjobboard_clickandpledge_RecurringMethod_Subscription']) && is_array($merchant['wpjobboard_clickandpledge_RecurringMethod_Subscription']) && count($merchant['wpjobboard_clickandpledge_RecurringMethod_Subscription']) > 0) {
				array_push($RecurringMethod, 'Subscription');
			}
			if(isset($merchant['wpjobboard_clickandpledge_RecurringMethod_Installment']) && is_array($merchant['wpjobboard_clickandpledge_RecurringMethod_Installment']) && count($merchant['wpjobboard_clickandpledge_RecurringMethod_Installment']) > 0) {
				array_push($RecurringMethod, 'Installment');
			}
			if(count($RecurringMethod) == 0) {
				array_push($RecurringMethod, 'Subscription');
				array_push($RecurringMethod, 'Installment');
			}
			if(count($RecurringMethod) > 0) {
				if(count($RecurringMethod) == 1) {
					$recurringhtml_eCheck .= '<input type="hidden" name="clickandpledge_RecurringMethod_eCheck" id="clickandpledge_RecurringMethod_eCheck" value="'.$RecurringMethod[0].'">';
				} else {
				$recurringhtml_eCheck .= '<br><div class="form-row" id="clickandpledge_RecurringMethod_div_eCheck">
				<label>
				  <span>'.__("Recurring Method", "wpjobboard").'</span>
				  <select id="clickandpledge_RecurringMethod_eCheck" name="clickandpledge_RecurringMethod_eCheck">';
				foreach ($RecurringMethod as $r) {
					$recurringhtml_eCheck .= '<option value="'.$r.'">'.$r.'</option>';
				}  
				$recurringhtml_eCheck .= '</select>
				</label>
			  </div>';
			  }
			} else {
				$recurringhtml_eCheck .= '<div class="form-row" id="clickandpledge_RecurringMethod_div_eCheck">
				<label>
				  <span>'.__("Recurring Method", "wpjobboard").'</span>
				  <select id="clickandpledge_RecurringMethod_eCheck" name="clickandpledge_RecurringMethod_eCheck">
					<option value="Subscription">Subscription</option>
					<option value="Installment">Installment</option>
				  </select>
				</label>
			  </div>';
			}
		
		if(isset($merchant['wpjobboard_clickandpledge_indefinite']) && is_array($merchant['wpjobboard_clickandpledge_indefinite']) && count($merchant['wpjobboard_clickandpledge_indefinite']) > 0) {
		$recurringhtml_eCheck .= '<br><div class="form-row" id="clickandpledge_indefinite_div_eCheck">
			<label>
			  <span>'.__("Indefinite Recurring", "wpjobboard").'</span>
			  <input type="checkbox" name="clickandpledge_indefinite_eCheck" id="clickandpledge_indefinite_eCheck"/>
			</label>
		  </div>';	
		}
		
		if(isset($merchant['wpjobboard_clickandpledge_Periodicity']) && is_array($merchant['wpjobboard_clickandpledge_Periodicity']) && count($merchant['wpjobboard_clickandpledge_Periodicity']) > 0) {
				$recurringhtml_eCheck .= '<br><div class="form-row" id="clickandpledge_Periodicity_div_eCheck">
				<label>
				  <span>'.__("Every", "wpjobboard").'</span>
				  <select id="clickandpledge_Periodicity_eCheck" name="clickandpledge_Periodicity_eCheck">';
				foreach ($merchant['wpjobboard_clickandpledge_Periodicity'] as $r) {
					$recurringhtml_eCheck .= '<option value="'.$r.'">'.$r.'</option>';
				}  
				$recurringhtml_eCheck .= '</select>
				</label>&nbsp;
				<span id="clickandpledge_Installment_div_eCheck"><input type="text" name="clickandpledge_Installment_eCheck" id="clickandpledge_Installment_eCheck" class="required" title="Installments" style="width:100px;" maxlength="3"/>&nbsp;<font color="#FF0000">*</font> payments</span>
			  </div>
			  <script>
				jQuery("#clickandpledge_Installment_eCheck").keypress(function(e) {
					var a = [];
					var k = e.which;

					for (i = 48; i < 58; i++)
						a.push(i);

					if (!(a.indexOf(k)>=0))
						e.preventDefault();
				});
				</script>';
		} else {
			$Periodicity = array();
			$Periodicity['Week']		= 'Week';
			$Periodicity['2 Weeks']	= '2 Weeks';
			$Periodicity['Month']		= 'Month';
			$Periodicity['2 Months']	= '2 Months';
			$Periodicity['Quarter']	= 'Quarter';
			$Periodicity['6 Months']	= '6 Months';
			$Periodicity['Year']		= 'Year';			
			$recurringhtml_eCheck .= '<br><div class="form-row" id="clickandpledge_Periodicity_div_eCheck">
				<label>
				  <span>'.__("Every", "wpjobboard").'</span>
				  <select id="clickandpledge_Periodicity_eCheck" name="clickandpledge_Periodicity_eCheck">';
				foreach ($Periodicity as $k => $v) {
					$recurringhtml_eCheck .= '<option value="'.$k.'">'.$v.'</option>';
				}  
				$recurringhtml_eCheck .= '</select>
				</label>&nbsp;
				<span id="clickandpledge_Installment_div_eCheck"><input type="text" name="clickandpledge_Installment_eCheck" id="clickandpledge_Installment_eCheck" class="required" title="Installments" style="width:100px;" maxlength="3"/>&nbsp;<font color="#FF0000">*</font> payments</span>
			  </div>
			  <script>
				jQuery("#clickandpledge_Installment_eCheck").keypress(function(e) {
					var a = [];
					var k = e.which;

					for (i = 48; i < 58; i++)
						a.push(i);

					if (!(a.indexOf(k)>=0))
						e.preventDefault();
				});
				</script>';
		}		
		
		 }
		$html .= '<div style="display:'.$eCheckdivdisplay.'" id="cnp_eCheck_div">';
		$html .= $clickandpledgename_eCheck;
		$html .= '
		<div class="form-row">
			<label>
			  <span>'.__("Routing Number&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <input type="text" data-clickandpledge="number" name="clickandpledge_echeck_RoutingNumber" id="clickandpledge_echeck_RoutingNumber" class="required RoutingNumber"  placeholder="Routing Number"/>
			</label>
		 </div>
		 <div class="form-row">
			<label>
			  <span>'.__("Check Number&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <input type="text" data-clickandpledge="number" name="clickandpledge_echeck_CheckNumber" id="clickandpledge_echeck_CheckNumber" class="required CheckNumber"  placeholder="Check Number"/>
			</label>
		 </div>
		 <div class="form-row">
			<label>
			  <span>'.__("Account Number&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <input type="text" data-clickandpledge="number" name="clickandpledge_echeck_AccountNumber" id="clickandpledge_echeck_AccountNumber" class="required AccountNumber"  placeholder="Account Number"/>
			</label>
		 </div>
		 <div class="form-row">
			<label>
			  <span>'.__("Retype Account Number&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <input type="text" data-clickandpledge="number" name="clickandpledge_echeck_retypeAccountNumber" id="clickandpledge_echeck_retypeAccountNumber" class="required AccountNumber" placeholder="Retype Account Number"/>
			</label>
		 </div>
		 <div class="form-row">
			<label>
			  <span>'.__("Account Type&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <select name="clickandpledge_echeck_AccountType" id="clickandpledge_echeck_AccountType" title="Account Type">
					<option value="SavingsAccount">SavingsAccount</option>
					<option value="CheckingAccount">CheckingAccount</option>
			  </select>
			</label>
		 </div>
		 <div class="form-row">
			<label>
			  <span>'.__("Check Type&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <select name="clickandpledge_echeck_CheckType" id="clickandpledge_echeck_CheckType" title="Check Type">
					<option value="Company">Company</option>
					<option value="Personal">Personal</option>
			  </select>
			</label>
		 </div>
		 <div class="form-row">
			<label>
			  <span>'.__("Name on Account&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <input type="text" data-clickandpledge="number" name="clickandpledge_echeck_NameOnAccount" id="clickandpledge_echeck_NameOnAccount" class="required AccountNumber"  placeholder="Name on Account"/>
			</label>
		 </div>
		 <div class="form-row">
			<label>
			  <span>'.__("Type of ID&nbsp;<font color='red'>*</font>", "wpjobboard").'</span>
			  <select name="clickandpledge_echeck_IdType" id="clickandpledge_echeck_IdType" title="Type of ID">
					<option value="Driver">Driver</option>
					<option value="Military">Military</option>
					<option value="State">State</option>
			  </select>
			</label>
		 </div>';
		$html .= $recurringhtml_eCheck;
		$html .= '</div>'; //eCheck Div End
		
		$Invoicedivdisplay = ($defaultpayment == 'Invoice') ? 'block' : 'none';
		$html .= '<div style="display:'.$Invoicedivdisplay.'" id="cnp_Invoice_div">';
		$html .= $clickandpledgename_Invoice;
		$html .= '
		<div class="form-row">
			<label>
			  <span>'.__("Invoice Number", "wpjobboard").'</span>
			  <input type="text" data-clickandpledge="number" name="clickandpledge_Invoice_InvoiceNumber" id="clickandpledge_Invoice_InvoiceNumber" class="InvoiceCheckNumber"  placeholder="Invoice Number"/>
			</label>
		 </div>';
		$html .= '</div>'; //Invoice Div End
		
		$PurchaseOrderdivdisplay = ($defaultpayment == 'PurchaseOrder') ? 'block' : 'none';
		$html .= '<div style="display:'.$PurchaseOrderdivdisplay.'" id="cnp_PurchaseOrder_div">';
		$html .= $clickandpledgename_PO;
		$html .= '
		<div class="form-row">
			<label>
			  <span>'.__("Purchase Order Number", "wpjobboard").'</span>
			  <input type="text" data-clickandpledge="number" name="clickandpledge_PurchaseOrder_OrderNumber" id="clickandpledge_PurchaseOrder_OrderNumber" class="PurchaseOrderNumber"  placeholder="Purchase Order Number"/>
			</label>
		 </div>';
		$html .= '</div>'; //PurchaseOrder Div End
			$listing_id = '';
			if(isset($_POST['listing']) && $_POST['listing'] != '')
			$listing_id = $_POST['listing'];
			elseif(isset($_POST['listing_type']) && $_POST['listing_type'] != '')
			$listing_id = $_POST['listing_type'];
			$varArray = array(
				'clickandpledge_AccountID'=>$merchant['wpjobboard_clickandpledge_'.$selectedCurrency.'_AccountID'],
				'clickandpledge_AccountGuid'=>$merchant['wpjobboard_clickandpledge_'.$selectedCurrency.'_AccountGuid'],
				'clickandpledge_OrderMode' => $merchant['wpjobboard_clickandpledge_'.$selectedCurrency.'_OrderMode'],
				'clickandpledge_Amount' => $this->_data->payment_sum+$this->_data->payment_discount,
				'clickandpledge_Discount' => $this->_data->payment_discount,
				'clickandpledge_OrganizationInformation' => htmlspecialchars($merchant['wpjobboard_clickandpledge_OrganizationInformation']),
				'clickandpledge_TermsCondition' => htmlspecialchars($merchant['wpjobboard_clickandpledge_TermsCondition']),
				'clickandpledge_email_customer' => (is_array($merchant['wpjobboard_clickandpledge_emailcustomer']) && count($merchant['wpjobboard_clickandpledge_emailcustomer']) > 0) ? $merchant['wpjobboard_clickandpledge_emailcustomer'][0] : '',
				'clickandpledge_maxrecurrings_Subscription' => $merchant['wpjobboard_clickandpledge_maxrecurrings_Subscription'],
				'clickandpledge_maxrecurrings_Installment' => $merchant['wpjobboard_clickandpledge_maxrecurrings_Installment'],
				'clickandpledge_listing_id' => $listing_id,
				'clickandpledge_coupon_code' => (isset($_POST['coupon'])) ? $_POST['coupon'] : '',
			);
			foreach($varArray as $k=>$v)
			{
				$html.= '<input type="hidden" name="'.$k.'" id="'.$k.'" value="'.$v.'" />';
			}
		  $html .= '<button type="submit">'.__("Submit Payment", "wpjobboard").'</button>
		</div>
		</form>';
		} else {
			if(count($availableCurrencies) > 0) {
			$html = 'Selected currency <b>'.$selectedCurrency.'</b> not supported by Click & Pledge. We are supporting <b>'.implode(',', $availableCurrencies).'</b> only. Please contact administrator.';
			} else {
			$html = 'Selected currency <b>'.$selectedCurrency.'</b> not supported by Click & Pledge. Please contact administrator.';
			}
		}
		return $html;        
     }
	 public function getMonths() {
		$str = '';
		for ($i = 1; $i <= 12; $i++) {
			if(date('m') == sprintf('%02d', $i)) {
			$str .= '<option value="'.sprintf('%02d', $i).'" selected>'.sprintf('%02d', $i).' ('.strftime('%B', mktime(0, 0, 0, $i, 1, 2000)).')</option>';
			} else {
			$str .= '<option value="'.sprintf('%02d', $i).'">'.sprintf('%02d', $i).' ('.strftime('%B', mktime(0, 0, 0, $i, 1, 2000)).')</option>';
			}			
		}
		return $str;
	 }
	 public function getYears() {		 
		 $str = '';
		 for ($i = date('Y'); $i < date('Y') + 11; $i++) {
			$str .= '<option value="'.strftime('%Y', mktime(0, 0, 0, 1, 1, $i)).'">'.strftime('%Y', mktime(0, 0, 0, 1, 1, $i)).'</option>';				
		}
		return $str;
	 }
	 public function number_format($number, $decimals = 2,$decsep = '', $ths_sep = '') {
		$parts = explode('.', $number);
		if(count($parts) > 1) {
			return $parts[0].'.'.substr($parts[1],0,$decimals);
		} else {
			return $number;
		}
	}
}
?>