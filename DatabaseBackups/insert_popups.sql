CREATE TABLE featured_event_paid_attendees (
    featured_event_id integer NOT NULL,
    user_id integer NOT NULL,
    stripe_token text NOT NULL
);
ALTER TABLE public.featured_event_paid_attendees OWNER TO mayosala_socialer;

CREATE FUNCTION get_featured_event_paid_attendance_list(in_featured_event_id integer, OUT user_id integer, OUT stripe_token text) RETURNS SETOF record
    LANGUAGE sql
    AS $_$
    SELECT user_id, stripe_token FROM featured_event_paid_attendees 	WHERE featured_event_id = $1;
  $_$;


ALTER FUNCTION public.get_featured_event_paid_attendance_list(in_featured_event_id integer, OUT user_id integer, OUT stripe_token text) OWNER TO mayosala_socialer;



CREATE FUNCTION set_featured_event_paid_attendance_status(in_featured_event_id integer, in_user_id integer, in_stripe_token text) RETURNS void
    LANGUAGE sql
    AS $$
	INSERT INTO featured_event_paid_attendees( featured_event_id, user_id, stripe_token ) VALUES ( $1, $2, $3 ); 
  $$;


ALTER FUNCTION public.set_featured_event_paid_attendance_status(in_featured_event_id integer, in_user_id integer, in_stripe_token text) OWNER TO mayosala_socialer;



---------------------------------------------------------------

ALTER TABLE featured_events ADD COLUMN price decimal;
ALTER TABLE featured_events ADD COLUMN headline text;
ALTER TABLE featured_events ADD COLUMN is_private boolean;
ALTER TABLE featured_events ADD COLUMN sub_headline text;

DROP FUNCTION new_featured_event(in_description text, in_starts_at timestamp with time zone, in_ends_at timestamp with time zone, location text, markup text);


DROP FUNCTION new_featured_event(in_description text, in_starts_at timestamp with time zone, in_ends_at timestamp with time zone, location text, markup text, price decimal, headline text, is_private boolean);


CREATE FUNCTION new_featured_event(in_description text, in_starts_at timestamp with time zone, in_ends_at timestamp with time zone, location text, markup text, price decimal, headline text, is_private boolean, sub_headline text) RETURNS void
    LANGUAGE sql
    AS $_$
    INSERT INTO featured_events( description, starts_at, ends_at, location, markup, price, headline, is_private, sub_headline ) VALUES ( $1, $2, $3, $4, $5, $6, $7, $8, $9 );
  $_$;

ALTER FUNCTION public.new_featured_event(in_description text, in_starts_at timestamp with time zone, in_ends_at timestamp with time zone, location text, markup text, price decimal, headline text, is_private boolean, sub_headline text) OWNER TO mayosala_socialer;




--- ---------------------------------------------

DROP FUNCTION featured_events(in_featured_event_id integer, OUT featured_event_id integer, OUT description text, OUT starts_at timestamp with time zone, OUT ends_at timestamp with time zone, OUT location text, OUT markup text);


DROP FUNCTION featured_events(in_featured_event_id integer, OUT featured_event_id integer, OUT description text, OUT starts_at timestamp with time zone, OUT ends_at timestamp with time zone, OUT location text, OUT markup text, OUT price decimal, OUT headline text,  OUT is_private boolean);



--
-- Name: featured_events(integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION featured_events(in_featured_event_id integer, OUT featured_event_id integer, OUT description text, OUT starts_at timestamp with time zone, OUT ends_at timestamp with time zone, OUT location text, OUT markup text, OUT price decimal, OUT headline text, OUT is_private boolean, OUT sub_headline text) RETURNS SETOF record
    LANGUAGE sql
    AS $_$
    SELECT featured_event_id, description, starts_at, ends_at, location, markup, price, headline, is_private, sub_headline FROM featured_events WHERE featured_event_id = $1;
  $_$;


ALTER FUNCTION public.featured_events(in_featured_event_id integer, OUT featured_event_id integer, OUT description text, OUT starts_at timestamp with time zone, OUT ends_at timestamp with time zone, OUT location text, OUT markup text, OUT price decimal, OUT headline text, OUT is_private boolean, OUT sub_headline text ) OWNER TO mayosala_socialer;

--
-- Name: featured_events(timestamp with time zone); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION featured_events(in_timestamp timestamp with time zone, OUT featured_event_id integer, OUT description text, OUT starts_at timestamp with time zone, OUT ends_at timestamp with time zone, OUT location text, OUT markup text) RETURNS SETOF record
    LANGUAGE sql
    AS $_$
    SELECT featured_event_id, description, starts_at, ends_at, location, markup FROM featured_events WHERE ends_at > $1;
  $_$;


ALTER FUNCTION public.featured_events(in_timestamp timestamp with time zone, OUT featured_event_id integer, OUT description text, OUT starts_at timestamp with time zone, OUT ends_at timestamp with time zone, OUT location text, OUT markup text) OWNER TO mayosala_socialer;





