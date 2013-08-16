<?php
/**
 * Admin functions for the shop_discount post type.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Discount
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Define Columns for the Discounts admin page.
 *
 * @access public
 * @param mixed $columns
 * @return array
 */
function woocommerce_edit_discount_columns($columns){

	$columns = array();

	$columns["cb"] 			= "<input type=\"checkbox\" />";
	$columns["title"] = __( 'Discount Name', 'woocommerce' );
	//$columns["type"] 		= __( 'Discount type', 'woocommerce' );
	//$columns["amount"] 		= __( 'Discount amount', 'woocommerce' );
	$columns["description"] = __( 'Discount Description', 'woocommerce' );
	//$columns["products"]	= __( 'Product IDs', 'woocommerce' );
	//$columns["usage"] 		= __( 'Usage / Limit', 'woocommerce' );
	//$columns["expiry_date"] = __( 'Expiry date', 'woocommerce' );
        $columns["time_inf"] = __( 'Активность в течение суток', 'woocommerce' );
        $columns["date_inf"] = __( 'Активность на данную дату', 'woocommerce' );
	return $columns;
}

add_filter( 'manage_edit-shop_discount_columns', 'woocommerce_edit_discount_columns' );


/**
 * Values for Columns on the Discounts admin page.
 *
 * @access public
 * @param mixed $column
 * @return void
 */
function woocommerce_custom_discount_columns( $column, $level = 0 ) {
	global $post, $woocommerce;
        
	switch ( $column ) {
		case "title" :
			$edit_link = get_edit_post_link( $post->ID );
			$title = _draft_or_post_title();
			$post_type_object = get_post_type_object( $post->post_type );
			$can_edit_post = current_user_can( $post_type_object->cap->edit_post, $post->ID );

			echo '<div class="code tips" data-tip="' . __( 'Edit discount', 'woocommerce' ) . '"><a href="' . esc_attr( $edit_link ) . '"><span>' . esc_html( $title ). '</span></a></div>';

			_post_states( $post );
                        
                        
			// Get actions
			$actions = array();

			if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
				if ( 'trash' == $post->post_status )
					$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . "'>" . __( 'Restore' ) . "</a>";
				elseif ( EMPTY_TRASH_DAYS )
					$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash' ) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash' ) . "</a>";
				if ( 'trash' == $post->post_status || !EMPTY_TRASH_DAYS )
					$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently' ) ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently' ) . "</a>";
			}
                                        $actions['edit'] = "<a class='submitdelete' title='" . esc_attr( __( 'Edit Rule' ) ) . "' href='" . esc_attr( $edit_link ) . "'>Edit</a>";

			$actions = apply_filters( 'post_row_actions', $actions, $post );

			echo '<div class="row-actions">';

			$i = 0;
			$action_count = sizeof($actions);

			foreach ( $actions as $action => $link ) {
				++$i;
				( $i == $action_count ) ? $sep = '' : $sep = ' | ';
				echo "<span class='$action'>$link$sep</span>";
			}
			echo '</div>';

		break;
		case "type" :
			echo esc_html( $woocommerce->get_discount_discount_type( get_post_meta( $post->ID, 'discount_type', true ) ) );
		break;
		case "times" :
			echo esc_html( get_post_meta( $post->ID, 'times_the_amount', true ) );
		break;
		case "amount" :
			echo esc_html( get_post_meta( $post->ID, 'discount_amount', true ) );
		break;
		case "products" :
			$product_ids = get_post_meta( $post->ID, 'product_ids', true );
			$product_ids = $product_ids ? array_map( 'absint', explode( ',', $product_ids ) ) : array();
			if ( sizeof( $product_ids ) > 0 )
				echo esc_html( implode( ', ', $product_ids ) );
			else
				echo '&ndash;';
		break;
		case "usage_limit" :
			$usage_limit = get_post_meta( $post->ID, 'usage_limit', true );

			if ( $usage_limit )
				echo esc_html( $usage_limit );
			else
				echo '&ndash;';
		break;
		case "usage" :
			$usage_count = absint( get_post_meta( $post->ID, 'usage_count', true ) );
			$usage_limit = esc_html( get_post_meta($post->ID, 'usage_limit', true) );

			if ( $usage_limit )
				printf( __( '%s / %s', 'woocommerce' ), $usage_count, $usage_limit );
			else
				printf( __( '%s / &infin;', 'woocommerce' ), $usage_count );
		break;
		case "expiry_date" :
			$expiry_date = get_post_meta($post->ID, 'expiry_date', true);

			if ( $expiry_date )
				echo esc_html( date_i18n( 'F j, Y', strtotime( $expiry_date ) ) );
			else
				echo '&ndash;';
		break;
		case "description" :
			echo wp_kses_post( $post->post_excerpt );
		break;
                case "time_inf" :
                        date_default_timezone_set('Europe/Moscow');
                        $current_date_time = getdate();
                        $time_from =  get_post_meta($post->ID,'time_from');
                        $time_to =  get_post_meta($post->ID,'time_to');
                         $time_from = str_replace(':', '', $time_from[0]);
                         $time_to = str_replace(':', '', $time_to[0]);
                         $curent_time = $current_date_time['hours'].$current_date_time['minutes'];

                         if ( (((int)$time_from) == 0) && ((int)$time_to == 0)){
                             echo 'YES';
                         }else{
                             if(($curent_time < $time_from) || ($curent_time > $time_to)){
                                echo 'NO';
                             }else{
                                 echo 'YES';
                             }   
                         }
                break;        
                
                case "date_inf" :
                    $date_from =  get_post_meta($post->ID,'date_from');
                    $date_to =  get_post_meta($post->ID,'date_to');
                    $date_from = $date_from[0];
                    $date_to = $date_to[0];
                    $date_from = strtotime($date_from);
                    $date_to = strtotime($date_to);
                    if (empty($date_from)) $date_from = 0;
                    if ($date_to == 0 || empty($date_to)) $date_to = 9999999999;

                    if((strtotime("now") < $date_from) || (strtotime("now") > $date_to)){
                        echo 'NO';
                    }else{
                        echo 'YES';    
                    }; 
                break;    
	}
}

add_action( 'manage_shop_discount_posts_custom_column', 'woocommerce_custom_discount_columns', 2 );

/**
 * Show custom filters to filter discounts by type.
 *
 * @access public
 * @return void
 */
function woocommerce_restrict_manage_discounts() {
	global $woocommerce, $typenow, $wp_query;

	if ( $typenow != 'shop_discount' )
		return;

	// Type
	?>
	<select name='discount_type' id='dropdown_shop_discount_type'>
		<option value=""><?php _e( 'Show all statuses', 'woocommerce' ); ?></option>
		<?php
			$types = $woocommerce->get_discount_discount_types();

			foreach ( $types as $name => $type ) {
				echo '<option value="' . esc_attr( $name ) . '"';

				if ( isset( $_GET['discount_type'] ) )
					selected( $name, $_GET['discount_type'] );

				echo '>' . esc_html__( $type, 'woocommerce' ) . '</option>';
			}
		?>
		</select>
	<?php

	$woocommerce->add_inline_js( "
		jQuery('select#dropdown_shop_discount_type, select[name=m]').css('width', '150px').chosen();
	" );
}

add_action( 'restrict_manage_posts', 'woocommerce_restrict_manage_discounts' );

/**
 * Filter the discounts by the type.
 *
 * @access public
 * @param mixed $vars
 * @return array
 */
function woocommerce_discounts_by_type_query( $vars ) {
	global $typenow, $wp_query;
    if ( $typenow == 'shop_discount' && ! empty( $_GET['discount_type'] ) ) {

		$vars['meta_key'] = 'discount_type';
		$vars['meta_value'] = woocommerce_clean( $_GET['discount_type'] );

	}

	return $vars;
}

add_filter( 'request', 'woocommerce_discounts_by_type_query' );