<?php
/**
 * Open-source licence 3.0
 *
 * @author    Alex, 2014
 * @copyright Coinzone BV
 * @license   http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
 * @version   Release: 1.0.0
 */

class CoinzoneAdapterPaymentReturnModuleFrontController extends ModuleFrontController
{
	public function initContent()
	{
		parent::initContent();
		$this->setTemplate( 'payment_return.tpl' );
	}
}