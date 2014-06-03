<?php

/* -----------------------------------------------------------------
 * 	$Id: validations.php 420 2013-06-19 18:04:39Z akausch $
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

function xtc_validate_email($email) {
    $valid_address = true;

    $mail_pat = '^(.+)@(.+)$';
    $valid_chars = "[^] \(\)<>@,;:\.\\\"\[]";
    $atom = "$valid_chars+";
    $quoted_user = '(\"[^\"]*\")';
    $word = "($atom|$quoted_user)";
    $user_pat = "^$word(\.$word)*$";
    $ip_domain_pat = '^\[([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\]$';
    $domain_pat = "^$atom(\.$atom)*$";

    if (preg_match('/' . $mail_pat . '/i', $email, $components)) {
        $user = $components[1];
        $domain = $components[2];
        // validate user
        if (preg_match('/' . $user_pat . '/i', $user)) {
            // validate domain
            if (preg_match('/' . $ip_domain_pat . '/i', $domain, $ip_components)) {
                // this is an IP address
                for ($i = 1; $i <= 4; $i++) {
                    if ($ip_components[$i] > 255) {
                        $valid_address = false;
                        break;
                    }
                }
            } else {
                // Domain is a name, not an IP
                if (preg_match('/' . $domain_pat . '/i', $domain)) {
                    /* domain name seems valid, but now make sure that it ends in a valid TLD or ccTLD
                      and that there's a hostname preceding the domain or country. */
                    $domain_components = explode(".", $domain);
                    // Make sure there's a host name preceding the domain.
                    if (sizeof($domain_components) < 2) {
                        $valid_address = false;
                    } else {
                        $top_level_domain = strtolower($domain_components[sizeof($domain_components) - 1]);
                        // Allow all 2-letter TLDs (ccTLDs)
                        if (preg_match('/^[a-z][a-z]$/i', $top_level_domain) != 1) {
                            $tld_pattern = '';
                            // Get authorized TLDs from text file
                            $tlds = file(DIR_WS_INCLUDES . 'tld.txt');
                            while (list(, $line) = each($tlds)) {
                                // Get rid of comments
                                $words = explode('#', $line);
                                $tld = trim($words[0]);
                                // TLDs should be 3 letters or more
                                if (preg_match('/^[a-z]{3,}$/i', $tld) == 1) {
                                    $tld_pattern .= '^' . $tld . '$|';
                                }
                            }
                            // Remove last '|'
                            $tld_pattern = substr($tld_pattern, 0, -1);
                            if (preg_match("/$tld_pattern/i", $top_level_domain) == 0) {
                                $valid_address = false;
                            }
                        }
                    }
                } else {
                    $valid_address = false;
                }
            }
        } else {
            $valid_address = false;
        }
    } else {
        $valid_address = false;
    }
    if ($valid_address && ENTRY_EMAIL_ADDRESS_CHECK == 'true') {
        if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
            $valid_address = false;
        }
    }
    return $valid_address;
}

