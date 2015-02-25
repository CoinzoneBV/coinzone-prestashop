<?php
/**
 * Open-source licence 3.0
 *
 * @author    Alex, 2014
 * @copyright Coinzone BV
 * @license   http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
 * @version   Release: 1.0.0
 */

class CoinzoneAdapterPaymentDisplayModuleFrontController extends ModuleFrontController
{
	public function postProcess()
	{
		$cart       = $this->context->cart;
		$payment_url = $this->getPaymentUrl($cart);

		if (!empty($payment_url))
		{
			Tools::redirect($payment_url);
			exit;
		}
		else
			die('Internal Error');
	}

	/**
	 * @param $cart
	 *
	 * @return bool
	 */
	public function getPaymentUrl($cart)
	{
		$display_order_information_items = array();

		$products = $cart->getProducts();

		$total = 0;
		foreach ($products as $product)
		{
			$image = $this->context->link->getImageLink(
				$product['link_rewrite'],
				$product['id_image'],
				ImageType::getFormatedName('small')
			);

			$display_order_information_item = new DisplayOrderInformationItem(
				$product['name'],
				$product['description_short'],
				$product['price'],
				$product['quantity'],
				$image
			);

			$total += $product['price'] * $product['quantity'];

			array_push($display_order_information_items, (array)$display_order_information_item);
		}

		$display_order_information = new DisplayOrderInformation(
			$display_order_information_items,
			(float)$cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS),
			(float)$cart->getOrderTotal(true, Cart::ONLY_SHIPPING),
			(float)$cart->getOrderTotal(true) - (float)$cart->getOrderTotal(false)
		);

		$total += (float)$cart->getOrderTotal(true, Cart::ONLY_SHIPPING);
		$total += (float)$cart->getOrderTotal(true) - (float)$cart->getOrderTotal(false);
		$total -= (float)$cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS);

		$description = '';
		if ($total != (float)$cart->getOrderTotal(true))
		{
			$display_order_information = null;
			$description = 'Item total';
		}

		$currency_object = new Currency($cart->id_currency);

		$customer = new Customer((int)$cart->id_customer);

		$transaction = new Transaction();
        $transaction->setPluginVersion(Configuration::get('COINZONE_PLUGIN_VERSION'));
		$transaction->setClientCode(Configuration::get('COINZONE_CLIENT_CODE'));
		$transaction->setApiKey(Configuration::get('COINZONE_API_KEY'));
		$transaction->setPrestashopContext($this->context->cart);
		$transaction->setAmount((float)$cart->getOrderTotal(true));
		$transaction->setCurrency($currency_object->iso_code);
		$transaction->setDescription($description);
		$transaction->setEmail($customer->email);
		$transaction->setNotificationUrl($this->context->link->getModuleLink('coinzoneadapter',
			'paymentNotification'));
		$transaction->setRedirectUrl($this->context->link->getModuleLink('coinzoneadapter', 'paymentReturn'));
		$transaction->setMerchantReference($cart->id);
		$transaction->setDisplayOrderInformation($display_order_information);

		$add_transaction_response = $transaction->addTransaction();

		return $add_transaction_response ? $add_transaction_response->getUrl() : null;

	}
}