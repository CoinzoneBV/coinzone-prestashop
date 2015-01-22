<?php
/**
 * AddTransactionResponse entity
 *
 * @category  classes
 * @author    Alex, 2014
 * @copyright Coinzone BV
 * @license   http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
 * @version   Release: 1.0.0 *
 */

class AddTransactionResponse
{

	/**
	 * @var
	 */
	private $status_code;

	/**
	 * @var
	 */
	private $message;

	/**
	 * @var
	 */
	private $ref_no;

	/**
	 * @var
	 */
	private $url;

	/**
	 * @var
	 */
	private $original_amount;

	/**
	 * @var
	 */
	private $original_currency;

	/**
	 * @var
	 */
	private $converted_amount;

	/**
	 * @var
	 */
	private $converted_currency;

	/**
	 * @var
	 */
	private $expiration_time;

	/**
	 * @var
	 */
	private $current_time;

	/**
	 * @param mixed $converted_amount
	 */
	public function setConvertedAmount($converted_amount)
	{
		$this->converted_amount = $converted_amount;
	}

	/**
	 * @return mixed
	 */
	public function getConvertedAmount()
	{
		return $this->converted_amount;
	}

	/**
	 * @param mixed $converted_currency
	 */
	public function setConvertedCurrency($converted_currency)
	{
		$this->converted_currency = $converted_currency;
	}

	/**
	 * @return mixed
	 */
	public function getConvertedCurrency()
	{
		return $this->converted_currency;
	}

	/**
	 * @param mixed $current_time
	 */
	public function setCurrentTime($current_time)
	{
		$this->current_time = $current_time;
	}

	/**
	 * @return mixed
	 */
	public function getCurrentTime()
	{
		return $this->current_time;
	}

	/**
	 * @param mixed $expiration_time
	 */
	public function setExpirationTime($expiration_time)
	{
		$this->expiration_time = $expiration_time;
	}

	/**
	 * @return mixed
	 */
	public function getExpirationTime()
	{
		return $this->expiration_time;
	}

	/**
	 * @return mixed
	 */
	public function getRefNo()
	{
		return $this->ref_no;
	}

	/**
	 * @param mixed $ref_no
	 */
	public function setRefNo($ref_no)
	{
		$this->ref_no = $ref_no;
	}

	/**
	 * @param mixed $message
	 */
	public function setMessage($message)
	{
		$this->message = $message;
	}

	/**
	 * @return mixed
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @param mixed $original_amount
	 */
	public function setOriginalAmount($original_amount)
	{
		$this->original_amount = $original_amount;
	}

	/**
	 * @return mixed
	 */
	public function getOriginalAmount()
	{
		return $this->original_amount;
	}

	/**
	 * @param mixed $original_currency
	 */
	public function setOriginalCurrency($original_currency)
	{
		$this->original_currency = $original_currency;
	}

	/**
	 * @return mixed
	 */
	public function getOriginalCurrency()
	{
		return $this->original_currency;
	}

	/**
	 * @param mixed $status_code
	 */
	public function setStatusCode($status_code)
	{
		$this->status_code = $status_code;
	}

	/**
	 * @return mixed
	 */
	public function getStatusCode()
	{
		return $this->status_code;
	}

	/**
	 * @param mixed $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @return mixed
	 */
	public function getUrl()
	{
		return $this->url;
	}


}
