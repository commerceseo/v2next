<?php
/*
  $Id: recover_cart_sales.php 420 2013-06-19 18:04:39Z akausch $
  Recover Cart Sales v2.12 for xt:Commerce GERMAN Language File

  Copyright (c) 2006 Andre Estel www.estelco.de

  Recover Cart Sales contribution: JM Ivler (c)
  Copyright (c) 2003-2005 JM Ivler / Ideas From the Deep / OSCommerce
  http://www.oscommerce.com

  Released under the GNU General Public License

  Modifed by Aalst (recover_cart_sales.php,v 1.2 .. 1.36)
  aalst@aalst.com

  Modifed by willross (recover_cart_sales.php,v 1.4)
  reply@qwest.net
  - don't forget to flush the 'scart' db table every so often

  Modifed by Lane (stats_recover_cart_sales.php,v 1.4d .. 2.11)
  lane@ifd.com www.osc-modsquad.com / www.ifd.com
*/

define('MESSAGE_STACK_CUSTOMER_ID', 'Warenkorb f&uuml;r Kundennummer ');
define('MESSAGE_STACK_DELETE_SUCCESS', ' erfolgreich gel&ouml;scht');
define('HEADING_TITLE', 'Offene Warenk&ouml;rbe PLUS');
define('HEADING_EMAIL_SENT', 'E-mail Sende-Report');
define('EMAIL_TEXT_SUBJECT', 'Anfrage von '.  STORE_NAME );
define('DAYS_FIELD_PREFIX', 'Zeige die letzten ');
define('DAYS_FIELD_POSTFIX', ' Tage ');
define('DAYS_FIELD_BUTTON', 'Anzeigen');
define('TABLE_HEADING_DATE', 'Datum');
define('TABLE_HEADING_CONTACT', 'kontaktieren?');
define('TABLE_HEADING_CUSTOMER', 'Kunden Name');
define('TABLE_HEADING_EMAIL', 'E-Mail');
define('TABLE_HEADING_STOPPED', 'Wo beendet');
define('TABLE_HEADING_PHONE', 'Telefon');
define('TABLE_HEADING_MODEL', 'Artikel');
define('TABLE_HEADING_DESCRIPTION', 'Beschreibung');
define('TABLE_HEADING_QUANTY', 'Menge');
define('TABLE_HEADING_PRICE', 'Preis');
define('TABLE_HEADING_TOTAL', 'Summe');
define('TABLE_GRAND_TOTAL', 'Summe netto Gesamt: ');
define('TABLE_CART_TOTAL', 'Summe netto: ');
define('TABLE_GRAND_TOTAL_BRUTTO', 'Summe brutto Gesamt: ');
define('TABLE_CART_TOTAL_BRUTTO', 'Summe brutto: ');
define('TEXT_CURRENT_CUSTOMER', 'Kunde');
define('TEXT_SEND_EMAIL', 'Sende E-mail');
define('TEXT_RETURN', '[Klick hier um zur&uuml;ckzugehen]');
define('TEXT_NOT_CONTACTED', 'Nicht kontaktiert');
define('PSMSG', 'Zus&auml;tzliche Nachricht (PS) am Ende der Mail: ');

define('TEXT_CART', 'Warenkorb');
define('TEXT_SHIPPING', 'Versand');
define('TEXT_PAYMENT', 'Zahlung');
define('TEXT_CONFIRM', 'Best&auml;tigung');
define('TABLE_HEADING_OUT_DATE', 'Nicht im Zeitraum');
?>
