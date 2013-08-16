CREATE TABLE wp_woo_compare_fields
(
  id bigserial NOT NULL,
  field_name bytea NOT NULL,
  field_key text NOT NULL,
  field_type text NOT NULL,
  default_value bytea NOT NULL,
  field_unit bytea NOT NULL,
  field_description bytea NOT NULL,
  field_order bigint NOT NULL,
  CONSTRAINT wp_woo_compare_fields_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

CREATE TABLE wp_woo_compare_categories_fields
(
  cat_id bigint NOT NULL,
  field_id bigint NOT NULL,
  field_order bigint NOT NULL
)
WITH (
  OIDS=FALSE
);
CREATE INDEX wp_woo_compare_categories_fields_cat_id_idx
  ON wp_woo_compare_categories_fields
  USING btree
  (cat_id);

CREATE TABLE wp_woo_compare_categories
(
  id bigserial NOT NULL,
  category_name bytea NOT NULL,
  category_order bigint NOT NULL,
  CONSTRAINT wp_woo_compare_categories_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);

