--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: fn; Type: SCHEMA; Schema: -; Owner: wordpress
--

CREATE SCHEMA fn;


ALTER SCHEMA fn OWNER TO wordpress;

SET search_path = fn, pg_catalog;

--
-- Name: __particular__text__512__idx(text); Type: FUNCTION; Schema: fn; Owner: wordpress
--

CREATE FUNCTION __particular__text__512__idx(text text) RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $_$
SELECT CASE WHEN length($1) <= 500 THEN substring($1,0,500) ELSE NULL END$_$;


ALTER FUNCTION fn.__particular__text__512__idx(text text) OWNER TO wordpress;

--
-- Name: _transient_wc_attribute_taxonomies(); Type: FUNCTION; Schema: fn; Owner: wordpress
--

CREATE FUNCTION _transient_wc_attribute_taxonomies() RETURNS text
    LANGUAGE plperlu
    AS $_$
use JSON;
my $r = spi_exec_query('SELECT * FROM wp_woocommerce_attribute_taxonomies');
return to_json( $r->{rows} );
$_$;


ALTER FUNCTION fn._transient_wc_attribute_taxonomies() OWNER TO wordpress;

--
-- Name: between(time without time zone, time without time zone, time without time zone); Type: FUNCTION; Schema: fn; Owner: wordpress
--

CREATE FUNCTION "between"("from" time without time zone, "to" time without time zone, it time without time zone) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$
SELECT
  CASE WHEN (($1 <= $3 OR $1 IS NULL) AND ($3 <= $2 OR $2 IS NULL)) THEN true
       WHEN ($2 < $1 AND (($3 >= $1 OR $1 IS NULL) OR ($2 >= $3 OR $2 IS NULL))) THEN true
       ELSE false
  END;
 $_$;


ALTER FUNCTION fn."between"("from" time without time zone, "to" time without time zone, it time without time zone) OWNER TO wordpress;

--
-- Name: json2php(text); Type: FUNCTION; Schema: fn; Owner: wordpress
--

CREATE FUNCTION json2php(json text) RETURNS text
    LANGUAGE plv8 IMMUTABLE STRICT
    AS $$
function serialize(jsonstr) { // Generates a storable representation of a value
  //
  // +   original by: Ates Goral (http://magnetiq.com)
  // +   adapted for IE: Ilia Kantor (http://javascript.ru)

  switch (typeof (jsonstr)) {
  case "number":
    if (isNaN(jsonstr) || !isFinite(jsonstr)) {
      return false;
    } else {
      return (Math.floor(jsonstr) == jsonstr ? "i" : "d") + ":" + jsonstr + ";";
    }
  case "string":
    return "s:" + mb_length(jsonstr) + ":\"" + jsonstr + "\";";
  case "boolean":
    return "b:" + (jsonstr ? "1" : "0") + ";";
  case "object":
    if (jsonstr == null) {
      return "N;";
    } else if (jsonstr instanceof Array) {
      var idxobj = {
        idx: -1
      };
      var map = []
      for (var i = 0; i < jsonstr.length; i++) {
        idxobj.idx++;
        var ser = serialize(jsonstr[i]);

        if (ser) {
          map.push(serialize(idxobj.idx) + ser)
        }
      }

      return "a:" + jsonstr.length + ":{" + map.join("") + "}"

    } else {
      var class_name = get_class(jsonstr);

      if (class_name == undefined) {
        return false;
      }

      var props = new Array();
      for (var prop in jsonstr) {
        var ser = serialize(jsonstr[prop]);

        if (ser) {
          props.push(serialize(prop) + ser);
        }
      }
      return "O:" + class_name.length + ":\"" + class_name + "\":" + props.length + ":{" + props.join("") + "}";
    }
  case "undefined":
    return "N;";
  }

  return false;
} //end serialize

function get_class(obj) { // Returns the name of the class of an object
  //
  // +   original by: Ates Goral (http://magnetiq.com)
  // +   improved by: David James

  if (obj instanceof Object && !(obj instanceof Array) && !(obj instanceof Function) && obj.constructor) {
    var arr = obj.constructor.toString().match(/function\s*(\w+)/);

    if (arr && arr.length == 2) {
      if (arr[1] == 'Object') return 'stdClass';
      else return arr[1];
    }
  }

  return false;
}

function mb_length(str) {
  if (/[абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ]/.test(str)) {
    var counter = 0;
    for (i = 0; str.length > i; i++) {
      if (/[абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ]/.test(str[i])) counter += 2;
      else counter++;
    }
    return counter;
  } else {
    return str.length;
  }
}

return serialize( eval(json) );
$$;


ALTER FUNCTION fn.json2php(json text) OWNER TO wordpress;

--
-- Name: php2json(text); Type: FUNCTION; Schema: fn; Owner: wordpress
--

CREATE FUNCTION php2json(php text) RETURNS text
    LANGUAGE plv8 IMMUTABLE STRICT
    AS $$
/*!
 * php-unserialize-js JavaScript Library
 * https://github.com/bd808/php-unserialize-js
 *
 * Copyright 2013 Bryan Davis and contributors
 * Released under the MIT license
 * http://www.opensource.org/licenses/MIT
 */

/**
 * Parse php serialized data into js objects.
 *
 * @param {String} phpstr Php serialized string to parse
 * @return {mixed} Parsed result
 */
function phpUnserialize (phpstr) {
  var idx = 0
    , rstack = []
    , ridx = 0

    , readLength = function () {
        var del = phpstr.indexOf(':', idx)
          , val = phpstr.substring(idx, del);
        idx = del + 2;
        return parseInt(val);
      } //end readLength

    , parseAsInt = function () {
        var del = phpstr.indexOf(';', idx)
          , val = phpstr.substring(idx, del);
        idx = del + 1;
        return parseInt(val);
      } //end parseAsInt

    , parseAsFloat = function () {
        var del = phpstr.indexOf(';', idx)
          , val = phpstr.substring(idx, del);
        idx = del + 1;
        return parseFloat(val);
      } //end parseAsFloat

    , parseAsBoolean = function () {
        var del = phpstr.indexOf(';', idx)
          , val = phpstr.substring(idx, del);
        idx = del + 1;
        return ("1" === val)? true: false;
      } //end parseAsBoolean

    , parseAsString = function () {
        var len = readLength()
          , utfLen = 0
          , bytes = 0
          , ch
          , val;
        while (bytes < len) {
          ch = phpstr.charCodeAt(idx + utfLen++);
          if (ch <= 0x007F) {
            bytes++;
          } else if (ch > 0x07FF) {
            bytes += 3;
          } else {
            bytes += 2;
          }
        }
        val = phpstr.substring(idx, idx + utfLen);
        idx += utfLen + 2;
        return val;
      } //end parseAsString

    , parseAsArray = function () {
        var len = readLength()
          , resultArray = []
          , resultHash = {}
          , keep = resultArray
          , lref = ridx++
          , key
          , val;

        rstack[lref] = keep;
        for (var i = 0; i < len; i++) {
          key = parseNext();
          val = parseNext();
          if (keep === resultArray && parseInt(key) == i) {
            // store in array version
            resultArray.push(val);
          } else {
            if (keep !== resultHash) {
              // found first non-sequential numeric key
              // convert existing data to hash
              for (var j = 0, alen = resultArray.length; j < alen; j++) {
                resultHash[j] = resultArray[j];
              }
              keep = resultHash;
              rstack[lref] = keep;
            }
            resultHash[key] = val;
          } //end if
        } //end for

        idx++;
        return keep;
      } //end parseAsArray

    , parseAsObject = function () {
        var len = readLength()
          , obj = {}
          , lref = ridx++
          , clazzname = phpstr.substring(idx, idx + len)
          , re_strip = new RegExp("^\u0000(\\*|" + clazzname + ")\u0000")
          , key
          , val;

        rstack[lref] = obj;
        idx += len + 2;
        len = readLength();
        for (var i = 0; i < len; i++) {
          key = parseNext();
          // private members start with "\u0000CLASSNAME\u0000"
          // protected members start with "\u0000*\u0000"
          // we will strip these prefixes
          key = key.replace(re_strip, '');
          val = parseNext();
          obj[key] = val;
        }
        idx++;
        return obj;
      } //end parseAsObject

    , parseAsRef = function () {
        var ref = parseAsInt();
        // php's ref counter is 1-based; our stack is 0-based.
        return rstack[ref - 1];
      } //end parseAsRef

    , readType = function () {
        var type = phpstr.charAt(idx);
        idx += 2;
        return type;
      } //end readType

    , parseNext = function () {
        var type = readType();
        switch (type) {
          case 'i': return parseAsInt();
          case 'd': return parseAsFloat();
          case 'b': return parseAsBoolean();
          case 's': return parseAsString();
          case 'a': return parseAsArray();
          case 'O': return parseAsObject();
          case 'r': return parseAsRef();
          case 'R': return parseAsRef();
          case 'N': return null;
          default:
            throw {
              name: "Parse Error",
              message: "Unknown type '" + type + "' at postion " + (idx - 2)
            }
        } //end switch
    }; //end parseNext

    return parseNext();
} //end phpUnserialize

return JSON.stringify( phpUnserialize(php) );$$;


ALTER FUNCTION fn.php2json(php text) OWNER TO wordpress;

--
-- Name: post_tsv(); Type: FUNCTION; Schema: fn; Owner: wordpress
--

CREATE FUNCTION post_tsv() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
  NEW.tsv = setweight(to_tsvector('russian', coalesce(NEW.post_title,'')), 'A') || setweight(to_tsvector('russian', coalesce(NEW.post_content,'')), 'B');
  RETURN NEW;
END;$$;


ALTER FUNCTION fn.post_tsv() OWNER TO wordpress;

--
-- Name: transient_wc_attribute_taxonomies(); Type: FUNCTION; Schema: fn; Owner: wordpress
--

CREATE FUNCTION transient_wc_attribute_taxonomies() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN

  INSERT INTO wp_options (option_name, option_value )
  SELECT '_transient_wc_attribute_taxonomies'::text, fn.json2php( fn._transient_wc_attribute_taxonomies() )::text
  WHERE NOT EXISTS (SELECT 1 FROM wp_options WHERE option_name = '_transient_wc_attribute_taxonomies');

  UPDATE wp_options SET option_value = fn.json2php( fn._transient_wc_attribute_taxonomies())
  WHERE option_name = '_transient_wc_attribute_taxonomies';

  RETURN NEW;
END;$$;


ALTER FUNCTION fn.transient_wc_attribute_taxonomies() OWNER TO wordpress;

--
-- Name: wp_plainto_tsquery(regconfig, text); Type: FUNCTION; Schema: fn; Owner: wordpress
--

CREATE FUNCTION wp_plainto_tsquery(regconfig, text) RETURNS tsquery
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
SELECT plainto_tsquery($1,$2);
$_$;


ALTER FUNCTION fn.wp_plainto_tsquery(regconfig, text) OWNER TO wordpress;

--
-- PostgreSQL database dump complete
--

