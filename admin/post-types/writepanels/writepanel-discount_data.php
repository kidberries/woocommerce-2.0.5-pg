<?php
/**
 * Discount Data
 *
 * Functions for displaying the discount data meta box.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/WritePanels
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Displays the discount data meta box.
 *
 * @access public
 * @param mixed $post
 * @return void
 */
function woocommerce_discount_data_meta_box( $post ) {
	global $woocommerce;

	wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );

	?>
	<style type="text/css">
		#edit-slug-box, #minor-publishing-actions { display:none }
	</style>
	<div id="discount_options" class="panel woocommerce_options_panel">
		<?php

			echo '<div class="options_group">';

                        
                        // Parent discount rule
			?>
			<p class="form-field"><label for="product_ids"><?php _e( 'Родительское правило', 'woocommerce' ) ?></label>
			<select id="parent_discount_rule" name="parent_discount_rule" class="chosen_select"  data-placeholder="<?php _e( 'Нет родителя', 'woocommerce' ); ?>">
				<?php
                                        global $wpdb;
                                        $possible_parent_rule = $wpdb->get_results("SELECT \"ID\", \"post_title\" FROM $wpdb->posts WHERE post_type = 'shop_discount' AND post_status = 'publish' AND \"ID\" != $post->ID ");
                                        
                                        $parent_discount_rule = $post->post_parent;
                                       
                                                echo '<option value="0"' . selected( $parent_discount_rule == 0, true, false ) . '>нет родительского правила</option>';
					if ( $possible_parent_rule ) foreach ( $possible_parent_rule as $parent_rule )
						echo '<option value="' . esc_attr( $parent_rule->ID ) . '"' . selected( $parent_rule->ID == $parent_discount_rule, true, false ) . '>' . esc_html( $parent_rule->post_title ) . '</option>';
				?>
			</select> <img class="help_tip" data-tip="<?php _e( 'Выбор родительского правила', 'woocommerce' ) ?>" src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
			<?php
                        echo '</div><div class="options_group">';
                        
                        
                        
			// Description
			woocommerce_wp_text_input( array( 'id' => 'discount_description', 'label' => __( 'Описание правила', 'woocommerce' ), 'description' => __( '', 'woocommerce' ), 'value' => $post->post_excerpt, 'name' => 'excerpt' ) );

			echo '</div><div class="options_group">';

            // Type
            woocommerce_wp_select( array( 'id' => 'discount_type', 'label' => __( 'Discount type', 'woocommerce' ), 'options' => $woocommerce->get_discount_discount_types() ) );

            // For Shipping
            woocommerce_wp_checkbox( array( 'id' => 'for_shipping', 'label' => __( 'На доставку', 'woocommerce' ), 'description' => __( 'Это правило управляет только стоимостью доставки', 'woocommerce' ) ) );

			//Shipping Types
            ?>
           
			<p class="form-field"><label for="shipping"><?php _e( 'Виды доставок', 'woocommerce' ) ?></label>
			<select id="shipping" name="shipping[]" class="chosen_select" multiple="multiple" data-placeholder="<?php _e( 'Any shipping', 'woocommerce' ); ?>">
				<?php
					$shipping_ids = maybe_unserialize( get_post_meta( $post->ID, 'shipping', true ) );
					foreach ( $woocommerce->shipping->load_shipping_methods() as $shipping)
						echo '<option value="' . esc_attr( $shipping->id ) . '"' . selected( in_array( $shipping->id, $shipping_ids ), true, false ) . '>' . esc_html( $shipping->title ) . '</option>';
				?>
			</select> <img class="help_tip" data-tip='<?php _e( 'Виды доставок на которые действует эта скидка', 'woocommerce' ) ?>' src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
			<?php

			// Times Amount
			woocommerce_wp_text_input( array( 'id' => 'times_the_amount', 'label' => __( 'Кратное количество', 'woocommerce' ), 'placeholder' => '1', 'description' => __( 'Это правило повлияет на каждый набор из N товаров корзины. Введите 0 если это правило должно распространяться на все подходящие товары в корзине.', 'woocommerce' ), 'type' => 'number', 'custom_attributes' => array(
					'step' 	=> '1',
					'min'	=> '0'
				)  ) );


			// Amount
                        
			woocommerce_wp_text_input( array( 'id' => 'discount_amount', 'label' => __( 'Discount amount', 'woocommerce' ), 'placeholder' => '0.00', 'description' => __( 'Value of the discount.', 'woocommerce' ), 'type' => 'number', 'custom_attributes' => array(
					'step' 	=> 'any',
					'min'	=> '0'
				)  ) );
                                    
			
			// Exclude Sale Products
			woocommerce_wp_checkbox( array( 'id' => 'exclude_sale_items', 'label' => __( 'Исключить распродажу', 'woocommerce' ), 'description' => __( 'Правило не распространяется на товары с распродажи', 'woocommerce' ) ) );

			echo '</div><div class="options_group">';
                            
			// minimum spend
                        /*
			woocommerce_wp_text_input( array( 'id' => 'minimum_amount', 'label' => __( 'Minimum amount', 'woocommerce' ), 'placeholder' => __( 'No minimum', 'woocommerce' ), 'description' => __( 'This field allows you to set the minimum subtotal needed to use the discount.', 'woocommerce' ), 'type' => 'number', 'custom_attributes' => array(
					'step' 	=> 'any',
					'min'	=> '0'
				) ) );

			echo '</div><div class="options_group">';
                          */
                        
			// Product ids
			?>
			<p class="form-field"><label for="product_ids"><?php _e( 'Товары', 'woocommerce' ) ?></label>
			<select id="product_ids" name="product_ids[]" class="ajax_chosen_select_products_and_variations" multiple="multiple" data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce' ); ?>">
				<?php
					$product_ids = get_post_meta( $post->ID, 'product_ids', true );
					if ( $product_ids ) {
						$product_ids = array_map( 'absint', explode( ',', $product_ids ) );
						foreach ( $product_ids as $product_id ) {

							$product      = get_product( $product_id );
							$product_name = woocommerce_get_formatted_product_name( $product );

							echo '<option value="' . esc_attr( $product_id ) . '" selected="selected">' . wp_kses_post( $product_name ) . '</option>';
						}
					}
				?>
			</select> <img class="help_tip" data-tip='<?php _e( 'Товары на которые распространяется данное правило', 'woocommerce' ) ?>' src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
			<?php


			// Categories
			?>
			<p class="form-field"><label for="product_categories"><?php _e( 'Категории товаров', 'woocommerce' ) ?></label>
			<select id="product_categories" name="product_categories[]" class="chosen_select" multiple="multiple" data-placeholder="<?php _e( 'Any category', 'woocommerce' ); ?>">
				<?php
					$category_ids = (array) get_post_meta( $post->ID, 'product_categories', true );

					$categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
					if ( $categories ) foreach ( $categories as $cat )
						echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
				?>
			</select> <img class="help_tip" data-tip='<?php _e( 'Категории товаров учавствующие в назначении скидки', 'woocommerce' ) ?>' src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
			<?php

                        
                        // Categories accopmaning goods
			?>
			<p class="form-field"><label for="product_ids"><?php _e( 'Товары этих категорий должны лежать в корзине', 'woocommerce' ) ?></label>
			<select id="product_categories_accompaining" name="product_categories_accompaining[]" class="chosen_select" multiple="multiple" data-placeholder="<?php _e( 'Any category', 'woocommerce' ); ?>">
				<?php
					$category_ids = (array) get_post_meta( $post->ID, 'product_categories_accompaining', true );

					$categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
					if ( $categories ) foreach ( $categories as $cat )
						echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
				?>
			</select> <img class="help_tip" data-tip='<?php _e( 'Категории товаров учавствующие в назначении скидки', 'woocommerce' ) ?>' src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
			<?php
                        
                         // Product Attributes
			?>
			<p class="form-field"><label for="product_ids"><?php _e( 'Атрибуты товаров', 'woocommerce' ) ?></label>
			<select id="product_attributes" name="product_attributes[]" class="chosen_select" multiple="multiple" data-placeholder="<?php _e( 'Any attribute', 'woocommerce' ); ?>">
				<?php
					$attributes_ids = (array) get_post_meta( $post->ID, 'product_attributes', true );

					//$categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
                                        $attributes_all = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies");
					if ( $attributes_all ) foreach ( $attributes_all as $attributes_record )
						echo '<option value="' . esc_attr( $attributes_record-> attribute_id ) . '"' . selected( in_array( $attributes_record->attribute_id, $attributes_ids ), true, false ) . '>' . esc_html( $attributes_record->attribute_name ) . '</option>';
				?>
			</select> <img class="help_tip" data-tip='<?php _e( 'Категории товаров учавствующие в назначении скидки', 'woocommerce' ) ?>' src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
			<?php
                        
                        
			// Rule date begin
			woocommerce_wp_text_input( array( 'id' => 'date_from', 'label' => __( 'Дата начала', 'woocommerce' ), 'placeholder' => _x('постоянно', 'placeholder', 'woocommerce'), 'description' => __( 'Укажите дату начала действия правила, <code>YYYY-MM-DD</code>.', 'woocommerce' ), 'class' => 'short date-picker', 'custom_attributes' => array( 'pattern' => "[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" ) ) );
                        
                        // Rule date finish
			woocommerce_wp_text_input( array( 'id' => 'date_to', 'label' => __( 'Дата окончания', 'woocommerce' ), 'placeholder' => _x('постоянно', 'placeholder', 'woocommerce'), 'description' => __( 'Укажите дату окончания действия правила, <code>YYYY-MM-DD</code>.', 'woocommerce' ), 'class' => 'short date-picker', 'custom_attributes' => array( 'pattern' => "[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" ) ) );
                        
                        // Rule start time
			woocommerce_wp_text_input( array( 'id' => 'time_from', 'label' => __( 'Время начала', 'woocommerce' ), 'placeholder' => _x('постоянно', 'placeholder', 'woocommerce'), 'description' => __( 'Укажите время начала действия правила, <code>HH:MM</code>.', 'woocommerce' ), 'class' => '', 'custom_attributes' => array( 'pattern' => "([0-1]\d|2[0123]):[0-5]\d" ) ) );

                        // Rule stop time
			woocommerce_wp_text_input( array( 'id' => 'time_to', 'label' => __( 'Время завершения', 'woocommerce' ), 'placeholder' => _x('постоянно', 'placeholder', 'woocommerce'), 'description' => __( 'Укажите время завершения действия правила, <code>HH:MM</code>.', 'woocommerce' ), 'class' => '', 'custom_attributes' => array( 'pattern' => "([0-1]\d|2[0123]):[0-5]\d" ) ) );
                        
			echo '</div>';

			do_action( 'woocommerce_discount_options' );
		?>
	</div>
	<?php
}


/**
 * Save the discount data meta box.
 *
 * @access public
 * @param mixed $post_id
 * @param mixed $post
 * @return void
 */
function woocommerce_process_shop_discount_meta( $post_id, $post ) {
    
	global $wpdb, $woocommerce_errors;
        
        
	// Ensure discount code is correctly formatted
	$post->post_title = apply_filters( 'woocommerce_discount_code', $post->post_title );
	$wpdb->update( $wpdb->posts, array( 'post_title' => $post->post_title ), array( 'ID' => $post_id ) );
        
        // Update parent discount rule
        if (isset($_POST['parent_discount_rule']) && preg_match('/^\d+$/',$_POST['parent_discount_rule'])){
            $parent_rule_query_ceck  = $wpdb->get_col("SELECT count(1) as check_fld FROM $wpdb->posts WHERE post_type = 'shop_discount' AND post_status = 'publish' AND \"ID\" = ".(int)$_POST['parent_discount_rule']);
            if ($parent_rule_query_ceck[0] > 0 || $_POST['parent_discount_rule'] == 0){
                $wpdb->update( $wpdb->posts, array( 'post_parent' => (int)$_POST['parent_discount_rule'] ), array( 'ID' => $post_id ) );
            }    
        }    

	// Check for dupe discounts
	$discount_found = $wpdb->get_var( $wpdb->prepare( "
		SELECT $wpdb->posts.\"ID\"
	    FROM $wpdb->posts
	    WHERE $wpdb->posts.post_type = 'shop_discount'
	    AND $wpdb->posts.post_status = 'publish'
	    AND $wpdb->posts.post_title = '%s'
	    AND $wpdb->posts.\"ID\" != %s
	 ", $post->post_title, $post_id ) );

	if ( $discount_found )
		$woocommerce_errors[] = __( 'Discount code already exists - customers will use the latest discount with this code.', 'woocommerce' );

	// Add/Replace data to array
	$type 			= woocommerce_clean( $_POST['discount_type'] );
	$amount 		= woocommerce_clean( $_POST['discount_amount'] );
	$times	 		= woocommerce_clean( $_POST['times_the_amount'] );
	$usage_limit 		= empty( $_POST['usage_limit'] ) ? '' : absint( $_POST['usage_limit'] );
	$individual_use 	= isset( $_POST['individual_use'] ) ? 'yes' : 'no';
	$date_from 		= woocommerce_clean( $_POST['date_from'] );
    $date_to 		= woocommerce_clean( $_POST['date_to'] );
    $time_from		= woocommerce_clean($_POST['time_from']);
    $time_to		= woocommerce_clean($_POST['time_to']);
	$apply_before_tax 	= isset( $_POST['apply_before_tax'] ) ? 'yes' : 'no';
	$free_shipping 		= isset( $_POST['free_shipping'] ) ? 'yes' : 'no';
	$exclude_sale_items	= isset( $_POST['exclude_sale_items'] ) ? 'yes' : 'no';
    $for_shipping	= isset( $_POST['for_shipping'] ) ? 'yes' : 'no';
	$minimum_amount 	= woocommerce_clean( $_POST['minimum_amount'] );
	$customer_email 	= array_filter( array_map( 'trim', explode( ',', woocommerce_clean( $_POST['customer_email'] ) ) ) );

	if ( isset( $_POST['product_ids'] ) ) {
		$product_ids 			= implode( ',', array_filter( array_map( 'intval', (array) $_POST['product_ids'] ) ) );
	} else {
		$product_ids = '';
	}

	if ( isset( $_POST['exclude_product_ids'] ) ) {
		$exclude_product_ids 	= implode( ',', array_filter( array_map( 'intval', (array) $_POST['exclude_product_ids'] ) ) );
	} else {
		$exclude_product_ids = '';
	}

    $shipping 			                = isset( $_POST['shipping'] ) ? serialize( $_POST['shipping'] ) : array();
	$product_categories 			    = isset( $_POST['product_categories'] ) ? array_map( 'intval', $_POST['product_categories'] ) : array();
    $product_categories_accompaining	= isset( $_POST['product_categories_accompaining'] ) ? array_map( 'intval', $_POST['product_categories_accompaining'] ) : array();
    $product_attributes 			    = isset( $_POST['product_attributes'] ) ? array_map( 'intval', $_POST['product_attributes'] ) : array();
	//$exclude_product_categories		= isset( $_POST['exclude_product_categories'] ) ? array_map( 'intval', $_POST['exclude_product_categories'] ) : array();

	// Save
	update_post_meta( $post_id, 'discount_type', $type );
	update_post_meta( $post_id, 'discount_amount', $amount );
	update_post_meta( $post_id, 'times_the_amount', ($times>=0)?(int)$times:0 );
	update_post_meta( $post_id, 'individual_use', $individual_use );
	update_post_meta( $post_id, 'product_ids', $product_ids );
	update_post_meta( $post_id, 'exclude_product_ids', $exclude_product_ids );
	update_post_meta( $post_id, 'usage_limit', $usage_limit );
	update_post_meta( $post_id, 'date_from', $date_from );
    update_post_meta( $post_id, 'date_to', $date_to );
    update_post_meta( $post_id, 'time_from', $time_from );
    update_post_meta( $post_id, 'time_to', $time_to );
        
	update_post_meta( $post_id, 'apply_before_tax', $apply_before_tax );
	update_post_meta( $post_id, 'free_shipping', $free_shipping );
	update_post_meta( $post_id, 'exclude_sale_items', $exclude_sale_items );
    update_post_meta( $post_id, 'for_shipping', $for_shipping );
    update_post_meta( $post_id, 'shipping', $shipping );
	update_post_meta( $post_id, 'product_categories', $product_categories );
    update_post_meta( $post_id, 'product_categories_accompaining', $product_categories_accompaining );
    update_post_meta( $post_id, 'product_attributes', $product_attributes );
	//update_post_meta( $post_id, 'exclude_product_categories', $exclude_product_categories );
	update_post_meta( $post_id, 'minimum_amount', $minimum_amount );
	update_post_meta( $post_id, 'customer_email', $customer_email );

	do_action( 'woocommerce_discount_options' );
}

add_action( 'woocommerce_process_shop_discount_meta', 'woocommerce_process_shop_discount_meta', 1, 2 );