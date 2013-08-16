<?php
/**
 * Product Images
 *
 * Function for displaying the product synonyms meta box.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/WritePanels
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Display the product images meta box.
 *
 * @access public
 * @return void
 */
function woocommerce_product_synonyms_box() {
	global $post;
    $keywords_base     = get_post_meta( $post->ID, '_tsv-synonyms-base-keywords', true );
    $keywords_variable = get_post_meta( $post->ID, '_tsv-synonyms-variable-keywords', true );

	?>
    <p><?php __('Site Search Engine synonyms / keywords', 'woocommerce'); ?></p>
	<p style="width: 100%">
		<?php woocommerce_wp_textarea_input( array(
            'id'          => '_synonyms_variable',
            'class'       => 'large',
            'label'       => __( 'Synonyms/Keywords variable part', 'woocommerce' ),
            'description' => __( 'Comma separated synonyms words or keywords for search engine of the site. You can place here a variable part of them you think.', 'woocommerce' ),
            'value'       => $keywords_variable,
            'name'        => '_synonyms_variable',
        ) ); ?>
	</p>
	<p style="width: 100%">
		<?php woocommerce_wp_textarea_input( array(
            'id'          => '_synonyms_base',
            'class'       => 'large',
            'label'       => __( 'Synonyms/Keywords base part', 'woocommerce' ),
            'description' => __( 'Comma separated synonyms words or keywords for search engine of the site. You can place here a base part of them you think.', 'woocommerce' ),
            'value'       => $keywords_base,
            'name'        => '_synonyms_base',
        ) ); ?>
	</p>
	<?php

}

function process_product_synonyms_box_meta( $post_id ) {
    global $post;

    $keywords_base_old     = get_post_meta( $post->ID, '_tsv-synonyms-base-keywords', true );
    $keywords_variable_old = get_post_meta( $post->ID, '_tsv-synonyms-variable-keywords', true );
    
    $keywords_base_new     = $_POST['_synonyms_base'] ? $_POST['_synonyms_base'] : $_GET['_synonyms_base'];
    $keywords_variable_new = $_POST['_synonyms_variable'] ? $_POST['_synonyms_variable'] : $_GET['_synonyms_variable'];
   
    if( $keywords_base_new != $keywords_base_old )
        add_post_meta( $post->ID, '_tsv-synonyms-base-keywords', $keywords_base_new, true ) || update_post_meta( $post->ID, '_tsv-synonyms-base-keywords', $keywords_base_new );

    if( $keywords_variable_new != $keywords_variable_old )
        add_post_meta( $post->ID, '_tsv-synonyms-variable-keywords', $keywords_variable_new, true ) || update_post_meta( $post->ID, '_tsv-synonyms-variable-keywords', $keywords_variable_new );
}

