<?php
class Config_ClickandPledge extends Wpjb_Form_Abstract_Payment
{ 
    public function init()
    {
        parent::init();
		
		parent::init();		
		
        wp_register_script( 'clickandpledge-admin-script', plugins_url( '/clickandpledge-admin.js', __FILE__ ) );
		wp_enqueue_script( 'clickandpledge-admin-script' );		
		$this->addGroup("clickandpledge", __("Click & Pledge", "wpjobboard"));
		
		
		//USD Account
		$this->_env = array(
            'USD' => __("", "wpjobboard"),
        );
		$e = $this->create("wpjobboard_clickandpledge_usdaccount", Daq_Form_Element::TYPE_CHECKBOX);
        $e->setValue($this->conf("wpjobboard_clickandpledge_usdaccount"));
        $e->setLabel(__("USD Account", "wpjobboard"));
		$e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge");
		
		$e = $this->create("wpjobboard_clickandpledge_USD_AccountID");
        $e->setValue($this->conf("wpjobboard_clickandpledge_USD_AccountID"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&P Account ID<span style='color: #ff0000;'>*</span>", "wpjobboard"));
		$e->setHint(__('Get your "Account ID" from Click & Pledge. [Portal > Account Info > API Information].', "wpjobboard"));
        $this->addElement($e, "clickandpledge");

        $e = $this->create("wpjobboard_clickandpledge_USD_AccountGuid");
        $e->setValue($this->conf("wpjobboard_clickandpledge_USD_AccountGuid"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&P API Account GUID<span style='color: #ff0000;'>*</span>", "wpjobboard"));
		$e->setHint(__('Get your "API Account GUID" from Click & Pledge [Portal > Account Info > API Information].', "wpjobboard"));
        $this->addElement($e, "clickandpledge");

        $this->_env = array(
            'test' => __("Test Mode", "wpjobboard"),
            'live' => __("Live Mode", "wpjobboard")
        );
        $e = $this->create("wpjobboard_clickandpledge_USD_OrderMode", Daq_Form_Element::TYPE_SELECT);
        $e->setValue($this->conf("wpjobboard_clickandpledge_USD_OrderMode"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;API Mode", "wpjobboard"));
        $e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge"); 
		//USD END
		
		//EUR Account
		$this->_env = array(
            'EUR' => __("", "wpjobboard"),
        );
		$e = $this->create("wpjobboard_clickandpledge_euraccount", Daq_Form_Element::TYPE_CHECKBOX);
        $e->setValue($this->conf("wpjobboard_clickandpledge_euraccount"));
        $e->setLabel(__("EUR Account", "wpjobboard"));
		$e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge");
		
		$e = $this->create("wpjobboard_clickandpledge_EUR_AccountID");
        $e->setValue($this->conf("wpjobboard_clickandpledge_EUR_AccountID"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&P Account ID<span style='color: #ff0000;'>*</span>", "wpjobboard"));
		$e->setHint(__('Get your "Account ID" from Click & Pledge. [Portal > Account Info > API Information].', "wpjobboard"));
        $this->addElement($e, "clickandpledge");

        $e = $this->create("wpjobboard_clickandpledge_EUR_AccountGuid");
        $e->setValue($this->conf("wpjobboard_clickandpledge_EUR_AccountGuid"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&P API Account GUID<span style='color: #ff0000;'>*</span>", "wpjobboard"));
		$e->setHint(__('Get your "API Account GUID" from Click & Pledge [Portal > Account Info > API Information].', "wpjobboard"));
        $this->addElement($e, "clickandpledge");

        $this->_env = array(
            'test' => __("Test Mode", "wpjobboard"),
            'live' => __("Live Mode", "wpjobboard")
        );
        $e = $this->create("wpjobboard_clickandpledge_EUR_OrderMode", Daq_Form_Element::TYPE_SELECT);
        $e->setValue($this->conf("wpjobboard_clickandpledge_EUR_OrderMode"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;API Mode", "wpjobboard"));
        $e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge");
		//EUR END
		
		//CAD Account
		$this->_env = array(
            'CAD' => __("", "wpjobboard"),
        );
		$e = $this->create("wpjobboard_clickandpledge_cadaccount", Daq_Form_Element::TYPE_CHECKBOX);
        $e->setValue($this->conf("wpjobboard_clickandpledge_cadaccount"));
        $e->setLabel(__("CAD Account", "wpjobboard"));
		$e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge");
		
		$e = $this->create("wpjobboard_clickandpledge_CAD_AccountID");
        $e->setValue($this->conf("wpjobboard_clickandpledge_CAD_AccountID"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&P Account ID<span style='color: #ff0000;'>*</span>", "wpjobboard"));
		$e->setHint(__('Get your "Account ID" from Click & Pledge. [Portal > Account Info > API Information].', "wpjobboard"));
        $this->addElement($e, "clickandpledge");

        $e = $this->create("wpjobboard_clickandpledge_CAD_AccountGuid");
        $e->setValue($this->conf("wpjobboard_clickandpledge_CAD_AccountGuid"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&P API Account GUID<span style='color: #ff0000;'>*</span>", "wpjobboard"));
		$e->setHint(__('Get your "API Account GUID" from Click & Pledge [Portal > Account Info > API Information].', "wpjobboard"));
        $this->addElement($e, "clickandpledge");

        $this->_env = array(
            'test' => __("Test Mode", "wpjobboard"),
            'live' => __("Live Mode", "wpjobboard")
        );
        $e = $this->create("wpjobboard_clickandpledge_CAD_OrderMode", Daq_Form_Element::TYPE_SELECT);
        $e->setValue($this->conf("wpjobboard_clickandpledge_CAD_OrderMode"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;API Mode", "wpjobboard"));
        $e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge");
		//CAD END
		
		//GBP Account
		$this->_env = array(
            'GBP' => __("", "wpjobboard"),
        );
		$e = $this->create("wpjobboard_clickandpledge_gbpaccount", Daq_Form_Element::TYPE_CHECKBOX);
        $e->setValue($this->conf("wpjobboard_clickandpledge_gbpaccount"));
        $e->setLabel(__("GBP Account", "wpjobboard"));
		$e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge");
		
		$e = $this->create("wpjobboard_clickandpledge_GBP_AccountID");
        $e->setValue($this->conf("wpjobboard_clickandpledge_GBP_AccountID"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&P Account ID<span style='color: #ff0000;'>*</span>", "wpjobboard"));
		$e->setHint(__('Get your "Account ID" from Click & Pledge. [Portal > Account Info > API Information].', "wpjobboard"));
        $this->addElement($e, "clickandpledge");

        $e = $this->create("wpjobboard_clickandpledge_GBP_AccountGuid");
        $e->setValue($this->conf("wpjobboard_clickandpledge_GBP_AccountGuid"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&P API Account GUID<span style='color: #ff0000;'>*</span>", "wpjobboard"));
		$e->setHint(__('Get your "API Account GUID" from Click & Pledge [Portal > Account Info > API Information].', "wpjobboard"));
        $this->addElement($e, "clickandpledge");

        $this->_env = array(
            'test' => __("Test Mode", "wpjobboard"),
            'live' => __("Live Mode", "wpjobboard")
        );
        $e = $this->create("wpjobboard_clickandpledge_GBP_OrderMode", Daq_Form_Element::TYPE_SELECT);
        $e->setValue($this->conf("wpjobboard_clickandpledge_GBP_OrderMode"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;API Mode", "wpjobboard"));
        $e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge");
		//GBP END
		
		//HKD Account
		$this->_env = array(
            'HKD' => __("", "wpjobboard"),
        );
		$e = $this->create("wpjobboard_clickandpledge_hkdaccount", Daq_Form_Element::TYPE_CHECKBOX);
        $e->setValue($this->conf("wpjobboard_clickandpledge_hkdaccount"));
        $e->setLabel(__("HKD Account", "wpjobboard"));
		$e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge");
		
		$e = $this->create("wpjobboard_clickandpledge_HKD_AccountID");
        $e->setValue($this->conf("wpjobboard_clickandpledge_HKD_AccountID"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&P Account ID<span style='color: #ff0000;'>*</span>", "wpjobboard"));
		$e->setHint(__('Get your "Account ID" from Click & Pledge. [Portal > Account Info > API Information].', "wpjobboard"));
        $this->addElement($e, "clickandpledge");

        $e = $this->create("wpjobboard_clickandpledge_HKD_AccountGuid");
        $e->setValue($this->conf("wpjobboard_clickandpledge_HKD_AccountGuid"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&P API Account GUID<span style='color: #ff0000;'>*</span>", "wpjobboard"));
		$e->setHint(__('Get your "API Account GUID" from Click & Pledge [Portal > Account Info > API Information].', "wpjobboard"));
        $this->addElement($e, "clickandpledge");

        $this->_env = array(
            'test' => __("Test Mode", "wpjobboard"),
            'live' => __("Live Mode", "wpjobboard")
        );
        $e = $this->create("wpjobboard_clickandpledge_HKD_OrderMode", Daq_Form_Element::TYPE_SELECT);
        $e->setValue($this->conf("wpjobboard_clickandpledge_HKD_OrderMode"));
        $e->setLabel(__("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;API Mode", "wpjobboard"));
        $e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge");
		//HKD END

		$this->_env = array(
            'CreditCard' => __("Credit Card", "wpjobboard"),
            'eCheck' => __("eCheck", "wpjobboard"),
			'Invoice' => __("Invoice", "wpjobboard"),
			'PurchaseOrder' => __("Purchase Order", "wpjobboard"),
        );
        $e = $this->create("wpjobboard_clickandpledge_Paymentmethods", Daq_Form_Element::TYPE_CHECKBOX);
        $e->setValue($this->conf("wpjobboard_clickandpledge_Paymentmethods"));
        $e->setLabel(__("Payment Methods", "wpjobboard"));
        $e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge");
		
		$this->_env = array(
            'CreditCard' => __("Credit Card", "wpjobboard"),
            'eCheck' => __("eCheck", "wpjobboard"),
			'Invoice' => __("Invoice", "wpjobboard"),
			'PurchaseOrder' => __("Purchase Order", "wpjobboard"),
        );
        $e = $this->create("wpjobboard_clickandpledge_DefaultpaymentMethod", Daq_Form_Element::TYPE_SELECT);
        $e->setValue($this->conf("wpjobboard_clickandpledge_DefaultpaymentMethod"));
        $e->setLabel(__("Default Payment Method", "wpjobboard"));
        $e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge");
		
		
		//Receipt Settings
		$this->addGroup("clickandpledge_receiptsettings", __("Receipt Settings", "wpjobboard"));
		$this->_env = array(
            'yes' => __("", "wpjobboard"),
        );
		$e = $this->create("wpjobboard_clickandpledge_emailcustomer", Daq_Form_Element::TYPE_CHECKBOX);
        $e->setValue($this->conf("wpjobboard_clickandpledge_emailcustomer"));
        $e->setLabel(__("Send Receipt to Patron", "wpjobboard"));
		$e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge_receiptsettings");
		
		$e = $this->create("wpjobboard_clickandpledge_OrganizationInformation", Daq_Form_Element::TYPE_TEXTAREA);
        $e->setValue($this->conf("wpjobboard_clickandpledge_OrganizationInformation"));
        $e->setLabel(__("Receipt Header", "wpjobboard"));
		$e->setHint(__('Maximum: 1500 characters, the following HTML tags are allowed:
&lt;P&gt;&lt;/P&gt;&lt;BR /&gt;&lt;OL&gt;&lt;/OL&gt;&lt;LI&gt;&lt;/LI&gt;&lt;UL&gt;&lt;/UL&gt;.  You have <span id="OrganizationInformation_countdown">1500</span> characters left.', "wpjobboard"));
        $this->addElement($e, "clickandpledge_receiptsettings");
		
		$e = $this->create("wpjobboard_clickandpledge_TermsCondition", Daq_Form_Element::TYPE_TEXTAREA);
        $e->setValue($this->conf("wpjobboard_clickandpledge_TermsCondition"));
        $e->setLabel(__("Terms & Conditions", "wpjobboard"));
		$e->setHint(__('To be added at the bottom of the receipt. Typically the text provides proof that the patron has read & agreed to the terms & conditions. The following HTML tags are allowed:&lt;P&gt;&lt;/P&gt;&lt;BR /&gt;&lt;OL&gt;&lt;/OL&gt;&lt;LI&gt;&lt;/LI&gt;&lt;UL&gt;&lt;/UL&gt;. <br>Maximum: 1500 characters, You have <span id="TermsCondition_countdown">1500</span> characters left.', "wpjobboard"));
        $this->addElement($e, "clickandpledge_receiptsettings");
		
		
		//Recurring Settings
		$this->addGroup("clickandpledge_recurringsettings", __("Recurring Settings", "wpjobboard"));		
        $e = $this->create("wpjobboard_clickandpledge_isRecurring", Daq_Form_Element::TYPE_SELECT);
		$e->addOption("0", "0", __("Disable", "wpjobboard"));
        $e->addOption(1, 1, __("Enable", "wpjobboard"));
        $e->setValue($this->conf("wpjobboard_clickandpledge_isRecurring"));
        $e->setLabel(__("Recurring Transaction", "wpjobboard"));
        $this->addElement($e, "clickandpledge_recurringsettings");
		
		$e = $this->create("wpjobboard_clickandpledge_RecurringLabel");
        $e->setValue($this->conf("wpjobboard_clickandpledge_RecurringLabel"));
        $e->setLabel(__("Label", "wpjobboard"));
        $this->addElement($e, "clickandpledge_recurringsettings");
		
		$this->_env = array(
            'Week' => __("Week", "wpjobboard"),
			'2 Weeks' => __("2 Weeks", "wpjobboard"),
			'Month' => __("Month", "wpjobboard"),
			'2 Months' => __("2 Months", "wpjobboard"),
			'Quarter' => __("Quarter", "wpjobboard"),
			'6 Months' => __("6 Months", "wpjobboard"),
			'Year' => __("Year", "wpjobboard"),
        );
		$e = $this->create("wpjobboard_clickandpledge_Periodicity", Daq_Form_Element::TYPE_CHECKBOX);
        $e->setValue($this->conf("wpjobboard_clickandpledge_Periodicity"));
        $e->setLabel(__("Periods", "wpjobboard"));
		$e->setHint(__('Supported recurring periods. If nothing selected all periods will display in front end.', "wpjobboard"));
		//$e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge_recurringsettings");
		
		$this->_env = array(
            'Subscription' => __("Subscription (example: Pay $10 every month for 20 times)", "wpjobboard"),
        );
		$e = $this->create("wpjobboard_clickandpledge_RecurringMethod_Subscription", Daq_Form_Element::TYPE_CHECKBOX);
        $e->setValue($this->conf("wpjobboard_clickandpledge_RecurringMethod_Subscription"));
        $e->setLabel(__("Recurring Method", "wpjobboard"));
		//$e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge_recurringsettings");
		
		$e = $this->create("wpjobboard_clickandpledge_maxrecurrings_Subscription");
        $e->setValue($this->conf("wpjobboard_clickandpledge_maxrecurrings_Subscription"));
        $e->setLabel(__("Subscription Max. Recurrings Allowed", "wpjobboard"));
		$e->setHint(__('Maximum number of payments allowed , range is 2-999.', "wpjobboard"));
        $this->addElement($e, "clickandpledge_recurringsettings");
		
		$this->_env = array(
			'Installment' => __("Installment (example: Split $1000 into 10 payments of $100 each)", "wpjobboard"),
        );
		$e = $this->create("wpjobboard_clickandpledge_RecurringMethod_Installment", Daq_Form_Element::TYPE_CHECKBOX);
        $e->setValue($this->conf("wpjobboard_clickandpledge_RecurringMethod_Installment"));
        $e->setLabel(__("", "wpjobboard"));
		//$e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge_recurringsettings");
		
		$e = $this->create("wpjobboard_clickandpledge_maxrecurrings_Installment");
        $e->setValue($this->conf("wpjobboard_clickandpledge_maxrecurrings_Installment"));
        $e->setLabel(__("Installment Max. Recurrings Allowed", "wpjobboard"));
		$e->setHint(__('Maximum number of payments allowed , range is 2-998.', "wpjobboard"));
        $this->addElement($e, "clickandpledge_recurringsettings");
		
		$this->_env = array(
            'on' => __("", "wpjobboard"),
        );
		$e = $this->create("wpjobboard_clickandpledge_indefinite", Daq_Form_Element::TYPE_CHECKBOX);
        $e->setValue($this->conf("wpjobboard_clickandpledge_indefinite"));
        $e->setLabel(__("Enable Indefinite Recurring", "wpjobboard"));
		//$e->addValidator(new Daq_Validate_InArray(array_keys($this->_env)));
        foreach($this->_env as $k => $v) {
            $e->addOption($k, $k,  $v);
        }
        $this->addElement($e, "clickandpledge_recurringsettings");
        
    }
}

?>
