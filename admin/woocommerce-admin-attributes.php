<?php
/**
 * Functions used for the attributes section in WordPress Admin
 *
 * The attributes section lets users add custom attributes to assign to products - they can also be used in the layered nav widget.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Attributes admin panel
 *
 * Shows the created attributes and lets you add new ones or edit existing ones.
 * The added attributes are stored in the database and can be used for layered navigation.
 *
 * @access public
 * @return void
 */
function woocommerce_attributes() {
	global $wpdb, $woocommerce;

	// Action to perform: add, edit, delete or none
	$action = '';
	if ( ! empty( $_POST['add_new_attribute'] ) ) {
		$action = 'add';
	} elseif ( ! empty( $_POST['save_attribute'] ) && ! empty( $_GET['edit'] ) ) {
		$action = 'edit';
	} elseif ( ! empty( $_GET['delete'] ) ) {
		$action = 'delete';
	}

	// Add or edit an attribute
	if ( 'add' === $action || 'edit' === $action ) {

		// Security check
		if ( 'add' === $action ) {
			check_admin_referer( 'woocommerce-add-new_attribute' );
		}
		if ( 'edit' === $action ) {
			$attribute_id = absint( $_GET['edit'] );
			check_admin_referer( 'woocommerce-save-attribute_' . $attribute_id );
		}

		// Grab the submitted data
		$attribute_label        = ( isset( $_POST['attribute_label'] ) )   ? (string) $_POST['attribute_label'] : '';
		$attribute_name         = ( isset( $_POST['attribute_name'] ) )    ? woocommerce_sanitize_taxonomy_name( stripslashes( (string) $_POST['attribute_name'] ) ) : '';
		$attribute_type         = ( isset( $_POST['attribute_type'] ) )    ? (string) $_POST['attribute_type'] : '';
		$attribute_description  = ( isset( $_POST['attribute_description'] ) ) ? (string) $_POST['attribute_description'] : '';
		$attribute_orderby      = ( isset( $_POST['attribute_orderby'] ) ) ? (string) $_POST['attribute_orderby'] : '';

		// Auto-generate the label or slug if only one of both was provided
		if ( ! $attribute_label ) {
			$attribute_label = ucwords( $attribute_name );
		} elseif ( ! $attribute_name ) {
			$attribute_name = woocommerce_sanitize_taxonomy_name( stripslashes( $attribute_label ) );
		}

		// Forbidden attribute names
		// http://codex.wordpress.org/Function_Reference/register_taxonomy#Reserved_Terms
		$reserved_terms = array(
			'attachment', 'attachment_id', 'author', 'author_name', 'calendar', 'cat', 'category', 'category__and',
			'category__in', 'category__not_in', 'category_name', 'comments_per_page', 'comments_popup', 'cpage', 'day',
			'debug', 'error', 'exact', 'feed', 'hour', 'link_category', 'm', 'minute', 'monthnum', 'more', 'name',
			'nav_menu', 'nopaging', 'offset', 'order', 'orderby', 'p', 'page', 'page_id', 'paged', 'pagename', 'pb', 'perm',
			'post', 'post__in', 'post__not_in', 'post_format', 'post_mime_type', 'post_status', 'post_tag', 'post_type',
			'posts', 'posts_per_archive_page', 'posts_per_page', 'preview', 'robots', 's', 'search', 'second', 'sentence',
			'showposts', 'static', 'subpost', 'subpost_id', 'tag', 'tag__and', 'tag__in', 'tag__not_in', 'tag_id',
			'tag_slug__and', 'tag_slug__in', 'taxonomy', 'tb', 'term', 'type', 'w', 'withcomments', 'withoutcomments', 'year',
		);

		// Error checking
		if ( ! $attribute_name || ! $attribute_name || ! $attribute_type ) {
			$error = __( 'Please, provide an attribute name, slug and type.', 'woocommerce' );
		} elseif ( strlen( $attribute_name ) >= 28 ) {
			$error = sprintf( __( 'Slug “%s” is too long (28 characters max). Shorten it, please.', 'woocommerce' ), sanitize_title( $attribute_name ) );
		} elseif ( in_array( $attribute_name, $reserved_terms ) ) {
			$error = sprintf( __( 'Slug “%s” is not allowed because it is a reserved term. Change it, please.', 'woocommerce' ), sanitize_title( $attribute_name ) );
		} else {
			$taxonomy_exists = taxonomy_exists( $woocommerce->attribute_taxonomy_name( $attribute_name ) );
			if ( 'add' === $action && $taxonomy_exists ) {
				$error = sprintf( __( 'Slug “%s” is already in use. Change it, please.', 'woocommerce' ), sanitize_title( $attribute_name ) );
			}
			if ( 'edit' === $action ) {
				$old_attribute_name = woocommerce_sanitize_taxonomy_name( $wpdb->get_var( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = $attribute_id" ) );
				if ( $old_attribute_name != $attribute_name && $taxonomy_exists ) {
					$error = sprintf( __( 'Slug “%s” is already in use. Change it, please.', 'woocommerce' ), sanitize_title( $attribute_name ) );
				}
			}
		}

		// Show the error message if any
		if ( ! empty( $error ) ) {
			echo '<div id="woocommerce_errors" class="error fade"><p>' . $error . '</p></div>';
		} else {

			// Add new attribute
			if ( 'add' === $action ) {
				$wpdb->insert(
					$wpdb->prefix . 'woocommerce_attribute_taxonomies',
					array(
						'attribute_label'        => $attribute_label,
						'attribute_name'         => $attribute_name,
						'attribute_type'         => $attribute_type,
						'attribute_description'  => $attribute_description,
						'attribute_orderby'      => $attribute_orderby
					)
				);

				$action_completed = true;
			}

			// Edit existing attribute
			if ( 'edit' === $action ) {
				$wpdb->update(
					$wpdb->prefix . 'woocommerce_attribute_taxonomies',
					array(
						'attribute_label'        => $attribute_label,
						'attribute_name'         => $attribute_name,
						'attribute_type'         => $attribute_type,
						'attribute_description'  => $attribute_description,
						'attribute_orderby'      => $attribute_orderby
					),
					array( 'attribute_id' => $attribute_id )
				);

				if ( $old_attribute_name != $attribute_name && ! empty( $old_attribute_name ) ) {
					// Update taxonomies in the wp term taxonomy table
					$wpdb->update(
						$wpdb->term_taxonomy,
						array( 'taxonomy' => $woocommerce->attribute_taxonomy_name( $attribute_name ) ),
						array( 'taxonomy' => $woocommerce->attribute_taxonomy_name( $old_attribute_name ) )
					);

					// Update taxonomy ordering term meta
					$wpdb->update(
						$wpdb->prefix . 'woocommerce_termmeta',
						array( 'meta_key' => 'order_pa_' . sanitize_title( $attribute_name ) ),
						array( 'meta_key' => 'order_pa_' . sanitize_title( $old_attribute_name ) )
					);

					// Update product attributes which use this taxonomy
					$old_attribute_name_length = strlen( $old_attribute_name ) + 3;
					$attribute_name_length = strlen( $attribute_name ) + 3;

					$wpdb->query( "
						UPDATE {$wpdb->postmeta}
						SET meta_value = REPLACE( meta_value, 's:{$old_attribute_name_length}:\"pa_{$old_attribute_name}\"', 's:{$attribute_name_length}:\"pa_{$attribute_name}\"' )
						WHERE meta_key = '_product_attributes'"
					);

					// Update variations which use this taxonomy
					$wpdb->update(
						$wpdb->postmeta,
						array( 'meta_key' => 'attribute_pa_' . sanitize_title( $attribute_name ) ),
						array( 'meta_key' => 'attribute_pa_' . sanitize_title( $old_attribute_name ) )
					);
				}

				$action_completed = true;
			}
		}
	}

	// Delete an attribute
	if ( 'delete' === $action ) {
		// Security check
		$attribute_id = absint( $_GET['delete'] );
		check_admin_referer( 'woocommerce-delete-attribute_' . $attribute_id );

		$attribute_name = $wpdb->get_var( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = $attribute_id" );

		if ( $attribute_name && $wpdb->query( "DELETE FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = $attribute_id" ) ) {

			$taxonomy = $woocommerce->attribute_taxonomy_name( $attribute_name );

			if ( taxonomy_exists( $taxonomy ) ) {
				$terms = get_terms( $taxonomy, 'orderby=name&hide_empty=0' );
				foreach ( $terms as $term ) {
					wp_delete_term( $term->term_id, $taxonomy );
				}
			}

			$action_completed = true;
		}
	}

	// If an attribute was added, edited or deleted: clear cache and redirect
	if ( ! empty( $action_completed ) ) {
		delete_transient( 'wc_attribute_taxonomies' );
		wp_safe_redirect( get_admin_url() . 'edit.php?post_type=product&page=woocommerce_attributes' );
		exit;
	}

	// Show admin interface
	if ( ! empty( $_GET['edit'] ) )
		woocommerce_edit_attribute();
	else
		woocommerce_add_attribute();
}


/**
 * Edit Attribute admin panel
 *
 * Shows the interface for changing an attributes type between select and text
 *
 * @access public
 * @return void
 */
function woocommerce_edit_attribute() {
	global $wpdb;

	$edit = absint( $_GET['edit'] );

	$attribute_to_edit = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_id = '$edit'");

	$att_type 	= $attribute_to_edit->attribute_type;
	$att_label 	= $attribute_to_edit->attribute_label;
	$att_description= $attribute_to_edit->attribute_description;
	$att_name 	= $attribute_to_edit->attribute_name;
	$att_orderby 	= $attribute_to_edit->attribute_orderby;
	?>
	<div class="wrap woocommerce">
		<div class="icon32 icon32-attributes" id="icon-woocommerce"><br/></div>
	    <h2><?php _e( 'Edit Attribute', 'woocommerce' ) ?></h2>
		<form action="admin.php?page=woocommerce_attributes&amp;edit=<?php echo absint( $edit ); ?>" method="post">
			<table class="form-table">
				<tbody>
					<tr class="form-field form-required">
						<th scope="row" valign="top">
							<label for="attribute_label"><?php _e( 'Name', 'woocommerce' ); ?></label>
						</th>
						<td>
							<input name="attribute_label" id="attribute_label" type="text" value="<?php echo esc_attr( $att_label ); ?>" />
							<p class="description"><?php _e( 'Name for the attribute (shown on the front-end).', 'woocommerce' ); ?></p>
						</td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row" valign="top">
							<label for="attribute_name"><?php _e( 'Slug', 'woocommerce' ); ?></label>
						</th>
						<td>
							<input name="attribute_name" id="attribute_name" type="text" value="<?php echo esc_attr( $att_name ); ?>" maxlength="28" pattern="[a-zA-Z0-9_\-]+"/>
							<p class="description"><?php _e( 'Unique slug/reference for the attribute; must be shorter than 28 characters.', 'woocommerce' ); ?></p>
						</td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row" valign="top">
							<label for="attribute_type"><?php _e( 'Type', 'woocommerce' ); ?></label>
						</th>
						<td>
							<select name="attribute_type" id="attribute_type">
								<option value="select" <?php selected( $att_type, 'select' ); ?>><?php _e( 'Select box', 'woocommerce' ) ?></option>
								<option value="text" <?php selected( $att_type, 'text' ); ?>><?php _e( 'Text field', 'woocommerce' ) ?></option>
								<?php do_action('woocommerce_admin_attribute_types'); ?>
							</select>
							<p class="description"><?php _e( 'Determines how you select attributes for products. <strong>Text</strong> allows manual entry via the product page, whereas <strong>select</strong> attribute terms can be defined from this section. If you plan on using an attribute for variations use <strong>select</strong>.', 'woocommerce' ); ?></p>
						</td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row" valign="top">
							<label for="attribute_description"><?php _e( 'Description', 'woocommerce' ); ?></label>
						</th>
						<td>
							<textarea name="attribute_description" id="attribute_description" rows="5" cols="40"><?php echo esc_attr( $att_description ); ?></textarea>
							<p class="description"><?php _e( 'The Description.', 'woocommerce' ); ?></p>
						</td>
					</tr>
					<tr class="form-field form-required">
						<th scope="row" valign="top">
							<label for="attribute_orderby"><?php _e( 'Default sort order', 'woocommerce' ); ?></label>
						</th>
						<td>
							<select name="attribute_orderby" id="attribute_orderby">
								<option value="menu_order" <?php selected( $att_orderby, 'menu_order' ); ?>><?php _e( 'Custom ordering', 'woocommerce' ) ?></option>
								<option value="name" <?php selected( $att_orderby, 'name' ); ?>><?php _e( 'Name', 'woocommerce' ) ?></option>
								<option value="id" <?php selected( $att_orderby, 'id' ); ?>><?php _e( 'Term ID', 'woocommerce' ) ?></option>
							</select>
							<p class="description"><?php _e( 'Determines the sort order on the frontend for this attribute. If using custom ordering, you can drag and drop the terms in this attribute', 'woocommerce' ); ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit"><input type="submit" name="save_attribute" id="submit" class="button-primary" value="<?php _e( 'Update', 'woocommerce' ); ?>"></p>
			<?php wp_nonce_field( 'woocommerce-save-attribute_' . $edit ); ?>
		</form>
	</div>
	<?php

}


/**
 * Add Attribute admin panel
 *
 * Shows the interface for adding new attributes
 *
 * @access public
 * @return void
 */
function woocommerce_add_attribute() {
	global $woocommerce;
	?>
	<div class="wrap woocommerce">
		<div class="icon32 icon32-attributes" id="icon-woocommerce"><br/></div>
	    <h2><?php _e( 'Attributes', 'woocommerce' ) ?></h2>
	    <br class="clear" />
	    <div id="col-container">
	    	<div id="col-right">
	    		<div class="col-wrap">
		    		<table class="widefat fixed" style="width:100%">
				        <thead>
				            <tr>
				                <th scope="col"><?php _e( 'Name', 'woocommerce' ) ?></th>
				                <th scope="col"><?php _e( 'Slug', 'woocommerce' ) ?></th>
				                <th scope="col"><?php _e( 'Type', 'woocommerce' ) ?></th>
				                <th scope="col"><?php _e( 'Description', 'woocommerce' ) ?></th>
				                <th scope="col"><?php _e( 'Order by', 'woocommerce' ) ?></th>
				                <th scope="col" colspan="2"><?php _e( 'Terms', 'woocommerce' ) ?></th>
				            </tr>
				        </thead>
				        <tbody>
				        	<?php
				        		$attribute_taxonomies = $woocommerce->get_attribute_taxonomies();
				        		if ( $attribute_taxonomies ) :
				        			foreach ($attribute_taxonomies as $tax) :
				        				?><tr>

				        					<td><a href="edit-tags.php?taxonomy=<?php echo esc_html($woocommerce->attribute_taxonomy_name($tax->attribute_name)); ?>&amp;post_type=product"><?php echo esc_html( $tax->attribute_label ); ?></a>

				        					<div class="row-actions"><span class="edit"><a href="<?php echo esc_url( add_query_arg('edit', $tax->attribute_id, 'admin.php?page=woocommerce_attributes') ); ?>"><?php _e( 'Edit', 'woocommerce' ); ?></a> | </span><span class="delete"><a class="delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg('delete', $tax->attribute_id, 'admin.php?page=woocommerce_attributes'), 'woocommerce-delete-attribute_' . $tax->attribute_id ) ); ?>"><?php _e( 'Delete', 'woocommerce' ); ?></a></span></div>
				        					</td>
				        					<td><?php echo esc_html( $tax->attribute_name ); ?></td>
				        					<td><?php echo esc_html( ucwords( $tax->attribute_type ) ); ?></td>
				        					<td><?php echo esc_html( ucwords( $tax->attribute_description ) ); ?></td>

				        					<td><?php
					        					switch ( $tax->attribute_orderby ) {
						        					case 'name' :
						        						_e( 'Name', 'woocommerce' );
						        					break;
						        					case 'id' :
						        						_e( 'Term ID', 'woocommerce' );
						        					break;
						        					default:
						        						_e( 'Custom ordering', 'woocommerce' );
						        					break;
					        					}
				        					?></td>
				        					<td><?php
				        						if (taxonomy_exists($woocommerce->attribute_taxonomy_name($tax->attribute_name))) :
					        						$terms_array = array();
					        						$terms = get_terms( $woocommerce->attribute_taxonomy_name($tax->attribute_name), 'orderby=name&hide_empty=0' );
					        						if ($terms) :
						        						foreach ($terms as $term) :
															$terms_array[] = $term->name;
														endforeach;
														echo implode(', ', $terms_array);
													else :
														echo '<span class="na">&ndash;</span>';
													endif;
												else :
													echo '<span class="na">&ndash;</span>';
												endif;
				        					?></td>
				        					<td><a href="edit-tags.php?taxonomy=<?php echo esc_html($woocommerce->attribute_taxonomy_name($tax->attribute_name)); ?>&amp;post_type=product" class="button alignright"><?php _e( 'Configure&nbsp;terms', 'woocommerce' ); ?></a></td>
				        				</tr><?php
				        			endforeach;
				        		else :
				        			?><tr><td colspan="6"><?php _e( 'No attributes currently exist.', 'woocommerce' ) ?></td></tr><?php
				        		endif;
				        	?>
				        </tbody>
				    </table>
	    		</div>
	    	</div>
	    	<div id="col-left">
	    		<div class="col-wrap">
	    			<div class="form-wrap">
	    				<h3><?php _e( 'Add New Attribute', 'woocommerce' ) ?></h3>
	    				<p><?php _e( 'Attributes let you define extra product data, such as size or colour. You can use these attributes in the shop sidebar using the "layered nav" widgets. Please note: you cannot rename an attribute later on.', 'woocommerce' ) ?></p>
	    				<form action="admin.php?page=woocommerce_attributes" method="post">
							<div class="form-field">
								<label for="attribute_label"><?php _e( 'Name', 'woocommerce' ); ?></label>
								<input name="attribute_label" id="attribute_label" type="text" value="" />
								<p class="description"><?php _e( 'Name for the attribute (shown on the front-end).', 'woocommerce' ); ?></p>
							</div>

							<div class="form-field">
								<label for="attribute_name"><?php _e( 'Slug', 'woocommerce' ); ?></label>
								<input name="attribute_name" id="attribute_name" type="text" value="" maxlength="28" pattern="[a-zA-Z0-9_\-]+"/>
								<p class="description"><?php _e( 'Unique slug/reference for the attribute; must be shorter than 28 characters.', 'woocommerce' ); ?></p>
							</div>

							<div class="form-field">
								<label for="attribute_type"><?php _e( 'Type', 'woocommerce' ); ?></label>
								<select name="attribute_type" id="attribute_type">
									<option value="select"><?php _e( 'Select box', 'woocommerce' ) ?></option>
									<option value="text"><?php _e( 'Text field', 'woocommerce' ) ?></option>
									<?php do_action('woocommerce_admin_attribute_types'); ?>
								</select>
								<p class="description"><?php _e( 'Determines how you select attributes for products. <strong>Text</strong> allows manual entry via the product page, whereas <strong>select</strong> attribute terms can be defined from this section. If you plan on using an attribute for variations use <strong>select</strong>.', 'woocommerce' ); ?></p>
							</div>

							<div class="form-field">
							    <label for="attribute_description"><?php _e( 'Description', 'woocommerce' ); ?></label>
							    <textarea name="attribute_description" id="attribute_description" rows="5" cols="40"></textarea>
							    <p class="description"><?php _e( 'The description', 'woocommerce' ); ?></p>
							</div>

							<div class="form-field">
								<label for="attribute_orderby"><?php _e( 'Default sort order', 'woocommerce' ); ?></label>
								<select name="attribute_orderby" id="attribute_orderby">
									<option value="menu_order"><?php _e( 'Custom ordering', 'woocommerce' ) ?></option>
									<option value="name"><?php _e( 'Name', 'woocommerce' ) ?></option>
									<option value="id"><?php _e( 'Term ID', 'woocommerce' ) ?></option>
								</select>
								<p class="description"><?php _e( 'Determines the sort order on the frontend for this attribute. If using custom ordering, you can drag and drop the terms in this attribute', 'woocommerce' ); ?></p>
							</div>

							<p class="submit"><input type="submit" name="add_new_attribute" id="submit" class="button" value="<?php _e( 'Add Attribute', 'woocommerce' ); ?>"></p>
							<?php wp_nonce_field( 'woocommerce-add-new_attribute' ); ?>
	    				</form>
	    			</div>
	    		</div>
	    	</div>
	    </div>
	    <script type="text/javascript">
		/* <![CDATA[ */
			function toTranslit(text) {
			    return text.replace(/([а-яё])|([\s_-])|([^a-z\d])/gi,

			    function (all, ch, space, words, i) {
			        if (space || words) {
			            return space ? '-' : '';
			        }
			        var code = ch.charCodeAt(0),
			            index = code == 1025 || code == 1105 ? 0 :
			                code > 1071 ? code - 1071 : code - 1039,
			            t = ['yo', 'a', 'b', 'v', 'g', 'd', 'e', 'zh','z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'shch', '', 'y', '', 'e', 'yu', 'ya' ]; 
			        return t[index];
			    });
			}

			jQuery(document).ready(function(){
			    jQuery('#attribute_label').keyup( function(){
				jQuery('#attribute_name').val( toTranslit (jQuery(this).val() ) );
			    });
			});

			jQuery('a.delete').click(function(){
	    		var answer = confirm ("<?php _e( 'Are you sure you want to delete this attribute?', 'woocommerce' ); ?>");
				if (answer) return true;
				return false;
	    	});

		/* ]]> */
		</script>
	</div>
	<?php
}