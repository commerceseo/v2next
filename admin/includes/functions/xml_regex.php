<?php

/* -----------------------------------------------------------------
 * 	$Id: xml_regex.php 420 2013-06-19 18:04:39Z akausch $
 * 	Copyright (c) 2011-2021 commerce:SEO by Webdesign Erfurt
 * 	http://www.commerce-seo.de
 * ------------------------------------------------------------------
 * 	based on:
 * 	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 * 	(c) 2002-2003 osCommerce - www.oscommerce.com
 * 	(c) 2003     nextcommerce - www.nextcommerce.org
 * 	(c) 2005     xt:Commerce - www.xt-commerce.com
 * 	Released under the GNU General Public License
 * --------------------------------------------------------------- */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

/*
 * * Useful constants for use with the third parameter of these
 * * functions (to discard or preserve the enclosing tags of the
 * * named element).
 */
define('ELEMENT_CONTENT_ONLY', true);
define('ELEMENT_PRESERVE_TAGS', false);

/*
 * * This function returns the content of the first element that
 * * exactly matches the provided name, found in the provided XML
 * * data. If this can't be found, then false is returned.
 * * DO NOT USE THIS FUNCTION ON AN ELEMENT THAT CONTAINS ANOTHER
 * * ELEMENT OF THE SAME NAME (SUCH AS A div WITHIN A div ELEMENT).
 * *
 * * $element_name - the name of the element whose content you desire;
 * * $xml - the XML data to search in;
 * * $content_only - if true, the tags enclosing the named element are
 * *     discarded. If false, the whole pattern match is returned, and
 * *     the enclosing tags are preserved. Defaults to true.
 */

function value_in($element_name, $xml, $content_only = true) {
    if ($xml == false) {
        return false;
    }
    $found = preg_match('#<' . $element_name . '(?:\s+[^>]+)?>(.*?)' .
            '</' . $element_name . '>#s', $xml, $matches);
    if ($found != false) {
        if ($content_only) {
            return $matches[1];  //ignore the enclosing tags
        } else {
            return $matches[0];  //return the full pattern match
        }
    }
    // No match found: return false.
    return false;
}

/*
 * * This function returns an array of elements whose name matches the
 * * provided string, found in the provided XML.
 * * If no match is found, this function returns false.
 * * DO NOT USE THIS FUNCTION ON AN ELEMENT THAT CONTAINS ANOTHER
 * * ELEMENT OF THE SAME NAME (SUCH AS A div WITHIN A div ELEMENT).
 * * 
 * * $element_name - the name of the elements to search for;
 * * $xml - the XML document to search through;
 * * $content_only - if true, the tags enclosing the named element are
 * *     discarded. If false, the whole pattern match is returned, and
 * *     the enclosing tags are preserved. Defaults to false.
 */

function element_set($element_name, $xml, $content_only = false) {
    if ($xml == false) {
        return false;
    }
    $found = preg_match_all('#<' . $element_name . '(?:\s+[^>]+)?>' .
            '(.*?)</' . $element_name . '>#s', $xml, $matches, PREG_PATTERN_ORDER);
    if ($found != false) {
        if ($content_only) {
            return $matches[1];  //ignore the enlosing tags
        } else {
            return $matches[0];  //return the full pattern match
        }
    }
    // No match found: return false.
    return false;
}

/*
 * * This function extracts the attributes from the first element that
 * * matches the provided name in the provided XML sample.
 * * The function returns an associative array, where the key is the
 * * attribute name, and the value is the attribute value.
 * * If the regular expression cannot find or extract attributes
 * * this function will return false.
 * * THIS FUNCTION CAN ONLY MATCH THE FIRST ELEMENT OF THE PROVIDED
 * * NAME THAT HAS ATTRIBUTES.
 * *
 * * $element_name - the name of the element to extract the attributes
 * *     from;
 * * $xml - the XML sample to search for the named element. 
 */

function element_attributes($element_name, $xml) {
    if ($xml == false) {
        return false;
    }
    // Grab the string of attributes inside an element tag.
    $found = preg_match('#<' . $element_name .
            '\s+([^>]+(?:"|\'))\s?/?>#', $xml, $matches);
    if ($found == 1) {
        $attribute_array = array();
        $attribute_string = $matches[1];
        // Match attribute-name attribute-value pairs.
        $found = preg_match_all(
                '#([^\s=]+)\s*=\s*(\'[^<\']*\'|"[^<"]*")#', $attribute_string, $matches, PREG_SET_ORDER);
        if ($found != 0) {
            // Create an associative array that matches attribute
            // names to attribute values.
            foreach ($matches as $attribute) {
                $attribute_array[$attribute[1]] =
                        substr($attribute[2], 1, -1);
            }
            return $attribute_array;
        }
    }
    // Attributes either weren't found, or couldn't be extracted
    // by the regular expression.
    return false;
}

