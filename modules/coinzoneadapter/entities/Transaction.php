<?php
/**
 * Transaction entity
 *
 * @category  classes
 * @author    Alex, 2014
 * @copyright Coinzone BV
 * @license   http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
 * @version   Release: 1.0.0 *
 */

require_once dirname(__FILE__).'/AddTransactionResponse.php';
require_once dirname(__FILE__).'/GetTransactionResponse.php';
require_once dirname(__FILE__).'/CancelTransactionResponse.php';

/**
 * Class Transaction
 */
class Transaction
{

    private $plugin_version;

	/**
	 * @var string
	 */
	private $api_url = 'https://api.coinzone.com/v2/';

	/**
	 * @var string
	 */
	private $amount;

	/**
	 * @var string
	 */
	private $currency;

	/**
	 * @var string
	 */
	private $merchant_reference;

	/**
	 * @var DisplayOrderInformation
	 */
	private $display_order_information;

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var string
	 */
	private $redirect_url;

	/**
	 * @var string
	 */
	private $notification_url;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var
	 */
	private $client_code;

	/**
	 * @var
	 */
	private $api_key;

	/**
	 * @var
	 */
	private $prestashop_context;

	/**
	 * @var
	 */
	private $reason;

    /**
     * @return mixed
     */
    public function getPluginVersion()
    {
        return $this->plugin_version;
    }

    /**
     * @param mixed $plugin_version
     */
    public function setPluginVersion($plugin_version)
    {
        $this->plugin_version = $plugin_version;
    }

	/**
	 * @param mixed $reason
	 */
	public function setReason($reason)
	{
		$this->reason = $reason;
	}

	/**
	 * @return mixed
	 */
	public function getReason()
	{
		return $this->reason;
	}

	/**
	 * @param string $amount
	 */
	public function setAmount($amount)
	{
		$this->amount = $amount;
	}

	/**
	 * @return string
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * @param mixed $api_key
	 */
	public function setApiKey($api_key)
	{
		$this->api_key = $api_key;
	}

	/**
	 * @return mixed
	 */
	public function getApiKey()
	{
		return $this->api_key;
	}

	/**
	 * @param string $api_url
	 */
	public function setApiUrl($api_url)
	{
		$this->api_url = $api_url;
	}

	/**
	 * @return string
	 */
	public function getApiUrl()
	{
		return $this->api_url;
	}

	/**
	 * @param mixed $client_code
	 */
	public function setClientCode($client_code)
	{
		$this->client_code = $client_code;
	}

	/**
	 * @return mixed
	 */
	public function getClientCode()
	{
		return $this->client_code;
	}

	/**
	 * @param string $currency
	 */
	public function setCurrency($currency)
	{
		$this->currency = $currency;
	}

	/**
	 * @return string
	 */
	public function getCurrency()
	{
		return $this->currency;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param \DisplayOrderInformation $display_order_information
	 */
	public function setDisplayOrderInformation($display_order_information)
	{
		$this->display_order_information = $display_order_information;
	}

	/**
	 * @return \DisplayOrderInformation
	 */
	public function getDisplayOrderInformation()
	{
		return $this->display_order_information;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param string $notification_url
	 */
	public function setNotificationUrl($notification_url)
	{
		$this->notification_url = $notification_url;
	}

	/**
	 * @return string
	 */
	public function getNotificationUrl()
	{
		return $this->notification_url;
	}

	/**
	 * @param string $redirect_url
	 */
	public function setRedirectUrl($redirect_url)
	{
		$this->redirect_url = $redirect_url;
	}

	/**
	 * @return string
	 */
	public function getRedirectUrl()
	{
		return $this->redirect_url;
	}

	/**
	 * @return string
	 */
	public function getMerchantReference()
	{
		return $this->merchant_reference;
	}

	/**
	 * @param string $merchant_reference
	 */
	public function setMerchantReference($merchant_reference)
	{
		$this->merchant_reference = $merchant_reference;
	}

	/**
	 * @param mixed $prestashop_context
	 */
	public function setPrestashopContext($prestashop_context)
	{
		$this->prestashop_context = $prestashop_context;
	}

	/**
	 * @return mixed
	 */
	public function getPrestashopContext()
	{
		return $this->prestashop_context;
	}

	/**
	 *
	 */
	public function addTransaction()
	{
		$timestamp = time();

		$headers = array(
			'Content-Type: application/json',
			'clientCode: '.$this->client_code,
			'timestamp: '.$timestamp
		);

		$display_order_information = $this->processDisplayOrderInformation((array)$this->getDisplayOrderInformation());
		$paydata = array(
			'amount'                  => $this->getAmount(),
			'currency'                => $this->getCurrency(),
			'email'                   => $this->getEmail(),
			'redirectUrl'             => $this->getRedirectUrl(),
			'notificationUrl'         => $this->getNotificationUrl(),
			'merchantReference'       => $this->getMerchantReference(),
			'description'             => $this->getDescription(),
			'displayOrderInformation' => $display_order_information,
            'userAgent' => 'Prestashop '. _PS_VERSION_ . ' - Plugin Version ' . $this->getPluginVersion()
		);

		$signature = $this->createSignature($this->api_url.'transaction', Tools::jsonEncode($paydata), $timestamp);
		$response = $this->sendApiCurl($this->api_url.'transaction', $headers, $signature, $paydata);

		if (empty($response) || $response->status->code != 201)
		{
			$error = Tools::displayError(sprintf('addTransaction: %s', (empty($response) ? null : $response->status->message)));
			Logger::addLog($error, 3, '0000002', 'Coinzone Adapter', (empty($response) ? null : $response->status->message));

			return;
		}

		$add_transaction_response = new AddTransactionResponse();
		$add_transaction_response->setStatusCode($response->status->code);
		$add_transaction_response->setMessage($response->status->message);
		$add_transaction_response->setConvertedCurrency($response->response->convertedCurrency);
		$add_transaction_response->setConvertedAmount($response->response->convertedAmount);
		$add_transaction_response->setCurrentTime($response->response->currentTime);
		$add_transaction_response->setExpirationTime($response->response->expirationTime);
		$add_transaction_response->setRefNo($response->response->refNo);
		$add_transaction_response->setOriginalAmount($response->response->amount);
		$add_transaction_response->setOriginalCurrency($response->response->currency);
		$add_transaction_response->setUrl($response->response->url);
		$this->addTransactionToDb(
			'payment',
			array(
				'refNo'    => $add_transaction_response->getRefNo(),
				'amount'   => $add_transaction_response->getOriginalAmount(),
				'currency' => $add_transaction_response->getOriginalCurrency(),
				'cart'     => $this->getPrestashopContext(),
				'reason'   => ''
			)
		);

		return $add_transaction_response;

	}


	/**
	 * @param $ref_no
	 *
	 * @return GetTransactionResponse
	 */
	public function getTransaction($ref_no)
	{
		$timestamp = time();

		$headers = array(
			'Content-Type: application/json',
			'clientCode: '.$this->client_code,
			'timestamp: '.$timestamp
		);

		$signature = $this->createSignature($this->api_url.'transaction/'.$ref_no, null, $timestamp);

		$response = $this->sendApiCurl($this->api_url.'transaction/'.$ref_no, $headers, $signature, $ref_no, 'GET');

		if (empty($response) || $response->status->code != 200)
		{
			$error = Tools::displayError(sprintf('getTransaction: %s', (empty($response) ? null : $response->status->message)));
			Logger::addLog($error, 3, '0000002', 'Coinzone Adapter', (empty($response) ? null : $response->status->message));

			return;
		}

		$transaction_response = new GetTransactionResponse();
		$transaction_response->setAmount($response->response->amount);
		$transaction_response->setConvertedAmount($response->response->convertedAmount);
		$transaction_response->setConvertedCurrency($response->response->convertedCurrency);
		$transaction_response->setCurrency($response->response->currency);
		$transaction_response->setReference($response->response->merchantReference);
		$transaction_response->setIdTransaction($response->response->refNo);
		$transaction_response->setStatus($response->response->status);

		return $transaction_response;

	}

	/**
	 * @param $ref_no
	 *
	 * @return CancelTransactionResponse
	 */
	public function cancelTransaction($ref_no)
	{
		$timestamp = time();

		$headers = array(
			'Content-Type: application/json',
			'clientCode: '.$this->client_code,
			'timestamp: '.$timestamp
		);

		$paydata = array(
			'amount'   => $this->getAmount(),
			'currency' => $this->getCurrency(),
			'refNo'    => $ref_no,
			'reason'   => $this->getReason(),
            'userAgent' => 'Prestashop '. _PS_VERSION_ . ' - Plugin Version ' . $this->getPluginVersion()
		);

		$signature = $this->createSignature($this->api_url.'cancel_request', Tools::jsonEncode($paydata),
			$timestamp);

		$response = $this->sendApiCurl($this->api_url.'cancel_request', $headers, $signature, $paydata);
		$cancel_transaction_response = new CancelTransactionResponse();
		$cancel_transaction_response->setStatusCode($response->status->code);
		$cancel_transaction_response->setStatusMessage($response->status->message);

		if (! empty($response) && $response->status->code == '201')
		{
			$this->addTransactionToDb(
				'refund',
				array(
					'refNo'    => $ref_no,
					'amount'   => $this->getAmount(),
					'currency' => $this->getCurrency(),
					'cart'     => $this->getPrestashopContext(),
					'reason'   => $this->getReason()
				)
			);

			$cancel_transaction_response->setRefNo($response->response->refNo);
			$cancel_transaction_response->setDateAdded($response->response->dateAdded);

		}
		else
		{
			$error = Tools::displayError(sprintf('cancelTransaction: %s', (empty($response) ? null : $response->status->message)));
			Logger::addLog($error, 3, '0000002', 'Coinzone Adapter', (empty($response) ? null : $response->status->message));

			return;
		}
		return $cancel_transaction_response;
	}

	/**
	 * @param $api_url
	 * @param $string
	 * @param $timestamp
	 *
	 * @return string
	 */
	public function createSignature($api_url, $string = null, $timestamp)
	{
		$string_to_sign = (empty($string) ? '' : $string).$api_url.$timestamp;
		$signature = $this->sha256Hmac($string_to_sign, $this->getApiKey());

		return $signature;
	}

	/**
	 * @param $api_url
	 * @param $headers
	 * @param $signature
	 * @param $paydata
	 * @param $request_type
	 *
	 * @return bool
	 */
	public function sendApiCurl($api_url, $headers, $signature, $paydata, $request_type = 'POST')
	{
		$content_length = 0;
		$content       = '';

		if (is_array($paydata))
		{
			$content_length = Tools::strlen(Tools::jsonEncode($paydata));
			$content       = Tools::jsonEncode($paydata);
		}
		array_push(
			$headers,
			'signature:'.$signature,
			'Content-Length: '.$content_length
		);

		$ch = curl_init($api_url);

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request_type);
		curl_setopt($ch, CURLOPT_POST, count($paydata));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$result = curl_exec($ch);

		if ($result === false)
			return curl_error($ch);

		$response = Tools::jsonDecode($result);
		curl_close($ch);

		return $response;
	}

	/**
	 * @param string $type
	 * @param $details
	 *
	 * @return mixed
	 */
	public function addTransactionToDb($type = 'payment', $details)
	{
		return Db::getInstance()->Execute(
			'INSERT INTO
                '._DB_PREFIX_.'coinzone_transaction
                (
                    type,
                    id_shop,
                    id_customer,
                    id_cart,
                    ref_no,
                    amount,
                    reason,
                    currency,
                    date_add,
                    status
               )
                VALUES
                (
                    \''.pSQL($type).'\',
                    '.(int)$details['cart']->id_shop.',
                    '.$details['cart']->id_customer.',
                    '.(int)$details['cart']->id.'
                    ,\''.pSQL($details['refNo']).'\',
                    \''.(float)$details['amount'].'\',
                    \''.Tools::substr(pSQL($details['reason']), 0, 255).'\',
                    \''.pSQL($details['currency']).
			'\',NOW(),
                    \'PENDING\'
               )'
		);
	}

	private function processDisplayOrderInformation($display_order_information_array)
	{
		if (isset($display_order_information_array['items']) && count($display_order_information_array['items']))
		{
			$items = count($display_order_information_array['items']);
			for ($index = 0; $index < $items; $index++)
			{
				$display_order_information_array['items'][$index]['unitPrice'] = $display_order_information_array['items'][$index]['unit_price'];
				$display_order_information_array['items'][$index]['shortDescription'] = Tools::substr(
					strip_tags($display_order_information_array['items'][$index]['short_description']), 0, 250);
				$display_order_information_array['items'][$index]['imageUrl'] = $display_order_information_array['items'][$index]['image_url'];

				unset($display_order_information_array['items'][$index]['unit_price']);
				unset($display_order_information_array['items'][$index]['short_description']);
				unset($display_order_information_array['items'][$index]['image_url']);
			}
		}

		if (isset($display_order_information_array['shipping_cost']))
		{
			$display_order_information_array['shippingCost'] = $display_order_information_array['shipping_cost'];
			unset($display_order_information_array['shipping_cost']);
		}

		return $display_order_information_array;
	}

	private function sha256Hmac($data, $key, $raw_output = false)
	{
		$pack = 'H'.Tools::strlen(hash('sha256', 'test'));
		$size = 64;
		$opad = str_repeat(chr(0x5C), $size);
		$ipad = str_repeat(chr(0x36), $size);

		if (Tools::strlen($key) > $size)
			$key = str_pad(pack($pack, hash('sha256', $key)), $size, chr(0x00));
		else
			$key = str_pad($key, $size, chr(0x00));

		$key_length = Tools::strlen( $key );
		for ($i = 0; $i < $key_length - 1; $i++)
		{
			$opad[$i] = $opad[$i] ^ $key[$i];
			$ipad[$i] = $ipad[$i] ^ $key[$i];
		}

		$output = hash('sha256', ($opad.pack($pack, hash('sha256', $ipad.$data))));
		return ($raw_output) ? pack($pack, $output) : $output;
	}
} 