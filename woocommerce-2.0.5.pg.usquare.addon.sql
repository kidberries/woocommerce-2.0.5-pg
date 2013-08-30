SET search_path TO 'public';

CREATE TABLE wp_usquare (
  id bigserial NOT NULL,
  name text NOT NULL,
  settings text NOT NULL,
  items text NOT NULL,
  CONSTRAINT wp_usquare_pkey PRIMARY KEY (id)
);
ALTER TABLE wp_usquare OWNER TO wordpress;
