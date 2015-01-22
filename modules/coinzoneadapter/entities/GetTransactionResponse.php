<?php
/**
 * GetTransactionResponse entity
 *
 * @category  classes
 * @author    Alex, 2014
 * @copyright Coinzone BV
 * @license   http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
 * @version   Release: 1.0.0 *
 */

class GetTransactionResponse
{
	/**
	 * @var
	 */
	private $merchant_reference;
	/**
	 * @var
	 */
	private $ref_no;
	/**
	 * @var int $amount
	 */
	private $amount;

	/**
	 * @var string $currency
	 */
	private $currency;

	/**
	 * @var int $converted_amount
	 */
	private $converted_amount;

	/**
	 * @var string $converted_currency
	 */
	private $converted_currency;

	/**
	 * @var string $status
	 */
	private $status;

	/**
	 * @param int $amount
	 */
	public function setAmount($amount)
	{
		$this->amount = $amount;
	}

	/**
	 * @return int
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * @param int $converted_amount
	 */
	public function setConvertedAmount($converted_amount)
	{
		$this->converted_amount = $converted_amount;
	}

	/**
	 * @return int
	 */
	public function getConvertedAmount()
	{
		return $this->converted_amount;
	}

	/**
	 * @param string $converted_currency
	 */
	public function setConvertedCurrency($converted_currency)
	{
		$this->converted_currency = $converted_currency;
	}

	/**
	 * @return string
	 */
	public function getConvertedCurrency()
	{
		return $this->converted_currency;
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
	 * @return mixed
	 */
	public function getMerchantReference()
	{
		return $this->merchant_reference;
	}

	/**
	 * @param mixed $merchant_reference
	 */
	public function setMerchantReference($merchant_reference)
	{
		$this->merchant_reference = $merchant_reference;
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
	 * @param string $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}
}
