<?php
/**
 * DisplayOrderInformation entity
 *
 * @category  classes
 * @author    Alex, 2014
 * @copyright Coinzone BV
 * @license   http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
 * @version   Release: 1.0.0 *
 */

class DisplayOrderInformation
{

	/**
	 * @var DisplayOrderInformationItem[]
	 */
	public $items;

	/**
	 * @var float
	 */
	public $tax;

	/**
	 * @var float
	 */
	public $shipping_cost;

	/**
	 * @var float
	 */
	public $discount;

	/**
	 * @param $items
	 * @param $discount
	 * @param $shipping_cost
	 * @param $tax
	 */
	public function __construct($items, $discount, $shipping_cost, $tax)
	{
		$this->items        = $items;
		$this->discount     = $discount;
		$this->shipping_cost = $shipping_cost;
		$this->tax          = $tax;
	}


	/**
	 * @param float $discount
	 */
	public function setDiscount($discount)
	{
		$this->discount = $discount;
	}

	/**
	 * @return float
	 */
	public function getDiscount()
	{
		return $this->discount;
	}

	/**
	 * @param \entities\DisplayOrderInformationItem[] $items
	 */
	public function setItems($items)
	{
		$this->items = $items;
	}

	/**
	 * @return \entities\DisplayOrderInformationItem[]
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * @param float $shipping_cost
	 */
	public function setShippingCost($shipping_cost)
	{
		$this->shipping_cost = $shipping_cost;
	}

	/**
	 * @return float
	 */
	public function getShippingCost()
	{
		return $this->shipping_cost;
	}

	/**
	 * @param float $tax
	 */
	public function setTax($tax)
	{
		$this->tax = $tax;
	}

	/**
	 * @return float
	 */
	public function getTax()
	{
		return $this->tax;
	}


}