<?php
/**
 * Open-source licence 3.0
 *
 * @author    Alex, 2014
 * @copyright Coinzone BV
 * @license   http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
 * @version   Release: 1.0.0
 */

require_once dirname(__FILE__).'/../../entities/Transaction.php';


/**
 * Class CoinzoneAdapterPaymentNotificationModuleFrontController
 */
class CoinzoneAdapterPaymentNotificationModuleFrontController extends ModuleFrontController
{

	/**
	 *
	 */
	public function postProcess()
	{
		header('Content-type: application/json');

		/** @var $module CoinzoneAdapter */

		$response = Tools::file_get_contents('php://input');

		$this->checkRequest($response);

		if (! empty($response))
		{
			$decoded_response = Tools::jsonDecode($response, true);
			if (json_last_error() !== JSON_ERROR_NONE)
				parse_str($response, $decoded_response);

			$transaction_response = new GetTransactionResponse();
			$transaction_response->setAmount($decoded_response['amount']);
			$transaction_response->setConvertedAmount($decoded_response['convertedAmount']);
			$transaction_response->setConvertedCurrency($decoded_response['convertedCurrency']);
			$transaction_response->setCurrency($decoded_response['currency']);
			$transaction_response->setMerchantReference($decoded_response['merchantReference']);
			$transaction_response->setRefNo($decoded_response['refNo']);
			$transaction_response->setStatus($decoded_response['status']);

			$cart     = new Cart((int)$transaction_response->getMerchantReference());
			$currency = new Currency((int)Currency::getIdByIsoCode($transaction_response->getCurrency()));

			if (! Validate::isLoadedObject($currency) || $currency->id != $cart->id_currency)
			{
				http_response_code(400);
				die(Tools::jsonEncode(
					array(
						'error'   => true,
						'message' => $this->module->l(
								'Invalid Currency ID'
							).' '.($currency->id.'|'.$cart->id_currency)
					)
				));
			}
			else
			{
				if (in_array($transaction_response->getStatus(), array( 'PAID', 'COMPLETE')))
					$order_status = (int)Configuration::get('PS_OS_PAYMENT');
				elseif ($transaction_response->getStatus() == 'REFUND')
				{
					if ($this->processRefund($transaction_response, $cart))
						$order_status = (int)Configuration::get('PS_OS_REFUND');
					else
						die(Tools::jsonEncode(array('success' => true)));
				}

				if ($cart->OrderExists())
				{
					$order                 = new Order((int)Order::getOrderByCartId($cart->id));
					$new_history           = new OrderHistory();
					$new_history->id_order = (int)$order->id;
					$new_history->changeIdOrderState((int)$order_status, $order, true);
					$new_history->addWithemail(true);

					die(Tools::jsonEncode(array('success' => true)));

				}
				else
				{
					$customer = new Customer((int)$cart->id_customer);

					if ($this->module->validateOrder(
						(int)$cart->id, (int)$order_status, (float)$transaction_response->getAmount(),
						$this->module->displayName, 'test', array(), null, false, $customer->secure_key))
						die(Tools::jsonEncode(array('success' => true)));
				}
			}

		}

	}

	private function processRefund($get_transaction_response, $cart)
	{
		Db::getInstance()->update(
			'coinzone_transaction',
			array('status' => pSQL('COMPLETE')),
			'id_cart = '.(int)$cart->id.' AND type = \''.pSQL(
				'refund'
			).'\' AND id_shop = '.(int)$cart->id_shop.' AND amount='.$get_transaction_response->getAmount().' AND status = \''.pSQL('PENDING').'\'',
			null,
			null,
			true
		);

		return Db::getInstance()->Affected_Rows();
	}

	private function checkRequest($string)
	{
		$headers = $this->getHeaders();

		$transaction = new Transaction();
		$transaction->setApiKey(Configuration::get('COINZONE_API_KEY'));
		$signature = $transaction->createSignature(
			$this->context->link->getModuleLink('coinzoneadapter', 'paymentNotification'),
			$string,
			$headers['timestamp']
		);

		if ($headers['signature'] != $signature)
		{
			http_response_code(400);
			die(Tools::jsonEncode(
				array(
					'error'   => true,
					'message' => $this->module->l('Invalid signature.')
				)
			));
		}

	}

	private function getHeaders()
	{
		if (! function_exists('getallheaders'))
		{
			$headers = array();
			foreach ($_SERVER as $name => $value)
			{
				if (Tools::strtolower(Tools::substr($name, 0, 5)) == 'http_')
				{
					$headers[str_replace(
						' ',
						'-',
						ucwords(Tools::strtolower(str_replace('_', ' ', Tools::substr($name, 5))))
					)] = $value;
				}
			}
			$request_headers = $headers;
		}
		else
			$request_headers = getallheaders();

		foreach ($request_headers as $key => $value)
			$request_headers[Tools::strtolower($key)] = $value;
		return $request_headers;
	}
}