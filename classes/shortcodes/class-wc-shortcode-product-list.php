<?php
/**
 * Product List Shortcode
 *
 * The Product List page displays list of the store products for SEO.
 *
 * @author	Kidberries Team
 * @category 	Shortcodes
 * @package 	WooCommerce/Shortcodes/ProductList
 * @version     2.0.0
 */

class WC_Shortcode_Product_List {

	/**
	 * Get the shortcode content.
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function get( $atts ) {
		global $woocommerce;
		return $woocommerce->shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $atts ) {
		global $woocommerce, $wpdb;
        $limit  = 100; //not 'ALL';
        $offset = '0';
        
        $search = array ("`<script[^>]*?>.*?</script>`si",  // Вырезает javaScript
                         "`<[/!]*?[^<>]*?>`si",           // Вырезает HTML-теги
                         "`&(quot|#34);`i",                 // Заменяет HTML-сущности
                         "`&(amp|#38);`i",
                         "`&(lt|#60);`i",
                         "`&(gt|#62);`i",
                         "`&(nbsp|#160);`i",
                         "`&(iexcl|#161);`i",
                         "`&(cent|#162);`i",
                         "`&(pound|#163);`i",
                         "`&(copy|#169);`i",
                         "`&#(d+);`e",                    // интерпретировать как php-код
                         "`\s+`",                 // Вырезает пробельные символы
        );

        $replace = array ("",
                          "",
                          "\"",
                          "&",
                          "<",
                          ">",
                          " ",
                          chr(161),
                          chr(162),
                          chr(163),
                          chr(169),
                          "chr(\1)",
                          " ",
        );

        //$wpdb->get_val( $wpdb->prepare("SELECT count(1) FROM wp_posts WHERE true AND post_type = 'product' AND post_status='publish'") );
		$q = "
            SELECT
              p.\"ID\",
              p.post_title,
              p.post_excerpt
            FROM
              {$wpdb->posts} p,
              {$wpdb->postmeta} m1
            WHERE true
              AND p.post_type = 'product' AND p.post_status='publish'
              AND m1.meta_key='_stock' AND m1.post_id = p.\"ID\"
            ORDER BY
              CAST( m1.meta_value AS NUMERIC) DESC
            LIMIT {$limit}
            OFFSET {$offset}";
        $rows = $wpdb->get_results($q);
        $c    = 0;
        $h    = (int) (count($rows) / 2);

        $style = "display: block; width: 45%; float: left;";
        echo "<p></p>";
        echo "<ol style=\"{$style}\" start=\"". ($offset+1) . "\">";
        foreach ($rows as $row) {

            $title = htmlspecialchars($row->post_title);
            $url   = get_permalink( $row->ID );
            $alt   = mb_substr( preg_replace($search, $replace, $row->post_excerpt ), 0, 200, 'utf-8' );
            echo "<li><a href=\"{$url}\" alt=\"{$alt}...\" title=\"{$alt}...\">{$title}</a></li>";
            if( $h == $c++ ) {
                echo "</ol><ol style=\"{$style}\" start=\"". ($c+1) . "\">";
            }
        }
        echo "</ol>";
        echo "<p></p>";

		//woocommerce_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );
	}
}