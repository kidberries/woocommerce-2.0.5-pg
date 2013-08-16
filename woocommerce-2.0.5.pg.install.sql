DROP TABLE IF EXISTS "wp_woocommerce_attribute_taxonomies";CREATE TABLE "wp_woocommerce_attribute_taxonomies" ( "attribute_id" BIGSERIAL, "attribute_name" varchar(200) NOT NULL, "attribute_label" TEXT, "attribute_type" varchar(200) NOT NULL, "attribute_orderby" varchar(200) NOT NULL);
DROP TABLE IF EXISTS "wp_woocommerce_downloadable_product_permissions";CREATE TABLE "wp_woocommerce_downloadable_product_permissions" ( "download_id" varchar(32) NOT NULL, "product_id" BIGINT NOT NULL, "order_id" BIGINT DEFAULT '0', "order_key" varchar(200) NOT NULL, "user_email" varchar(200) NOT NULL, "user_id" BIGINT DEFAULT NULL, "downloads_remaining" varchar(9) DEFAULT NULL, "access_granted" TIMESTAMP WITHOUT TIME ZONE DEFAULT '0001-01-01 00:00:00', "access_expires" TIMESTAMP WITHOUT TIME ZONE DEFAULT NULL, "download_count" BIGINT DEFAULT '0');
DROP TABLE IF EXISTS "wp_woocommerce_order_itemmeta";CREATE TABLE "wp_woocommerce_order_itemmeta" ( "meta_id" BIGSERIAL, "order_item_id" BIGINT NOT NULL, "meta_key" varchar(255) DEFAULT NULL, "meta_value" TEXT);
DROP TABLE IF EXISTS "wp_woocommerce_order_items";CREATE TABLE "wp_woocommerce_order_items" ( "order_item_id" BIGSERIAL, "order_item_name" TEXT NOT NULL, "order_item_type" varchar(200) DEFAULT '', "order_id" BIGINT NOT NULL);
DROP TABLE IF EXISTS "wp_woocommerce_tax_rate_locations";CREATE TABLE "wp_woocommerce_tax_rate_locations" ( "location_id" BIGSERIAL, "location_code" varchar(255) NOT NULL, "tax_rate_id" BIGINT NOT NULL, "location_type" varchar(40) NOT NULL);
DROP TABLE IF EXISTS "wp_woocommerce_tax_rates";CREATE TABLE "wp_woocommerce_tax_rates" ( "tax_rate_id" BIGSERIAL, "tax_rate_country" varchar(200) DEFAULT '', "tax_rate_state" varchar(200) DEFAULT '', "tax_rate" varchar(200) DEFAULT '', "tax_rate_name" varchar(200) DEFAULT '', "tax_rate_priority" BIGINT NOT NULL, "tax_rate_compound" INTEGER DEFAULT '0', "tax_rate_shipping" INTEGER DEFAULT '1', "tax_rate_order" BIGINT NOT NULL, "tax_rate_class" varchar(200) DEFAULT '');
DROP TABLE IF EXISTS "wp_woocommerce_termmeta";CREATE TABLE "wp_woocommerce_termmeta" ( "meta_id" BIGSERIAL, "woocommerce_term_id" BIGINT NOT NULL, "meta_key" varchar(255) DEFAULT NULL, "meta_value" TEXT);
ALTER TABLE "wp_woocommerce_tax_rate_locations" ADD CONSTRAINT "wp_woocommerce_tax_rate_locations_pkey" PRIMARY KEY("location_id");
CREATE INDEX "wp_woocommerce_termmeta_meta_key_idx" ON "wp_woocommerce_termmeta" ("meta_key");
CREATE INDEX "wp_woocommerce_termmeta_woocommerce_term_id_idx" ON "wp_woocommerce_termmeta" ("woocommerce_term_id");
ALTER TABLE "wp_woocommerce_tax_rates" ADD CONSTRAINT "wp_woocommerce_tax_rates_pkey" PRIMARY KEY("tax_rate_id");
CREATE INDEX "wp_woocommerce_tax_rate_locations_location_type_location_code_idx" ON "wp_woocommerce_tax_rate_locations" ("location_type","location_code");
CREATE INDEX "wp_woocommerce_order_itemmeta_order_item_id_idx" ON "wp_woocommerce_order_itemmeta" ("order_item_id");
CREATE INDEX "wp_woocommerce_attribute_taxonomies_attribute_name_idx" ON "wp_woocommerce_attribute_taxonomies" ("attribute_name");
CREATE INDEX "wp_woocommerce_tax_rate_locations_location_type_idx" ON "wp_woocommerce_tax_rate_locations" ("location_type");
ALTER TABLE "wp_woocommerce_order_items" ADD CONSTRAINT "wp_woocommerce_order_items_pkey" PRIMARY KEY("order_item_id");
ALTER TABLE "wp_woocommerce_attribute_taxonomies" ADD CONSTRAINT "wp_woocommerce_attribute_taxonomies_pkey" PRIMARY KEY("attribute_id");
CREATE INDEX "wp_woocommerce_tax_rates_tax_rate_class_idx" ON "wp_woocommerce_tax_rates" ("tax_rate_class");
CREATE INDEX "wp_woocommerce_downloadable_product_permissions_download_id_order_id_product_id_idx" ON "wp_woocommerce_downloadable_product_permissions" ("download_id","order_id","product_id");
CREATE INDEX "wp_woocommerce_tax_rates_tax_rate_country_idx" ON "wp_woocommerce_tax_rates" ("tax_rate_country");
CREATE INDEX "wp_woocommerce_order_items_order_id_idx" ON "wp_woocommerce_order_items" ("order_id");
ALTER TABLE "wp_woocommerce_downloadable_product_permissions" ADD CONSTRAINT "wp_woocommerce_downloadable_product_permissions_pkey" PRIMARY KEY("product_id","order_id","order_key","download_id");
CREATE INDEX "wp_woocommerce_order_itemmeta_meta_key_idx" ON "wp_woocommerce_order_itemmeta" ("meta_key");
ALTER TABLE "wp_woocommerce_order_itemmeta" ADD CONSTRAINT "wp_woocommerce_order_itemmeta_pkey" PRIMARY KEY("meta_id");
ALTER TABLE "wp_woocommerce_termmeta" ADD CONSTRAINT "wp_woocommerce_termmeta_pkey" PRIMARY KEY("meta_id");
CREATE INDEX "wp_woocommerce_tax_rates_tax_rate_priority_idx" ON "wp_woocommerce_tax_rates" ("tax_rate_priority");
CREATE INDEX "wp_woocommerce_tax_rates_tax_rate_state_idx" ON "wp_woocommerce_tax_rates" ("tax_rate_state");
ALTER TABLE "wp_woocommerce_termmeta" ADD CONSTRAINT "wp_woocommerce_termmeta_woocommerce_term_id_fkey" FOREIGN KEY ("woocommerce_term_id") REFERENCES "wp_terms" ("term_id") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE "wp_woocommerce_order_items" ADD CONSTRAINT "wp_woocommerce_order_items_order_id_fkey" FOREIGN KEY ("order_id") REFERENCES "wp_posts" ("ID") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE "wp_woocommerce_order_itemmeta" ADD CONSTRAINT "wp_woocommerce_order_itemmeta_order_item_id_fkey" FOREIGN KEY ("order_item_id") REFERENCES "wp_woocommerce_order_items" ("order_item_id") MATCH SIMPLE ON UPDATE CASCADE ON DELETE CASCADE;

CREATE OR REPLACE FUNCTION fn.__get_next_post_uri()
  RETURNS text AS
$BODY$
  SELECT coalesce((SELECT option_value FROM wp_options WHERE option_name='siteurl' LIMIT 1), '') || '/?page_id=' || coalesce( (SELECT currval('"wp_posts_ID_seq"'::regclass)),'0')::text;
$BODY$
  LANGUAGE sql VOLATILE
  COST 100;

CREATE OR REPLACE FUNCTION fn.__new_taxonomy(text,text)
  RETURNS bool AS
$BODY$
  INSERT INTO wp_terms (name, slug, term_group) SELECT $1, $1, 0 WHERE NOT EXISTS (SELECT 1 FROM wp_terms WHERE slug=$1);
  INSERT INTO wp_term_taxonomy (term_id, taxonomy, description, parent, count) SELECT (SELECT term_id FROM wp_terms WHERE slug=$1), $2, '', 0, 0 WHERE NOT EXISTS (SELECT 1 FROM wp_terms JOIN wp_term_taxonomy USING(term_id) WHERE slug=$1 AND taxonomy=$2);
  SELECT true FROM wp_terms JOIN wp_term_taxonomy USING(term_id) WHERE slug=$1 AND taxonomy=$2;
$BODY$
  LANGUAGE sql VOLATILE
  COST 100;

INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count, tsv) VALUES (1, now(), now(), '', 'Shop', '', 'publish', 'closed', 'open', '', 'shop', '', '', now(), now(), '', 0, fn.__get_next_post_uri(), 0, 'page', '', 0, NULL);
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count, tsv) VALUES (1, now(), now(), '[woocommerce_cart]', 'Cart', '', 'publish', 'closed', 'open', '', 'cart', '', '', now(), now(), '', 0, fn.__get_next_post_uri(), 0, 'page', '', 0, NULL);
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count, tsv) VALUES (1, now(), now(), '[woocommerce_checkout]', 'Checkout', '', 'publish', 'closed', 'open', '', 'checkout', '', '', now(), now(), '', 0, fn.__get_next_post_uri(), 0, 'page', '', 0, NULL);
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count, tsv) VALUES (1, now(), now(), '[woocommerce_pay]', 'Checkout &rarr; Pay', '', 'publish', 'closed', 'open', '', 'pay', '', '', now(), now(), '', (SELECT "ID" FROM wp_posts WHERE post_name='checkout' LIMIT 1), fn.__get_next_post_uri(), 0, 'page', '', 0, NULL);
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count, tsv) VALUES (1, now(), now(), '[woocommerce_thankyou]', 'Order Received', '', 'publish', 'closed', 'open', '', 'order-received', '', '', now(), now(), '', (SELECT "ID" FROM wp_posts WHERE post_name='checkout' LIMIT 1), fn.__get_next_post_uri(), 0, 'page', '', 0, NULL);
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count, tsv) VALUES (1, now(), now(), '[woocommerce_my_account]', 'My Account', '', 'publish', 'closed', 'open', '', 'my-account', '', '', now(), now(), '', 0, fn.__get_next_post_uri(), 0, 'page', '', 0, NULL);
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count, tsv) VALUES (1, now(), now(), '[woocommerce_lost_password]', 'Lost Password', '', 'publish', 'closed', 'open', '', 'lost-password', '', '', now(), now(), '', (SELECT "ID" FROM wp_posts WHERE post_name='my-account' LIMIT 1), fn.__get_next_post_uri(), 0, 'page', '', 0, NULL);
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count, tsv) VALUES (1, now(), now(), '[woocommerce_edit_address]', 'Edit My Address', '', 'publish', 'closed', 'open', '', 'edit-address', '', '', now(), now(), '', (SELECT "ID" FROM wp_posts WHERE post_name='my-account' LIMIT 1), fn.__get_next_post_uri(), 0, 'page', '', 0, NULL);
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count, tsv) VALUES (1, now(), now(), '[woocommerce_view_order]', 'View Order', '', 'publish', 'closed', 'open', '', 'view-order', '', '', now(), now(), '', (SELECT "ID" FROM wp_posts WHERE post_name='my-account' LIMIT 1), fn.__get_next_post_uri(), 0, 'page', '', 0, NULL);
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count, tsv) VALUES (1, now(), now(), '[woocommerce_change_password]', 'Change Password', '', 'publish', 'closed', 'open', '', 'change-password', '', '', now(), now(), '', (SELECT "ID" FROM wp_posts WHERE post_name='my-account' LIMIT 1), fn.__get_next_post_uri(), 0, 'page', '', 0, NULL);
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count, tsv) VALUES (1, now(), now(), '', 'Logout', '', 'publish', 'closed', 'open', '', 'logout', '', '', now(), now(), '', (SELECT "ID" FROM wp_posts WHERE post_name='my-account' LIMIT 1), fn.__get_next_post_uri(), 0, 'page', '', 0, NULL);

INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_shop_page_display', '', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('recently_activated', 'a:0:{}', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_default_country', 'GB', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_currency', 'GBP', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_allowed_countries', 'all', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_specific_allowed_countries', '', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_demo_store', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_demo_store_notice', 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_coupons', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_guest_checkout', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_order_comments', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_force_ssl_checkout', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_unforce_ssl_checkout', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_signup_and_login_from_checkout', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_myaccount_registration', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_registration_email_for_username', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_lock_down_admin', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_clear_cart_on_logout', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_allow_customers_to_reorder', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_frontend_css', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_lightbox', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_chosen', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_file_download_method', 'force', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_downloads_require_login', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_downloads_grant_access_after_payment', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_shop_page_id', '4', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_terms_page_id', '', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_cart_page_id', '5', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_checkout_page_id', '6', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_pay_page_id', '13', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_thanks_page_id', '14', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_myaccount_page_id', '7', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_edit_address_page_id', '9', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_view_order_page_id', '10', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_change_password_page_id', '11', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_logout_page_id', '12', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_lost_password_page_id', '8', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_default_catalog_orderby', 'title', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_category_archive_display', '', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_cart_redirect_after_add', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_ajax_add_to_cart', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_sku', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_weight', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_dimensions', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_dimension_product_attributes', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_weight_unit', 'kg', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_dimension_unit', 'cm', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_review_rating', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_review_rating_required', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_review_rating_verification_label', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_currency_pos', 'left', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_price_thousand_sep', ',', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_price_decimal_sep', '.', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_price_num_decimals', '2', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_price_trim_zeros', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('shop_catalog_image_size', 'a:3:{s:5:"width";s:3:"150";s:6:"height";s:3:"150";s:4:"crop";b:1;}', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('shop_single_image_size', 'a:3:{s:5:"width";s:3:"300";s:6:"height";s:3:"300";s:4:"crop";i:1;}', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('shop_thumbnail_image_size', 'a:3:{s:5:"width";s:2:"90";s:6:"height";s:2:"90";s:4:"crop";i:1;}', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_manage_stock', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_hold_stock_minutes', '60', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_notify_low_stock', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_notify_no_stock', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_stock_email_recipient', 'admin@admin.admin', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_notify_low_stock_amount', '2', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_notify_no_stock_amount', '0', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_hide_out_of_stock_items', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_stock_format', '', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_calc_shipping', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_enable_shipping_calc', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_shipping_cost_requires_address', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_shipping_method_format', '', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_ship_to_billing_address_only', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_ship_to_same_address', 'yes', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_require_shipping_address', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_calc_taxes', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_prices_include_tax', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_tax_based_on', 'shipping', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_default_customer_address', 'base', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_shipping_tax_class', 'title', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_tax_round_at_subtotal', 'no', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_tax_classes', 'Reduced Rate
Zero Rate', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_tax_display_cart', 'excl', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_email_from_name', 'the site', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_email_from_address', 'admin@admin.admin', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_email_header_image', '', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_email_footer_text', 'the site - Powered by WooCommerce', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_email_base_color', '#557da1', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_email_background_color', '#f5f5f5', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_email_body_background_color', '#fdfdfd', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_email_text_color', '#505050', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_db_version', '2.0.5', 'yes');
INSERT INTO wp_options (option_name, option_value, autoload) VALUES ('woocommerce_version', '2.0.5', 'yes');


SELECT fn.__new_taxonomy('simple','product_type');
SELECT fn.__new_taxonomy('grouped','product_type');
SELECT fn.__new_taxonomy('variable','product_type');
SELECT fn.__new_taxonomy('external','product_type');
SELECT fn.__new_taxonomy('pending','shop_order_status');
SELECT fn.__new_taxonomy('failed','shop_order_status');
SELECT fn.__new_taxonomy('on-hold','shop_order_status');
SELECT fn.__new_taxonomy('processing','shop_order_status');
SELECT fn.__new_taxonomy('completed','shop_order_status');
SELECT fn.__new_taxonomy('refunded','shop_order_status');
SELECT fn.__new_taxonomy('cancelled','shop_order_status');

DROP FUNCTION fn.__get_next_post_uri() CASCADE;
DROP FUNCTION fn.__new_taxonomy(text, text);
