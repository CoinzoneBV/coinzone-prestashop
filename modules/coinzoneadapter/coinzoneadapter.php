<?php
/**
 * Coinzone Adapter
 *
 * @author    Alex, 2014
 * @copyright Coinzone BV
 * @license   http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
 * @version   Release: 1.0.0
 */

if (! defined('_PS_VERSION_'))
	exit;


require_once dirname(__FILE__).'/entities/Transaction.php';
require_once dirname(__FILE__).'/entities/CancelTransactionResponse.php';
require_once dirname(__FILE__).'/entities/DisplayOrderInformation.php';
require_once dirname(__FILE__).'/entities/DisplayOrderInformationItem.php';

/**
 * Class CoinzoneAdapter
 */
class CoinzoneAdapter extends PaymentModule
{

	/**
	 * @var array
	 */
	public $currencies = array(
		'EUR',
		'USD',
		'SIT',
		'AUD',
		'CAD',
		'RON'
	);

	/**
	 *
	 */
	public function __construct()
	{
		$this->name                   = 'coinzoneadapter';
		$this->version                = 1.1;
		$this->author                 = 'Coinzone';
		$this->className              = 'CoinzoneAdapter';
		$this->currencies             = true;
		$this->currencies_mode        = 'checkbox';
		$this->tab                    = 'payments_gateways';
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');

		$this->bootstrap = true;

		if (_PS_VERSION_ > '1.5')
			$this->controllers = array('payment', 'validation');

		parent::__construct();

		$this->displayName = $this->l('Coinzone Adapter');
		$this->description = $this->l('Module for making payments using the Coinzone API.');

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

		if (! Configuration::get('COINZONE_CLIENT_CODE'))
			$this->warning = $this->l('No client code provided.');

		if (! Configuration::get('COINZONE_API_KEY'))
			$this->warning = $this->l('No api key provided.');

		if (_PS_VERSION_ < 1.5)
			$this->warning = $this->l('Coinzone Adapter not compatible with Prestashop '._PS_VERSION_.'. Install Prestashop 1.5 or higher.');

	}

	/**
	 * @return bool
	 */
	public function install()
	{
		if (Shop::isFeatureActive())
			Shop::setContext(Shop::CONTEXT_ALL);

		if (! function_exists('curl_version'))
		{
			$this->_errors[] = $this->l(
				'Sorry, this module requires the cURL PHP extension but it is not enabled on your server.
				Please ask your web hosting provider for assistance.'
			);
			return false;
		}

		if (!parent::install() || !$this->registerHook('header') || !$this->registerHook('payment') || !$this->registerHook('paymentReturn') ||
			!$this->registerHook('paymentNotification') || !$this->registerHook('paymentDisplay') || !$this->registerHook('adminOrder') ||
			!$this->installDb() || !Configuration::updateValue('COINZONE_TITLE', 'Coinzone Adapter') || !$this->registerHook('displayBackOfficeHeader'))
			return false;

		return true;
	}

	/**
	 * @return bool
	 */
	public function uninstall()
	{
		if (!parent::uninstall() || !Configuration::deleteByName('COINZONE_TITLE'))
			return false;

		return true;
	}

	/**
	 * @return bool
	 */
	private function installDb()
	{
		return Db::getInstance()->Execute(
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'coinzone_transaction` (
			`id_coinzone_transaction` int(11) NOT NULL AUTO_INCREMENT,
			`type` enum(\'payment\',\'refund\') NOT NULL,
			`id_shop` int(11) unsigned NOT NULL DEFAULT \'0\',
			`id_customer` int(11) unsigned NOT NULL,
			`id_cart` int(11) unsigned NOT NULL,
			`ref_no` varchar(32) NOT NULL,
			`amount` decimal(10,2) NOT NULL,
			`reason` varchar(255),
			`currency` varchar(3) NOT NULL,
			`date_add` datetime NOT NULL,
			`status` enum(\'PENDING\',\'COMPLETE\') NOT NULL,
		    PRIMARY KEY (`id_coinzone_transaction`), KEY `idx_transaction` (`type`,`id_cart`))
		    ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1'
		);
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		$output = null;

		if (Tools::isSubmit('submit'.$this->name))
		{
			$coinzone_client_code = (string)Tools::getValue('COINZONE_CLIENT_CODE');
			$coinzone_api_key     = (string)Tools::getValue('COINZONE_API_KEY');

			if (!$coinzone_client_code || empty($coinzone_client_code))
				$output .= $this->displayError($this->l('Invalid client code value.'));
			elseif (!$coinzone_api_key || empty($coinzone_api_key))
				$output .= $this->displayError($this->l('Invalid api key value.'));
			else
			{
				$error = false;
				if ($error === false)
				{
					Configuration::updateValue('COINZONE_CLIENT_CODE', $coinzone_client_code);
					Configuration::updateValue('COINZONE_API_KEY', addslashes($coinzone_api_key));
					$output .= $this->displayConfirmation($this->l('Settings updated'));
				}
			}
		}

		$output .= $this->displayForm();

		return $output;

	}

	/**
	 * @return mixed
	 */
	public function displayForm()
	{
		// Get default language
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$fields_form = array();
		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Settings'),
			),
			'input'  => array(
				array(
					'type'     => 'free',
					'name'     => 'COINZONE_DESCRIPTION'
				),
				array(
					'type'     => 'text',
					'label'    => $this->l('Client Code'),
					'name'     => 'COINZONE_CLIENT_CODE',
					'required' => true,
					'size'     => 20
				),
				array(
					'type'     => 'text',
					'label'    => $this->l('API Key'),
					'name'     => 'COINZONE_API_KEY',
					'required' => true
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);

		$helper = new HelperForm();

		// Module, token and currentIndex
		$helper->module          = $this;
		$helper->name_controller = $this->name;
		$helper->token           = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex    = AdminController::$currentIndex.'&configure='.$this->name;

		// Language
		$helper->default_form_language    = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;

		// Title and toolbar
		$helper->title          = $this->displayName;
		$helper->show_toolbar   = true; // false -> remove toolbar
		$helper->toolbar_scroll = true; // yes - > Toolbar is always visible on the top of the screen.
		$helper->submit_action  = 'submit'.$this->name;
		$helper->toolbar_btn    = array(
			'save' =>
				array(
					'desc' => $this->l('Save'),
					'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
				),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);

		// Load current value
		$helper->fields_value['COINZONE_DESCRIPTION'] = '<div class="no-col-lg-offset-3">
		<p>Add your Client Code and API Key below to configure Coinzone.  This can be found on the API tab of the Settings page in the
		<a href="https://merchant.coinzone.com/settings#apiTab" target="_blank">Coinzone Control Panel</a>.</p>
		<p>Have questions?  Please visit our
		<a href="http://support.coinzone.com/" target="_blank">customer support site</a>.</p>
		<p>Don\'t have a Coinzone account?
		<a href="https://merchant.coinzone.com/signup?source=prestashop" target="_blank">Sign up for free</a>. </p>';
		$helper->fields_value['COINZONE_CLIENT_CODE'] = Configuration::get('COINZONE_CLIENT_CODE');
		$helper->fields_value['COINZONE_API_KEY']     = Configuration::get('COINZONE_API_KEY');

		return $helper->generateForm($fields_form);
	}

	/**
	 *
	 */
	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path.'css/style.css', 'all');
	}

	/**
	 * @param $params
	 *
	 * @return mixed
	 */
	public function hookPayment($params)
	{
		if (!$this->active)
			return;

		if (!$this->checkCurrency($params['cart']))
			return;

		$this->smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_bw' => $this->_path,
			'this_path_ssl' => $this->context->link->getModuleLink('coinzoneadapter', 'paymentDisplay')
		));

		return $this->display(__FILE__, 'payment.tpl');
	}

	/**
	 *
	 */
	public function hookPaymentNotification()
	{
		if (! $this->active)
			return;

		return $this->display(__FILE__, 'payment_notification.tpl');
	}

	/**
	 *
	 */
	public function hookPaymentDisplay()
	{
		if (! $this->active)
			return;

		return $this->display(__FILE__, 'payment_display.tpl');
	}

	/**
	 * @param $params
	 *
	 * @return mixed
	 */
	public function hookPaymentReturn()
	{
		if (! $this->active)
			return;

		//TODO something on return
		return $this->display(__FILE__, 'payment_return.tpl');
	}

	/**
	 * @throws PrestaShopDatabaseException
	 */
	public function hookAdminOrder()
	{
		$cart = $this->context->cart;

		if (isset($_REQUEST['refund_amount']) && isset($_REQUEST['ref_no']))
		{
			$transaction = new Transaction();
			$transaction->setClientCode(Configuration::get('COINZONE_CLIENT_CODE'));
			$transaction->setApiKey(Configuration::get('COINZONE_API_KEY'));
			$transaction->setAmount(round((float)$_REQUEST['refund_amount'], 2));
			$transaction->setPrestashopContext($this->context->cart);
			$transaction->setReason(isset($_REQUEST['refund_reason']) ? $_REQUEST['refund_reason'] : '');

			$currency_object = new Currency($cart->id_currency);
			$transaction->setCurrency($currency_object->iso_code);

			$cancel_transaction_response = $transaction->cancelTransaction($_REQUEST['ref_no']);
			if ($cancel_transaction_response->getStatusCode() != 201)
			{
				$this->context->smarty->assign('refund', 0);
				$this->context->smarty->assign('refund_error', $cancel_transaction_response->getStatusMessage());
			}
		}

		/* Check if the order was paid with this Addon and display the Transaction details */
		$id_cart = Order::getCartIdStatic((int)Tools::getValue('id_order'), $this->context->customer->id);

		if (Db::getInstance()->getValue('
				SELECT
					module
				FROM
					'._DB_PREFIX_.'orders
				WHERE
					id_cart = '.$id_cart) == $this->name)
		{
			/* Do not display the refund block unless the API crendetials are set */
			if (Configuration::get('COINZONE_CLIENT_CODE') == '' || Configuration::get('COINZONE_API_KEY') == '')
				return;

			/* Retrieve the transaction details */
			$transaction_details = Db::getInstance()->getRow('
				SELECT
 					*
				FROM
					'._DB_PREFIX_.'coinzone_transaction
				WHERE
					id_cart = '.$id_cart.'
				AND
					type = \'payment\'
				AND id_shop = '.(int)$this->context->shop->id);

			/* Get all the refunds previously made (to build a list and determine if another refund is still possible) */
			$refund_details = Db::getInstance()->ExecuteS('
				SELECT
					amount,
					date_add,
					currency,
					status,
					reason
				FROM
					'._DB_PREFIX_.'coinzone_transaction
				WHERE
					id_cart = '.$id_cart.'
				AND
					type = \'refund\'
				AND
					id_shop = '.(int)$this->context->shop->id.'
				ORDER BY date_add DESC');

			$this->context->smarty->assign(
				array(
					'more60d' => ((time() - strtotime($transaction_details['date_add'])) > (60 * 86400)),
					/* Do not allow refund if the order has been placed more than 60 days ago */
					'transaction_details' => $transaction_details,
					'refund_details'      => $refund_details
				)
			);

			return $this->display(__FILE__, 'views/templates/admin/admin-order.tpl');
		}
	}

	/**
	 * @param $cart
	 *
	 * @return bool
	 */
	public function checkCurrency($cart)
	{
		$currency_order = new Currency($cart->id_currency);
		$currencies_module = $this->getCurrency($cart->id_currency);

		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}

	/**
	 *
	 */
	public function hookDisplayBackOfficeHeader()
	{
		$this->context->controller->addCSS(($this->_path).'css/admin.css', 'all');
	}

}