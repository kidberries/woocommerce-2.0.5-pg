<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Local Pickup Shipping Method
 *
 * A simple shipping method allowing free pickup as a shipping method
 *
 * @class 		WC_Shipping_Free_Local_Pickup
 * @version		2.0.0
 * @package		WooCommerce/Classes/Shipping
 * @author 		WooThemes
 */
class WC_Shipping_Free_Local_Pickup extends WC_Shipping_Method {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		$this->id 		= 'free_local_pickup';
		$this->method_title = __( 'Free Local Pickup', 'woocommerce' );
		$this->init();
	}

    /**
     * init function.
     *
     * @access public
     * @return void
     */
    function init() {

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->enabled		= $this->get_option( 'enabled' );
		$this->title		= $this->get_option( 'title' );
		$this->address		= $this->get_option( 'address' );

		// Actions
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

	}

	/**
	 * calculate_shipping function.
	 *
	 * @access public
	 * @return void
	 */
	function calculate_shipping() {
		$rate = array(
			'id' 		=> $this->id,
			'label' 	=> $this->title,
		);
		$this->add_rate($rate);
	}

	/**
	 * init_form_fields function.
	 *
	 * @access public
	 * @return void
	 */
	function init_form_fields() {
    	global $woocommerce;
    	$this->form_fields = array(
			'enabled' => array(
				'title' 		=> __( 'Enable', 'woocommerce' ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable local pickup for free', 'woocommerce' ),
				'default' 		=> 'no'
			),
			'title' => array(
				'title' 		=> __( 'Title', 'woocommerce' ),
				'type' 			=> 'text',
				'description' 	=> __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'		=> __( 'Free Local Pickup', 'woocommerce' ),
				'desc_tip'      => true,
			),
			'address' => array(
				'title' 		=> __( 'Pickup point address', 'woocommerce' ),
				'type' 			=> 'textarea',
				'description' 	=> __( 'This controls the pickup place point which the user sees.', 'woocommerce' ),
				'default'		=> '',
				'desc_tip'      => true,
				'placeholder'	=> 'Your store or office placement address etc'
			),
		);
	}

	/**
	 * admin_options function.
	 *
	 * @access public
	 * @return void
	 */
	function admin_options() {
		global $woocommerce; ?>
		<h3><?php echo $this->method_title; ?></h3>
		<p><?php _e( 'Local pickup is a simple method which allows the customer to pick up their order themselves.', 'woocommerce' ); ?></p>
		<table class="form-table">
    		<?php $this->generate_settings_html(); ?>
    	</table> <?php
	}

	/**
	 * is_available function.
	 *
	 * @access public
	 * @param array $package
	 * @return bool
	 */
	function is_available( $package ) {
		global $woocommerce;

		$is_available = true;

		if ( $this->enabled == "no" )
			$is_available = false;

		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
	}


	/**
	* clean function.
	*
	* @access public
	* @param mixed $code
	* @return string
	*/
	function clean( $code ) {
		return str_replace( '-', '', sanitize_title( $code ) ) . ( strstr( $code, '*' ) ? '*' : '' );
	}

}
