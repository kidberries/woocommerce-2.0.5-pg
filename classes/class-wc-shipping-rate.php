<?php
/**
 * WooCommerce Shipping Rate Class
 *
 * Simple Class for storing rates.
 *
 * @class 		WC_Shipping_Rate
 * @version		2.0.0
 * @package		WooCommerce/Classes/Shipping
 * @category	Class
 * @author 		WooThemes
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Shipping_Rate {

	var $id 		= '';
	var $label 		= '';
	var $label_extra= '';
	var $cost 		= 0;
	var $cost_real  = 0;
	var $info       = array();
	var $taxes 		= array();
	var $method_id	= '';

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $id
	 * @param mixed $label
	 * @param mixed $cost
	 * @param mixed $taxes
	 * @return void
	 */
	public function __construct( $id, $label, $cost, $cost_real, $taxes, $method_id, $label_extra, $info ) {
		$this->id 		= $id;
		$this->label 		= $label;
		$this->label_extra	= $label_extra;
		$this->cost 		= $cost;
		$this->cost_real	= $cost_real;
		$this->taxes 		= $taxes ? $taxes : array();
		$this->info 		= $info ? $info : array();
		$this->method_id 	= $method_id;
		$this->options		= null;
	}

	/**
	 * get_shipping_tax function.
	 *
	 * @access public
	 * @return array
	 */
	function get_shipping_tax() {
		$taxes = 0;
		if ( $this->taxes && sizeof( $this->taxes ) > 0 )
			$taxes = array_sum( $this->taxes );
		return $taxes;
	}
}
