<?php
/**
 * DisplayOrderInformationItem entity
 *
 * @category  classes
 * @author    Alex, 2014
 * @copyright Coinzone BV
 * @license   http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
 * @version   Release: 1.0.0 *
 */

class DisplayOrderInformationItem
{
	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var int
	 */
	public $quantity;

	/**
	 * @var float
	 */
	public $unit_price;

	/**
	 * @var string
	 */
	public $short_description;

	/**
	 * @var string
	 */
	public $image_url;

	/**
	 * @param $name
	 * @param $short_description
	 * @param $unit_price
	 * @param $quantity
	 * @param $image_url
	 */
	public function __construct($name, $short_description, $unit_price, $quantity, $image_url)
	{
		$this->image_url         = $image_url;
		$this->name             = $name;
		$this->quantity         = $quantity;
		$this->short_description = $short_description;
		$this->unit_price        = $unit_price;
	}


	/**
	 * @param string $image_url
	 */
	public function setImageUrl($image_url)
	{
		$this->image_url = $image_url;
	}

	/**
	 * @return string
	 */
	public function getImageUrl()
	{
		return $this->image_url;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param int $quantity
	 */
	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
	}

	/**
	 * @return int
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}

	/**
	 * @param string $short_description
	 */
	public function setShortDescription($short_description)
	{
		$this->short_description = $short_description;
	}

	/**
	 * @return string
	 */
	public function getShortDescription()
	{
		return $this->short_description;
	}

	/**
	 * @param float $unit_price
	 */
	public function setUnitPrice($unit_price)
	{
		$this->unit_price = $unit_price;
	}

	/**
	 * @return float
	 */
	public function getUnitPrice()
	{
		return $this->unit_price;
	}


}