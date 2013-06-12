--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: -; Owner: postgres
--

CREATE PROCEDURAL LANGUAGE plpgsql;


ALTER PROCEDURAL LANGUAGE plpgsql OWNER TO postgres;

SET search_path = public, pg_catalog;

--
-- Name: accept_friend_request(integer, integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION accept_friend_request(in_recipient_id integer, in_sender_id integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
    DECLARE

    BEGIN
      IF delete_friend_request( in_recipient_id, in_sender_id ) THEN
        PERFORM add_friend( in_recipient_id, in_sender_id );
        RETURN TRUE;
      END IF;

      RETURN FALSE;
    END;
  $$;


ALTER FUNCTION public.accept_friend_request(in_recipient_id integer, in_sender_id integer) OWNER TO mayosala_socialer;

--
-- Name: add_friend(integer, integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION add_friend(in_user_one integer, in_user_two integer) RETURNS void
    LANGUAGE sql
    AS $_$
    INSERT INTO friends ( user_one, user_two ) VALUES ( $1, $2 );
    INSERT INTO friends ( user_one, user_two ) VALUES ( $2, $1 );
  $_$;


ALTER FUNCTION public.add_friend(in_user_one integer, in_user_two integer) OWNER TO mayosala_socialer;

--
-- Name: add_photo(integer, text); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION add_photo(in_user_id integer, in_path text) RETURNS integer
    LANGUAGE plpgsql
    AS $$
    DECLARE
      v_photo_id INTEGER;
    BEGIN
      INSERT INTO photos ( user_id, path, is_default ) VALUES ( in_user_id, in_path, NOT user_has_photo( in_user_id ) ) RETURNING photo_id INTO v_photo_id;
      RETURN v_photo_id;
    END;
  $$;


ALTER FUNCTION public.add_photo(in_user_id integer, in_path text) OWNER TO mayosala_socialer;

--
-- Name: attempt_login(text, text); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION attempt_login(email text, pwd text) RETURNS SETOF integer
    LANGUAGE plpgsql
    AS $$
    BEGIN
      RETURN QUERY SELECT user_id FROM public.users WHERE email_address = email AND password = pwd;
    END;
  $$;


ALTER FUNCTION public.attempt_login(email text, pwd text) OWNER TO mayosala_socialer;

--
-- Name: delete_friend_request(integer, integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION delete_friend_request(in_recipient_id integer, in_sender_id integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
    DECLARE

    BEGIN
      DELETE FROM friend_requests WHERE recipient_id = in_recipient_id and sender_id = in_sender_id;
      RETURN FOUND;
    END;
  $$;


ALTER FUNCTION public.delete_friend_request(in_recipient_id integer, in_sender_id integer) OWNER TO mayosala_socialer;

--
-- Name: delete_location(integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION delete_location(in_location_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
    BEGIN
      DELETE FROM locations l WHERE l.location_id = in_location_id;
    END;
  $$;


ALTER FUNCTION public.delete_location(in_location_id integer) OWNER TO mayosala_socialer;

--
-- Name: events_for_date(date); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION events_for_date(event_date date) RETURNS SETOF integer
    LANGUAGE plpgsql
    AS $$
    BEGIN
      RETURN QUERY SELECT event_id FROM events WHERE starts_at::DATE = event_date;
    END;
  $$;


ALTER FUNCTION public.events_for_date(event_date date) OWNER TO mayosala_socialer;

--
-- Name: gender(text); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION gender(in_gender_text text, OUT gender_id character) RETURNS character
    LANGUAGE sql
    AS $_$
    SELECT gender_id FROM genders WHERE description = $1;
  $_$;


ALTER FUNCTION public.gender(in_gender_text text, OUT gender_id character) OWNER TO mayosala_socialer;

--
-- Name: gender_by_id(character); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION gender_by_id(in_gender_id character, OUT gender text) RETURNS text
    LANGUAGE sql
    AS $_$
    SELECT description FROM genders WHERE gender_id = $1;
  $_$;


ALTER FUNCTION public.gender_by_id(in_gender_id character, OUT gender text) OWNER TO mayosala_socialer;

--
-- Name: get_attendance_status_id(text); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION get_attendance_status_id(in_attendance_status text, OUT attendance_status_id integer) RETURNS integer
    LANGUAGE sql
    AS $_$
    SELECT attendance_status_id FROM attendance_statuses WHERE attendance_status = $1;
  $_$;


ALTER FUNCTION public.get_attendance_status_id(in_attendance_status text, OUT attendance_status_id integer) OWNER TO mayosala_socialer;

--
-- Name: get_attendance_statuses_for_date(integer, date); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION get_attendance_statuses_for_date(in_location_id integer, in_date date, OUT user_id integer, OUT attendance_status text) RETURNS SETOF record
    LANGUAGE sql
    AS $_$
    SELECT user_id, attendance_status FROM location_attendees JOIN attendance_statuses USING ( attendance_status_id ) WHERE location_id = $1 AND attendance_date = $2;
  $_$;


ALTER FUNCTION public.get_attendance_statuses_for_date(in_location_id integer, in_date date, OUT user_id integer, OUT attendance_status text) OWNER TO mayosala_socialer;

--
-- Name: get_data(integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION get_data(in_user_id integer, OUT first_name text, OUT last_name text, OUT email_address text, OUT date_of_birth date, OUT gender character, OUT interested_in character) RETURNS record
    LANGUAGE plpgsql
    AS $$
    DECLARE
      info_record RECORD;
    BEGIN
      SELECT INTO info_record * FROM users WHERE user_id = 1;
      first_name := info_record.first_name;
      last_name := info_record.last_name;
      email_address := info_record.email_address;
      date_of_birth := info_record.date_of_birth;
      gender := info_record.gender;
      interested_in := info_record.interested_in;
      RETURN;
    END;
  $$;


ALTER FUNCTION public.get_data(in_user_id integer, OUT first_name text, OUT last_name text, OUT email_address text, OUT date_of_birth date, OUT gender character, OUT interested_in character) OWNER TO mayosala_socialer;

--
-- Name: get_friend_requests(integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION get_friend_requests(in_recipient_id integer, OUT sender_id integer, OUT sent_at timestamp with time zone) RETURNS SETOF record
    LANGUAGE sql
    AS $_$
    SELECT sender_id, sent_at FROM friend_requests WHERE recipient_id = $1;
  $_$;


ALTER FUNCTION public.get_friend_requests(in_recipient_id integer, OUT sender_id integer, OUT sent_at timestamp with time zone) OWNER TO mayosala_socialer;

--
-- Name: get_location(integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION get_location(in_location_id integer, OUT location_id integer, OUT city_id integer, OUT location_name text, OUT street_address text, OUT description text, OUT website text, OUT yelp_id text, OUT latitude numeric, OUT longitude numeric) RETURNS SETOF record
    LANGUAGE plpgsql
    AS $$
    DECLARE
    BEGIN
       RETURN QUERY SELECT l.location_id, l.city_id, l.location_name, l.street_address, l.description, l.website, l.yelp_id,
                           l.latitude, l.longitude FROM
        locations l WHERE l.location_id = in_location_id;
    END;
  $$;


ALTER FUNCTION public.get_location(in_location_id integer, OUT location_id integer, OUT city_id integer, OUT location_name text, OUT street_address text, OUT description text, OUT website text, OUT yelp_id text, OUT latitude numeric, OUT longitude numeric) OWNER TO mayosala_socialer;

--
-- Name: get_locations(); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION get_locations(OUT location_id integer) RETURNS SETOF integer
    LANGUAGE plpgsql
    AS $$
    BEGIN
      RETURN QUERY SELECT l.location_id FROM public.locations l ORDER BY location_name;
    END;
  $$;


ALTER FUNCTION public.get_locations(OUT location_id integer) OWNER TO mayosala_socialer;

--
-- Name: get_member_photo(integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION get_member_photo(in_user_id integer, OUT photo_id integer, OUT path text, OUT added_at timestamp with time zone) RETURNS record
    LANGUAGE sql
    AS $_$
    SELECT photo_id, path, added_at  FROM photos WHERE user_id = $1 AND is_default;
  $_$;


ALTER FUNCTION public.get_member_photo(in_user_id integer, OUT photo_id integer, OUT path text, OUT added_at timestamp with time zone) OWNER TO mayosala_socialer;

--
-- Name: get_member_photos(integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION get_member_photos(in_user_id integer, OUT photo_id integer, OUT path text, OUT is_default boolean, OUT added_at timestamp with time zone) RETURNS SETOF record
    LANGUAGE sql
    AS $_$
    SELECT photo_id, path, is_default, added_at FROM photos WHERE user_id = $1 ORDER BY is_default, added_at;
  $_$;


ALTER FUNCTION public.get_member_photos(in_user_id integer, OUT photo_id integer, OUT path text, OUT is_default boolean, OUT added_at timestamp with time zone) OWNER TO mayosala_socialer;

--
-- Name: get_photo(integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION get_photo(in_photo_id integer, OUT user_id integer, OUT is_default boolean, OUT path text, OUT added_at timestamp with time zone) RETURNS record
    LANGUAGE sql
    AS $_$
    SELECT user_id, is_default, path, added_at  FROM photos WHERE photo_id = $1;
  $_$;


ALTER FUNCTION public.get_photo(in_photo_id integer, OUT user_id integer, OUT is_default boolean, OUT path text, OUT added_at timestamp with time zone) OWNER TO mayosala_socialer;

--
-- Name: get_user_attendance_status(integer, integer, date); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION get_user_attendance_status(in_user_id integer, in_location_id integer, in_date date) RETURNS text
    LANGUAGE sql
    AS $_$
    SELECT attendance_status FROM location_attendees JOIN attendance_statuses USING ( attendance_status_id ) WHERE location_id = $2 AND attendance_date = $3 AND user_id = $1;
  $_$;


ALTER FUNCTION public.get_user_attendance_status(in_user_id integer, in_location_id integer, in_date date) OWNER TO mayosala_socialer;

--
-- Name: get_user_recommendations(integer, integer[], integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION get_user_recommendations(in_user_id integer, in_ignore_list integer[], in_limit integer) RETURNS SETOF integer
    LANGUAGE plpgsql
    AS $$
    BEGIN
      RETURN QUERY SELECT user_id FROM users WHERE user_id <> in_user_id AND user_id <> ALL ( in_ignore_list ) ORDER BY RANDOM( ) LIMIT in_limit;
    END;
  $$;


ALTER FUNCTION public.get_user_recommendations(in_user_id integer, in_ignore_list integer[], in_limit integer) OWNER TO mayosala_socialer;

--
-- Name: insert_location(integer, text, text, text, text, text); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION insert_location(in_city_id integer, in_location_name text, in_street_address text, in_description text, in_website text, in_yelp_id text) RETURNS integer
    LANGUAGE plpgsql
    AS $$
    DECLARE
      new_id INTEGER;
    BEGIN
      INSERT INTO locations ( location_id, city_id, location_name, street_address, description, website, yelp_id )
                  VALUES ( default, in_city_id, in_location_name, in_street_address, in_description, in_website, in_yelp_id ) 
                  RETURNING location_id INTO new_id;
      RETURN new_id;
    END;
  $$;


ALTER FUNCTION public.insert_location(in_city_id integer, in_location_name text, in_street_address text, in_description text, in_website text, in_yelp_id text) OWNER TO mayosala_socialer;

--
-- Name: member_data(integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION member_data(in_user_id integer, OUT first_name text, OUT last_name text, OUT email_address text, OUT date_of_birth date, OUT gender character) RETURNS record
    LANGUAGE sql
    AS $_$
    SELECT first_name, last_name, email_address, date_of_birth, gender_by_id(gender) FROM users WHERE user_id = $1;
  $_$;


ALTER FUNCTION public.member_data(in_user_id integer, OUT first_name text, OUT last_name text, OUT email_address text, OUT date_of_birth date, OUT gender character) OWNER TO mayosala_socialer;

--
-- Name: new_friend_request(integer, integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION new_friend_request(in_sender_id integer, in_recipient_id integer) RETURNS void
    LANGUAGE sql
    AS $_$
    INSERT INTO friend_requests( sender_id, recipient_id ) VALUES ( $1, $2 );
  $_$;


ALTER FUNCTION public.new_friend_request(in_sender_id integer, in_recipient_id integer) OWNER TO mayosala_socialer;

--
-- Name: new_profile_field(text); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION new_profile_field(in_field text) RETURNS integer
    LANGUAGE sql
    AS $_$
    INSERT INTO profile_fields ( profile_field ) VALUES ( $1 ) RETURNING profile_field_id;
  $_$;


ALTER FUNCTION public.new_profile_field(in_field text) OWNER TO mayosala_socialer;

--
-- Name: new_quick_pick(integer, text); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION new_quick_pick(in_quick_pick_topic_id integer, in_quick_pick_description text, OUT quick_pick_id integer) RETURNS integer
    LANGUAGE sql
    AS $_$
    INSERT INTO quick_picks( quick_pick_topic_id, quick_pick_description ) VALUES ( $1, $2 ) RETURNING quick_pick_id;
  $_$;


ALTER FUNCTION public.new_quick_pick(in_quick_pick_topic_id integer, in_quick_pick_description text, OUT quick_pick_id integer) OWNER TO mayosala_socialer;

--
-- Name: new_quick_pick_topic(text); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION new_quick_pick_topic(in_description text, OUT quick_pick_topic_id integer) RETURNS integer
    LANGUAGE sql
    AS $_$
    INSERT INTO quick_pick_topics( quick_pick_topic_description ) VALUES ( $1 ) RETURNING quick_pick_topic_id;
  $_$;


ALTER FUNCTION public.new_quick_pick_topic(in_description text, OUT quick_pick_topic_id integer) OWNER TO mayosala_socialer;

--
-- Name: profile_field_id(text); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION profile_field_id(in_field text) RETURNS integer
    LANGUAGE sql
    AS $_$
    SELECT profile_field_id FROM profile_fields WHERE profile_field = $1;
  $_$;


ALTER FUNCTION public.profile_field_id(in_field text) OWNER TO mayosala_socialer;

--
-- Name: profile_fields(integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION profile_fields(in_user_id integer, OUT profile_field text, OUT profile_field_value text) RETURNS SETOF record
    LANGUAGE sql
    AS $_$
    SELECT profile_field, profile_field_value FROM profile_field_values JOIN profile_fields USING ( profile_field_id ) WHERE user_id = $1;
  $_$;


ALTER FUNCTION public.profile_fields(in_user_id integer, OUT profile_field text, OUT profile_field_value text) OWNER TO mayosala_socialer;

--
-- Name: quick_pick_topics(); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION quick_pick_topics(OUT quick_pick_topic_id integer, OUT quick_pick_topic_description text) RETURNS SETOF record
    LANGUAGE sql
    AS $$
    SELECT quick_pick_topic_id, quick_pick_topic_description FROM quick_pick_topics;
  $$;


ALTER FUNCTION public.quick_pick_topics(OUT quick_pick_topic_id integer, OUT quick_pick_topic_description text) OWNER TO mayosala_socialer;

--
-- Name: register(text, text, text, date, text, character, character); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION register(in_first_name text, in_last_name text, in_email_address text, in_date_of_birth date, in_password text, in_gender character, in_interested_in character) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
    BEGIN
      IF AGE( in_date_of_birth ) < INTERVAL '13 YEARS' THEN
        RETURN FALSE;
      END IF;

      INSERT INTO users ( first_name, last_name, email_address, date_of_birth, password, gender, interested_in )
      VALUES ( in_first_name, in_last_name, LOWER( in_email_address ), in_date_of_birth, in_password, in_gender, in_interested_in );
      
      RETURN TRUE;
    END;
  $$;


ALTER FUNCTION public.register(in_first_name text, in_last_name text, in_email_address text, in_date_of_birth date, in_password text, in_gender character, in_interested_in character) OWNER TO mayosala_socialer;

--
-- Name: register(text, text, text, date, text, character); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION register(in_first_name text, in_last_name text, in_email_address text, in_date_of_birth date, in_password text, in_gender character) RETURNS integer
    LANGUAGE plpgsql
    AS $$
    DECLARE
      v_user_id INTEGER;
    BEGIN
      IF AGE( in_date_of_birth ) < INTERVAL '13 YEARS' THEN
        RETURN FALSE;
      END IF;

      INSERT INTO users ( first_name, last_name, email_address, date_of_birth, password, gender )
      VALUES ( in_first_name, in_last_name, LOWER( in_email_address ), in_date_of_birth, in_password, in_gender )
      RETURNING user_id INTO v_user_id;

      RETURN v_user_id;
    END;
  $$;


ALTER FUNCTION public.register(in_first_name text, in_last_name text, in_email_address text, in_date_of_birth date, in_password text, in_gender character) OWNER TO mayosala_socialer;

--
-- Name: register(text, text, text, date, text, text); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION register(in_first_name text, in_last_name text, in_email_address text, in_date_of_birth date, in_password text, in_gender text) RETURNS integer
    LANGUAGE plpgsql
    AS $$
    DECLARE
      v_user_id INTEGER;
    BEGIN
      IF AGE( in_date_of_birth ) < INTERVAL '13 YEARS' THEN
        RETURN FALSE;
      END IF;

      INSERT INTO users ( first_name, last_name, email_address, date_of_birth, password, gender )
      VALUES ( in_first_name, in_last_name, LOWER( in_email_address ), in_date_of_birth, in_password, gender( in_gender ) )
      RETURNING user_id INTO v_user_id;

      RETURN v_user_id;
    END;
  $$;


ALTER FUNCTION public.register(in_first_name text, in_last_name text, in_email_address text, in_date_of_birth date, in_password text, in_gender text) OWNER TO mayosala_socialer;

--
-- Name: set_attendance_status(integer, integer, text, date); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION set_attendance_status(in_user_id integer, in_location_id integer, in_attendance_status text, in_date date) RETURNS void
    LANGUAGE plpgsql
    AS $$
    DECLARE
      v_attendance_status_id INTEGER;
    BEGIN
      v_attendance_status_id := get_attendance_status_id( in_attendance_status );
      UPDATE location_attendees SET attendance_status_id = v_attendance_status_id, set_at = CURRENT_TIMESTAMP WHERE user_id = in_user_id AND location_id = in_location_id AND attendance_date = in_date;
      IF NOT FOUND THEN
        INSERT INTO location_attendees ( location_id, user_id, attendance_date, attendance_status_id ) VALUES ( in_location_id, in_user_id, in_date, v_attendance_status_id );
      END IF;
    END;
  $$;


ALTER FUNCTION public.set_attendance_status(in_user_id integer, in_location_id integer, in_attendance_status text, in_date date) OWNER TO mayosala_socialer;

--
-- Name: set_lat_long(integer, numeric, numeric); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION set_lat_long(in_location_id integer, in_latitude numeric, in_longitude numeric) RETURNS void
    LANGUAGE sql
    AS $_$
    UPDATE locations SET latitude = $2, longitude = $3 WHERE location_id = $1;
  $_$;


ALTER FUNCTION public.set_lat_long(in_location_id integer, in_latitude numeric, in_longitude numeric) OWNER TO mayosala_socialer;

--
-- Name: set_profile_field(integer, text, text); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION set_profile_field(in_user_id integer, in_field text, in_value text) RETURNS void
    LANGUAGE plpgsql
    AS $$
    DECLARE
      v_profile_field_id INTEGER;
    BEGIN
      v_profile_field_id := profile_field_id( in_field );

      UPDATE profile_field_values SET profile_field_value = in_value WHERE user_id = in_user_id AND profile_field_id = v_profile_field_id;   
      IF NOT FOUND THEN
        INSERT INTO profile_field_values ( user_id, profile_field_id, profile_field_value ) VALUES ( in_user_id, v_profile_field_id, in_value );
      END IF;
    END;
  $$;


ALTER FUNCTION public.set_profile_field(in_user_id integer, in_field text, in_value text) OWNER TO mayosala_socialer;

--
-- Name: set_user_quick_picks(integer, integer, integer[]); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION set_user_quick_picks(in_user_id integer, in_quick_pick_topic_id integer, in_quick_pick_ids integer[]) RETURNS void
    LANGUAGE plpgsql
    AS $$
    DECLARE

    BEGIN
      DELETE FROM user_quick_picks WHERE user_id = in_user_id AND quick_pick_id <> ANY ( in_quick_pick_ids );

      INSERT INTO user_quick_picks ( user_id, quick_pick_id ) (
        SELECT in_user_id, in_quick_pick_ids[i] as val FROM generate_series(array_lower(in_quick_pick_ids,1), array_upper(in_quick_pick_ids,1)) AS i WHERE in_quick_pick_ids[i] NOT IN ( SELECT quick_pick_id FROM user_quick_picks WHERE user_id = in_user_id ) );

    END;
  $$;


ALTER FUNCTION public.set_user_quick_picks(in_user_id integer, in_quick_pick_topic_id integer, in_quick_pick_ids integer[]) OWNER TO mayosala_socialer;

--
-- Name: update_user_info(integer, text, text); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION update_user_info(in_user_id integer, in_first_name text, in_last_name text) RETURNS void
    LANGUAGE sql
    AS $_$
    UPDATE users SET first_name = $2, last_name = $3 WHERE user_id = $1;
  $_$;


ALTER FUNCTION public.update_user_info(in_user_id integer, in_first_name text, in_last_name text) OWNER TO mayosala_socialer;

--
-- Name: user_has_photo(integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION user_has_photo(in_user_id integer) RETURNS boolean
    LANGUAGE sql
    AS $_$
    SELECT EXISTS ( SELECT TRUE FROM photos WHERE user_id = $1 );
  $_$;


ALTER FUNCTION public.user_has_photo(in_user_id integer) OWNER TO mayosala_socialer;

--
-- Name: user_quick_picks(integer); Type: FUNCTION; Schema: public; Owner: mayosala_socialer
--

CREATE FUNCTION user_quick_picks(in_user_id integer, OUT quick_pick_id integer, OUT set_at timestamp with time zone, OUT quick_pick_description text, OUT quick_pick_topic_id integer, OUT quick_pick_topic_description text) RETURNS SETOF record
    LANGUAGE sql
    AS $_$                
    SELECT quick_pick_id, set_at, quick_pick_description, quick_pick_topic_id, quick_pick_topic_description
      FROM user_quick_picks JOIN quick_picks USING ( quick_pick_id ) JOIN quick_pick_topics USING ( quick_pick_topic_id ) WHERE user_id = $1;
  $_$;


ALTER FUNCTION public.user_quick_picks(in_user_id integer, OUT quick_pick_id integer, OUT set_at timestamp with time zone, OUT quick_pick_description text, OUT quick_pick_topic_id integer, OUT quick_pick_topic_description text) OWNER TO mayosala_socialer;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: attendance_statuses; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE attendance_statuses (
    attendance_status_id integer NOT NULL,
    attendance_status text NOT NULL
);


ALTER TABLE public.attendance_statuses OWNER TO mayosala_socialer;

--
-- Name: attendance_statuses_attendance_status_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE attendance_statuses_attendance_status_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.attendance_statuses_attendance_status_id_seq OWNER TO mayosala_socialer;

--
-- Name: attendance_statuses_attendance_status_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE attendance_statuses_attendance_status_id_seq OWNED BY attendance_statuses.attendance_status_id;


--
-- Name: attendance_statuses_attendance_status_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('attendance_statuses_attendance_status_id_seq', 3, true);


--
-- Name: cities; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE cities (
    city_id integer NOT NULL,
    city_name text NOT NULL,
    state_id integer
);


ALTER TABLE public.cities OWNER TO mayosala_socialer;

--
-- Name: cities_city_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE cities_city_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cities_city_id_seq OWNER TO mayosala_socialer;

--
-- Name: cities_city_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE cities_city_id_seq OWNED BY cities.city_id;


--
-- Name: cities_city_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('cities_city_id_seq', 1, true);


--
-- Name: countries; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE countries (
    country_id integer NOT NULL,
    country_name text NOT NULL
);


ALTER TABLE public.countries OWNER TO mayosala_socialer;

--
-- Name: countries_country_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE countries_country_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.countries_country_id_seq OWNER TO mayosala_socialer;

--
-- Name: countries_country_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE countries_country_id_seq OWNED BY countries.country_id;


--
-- Name: countries_country_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('countries_country_id_seq', 1, true);


--
-- Name: event_interest_types; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE event_interest_types (
    interest_type_id integer NOT NULL,
    interest_type_description text
);


ALTER TABLE public.event_interest_types OWNER TO mayosala_socialer;

--
-- Name: event_interest_types_interest_type_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE event_interest_types_interest_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.event_interest_types_interest_type_id_seq OWNER TO mayosala_socialer;

--
-- Name: event_interest_types_interest_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE event_interest_types_interest_type_id_seq OWNED BY event_interest_types.interest_type_id;


--
-- Name: event_interest_types_interest_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('event_interest_types_interest_type_id_seq', 3, true);


--
-- Name: event_interests; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE event_interests (
    user_id integer NOT NULL,
    event_id integer NOT NULL,
    interest_type_id integer NOT NULL
);


ALTER TABLE public.event_interests OWNER TO mayosala_socialer;

--
-- Name: events; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE events (
    event_id integer NOT NULL,
    location_id integer,
    starts_at timestamp with time zone NOT NULL,
    ends_at timestamp with time zone NOT NULL,
    description text NOT NULL
);


ALTER TABLE public.events OWNER TO mayosala_socialer;

--
-- Name: events_event_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE events_event_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.events_event_id_seq OWNER TO mayosala_socialer;

--
-- Name: events_event_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE events_event_id_seq OWNED BY events.event_id;


--
-- Name: events_event_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('events_event_id_seq', 1, false);


--
-- Name: friend_requests; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE friend_requests (
    recipient_id integer NOT NULL,
    sender_id integer NOT NULL,
    sent_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.friend_requests OWNER TO mayosala_socialer;

--
-- Name: friends; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE friends (
    user_one integer NOT NULL,
    user_two integer NOT NULL,
    created_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.friends OWNER TO mayosala_socialer;

--
-- Name: genders; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE genders (
    gender_id character(1) NOT NULL,
    description text
);


ALTER TABLE public.genders OWNER TO mayosala_socialer;

--
-- Name: location_attendees; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE location_attendees (
    location_id integer NOT NULL,
    user_id integer NOT NULL,
    attendance_date date NOT NULL,
    attendance_status_id integer NOT NULL,
    set_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.location_attendees OWNER TO mayosala_socialer;

--
-- Name: locations; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE locations (
    location_id integer NOT NULL,
    city_id integer NOT NULL,
    location_name text NOT NULL,
    street_address text NOT NULL,
    description text DEFAULT ''::text NOT NULL,
    website text,
    yelp_id text,
    latitude numeric(10,7),
    longitude numeric(10,7)
);


ALTER TABLE public.locations OWNER TO mayosala_socialer;

--
-- Name: locations_location_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE locations_location_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.locations_location_id_seq OWNER TO mayosala_socialer;

--
-- Name: locations_location_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE locations_location_id_seq OWNED BY locations.location_id;


--
-- Name: locations_location_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('locations_location_id_seq', 33, true);


--
-- Name: network_mappings; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE network_mappings (
    user_id integer NOT NULL,
    network_id integer NOT NULL,
    added_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.network_mappings OWNER TO mayosala_socialer;

--
-- Name: network_types; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE network_types (
    network_type_id integer NOT NULL,
    network_type_name text NOT NULL
);


ALTER TABLE public.network_types OWNER TO mayosala_socialer;

--
-- Name: network_types_network_type_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE network_types_network_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.network_types_network_type_id_seq OWNER TO mayosala_socialer;

--
-- Name: network_types_network_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE network_types_network_type_id_seq OWNED BY network_types.network_type_id;


--
-- Name: network_types_network_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('network_types_network_type_id_seq', 1, false);


--
-- Name: networks; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE networks (
    network_id integer NOT NULL,
    network_type_id integer NOT NULL,
    network_name text NOT NULL
);


ALTER TABLE public.networks OWNER TO mayosala_socialer;

--
-- Name: networks_network_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE networks_network_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.networks_network_id_seq OWNER TO mayosala_socialer;

--
-- Name: networks_network_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE networks_network_id_seq OWNED BY networks.network_id;


--
-- Name: networks_network_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('networks_network_id_seq', 1, false);


--
-- Name: photos; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE photos (
    photo_id integer NOT NULL,
    user_id integer NOT NULL,
    is_default boolean DEFAULT true NOT NULL,
    path text NOT NULL,
    added_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.photos OWNER TO mayosala_socialer;

--
-- Name: photos_photo_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE photos_photo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.photos_photo_id_seq OWNER TO mayosala_socialer;

--
-- Name: photos_photo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE photos_photo_id_seq OWNED BY photos.photo_id;


--
-- Name: photos_photo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('photos_photo_id_seq', 88, true);


--
-- Name: profile_field_values; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE profile_field_values (
    user_id integer NOT NULL,
    profile_field_id integer NOT NULL,
    profile_field_value text NOT NULL
);


ALTER TABLE public.profile_field_values OWNER TO mayosala_socialer;

--
-- Name: profile_fields; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE profile_fields (
    profile_field_id integer NOT NULL,
    profile_field text NOT NULL
);


ALTER TABLE public.profile_fields OWNER TO mayosala_socialer;

--
-- Name: profile_fields_profile_field_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE profile_fields_profile_field_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.profile_fields_profile_field_id_seq OWNER TO mayosala_socialer;

--
-- Name: profile_fields_profile_field_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE profile_fields_profile_field_id_seq OWNED BY profile_fields.profile_field_id;


--
-- Name: profile_fields_profile_field_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('profile_fields_profile_field_id_seq', 4, true);


--
-- Name: quick_pick_topics; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE quick_pick_topics (
    quick_pick_topic_id integer NOT NULL,
    quick_pick_topic_description text NOT NULL
);


ALTER TABLE public.quick_pick_topics OWNER TO mayosala_socialer;

--
-- Name: quick_pick_topics_quick_pick_topic_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE quick_pick_topics_quick_pick_topic_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.quick_pick_topics_quick_pick_topic_id_seq OWNER TO mayosala_socialer;

--
-- Name: quick_pick_topics_quick_pick_topic_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE quick_pick_topics_quick_pick_topic_id_seq OWNED BY quick_pick_topics.quick_pick_topic_id;


--
-- Name: quick_pick_topics_quick_pick_topic_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('quick_pick_topics_quick_pick_topic_id_seq', 3, true);


--
-- Name: quick_picks; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE quick_picks (
    quick_pick_id integer NOT NULL,
    quick_pick_topic_id integer,
    quick_pick_description text NOT NULL
);


ALTER TABLE public.quick_picks OWNER TO mayosala_socialer;

--
-- Name: quick_picks_quick_pick_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE quick_picks_quick_pick_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.quick_picks_quick_pick_id_seq OWNER TO mayosala_socialer;

--
-- Name: quick_picks_quick_pick_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE quick_picks_quick_pick_id_seq OWNED BY quick_picks.quick_pick_id;


--
-- Name: quick_picks_quick_pick_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('quick_picks_quick_pick_id_seq', 13, true);


--
-- Name: states; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE states (
    state_id integer NOT NULL,
    state_name text NOT NULL,
    country_id integer
);


ALTER TABLE public.states OWNER TO mayosala_socialer;

--
-- Name: states_state_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE states_state_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.states_state_id_seq OWNER TO mayosala_socialer;

--
-- Name: states_state_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE states_state_id_seq OWNED BY states.state_id;


--
-- Name: states_state_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('states_state_id_seq', 1, true);


--
-- Name: user_quick_picks; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE user_quick_picks (
    user_id integer NOT NULL,
    quick_pick_id integer NOT NULL,
    set_at timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.user_quick_picks OWNER TO mayosala_socialer;

--
-- Name: users; Type: TABLE; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

CREATE TABLE users (
    user_id integer NOT NULL,
    first_name character varying(75) NOT NULL,
    last_name character varying(75) NOT NULL,
    email_address character varying(75) NOT NULL,
    date_of_birth date NOT NULL,
    password text NOT NULL,
    gender character(1) NOT NULL,
    signup_date timestamp with time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.users OWNER TO mayosala_socialer;

--
-- Name: users_user_id_seq; Type: SEQUENCE; Schema: public; Owner: mayosala_socialer
--

CREATE SEQUENCE users_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.users_user_id_seq OWNER TO mayosala_socialer;

--
-- Name: users_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mayosala_socialer
--

ALTER SEQUENCE users_user_id_seq OWNED BY users.user_id;


--
-- Name: users_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mayosala_socialer
--

SELECT pg_catalog.setval('users_user_id_seq', 124, true);


--
-- Name: attendance_status_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE attendance_statuses ALTER COLUMN attendance_status_id SET DEFAULT nextval('attendance_statuses_attendance_status_id_seq'::regclass);


--
-- Name: city_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE cities ALTER COLUMN city_id SET DEFAULT nextval('cities_city_id_seq'::regclass);


--
-- Name: country_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE countries ALTER COLUMN country_id SET DEFAULT nextval('countries_country_id_seq'::regclass);


--
-- Name: interest_type_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE event_interest_types ALTER COLUMN interest_type_id SET DEFAULT nextval('event_interest_types_interest_type_id_seq'::regclass);


--
-- Name: event_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE events ALTER COLUMN event_id SET DEFAULT nextval('events_event_id_seq'::regclass);


--
-- Name: location_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE locations ALTER COLUMN location_id SET DEFAULT nextval('locations_location_id_seq'::regclass);


--
-- Name: network_type_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE network_types ALTER COLUMN network_type_id SET DEFAULT nextval('network_types_network_type_id_seq'::regclass);


--
-- Name: network_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE networks ALTER COLUMN network_id SET DEFAULT nextval('networks_network_id_seq'::regclass);


--
-- Name: photo_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE photos ALTER COLUMN photo_id SET DEFAULT nextval('photos_photo_id_seq'::regclass);


--
-- Name: profile_field_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE profile_fields ALTER COLUMN profile_field_id SET DEFAULT nextval('profile_fields_profile_field_id_seq'::regclass);


--
-- Name: quick_pick_topic_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE quick_pick_topics ALTER COLUMN quick_pick_topic_id SET DEFAULT nextval('quick_pick_topics_quick_pick_topic_id_seq'::regclass);


--
-- Name: quick_pick_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE quick_picks ALTER COLUMN quick_pick_id SET DEFAULT nextval('quick_picks_quick_pick_id_seq'::regclass);


--
-- Name: state_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE states ALTER COLUMN state_id SET DEFAULT nextval('states_state_id_seq'::regclass);


--
-- Name: user_id; Type: DEFAULT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE users ALTER COLUMN user_id SET DEFAULT nextval('users_user_id_seq'::regclass);


--
-- Data for Name: attendance_statuses; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY attendance_statuses (attendance_status_id, attendance_status) FROM stdin;
1	yes
2	no
3	maybe
\.


--
-- Data for Name: cities; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY cities (city_id, city_name, state_id) FROM stdin;
1	Philadelphia	\N
\.


--
-- Data for Name: countries; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY countries (country_id, country_name) FROM stdin;
1	United States
\.


--
-- Data for Name: event_interest_types; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY event_interest_types (interest_type_id, interest_type_description) FROM stdin;
1	Yes
2	No
3	Maybe
\.


--
-- Data for Name: event_interests; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY event_interests (user_id, event_id, interest_type_id) FROM stdin;
\.


--
-- Data for Name: events; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY events (event_id, location_id, starts_at, ends_at, description) FROM stdin;
\.


--
-- Data for Name: friend_requests; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY friend_requests (recipient_id, sender_id, sent_at) FROM stdin;
115	1	2012-02-06 21:57:00.929259-05
107	1	2012-02-06 21:57:33.618286-05
113	1	2012-02-06 21:57:50.643175-05
107	106	2012-02-06 23:31:36.092164-05
123	1	2012-02-07 11:09:28.033944-05
105	1	2012-02-07 11:10:37.818329-05
117	119	2012-02-07 12:09:52.883873-05
113	119	2012-02-07 13:50:13.124579-05
115	119	2012-02-07 13:52:04.50139-05
120	124	2012-02-07 14:06:01.070681-05
1	114	2012-02-07 22:40:26.61322-05
1	112	2012-02-07 22:40:28.442777-05
1	113	2012-02-07 22:40:30.049409-05
\.


--
-- Data for Name: friends; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY friends (user_one, user_two, created_at) FROM stdin;
1	114	2012-02-07 22:16:07.534035-05
114	1	2012-02-07 22:16:07.534035-05
\.


--
-- Data for Name: genders; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY genders (gender_id, description) FROM stdin;
m	male
f	female
\.


--
-- Data for Name: location_attendees; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY location_attendees (location_id, user_id, attendance_date, attendance_status_id, set_at) FROM stdin;
20	1	2011-12-11	1	2011-12-12 08:56:24.544651-05
13	1	2011-12-10	1	2011-12-25 15:16:52.517575-05
13	1	2011-12-07	3	2011-12-25 15:38:14.619787-05
13	1	2011-12-25	2	2011-12-25 16:23:33.35383-05
20	1	2011-12-25	2	2011-12-25 16:23:34.11854-05
13	1	2012-01-28	3	2011-12-26 12:32:01.556026-05
30	1	2011-12-26	1	2011-12-26 17:17:42.547867-05
13	1	2011-12-26	2	2011-12-26 14:07:13.292271-05
27	1	2011-12-28	1	2011-12-26 16:11:22.48313-05
26	1	2011-12-27	1	2011-12-26 16:41:32.288552-05
20	1	2011-12-26	1	2011-12-26 17:17:27.851793-05
27	1	2011-12-26	2	2011-12-26 17:17:40.979037-05
20	1	2011-12-28	3	2011-12-28 10:08:15.062965-05
13	1	2011-12-28	1	2011-12-28 10:59:33.318833-05
20	1	2011-12-29	1	2011-12-26 17:46:06.610125-05
31	1	2011-12-28	1	2011-12-27 15:31:33.174272-05
31	1	2011-12-27	1	2011-12-27 20:48:50.880141-05
31	1	2012-01-01	2	2011-12-28 10:08:01.313128-05
26	1	2011-12-29	2	2011-12-28 10:08:01.349743-05
13	1	2011-12-30	1	2011-12-28 10:08:01.363861-05
29	1	2011-12-28	3	2011-12-28 10:08:01.368918-05
28	1	2011-12-31	2	2011-12-28 10:08:01.373943-05
25	1	2012-01-01	3	2011-12-28 10:08:15.040439-05
32	1	2011-12-28	1	2011-12-28 10:08:15.076958-05
28	1	2011-12-28	2	2011-12-28 11:04:15.951116-05
32	1	2011-12-29	1	2011-12-29 16:09:42.567443-05
30	1	2011-12-29	1	2011-12-29 16:11:19.727841-05
13	1	2011-12-29	1	2011-12-29 16:27:10.32289-05
32	1	2011-12-31	3	2011-12-29 16:27:28.074448-05
27	1	2011-12-29	3	2011-12-29 20:38:09.793678-05
27	1	2012-01-02	1	2012-01-02 22:23:17.841195-05
30	1	2012-01-03	3	2012-01-03 10:48:58.751705-05
25	1	2012-01-03	3	2012-01-03 14:37:29.039551-05
29	1	2012-01-03	3	2012-01-03 14:49:28.363841-05
27	1	2012-01-04	1	2012-01-04 12:45:53.800806-05
30	1	2012-01-07	1	2012-01-04 12:46:01.598409-05
31	1	2012-01-04	1	2012-01-04 12:56:08.889391-05
30	1	2012-01-06	3	2012-01-04 12:56:53.482868-05
28	1	2012-01-05	1	2012-01-05 08:26:27.850531-05
27	1	2012-01-06	3	2012-01-06 10:18:36.901096-05
25	1	2012-01-06	2	2012-01-06 10:23:35.797173-05
27	1	2012-01-10	1	2012-01-08 17:17:31.910678-05
30	1	2012-01-08	1	2012-01-08 18:24:40.128063-05
29	1	2012-01-11	1	2012-01-11 12:49:31.203764-05
25	1	2012-01-11	3	2012-01-11 12:50:49.276427-05
27	1	2012-01-11	3	2012-01-11 12:50:51.358278-05
27	1	2012-01-13	1	2012-01-12 22:19:14.201032-05
27	1	2012-01-12	1	2012-01-12 22:19:46.626932-05
29	1	2012-01-12	1	2012-01-12 22:19:47.737127-05
13	1	2012-01-20	3	2012-01-16 16:08:11.014074-05
29	1	2012-01-20	3	2012-01-16 16:08:11.016953-05
27	1	2012-01-16	1	2012-01-16 16:08:20.078245-05
33	1	2012-01-16	1	2012-01-16 21:24:33.837457-05
13	1	2012-01-16	1	2012-01-16 21:53:22.851513-05
13	1	2012-01-19	1	2012-01-19 09:36:42.570947-05
27	1	2012-01-27	3	2012-01-27 11:13:26.54492-05
29	1	2012-01-27	3	2012-01-27 11:13:27.647801-05
25	1	2012-02-02	1	2012-02-02 21:30:55.985172-05
13	1	2012-02-01	2	2012-02-01 21:08:48.606739-05
27	1	2012-02-04	2	2012-02-01 21:08:57.264798-05
30	1	2012-02-01	2	2012-02-01 21:08:57.253807-05
33	1	2012-02-01	2	2012-02-01 21:08:57.306203-05
25	106	2012-02-02	1	2012-02-02 20:39:00.833171-05
30	1	2012-02-08	3	2012-02-06 16:43:40.791657-05
13	1	2012-02-08	1	2012-02-06 16:43:41.451586-05
27	1	2012-02-02	1	2012-02-02 21:49:16.287328-05
29	106	2012-02-06	3	2012-02-06 16:44:30.821645-05
27	1	2012-02-05	1	2012-02-05 16:42:53.408247-05
27	1	2012-02-03	2	2012-02-03 11:33:20.997307-05
30	106	2012-02-06	1	2012-02-06 16:44:31.814201-05
29	1	2012-02-05	2	2012-02-05 16:42:54.802125-05
27	106	2012-02-07	1	2012-02-06 16:44:34.797163-05
29	1	2012-02-03	1	2012-02-03 11:35:05.370716-05
25	1	2012-02-05	1	2012-02-05 16:42:56.039368-05
13	1	2012-02-05	3	2012-02-05 16:42:57.001288-05
25	106	2012-02-07	1	2012-02-06 16:44:35.406319-05
29	107	2012-02-03	1	2012-02-03 16:07:53.281083-05
30	1	2012-02-05	2	2012-02-05 16:43:00.267684-05
27	107	2012-02-03	3	2012-02-03 16:07:57.82293-05
29	112	2012-02-03	1	2012-02-03 16:08:15.564586-05
29	114	2012-02-03	1	2012-02-03 16:11:26.772203-05
29	115	2012-02-03	1	2012-02-03 16:13:35.041539-05
27	107	2012-02-06	1	2012-02-06 16:39:23.798763-05
29	107	2012-02-06	1	2012-02-06 16:39:24.648193-05
25	107	2012-02-06	3	2012-02-06 16:39:25.509922-05
27	116	2012-02-03	1	2012-02-03 16:16:44.197147-05
29	116	2012-02-03	3	2012-02-03 16:17:38.899099-05
27	107	2012-02-07	1	2012-02-06 16:39:28.282579-05
29	107	2012-02-07	3	2012-02-06 16:39:29.060992-05
25	107	2012-02-07	1	2012-02-06 16:39:30.602151-05
13	107	2012-02-07	3	2012-02-06 16:39:32.471636-05
30	107	2012-02-07	1	2012-02-06 16:39:33.176588-05
27	107	2012-02-08	1	2012-02-06 16:39:37.127247-05
29	107	2012-02-08	1	2012-02-06 16:39:37.854031-05
25	107	2012-02-08	3	2012-02-06 16:39:38.599812-05
13	107	2012-02-08	3	2012-02-06 16:39:39.510277-05
29	112	2012-02-06	1	2012-02-06 16:39:55.638099-05
13	112	2012-02-06	1	2012-02-06 16:39:57.378045-05
27	112	2012-02-06	1	2012-02-06 16:39:58.284864-05
13	106	2012-02-07	3	2012-02-06 16:44:36.167213-05
27	112	2012-02-07	1	2012-02-06 16:40:03.859735-05
30	112	2012-02-07	3	2012-02-06 16:40:04.676731-05
25	112	2012-02-07	1	2012-02-06 16:40:05.0354-05
27	112	2012-02-08	1	2012-02-06 16:40:09.570017-05
13	112	2012-02-07	1	2012-02-06 16:40:09.634375-05
29	112	2012-02-08	1	2012-02-06 16:40:10.162957-05
25	112	2012-02-08	1	2012-02-06 16:40:11.091956-05
13	113	2012-02-03	1	2012-02-03 16:26:32.326432-05
30	113	2012-02-03	1	2012-02-03 16:26:33.728861-05
27	106	2012-02-03	1	2012-02-03 16:27:10.876511-05
29	113	2012-02-06	1	2012-02-06 16:40:33.219707-05
27	113	2012-02-06	1	2012-02-06 16:40:34.267757-05
30	106	2012-02-03	1	2012-02-03 16:27:15.55508-05
25	106	2012-02-03	3	2012-02-03 16:27:16.157336-05
13	106	2012-02-03	2	2012-02-03 16:27:18.08984-05
29	113	2012-02-03	3	2012-02-03 16:31:27.313336-05
13	113	2012-02-06	3	2012-02-06 16:40:37.16581-05
30	113	2012-02-06	1	2012-02-06 16:40:39.067518-05
27	113	2012-02-03	1	2012-02-03 16:31:30.558723-05
27	113	2012-02-07	1	2012-02-06 16:40:41.27275-05
25	113	2012-02-03	1	2012-02-03 16:31:34.09809-05
30	119	2012-02-03	1	2012-02-03 16:35:56.090722-05
13	119	2012-02-03	1	2012-02-03 16:35:59.471477-05
25	113	2012-02-07	1	2012-02-06 16:40:41.813481-05
30	113	2012-02-07	1	2012-02-06 16:40:42.386163-05
13	113	2012-02-07	3	2012-02-06 16:40:43.463769-05
27	113	2012-02-08	1	2012-02-06 16:40:46.731742-05
29	106	2012-02-03	1	2012-02-03 16:40:04.359914-05
25	113	2012-02-08	1	2012-02-06 16:40:47.399588-05
27	119	2012-02-05	1	2012-02-03 16:40:42.705799-05
30	113	2012-02-08	1	2012-02-06 16:40:48.494449-05
29	119	2012-02-03	1	2012-02-03 16:40:46.435869-05
27	119	2012-02-03	1	2012-02-03 16:40:52.988027-05
25	119	2012-02-03	1	2012-02-03 16:40:55.927685-05
27	106	2012-02-04	1	2012-02-04 16:58:27.711239-05
27	120	2012-02-04	1	2012-02-04 17:03:15.399251-05
27	116	2012-02-04	1	2012-02-04 17:05:32.77755-05
29	116	2012-02-04	1	2012-02-04 17:05:34.724261-05
25	116	2012-02-04	3	2012-02-04 17:05:35.969059-05
27	114	2012-02-06	1	2012-02-06 16:41:38.891747-05
29	114	2012-02-06	1	2012-02-06 16:41:39.633236-05
30	114	2012-02-06	1	2012-02-06 16:41:40.739077-05
13	114	2012-02-06	1	2012-02-06 16:41:41.500302-05
27	114	2012-02-07	1	2012-02-06 16:41:44.706371-05
30	106	2012-02-07	1	2012-02-06 16:44:37.394817-05
25	114	2012-02-07	3	2012-02-06 16:41:47.45084-05
30	114	2012-02-07	1	2012-02-06 16:41:47.735382-05
13	114	2012-02-07	1	2012-02-06 16:41:48.888266-05
27	114	2012-02-08	1	2012-02-06 16:41:51.404187-05
29	114	2012-02-08	3	2012-02-06 16:41:52.5947-05
25	114	2012-02-08	1	2012-02-06 16:41:53.248593-05
30	114	2012-02-08	1	2012-02-06 16:41:54.799912-05
13	114	2012-02-08	1	2012-02-06 16:41:58.728641-05
27	116	2012-02-06	1	2012-02-06 16:42:15.033037-05
29	116	2012-02-06	1	2012-02-06 16:42:16.120201-05
13	116	2012-02-06	3	2012-02-06 16:42:17.480096-05
30	116	2012-02-06	1	2012-02-06 16:42:18.192934-05
25	116	2012-02-06	3	2012-02-06 16:42:19.866375-05
27	116	2012-02-07	1	2012-02-06 16:42:22.891348-05
30	116	2012-02-07	3	2012-02-06 16:42:23.799264-05
27	106	2012-02-06	1	2012-02-06 16:51:03.369875-05
25	116	2012-02-07	1	2012-02-06 16:42:24.439656-05
13	116	2012-02-07	1	2012-02-06 16:42:25.296691-05
13	106	2012-02-06	1	2012-02-06 16:44:44.932047-05
29	116	2012-02-07	3	2012-02-06 16:42:26.440488-05
27	116	2012-02-08	1	2012-02-06 16:42:28.558415-05
25	116	2012-02-08	1	2012-02-06 16:42:29.299213-05
30	116	2012-02-08	3	2012-02-06 16:42:30.224601-05
29	116	2012-02-08	1	2012-02-06 16:42:30.663666-05
29	119	2012-02-06	1	2012-02-06 16:42:54.107914-05
27	119	2012-02-06	1	2012-02-06 16:42:55.824264-05
30	119	2012-02-06	3	2012-02-06 16:42:56.837943-05
13	119	2012-02-06	1	2012-02-06 16:42:57.342846-05
25	119	2012-02-06	1	2012-02-06 16:42:58.565002-05
30	119	2012-02-07	1	2012-02-06 16:43:02.618198-05
13	119	2012-02-07	1	2012-02-06 16:43:03.330766-05
27	119	2012-02-08	1	2012-02-06 16:43:06.445942-05
25	119	2012-02-08	3	2012-02-06 16:43:07.403264-05
29	119	2012-02-08	1	2012-02-06 16:43:08.041308-05
30	119	2012-02-08	1	2012-02-06 16:43:08.607819-05
13	119	2012-02-08	1	2012-02-06 16:43:10.279197-05
27	1	2012-02-06	1	2012-02-06 16:43:28.45417-05
13	1	2012-02-06	1	2012-02-06 16:43:29.603471-05
30	1	2012-02-06	1	2012-02-06 16:43:30.775065-05
30	1	2012-02-07	3	2012-02-06 16:43:33.750764-05
27	1	2012-02-07	1	2012-02-06 16:43:34.357154-05
25	1	2012-02-07	1	2012-02-06 16:43:35.027413-05
13	1	2012-02-07	1	2012-02-06 16:43:35.9113-05
27	1	2012-02-08	1	2012-02-06 16:43:38.783682-05
29	1	2012-02-08	1	2012-02-06 16:43:39.52005-05
25	1	2012-02-08	1	2012-02-06 16:43:39.989204-05
25	106	2012-02-06	1	2012-02-06 16:44:46.687461-05
27	106	2012-02-08	1	2012-02-06 16:44:49.463679-05
25	106	2012-02-08	1	2012-02-06 16:44:50.636182-05
29	106	2012-02-08	3	2012-02-06 16:44:51.127473-05
13	106	2012-02-08	1	2012-02-06 16:44:52.063546-05
30	106	2012-02-08	1	2012-02-06 16:44:53.074502-05
27	121	2012-02-06	1	2012-02-06 16:54:34.680406-05
29	122	2012-02-06	1	2012-02-06 22:10:49.151166-05
27	122	2012-02-06	1	2012-02-06 23:26:22.865723-05
29	106	2012-02-07	1	2012-02-07 11:01:10.942913-05
27	123	2012-02-07	1	2012-02-07 11:03:54.062145-05
27	123	2012-02-09	1	2012-02-07 11:03:57.750145-05
29	123	2012-02-09	3	2012-02-07 11:04:00.157865-05
25	123	2012-02-09	1	2012-02-07 11:04:01.421089-05
30	123	2012-02-07	1	2012-02-07 11:04:10.315432-05
27	123	2012-02-08	1	2012-02-07 11:04:12.63718-05
25	123	2012-02-08	1	2012-02-07 11:04:13.513841-05
29	123	2012-02-08	1	2012-02-07 11:04:14.42564-05
13	123	2012-02-08	3	2012-02-07 11:04:18.480806-05
30	123	2012-02-08	3	2012-02-07 11:04:19.343268-05
29	119	2012-02-07	3	2012-02-07 13:50:43.251096-05
13	123	2012-02-09	1	2012-02-07 11:04:21.890618-05
25	119	2012-02-07	1	2012-02-07 12:11:03.923875-05
27	119	2012-02-07	3	2012-02-07 13:54:00.236414-05
25	124	2012-02-07	3	2012-02-07 14:03:41.934448-05
27	124	2012-02-07	1	2012-02-07 14:05:53.929489-05
29	124	2012-02-08	1	2012-02-07 14:06:28.113173-05
\.


--
-- Data for Name: locations; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY locations (location_id, city_id, location_name, street_address, description, website, yelp_id, latitude, longitude) FROM stdin;
28	1	Alfa	1709 Walnut St	Conceived as a casual hangout for the upscale Rittenhouse neighborhood, Alfa is perfect for Happy Hour, to meet friends for a cocktail, or to have an affordable but casual dinner.	http://alfa-bar.com/	0	39.9502490	-75.1694890
30	1	Oh Shea's Pub	1907 Sansom Street	Oh! Shea.s Pub.is located in center city Philadelphia at 19th and Sansom street, one block from Rittenhouse Square with friendly atmosphere, good times, great drinks and outstanding food. Our menu offers a variety of appetizers, salads, sandwiches, entrees and desserts at great prices. Oh! Shea.s has two large bars, two dining rooms, two large flat screen plasma TVs for sports fans. 	http://ohsheaspub.com/	0	39.9511580	-75.1723775
13	1	The Bards	2013 Walnut Street	The Bards Irish Pub is a place where the locals can gather for good times; to meet and embrace each other and to lean on each other in bad times. Life long friends are made at The Bards.	http://www.bardsirishbar.com	0	39.9506702	-75.1742922
20	1	Drinker's Tavern	124 Market Street	If you're sick of the up tight bars and need to let loose, then come liberate yourself at Drinker´s Tavern. Located in the heart of Old City, the Tavern brings rousing nightlife to the neighborhood. Drinker´s Tavern carries an energetic atmosphere throughout two floors. The main room is lined with a wood decor, a huge bar and ample seating. Admire the Elvis memorabilia and play your favorite songs on our jukebox which has been rated best jukebox several years in running. The kitchen is open until 11pm daily with $1 Tacos available at any time.	http://www.drinkers215.com	0	39.9496100	-75.1432590
25	1	The Bards	2013 Walnut Street	The Bards Irish Pub is a place where the locals can gather for good times; to meet and embrace each other and to lean on each other in bad times. Life long friends are made at The Bards.	http://www.facebook.com/pages/The-Bards/110268725696386	0	39.9506702	-75.1742922
26	1	Irish Pub	2007 Walnut Street	The Philadelphia Irish Pub is the classic Irish American Pub and has two locations to serve you. Long time staff, always lively atmosphere, and hearty pub fare, the Irish Pub is one of the most beloved in the city. Visit us for your favorite drink, eat our delicious meals and socialize with the many great visitors. From famous sports stars to local celebrities to politicians to locals, everyone has a story about a night at the Irish Pub!	http://www.irishpubphilly.com/20thstreet/home.php	0	39.9505503	-75.1740916
27	1	Rum Bar	2005 Walnut Street	Located just off Philadelphia's famed Rittenhouse Square Park, Rum Bar offers a unique escape and strikes the perfect balance between upbeat and intimate, lively, but laid back. Music ranges from contemporary Rock 'n' Roll to Reggae to late-night lounge.	http://rum-bar.com/	0	39.9506514	-75.1739867
31	1	Drinker's Pub	1903 Chestnut Street	Drinker's Pub is a classic bar well-known for its friendly atmosphere and all around good times. Located on Chestnut Street, in the heart of Center City the Pub features a delicious food menu, amazing drink specials and there will never be a cover charge.	http://www.drinkerspub215.com/	0	39.9520989	-75.1720640
32	1	Noche	1901 Chestnut St	Noche Lounge, located just 1 block from Rittenhouse Square is the perfect spot to relax. The wide open space and second floor corner location are great for people watching on 19th and Chestnut streets. The room invites the outdoors in with sky-high windows and large booths lining the corner lounge, perfect for kicking back and soaking up the urban scene.	http://www.noche215.com/	0	39.9519225	-75.1720689
33	1	Devil's Alley	1907 Chestnut St	In Short . Set up to mimic an urban park, this bi-level restaurant and bar is lush with potted plants, patio furniture and garden benches. The menu blends comfort foods--whole BBQ chickens, onion rings, marshmallow-topped sweet potatoes, pulled pork, mac and cheese, stuffing shaped into muffins--with light, international fare.	http://www.devilsalleybarandgrill.com/	0	39.9521110	-75.1722020
29	1	Tavern on Broad	200 S. Broad Street	Located in the heart of Center City beneath the Bellevue on the Corner of Broad and Walnut Streets, Tavern on Broad is open seven days per week from 11am-2am.  Tavern on Broad offers our guests a true fan experience with 34 HDTV's...there is not a bad seat in the house.  Come and enjoy cold beer and great food. 	http://www.tavernonbroad.com/	0	39.9490280	-75.1644820
\.


--
-- Data for Name: network_mappings; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY network_mappings (user_id, network_id, added_at) FROM stdin;
\.


--
-- Data for Name: network_types; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY network_types (network_type_id, network_type_name) FROM stdin;
\.


--
-- Data for Name: networks; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY networks (network_id, network_type_id, network_name) FROM stdin;
\.


--
-- Data for Name: photos; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY photos (photo_id, user_id, is_default, path, added_at) FROM stdin;
29	1	f	4f1366001f49b.jpg	2012-01-15 18:49:20.219289-05
30	1	f	4f13789d2da49.jpg	2012-01-15 20:08:45.261273-05
31	1	f	4f137b3a65ae7.jpg	2012-01-15 20:19:54.484664-05
32	1	f	4f1448f70a3da.jpg	2012-01-16 10:57:43.171955-05
33	1	f	4f144a736e386.jpg	2012-01-16 11:04:03.49261-05
34	1	f	4f144f08a6fb7.jpg	2012-01-16 11:23:36.766424-05
35	1	f	4f144f54edce1.jpg	2012-01-16 11:24:53.033761-05
36	1	f	4f144fe40eaa1.jpg	2012-01-16 11:27:16.120597-05
37	1	f	4f1450191c630.jpg	2012-01-16 11:28:09.20324-05
38	1	f	4f1450282a70e.jpg	2012-01-16 11:28:24.230014-05
39	1	f	4f14504ea1b58.jpg	2012-01-16 11:29:02.761465-05
40	1	f	4f145092d3382.jpg	2012-01-16 11:30:10.952334-05
41	1	f	4f14515fb1df2.jpg	2012-01-16 11:33:35.804155-05
42	1	f	4f1451ac98b4f.jpg	2012-01-16 11:34:52.725276-05
43	1	f	4f1451d584075.jpg	2012-01-16 11:35:33.625576-05
44	1	f	4f145255bcbde.jpg	2012-01-16 11:37:41.834492-05
45	1	f	4f145287e26f6.jpg	2012-01-16 11:38:32.020831-05
46	1	f	4f1452a4aadaf.jpg	2012-01-16 11:39:01.280814-05
47	1	f	4f1452be79b3d.jpg	2012-01-16 11:39:26.585709-05
48	1	f	4f1454024b2be.jpg	2012-01-16 11:44:50.364622-05
49	1	f	4f1454939a35d.jpg	2012-01-16 11:47:15.716949-05
50	1	f	4f1454ef763d0.jpg	2012-01-16 11:48:47.567712-05
51	1	f	4f14555719955.jpg	2012-01-16 11:50:31.19134-05
52	1	f	4f1455c9ba1d9.jpg	2012-01-16 11:52:25.846883-05
53	1	f	4f1455d927853.jpg	2012-01-16 11:52:41.22132-05
54	1	f	4f14561923696.jpg	2012-01-16 11:53:45.182746-05
55	1	f	4f14561fe84b8.jpg	2012-01-16 11:53:52.00063-05
56	1	f	4f14564cb3094.jpg	2012-01-16 11:54:36.822364-05
57	1	f	4f1457547ec51.jpg	2012-01-16 11:59:00.612988-05
58	1	f	4f145761766c4.jpg	2012-01-16 11:59:13.527466-05
59	1	f	4f14bb3886a3d.jpg	2012-01-16 19:05:12.628205-05
60	1	f	4f14d9da39a67.jpg	2012-01-16 21:15:54.311324-05
61	1	f	4f14e4665906c.jpg	2012-01-16 22:00:54.404767-05
66	106	t	4f2b35b7e6263.jpg	2012-02-02 20:17:43.980757-05
67	106	f	4f2b35cf978fd.jpg	2012-02-02 20:18:07.656661-05
68	106	f	4f2b35d34e013.jpg	2012-02-02 20:18:11.362776-05
69	106	f	4f2b377cba3b3.jpg	2012-02-02 20:25:16.84032-05
70	1	f	4f2b405191115.jpg	2012-02-02 21:02:57.697087-05
71	1	f	4f2b40af35dd6.jpg	2012-02-02 21:04:31.367837-05
72	1	f	4f2b411fa6271.jpg	2012-02-02 21:06:23.773334-05
74	107	t	4f2c4a1dc5337.jpg	2012-02-03 15:57:02.004699-05
75	112	t	4f2c4ad586f81.jpg	2012-02-03 16:00:05.570514-05
76	106	f	4f2c4be00af05.jpg	2012-02-03 16:04:32.078365-05
77	113	t	4f2c4c6859837.jpg	2012-02-03 16:06:48.430482-05
78	114	t	4f2c4d5613a33.jpg	2012-02-03 16:10:46.240046-05
79	114	f	4f2c4d6680276.jpg	2012-02-03 16:11:02.641673-05
80	115	t	4f2c4de89d476.jpg	2012-02-03 16:13:12.692451-05
81	116	t	4f2c4ea0e37cf.jpg	2012-02-03 16:16:16.984611-05
82	119	t	4f2c531b1303f.jpg	2012-02-03 16:35:23.126526-05
83	120	t	4f2dab1067690.jpg	2012-02-04 17:02:56.519666-05
73	1	t	4f2b415e6ef99.jpg	2012-02-02 21:07:26.565534-05
84	121	t	4f304b6bdb164.jpg	2012-02-06 16:51:39.94987-05
85	122	t	4f30982044984.jpg	2012-02-06 22:18:56.358495-05
86	123	t	4f314b3843389.jpg	2012-02-07 11:03:04.357439-05
87	124	t	4f3174d6b06b9.jpg	2012-02-07 14:00:38.839296-05
88	124	f	4f317508acd60.jpg	2012-02-07 14:01:28.762607-05
\.


--
-- Data for Name: profile_field_values; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY profile_field_values (user_id, profile_field_id, profile_field_value) FROM stdin;
1	2	SomeCollege
1	4	LocationLocationLocation
1	3	I like to do things.
106	2	Vanderbilt University
106	4	Philadelphia
106	3	From Anaheim, CA... moved to Phily after Graduation-- I love music and sports and I founded The Socialer with my friends
105	2	
105	4	
105	3	This is going to be maybe two lines.  Maybe even three by the time I'm done with it.
107	2	Lehigh 
107	4	Philadelphia
107	3	Live and LOVE Philly-- Outdoorsy!
112	2	Vanderbilt
112	4	Philadelphia
112	3	Im from Cincinatti, love the midwest-- play soccer and enjoy hiking
113	2	Columbia
113	4	Philadelphia
113	3	From California-- Have a passion for meeting interesting people
114	2	Vanderbilt
114	4	Philadelphia
114	3	
115	2	Vanderbilt
115	4	Philadelphia
115	3	
116	2	UPenn
116	4	Philadelphia
116	3	
119	2	Vanderbilt
119	4	Philadelphia
119	3	Im a singer in a band -- but i have a real job also
120	2	Vanderbilt
120	4	Conshohocken
120	3	I almost never go out, but when I do, I prefer to avoid guidos.
122	2	American University
122	4	Philadelphia
122	3	I love the Bachelor.
123	2	Columbia
123	4	Philadelphia
123	3	I played football and wrestled in college
124	2	USC
124	4	Philadelphia
124	3	I am tall and good looking
\.


--
-- Data for Name: profile_fields; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY profile_fields (profile_field_id, profile_field) FROM stdin;
2	College
3	AboutMe
4	Location
\.


--
-- Data for Name: quick_pick_topics; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY quick_pick_topics (quick_pick_topic_id, quick_pick_topic_description) FROM stdin;
1	When I go out I...
2	When I am not in the city I...
3	After work I...
\.


--
-- Data for Name: quick_picks; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY quick_picks (quick_pick_id, quick_pick_topic_id, quick_pick_description) FROM stdin;
1	1	like to watch football
2	1	find a dive bar
3	1	eat at places I read about
4	1	have drinks outside
5	1	meet up with friends
6	2	travel to little known places
7	2	like to take the train
8	2	spend time with the family
9	2	go to wine country
10	3	head to the gym
11	3	drink a margarita
12	3	watch the sunset
13	3	walk in the park
\.


--
-- Data for Name: states; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY states (state_id, state_name, country_id) FROM stdin;
1	Pennsylvania	\N
\.


--
-- Data for Name: user_quick_picks; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY user_quick_picks (user_id, quick_pick_id, set_at) FROM stdin;
1	1	2012-01-31 23:19:21.054111-05
1	2	2012-01-31 23:19:21.054111-05
1	3	2012-01-31 23:19:21.054111-05
1	4	2012-01-31 23:19:21.054111-05
1	5	2012-01-31 23:19:21.054111-05
1	6	2012-01-31 23:19:21.054111-05
1	7	2012-01-31 23:19:21.054111-05
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: mayosala_socialer
--

COPY users (user_id, first_name, last_name, email_address, date_of_birth, password, gender, signup_date) FROM stdin;
1	Matt	Kemmerer	mattbh@gmail.com	1984-05-13	password	m	2011-11-27 17:38:40.819833-05
106	Trevor	Tait	trevor.k.tait@gmail.com	1988-06-05	Hello123	m	2012-02-02 20:14:15.415927-05
105	Timmy	Jones	timmy@kemmerer.co	1990-03-13	123	m	2012-02-02 20:10:36.829281-05
107	Nicole	Clemente	nicole@thesocialer.com	1987-07-07	Hello123	f	2012-02-02 20:36:34.321527-05
112	Chris	Ryan	chris@thesocialer.com	1987-08-06	Hello123	m	2012-02-03 15:58:54.578452-05
113	Ray	Rangel	ray@thesocialer.com	1987-06-11	Hello123	m	2012-02-03 16:06:00.082769-05
114	Anna	Barnwell	anna@thesocialer.com	1987-09-06	Hello234	f	2012-02-03 16:09:58.217774-05
115	Alexa	Papaila	alexa@thesocialer.com	1988-05-06	HEllo123	f	2012-02-03 16:12:55.3194-05
116	Akpo	Ghenobe	akpo@thesocialer.com	1988-07-06	Hello123	m	2012-02-03 16:15:47.615958-05
117	Bryan	Edwards	bryan@thesocialer.com	1988-09-05	Hello123	m	2012-02-03 16:18:10.552863-05
118	Bryan	Edwards	bryan1@thesocialer.com	1988-09-05	Hello123	m	2012-02-03 16:19:12.433965-05
119	Ryan	Coughlin	ryan@thesocialer.com	1988-08-06	password	m	2012-02-03 16:34:36.083182-05
120	Jessica	Bass	jessica@thesocialer.com	1987-12-08	password	f	2012-02-04 17:00:56.454088-05
122	Kristie 	Cole	kristie@thesocialer.com	1987-12-23	password	f	2012-02-06 22:10:00.976996-05
123	Lou	Miller	lou@thesocialer.com	1987-09-07	password	m	2012-02-07 11:01:44.8145-05
124	Mike	Fitzgibbons	mike@thesocialer.com	1988-10-05	password	m	2012-02-07 13:59:41.606768-05
\.


--
-- Name: attendance_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY attendance_statuses
    ADD CONSTRAINT attendance_statuses_pkey PRIMARY KEY (attendance_status_id);


--
-- Name: cities_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY cities
    ADD CONSTRAINT cities_pkey PRIMARY KEY (city_id);


--
-- Name: countries_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY countries
    ADD CONSTRAINT countries_pkey PRIMARY KEY (country_id);


--
-- Name: event_interest_types_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY event_interest_types
    ADD CONSTRAINT event_interest_types_pkey PRIMARY KEY (interest_type_id);


--
-- Name: event_interests_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY event_interests
    ADD CONSTRAINT event_interests_pkey PRIMARY KEY (user_id, event_id);


--
-- Name: events_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY events
    ADD CONSTRAINT events_pkey PRIMARY KEY (event_id);


--
-- Name: friend_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY friend_requests
    ADD CONSTRAINT friend_requests_pkey PRIMARY KEY (recipient_id, sender_id);


--
-- Name: friends_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY friends
    ADD CONSTRAINT friends_pkey PRIMARY KEY (user_one, user_two);


--
-- Name: genders_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY genders
    ADD CONSTRAINT genders_pkey PRIMARY KEY (gender_id);


--
-- Name: location_attendees_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY location_attendees
    ADD CONSTRAINT location_attendees_pkey PRIMARY KEY (location_id, user_id, attendance_date);


--
-- Name: locations_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY locations
    ADD CONSTRAINT locations_pkey PRIMARY KEY (location_id);


--
-- Name: network_mappings_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY network_mappings
    ADD CONSTRAINT network_mappings_pkey PRIMARY KEY (user_id, network_id);


--
-- Name: network_types_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY network_types
    ADD CONSTRAINT network_types_pkey PRIMARY KEY (network_type_id);


--
-- Name: networks_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY networks
    ADD CONSTRAINT networks_pkey PRIMARY KEY (network_id);


--
-- Name: photos_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY photos
    ADD CONSTRAINT photos_pkey PRIMARY KEY (photo_id);


--
-- Name: profile_field_values_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY profile_field_values
    ADD CONSTRAINT profile_field_values_pkey PRIMARY KEY (user_id, profile_field_id);


--
-- Name: profile_fields_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY profile_fields
    ADD CONSTRAINT profile_fields_pkey PRIMARY KEY (profile_field_id);


--
-- Name: profile_fields_profile_field_key; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY profile_fields
    ADD CONSTRAINT profile_fields_profile_field_key UNIQUE (profile_field);


--
-- Name: quick_pick_topics_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY quick_pick_topics
    ADD CONSTRAINT quick_pick_topics_pkey PRIMARY KEY (quick_pick_topic_id);


--
-- Name: quick_pick_topics_quick_pick_topic_description_key; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY quick_pick_topics
    ADD CONSTRAINT quick_pick_topics_quick_pick_topic_description_key UNIQUE (quick_pick_topic_description);


--
-- Name: quick_picks_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY quick_picks
    ADD CONSTRAINT quick_picks_pkey PRIMARY KEY (quick_pick_id);


--
-- Name: quick_picks_quick_pick_topic_id_key; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY quick_picks
    ADD CONSTRAINT quick_picks_quick_pick_topic_id_key UNIQUE (quick_pick_topic_id, quick_pick_description);


--
-- Name: states_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY states
    ADD CONSTRAINT states_pkey PRIMARY KEY (state_id);


--
-- Name: users_email_address_key; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email_address_key UNIQUE (email_address);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: mayosala_socialer; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (user_id);


--
-- Name: cities_state_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY cities
    ADD CONSTRAINT cities_state_id_fkey FOREIGN KEY (state_id) REFERENCES states(state_id);


--
-- Name: event_interests_event_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY event_interests
    ADD CONSTRAINT event_interests_event_id_fkey FOREIGN KEY (event_id) REFERENCES events(event_id);


--
-- Name: event_interests_interest_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY event_interests
    ADD CONSTRAINT event_interests_interest_type_id_fkey FOREIGN KEY (interest_type_id) REFERENCES event_interest_types(interest_type_id);


--
-- Name: event_interests_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY event_interests
    ADD CONSTRAINT event_interests_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id);


--
-- Name: events_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY events
    ADD CONSTRAINT events_location_id_fkey FOREIGN KEY (location_id) REFERENCES locations(location_id);


--
-- Name: friend_requests_recipient_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY friend_requests
    ADD CONSTRAINT friend_requests_recipient_id_fkey FOREIGN KEY (recipient_id) REFERENCES users(user_id);


--
-- Name: friend_requests_sender_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY friend_requests
    ADD CONSTRAINT friend_requests_sender_id_fkey FOREIGN KEY (sender_id) REFERENCES users(user_id);


--
-- Name: friends_user_one_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY friends
    ADD CONSTRAINT friends_user_one_fkey FOREIGN KEY (user_one) REFERENCES users(user_id);


--
-- Name: friends_user_two_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY friends
    ADD CONSTRAINT friends_user_two_fkey FOREIGN KEY (user_two) REFERENCES users(user_id);


--
-- Name: location_attendees_attendance_status_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY location_attendees
    ADD CONSTRAINT location_attendees_attendance_status_id_fkey FOREIGN KEY (attendance_status_id) REFERENCES attendance_statuses(attendance_status_id);


--
-- Name: location_attendees_location_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY location_attendees
    ADD CONSTRAINT location_attendees_location_id_fkey FOREIGN KEY (location_id) REFERENCES locations(location_id);


--
-- Name: location_attendees_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY location_attendees
    ADD CONSTRAINT location_attendees_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id);


--
-- Name: locations_city_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY locations
    ADD CONSTRAINT locations_city_id_fkey FOREIGN KEY (city_id) REFERENCES cities(city_id);


--
-- Name: network_mappings_network_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY network_mappings
    ADD CONSTRAINT network_mappings_network_id_fkey FOREIGN KEY (network_id) REFERENCES networks(network_id);


--
-- Name: network_mappings_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY network_mappings
    ADD CONSTRAINT network_mappings_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id);


--
-- Name: networks_network_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY networks
    ADD CONSTRAINT networks_network_type_id_fkey FOREIGN KEY (network_type_id) REFERENCES network_types(network_type_id);


--
-- Name: photos_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY photos
    ADD CONSTRAINT photos_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id);


--
-- Name: profile_field_values_profile_field_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY profile_field_values
    ADD CONSTRAINT profile_field_values_profile_field_id_fkey FOREIGN KEY (profile_field_id) REFERENCES profile_fields(profile_field_id);


--
-- Name: profile_field_values_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY profile_field_values
    ADD CONSTRAINT profile_field_values_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id);


--
-- Name: quick_picks_quick_pick_topic_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY quick_picks
    ADD CONSTRAINT quick_picks_quick_pick_topic_id_fkey FOREIGN KEY (quick_pick_topic_id) REFERENCES quick_pick_topics(quick_pick_topic_id);


--
-- Name: states_country_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY states
    ADD CONSTRAINT states_country_id_fkey FOREIGN KEY (country_id) REFERENCES countries(country_id);


--
-- Name: user_quick_picks_quick_pick_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY user_quick_picks
    ADD CONSTRAINT user_quick_picks_quick_pick_id_fkey FOREIGN KEY (quick_pick_id) REFERENCES quick_picks(quick_pick_id);


--
-- Name: user_quick_picks_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY user_quick_picks
    ADD CONSTRAINT user_quick_picks_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id);


--
-- Name: users_gender_fkey; Type: FK CONSTRAINT; Schema: public; Owner: mayosala_socialer
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_gender_fkey FOREIGN KEY (gender) REFERENCES genders(gender_id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

