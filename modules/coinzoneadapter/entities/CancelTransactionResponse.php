<?php
/**
 * CancelTransactionResponse entity
 *
 * @category  classes
 * @author    Alex, 2014
 * @copyright Coinzone BV
 * @license   http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
 * @version   Release: 1.0.0 *
 */

class CancelTransactionResponse
{
	/**
	 * @var
	 */
	private $status_code;
	/**
	 * @var
	 */
	private $status_message;
	/**
	 * @var
	 */
	private $ref_no;
	/**
	 * @var
	 */
	private $date_added;

	/**
	 * @param mixed $date_added
	 */
	public function setDateAdded($date_added)
	{
		$this->date_added = $date_added;
	}

	/**
	 * @return mixed
	 */
	public function getDateAdded()
	{
		return $this->date_added;
	}

	/**
	 * @param mixed $ref_no
	 */
	public function setRefNo($ref_no)
	{
		$this->ref_no = $ref_no;
	}

	/**
	 * @return mixed
	 */
	public function getRefNo()
	{
		return $this->ref_no;
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
	 * @param mixed $status_message
	 */
	public function setStatusMessage($status_message)
	{
		$this->status_message = $status_message;
	}

	/**
	 * @return mixed
	 */
	public function getStatusMessage()
	{
		return $this->status_message;
	}


}