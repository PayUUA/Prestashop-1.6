<?php
if (!defined('_CAN_LOAD_FILES_')) exit;

class payu extends PaymentModule
{
	var $cellArray = array(	'PAYU_MERCHANT_UAH', 'PAYU_SECRET_KEY_UAH','PAYU_MERCHANT_EUR', 'PAYU_SECRET_KEY_EUR','PAYU_MERCHANT_USD', 'PAYU_SECRET_KEY_USD', 'PAYU_SYSTEM_URL', 'PAYU_CURRENCY', 
							'PAYU_VAT', 'PAYU_DEBUG_MODE', 'PAYU_BACK_REF', 'PAYU_LANGUAGE' 
					   		);

	public function __construct()
	{
		$this->name = 'payu';
		$this->tab = 'payments_gateways';
		$this->version = '0.1';
		$this->author = 'PayU';
		
 		parent::__construct();
		$this->displayName = $this->l('PayU - credit card payment');
		$this->description = $this->l('PayU - credit card payment');
		$this->confirmUninstall = $this->l('Are you sure you want to remove the module ??');
	}

	public function install()
	{
			if (!parent::install() || !$this->registerHook('payment') || !$this->registerHook('paymentReturn') ||
		!$this->registerHook('shoppingCartExtra') || !$this->registerHook('backBeforePayment') || !$this->registerHook('rightColumn') ||
		!$this->registerHook('cancelProduct') || !$this->registerHook('productFooter') || !$this->registerHook('header'))
			return false;
		return true;
	}
	
			
			

	public function uninstall()
	{	
		foreach ( $this->cellArray as $val) 
			if ( !Configuration::deleteByName($val) ) 
			return false;
		if (!parent::uninstall() ) return false;
		return true;
	}

	public function Payu_getVar( $name )
	{
		return Configuration::get( "PAYU_".strtoupper( $name ) );
	}

	private function _displayForm()
	{
	
	$this->_html .= '<strong>IPN URL:</strong> '.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/payu/ipn.php <br/><br/>';
	
		$this->_html .=
		'<form action="'.Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Contact details').'</legend>
				<table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
					<tr><td colspan="2">'.$this->l('Please specify the PayU account details for customers').'.<br /><br /></td></tr>

					<tr>
						<td width="130" style="height: 35px;">'.$this->l('Merchant').' UAH</td>
						<td><input type="text" name="merchant_uah" value="'.$this->Payu_getVar("merchant_uah").'" style="width: 300px;" /></td>
					</tr>
					<tr>
						<td width="130" style="height: 35px;">'.$this->l('Secret key').' UAH</td>
						<td><input type="text" name="secret_key_uah" value="'.$this->Payu_getVar("secret_key_uah").'" style="width: 300px;" /></td>
					</tr>
					
					<tr>
						<td width="130" style="height: 35px;">'.$this->l('Merchant').' EUR</td>
						<td><input type="text" name="merchant_eur" value="'.$this->Payu_getVar("merchant_eur").'" style="width: 300px;" /></td>
					</tr>
					<tr>
						<td width="130" style="height: 35px;">'.$this->l('Secret key').' EUR</td>
						<td><input type="text" name="secret_key_eur" value="'.$this->Payu_getVar("secret_key_eur").'" style="width: 300px;" /></td>
					</tr>
					
					<tr>
						<td width="130" style="height: 35px;">'.$this->l('Merchant').' USD</td>
						<td><input type="text" name="merchant_usd" value="'.$this->Payu_getVar("merchant_usd").'" style="width: 300px;" /></td>
					</tr>
					<tr>
						<td width="130" style="height: 35px;">'.$this->l('Secret key').' USD</td>
						<td><input type="text" name="secret_key_usd" value="'.$this->Payu_getVar("secret_key_usd").'" style="width: 300px;" /></td>
					</tr>
					
					
					
					<tr><td colspan="2" align="center"><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
				</table>
			</fieldset>
			<input type="hidden" name="system_url" value="https://secure.payu.ua/order/lu.php" style="width: 300px;" />
			<input type="hidden" name="debug_mode" value="0" style="width: 300px;" />
			
		</form>';
	}

	private function _displayPayU()
	{
		$this->_html .= '<img src="../modules/payu/img/payu.jpg" style="float:left; margin-right:15px;"><b>'.
						$this->l('This module allows you to accept payments by PayU.').'</b><br /><br />'.
						$this->l('If the client chooses this payment mode, the order will change its status into a \'Waiting for payment\' status.').
						'<br /><br /><br />';
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error">'. $err .'</div>';
		}
		else
			$this->_html .= '<br />';
		$this->_displayPayU();
		$this->_displayForm();
		return $this->_html;
	}


	private function _postValidation()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
		/*$this->_postErrors[] = $this->l('Account details are required.');*/
		}
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit'))
		{	
			Configuration::updateValue('PAYU_MERCHANT_UAH', Tools::getValue('merchant_UAH'));
			Configuration::updateValue('PAYU_SECRET_KEY_UAH', Tools::getValue('secret_key_UAH'));
			Configuration::updateValue('PAYU_MERCHANT_EUR', Tools::getValue('merchant_eur'));
			Configuration::updateValue('PAYU_SECRET_KEY_EUR', Tools::getValue('secret_key_eur'));
			Configuration::updateValue('PAYU_MERCHANT_USD', Tools::getValue('merchant_usd'));
			Configuration::updateValue('PAYU_SECRET_KEY_USD', Tools::getValue('secret_key_usd'));
			Configuration::updateValue('PAYU_SYSTEM_URL', Tools::getValue('system_url'));
			Configuration::updateValue('PAYU_CURRENCY', Tools::getValue('currency'));
			Configuration::updateValue('PAYU_DEBUG_MODE', Tools::getValue('debug_mode'));
			Configuration::updateValue('PAYU_BACK_REF', Tools::getValue('back_ref'));
			Configuration::updateValue('PAYU_LANGUAGE', Tools::getValue('language'));
		}
		$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('ok').'" /> '.$this->l('Settings updated').'</div>';
	}

	# Display

	public function hookPayment($params)
	{
		if (!$this->active)	return;
		if (!$this->_checkCurrency($params['cart'])) return;

		global $smarty;
		$smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/',
			'this_description' => 'PayU - credit card payment'
		));

		return $this->display(__FILE__, 'payu.tpl');
	}

	private function _checkCurrency($cart)
	{
		$currency_order = new Currency((int)($cart->id_currency));
		$currencies_module = $this->getCurrency((int)$cart->id_currency);
		$currency_default = Configuration::get('PS_CURRENCY_DEFAULT');
		
		if (is_array($currencies_module))
			foreach ($currencies_module AS $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}

}
?>
