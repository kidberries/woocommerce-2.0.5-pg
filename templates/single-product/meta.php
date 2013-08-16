<?php
/**
 * Single Product Meta
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product;
?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php // This is right place for Changeable attribute shortcodes. But true way place it into a short description section.
	      // In following for /srvs/wp-mysql/docs/wp-content/themes/bazar/woocommerce/single-product/tabs/description.php
	/*
		if ( $product->is_type( array( 'variable' ) ) ) {
			echo '<div class="changeable content">';

			$content = $post->post_content;
			$content = preg_replace( "/(\r?\n)/", '', $content );

			if ( preg_match( '/(\[changeables\].*\[\/changeables\])/', $content, $changeables ) ) {
				for ( $i = 1; $i < sizeof($changeables); $i++ ) {
				    echo do_shortcode($changeables[$i]);
				}
			}

			echo '</div><hr/>';
		}
	*/?>

	<?php if ( $product->is_type( array( 'simple', 'variable' ) ) && get_option( 'woocommerce_enable_sku' ) == 'yes' && $product->get_sku() ) : ?>
		<div itemprop="productID" class="sku_wrapper"><?php _e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo $product->get_sku(); ?></span>.</div>
	<?php endif; ?>

	<?php
		$size = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
//		echo $product->get_categories( ', ', '<div class="posted_in">' . _n( 'Category:', 'Categories:', $size, 'woocommerce' ) . ' ', '.</div>' );
		echo $product->get_categories( ', ' );
	?>

	<?php
		$size = sizeof( get_the_terms( $post->ID, 'product_tag' ) );
		echo $product->get_tags( ', ', '<div class="tagged_as">' . _n( 'Tag:', 'Tags:', $size, 'woocommerce' ) . ' ', '.</div>' );
	?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>