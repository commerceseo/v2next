<?php

/* -----------------------------------------------------------------
 * 	$Id: xtc_validate_email.inc.php 1247 2014-10-22 19:19:03Z akausch $
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
function xtc_validate_email($email) {
    $valid_address = true;

    // sql injection fix 16.02.2011
    if (strpos($email,"\0")!==false) {return false;}
    if (strpos($email,"\x00")!==false) {return false;}
    if (strpos($email,"\u0000")!==false) {return false;}
    if (strpos($email,"\000")!==false) {return false;}
	
    $mail_pat = '/^(.+)@(.+)$/i';
    $valid_chars = "[^] \(\)<>@,;:\.\\\"\[]";
    $atom = "$valid_chars+";
    $quoted_user='(\"[^\"]*\")';
    $word = "($atom|$quoted_user)";
    $user_pat = "/^$word(\.$word)*$/i";
    $ip_domain_pat='/^\[([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\]$/i';
    $domain_pat = "/^$atom(\.$atom)*$/i";

    if (preg_match($mail_pat, $email, $components)) {
      $user = $components[1];
      $domain = $components[2];
      // validate user
      if (preg_match($user_pat, $user)) {
        // validate domain
        if (preg_match($ip_domain_pat, $domain, $ip_components)) {
          // this is an IP address
      	  for ($i=1;$i<=4;$i++) {
      	    if ($ip_components[$i] > 255) {
      	      $valid_address = false;
      	      break;
      	    }
          }
        } else {
          // Domain is a name, not an IP
          if (preg_match($domain_pat, $domain)) {
            /* domain name seems valid, but now make sure that it ends in a valid TLD or ccTLD
               and that there's a hostname preceding the domain or country. */
            $domain_components = explode(".", $domain);
            // Make sure there's a host name preceding the domain.
            if (sizeof($domain_components) < 2) {
              $valid_address = false;
            } else {
				$top_level_domain = strtolower($domain_components[sizeof($domain_components)-1]);
				if(strlen_wrapper($top_level_domain) < 2)
				{
					$valid_address = false;
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