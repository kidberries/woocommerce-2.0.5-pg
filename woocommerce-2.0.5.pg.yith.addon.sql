SET search_path TO 'public';

CREATE TABLE wp_yith_wcwl
(
  "ID" bigserial NOT NULL,
  prod_id bigint NOT NULL,
  quantity bigint NOT NULL,
  user_id bigint NOT NULL,
  dateadded timestamp without time zone NOT NULL DEFAULT now(),
  CONSTRAINT wp_yith_wcwl_pkey PRIMARY KEY ("ID")
)
WITH (
  OIDS=FALSE
);
ALTER TABLE wp_yith_wcwl
  OWNER TO wordpress;

CREATE INDEX yith_wcwl_product_idx
  ON wp_yith_wcwl
  USING btree
  (prod_id);

