SET search_path TO 'public';

CREATE TABLE wp_revslider_sliders (
  id bigserial NOT NULL,
  title text NOT NULL,
  alias text,
  params text NOT NULL,
  siteid bigint,
  CONSTRAINT wp_revslider_sliders_pkey PRIMARY KEY (id)
);
ALTER TABLE wp_revslider_sliders OWNER TO wordpress;

CREATE TABLE wp_revslider_slides (
  id bigserial NOT NULL,
  slider_id bigint NOT NULL,
  slide_order bigint NOT NULL,
  params text NOT NULL,
  layers text NOT NULL,
  CONSTRAINT wp_revslider_slides_pkey PRIMARY KEY (id)
);
ALTER TABLE wp_revslider_slides OWNER TO wordpress;
