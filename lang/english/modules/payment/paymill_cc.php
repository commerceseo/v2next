<?php
define("MODULE_PAYMENT_PAYMILL_CC_STATUS_TITLE", "Activate");
define("MODULE_PAYMENT_PAYMILL_CC_DESCRIPTION", "PAYMILL log");
define("MODULE_PAYMENT_PAYMILL_CC_FASTCHECKOUT_TITLE", "Enable fast checkout");
define("MODULE_PAYMENT_PAYMILL_CC_FASTCHECKOUT_DESC", "If enabled, your customers' data will be stored by PAYMILL and made available again for future purchases . The customer will only have to enter his data once. This solution is PCI compliant.");
define("MODULE_PAYMENT_PAYMILL_CC_SORT_ORDER_TITLE", "Sequence");
define("MODULE_PAYMENT_PAYMILL_CC_SORT_ORDER_DESC", "Position of display during checkout.");
define("MODULE_PAYMENT_PAYMILL_CC_PRIVATEKEY_TITLE", "Private key");
define("MODULE_PAYMENT_PAYMILL_CC_PRIVATEKEY_DESC", "You can find your private key in the PAYMILL cockpit.");
define("MODULE_PAYMENT_PAYMILL_CC_PUBLICKEY_TITLE", "Public key");
define("MODULE_PAYMENT_PAYMILL_CC_PUBLICKEY_DESC", "You can find your public key in the PAYMILL cockpit.");
define("MODULE_PAYMENT_PAYMILL_CC_TRANSACTION_ORDER_STATUS_ID_TITLE", "Transaction Order Status");
define("MODULE_PAYMENT_PAYMILL_CC_TRANSACTION_ORDER_STATUS_ID_DESC", "Include transaction information in this order status level.");
define("MODULE_PAYMENT_PAYMILL_CC_LOGGING_TITLE", "Activate logging.");
define("MODULE_PAYMENT_PAYMILL_CC_LOGGING_DESC", "If enabled, information regarding the progress of order processing will be written to the log. ");
define("MODULE_PAYMENT_PAYMILL_CC_ORDER_STATUS_ID_TITLE", "Orderstate");
define("MODULE_PAYMENT_PAYMILL_CC_TRANS_ORDER_STATUS_ID_TITLE", "Transaction Order Status");
define("MODULE_PAYMENT_PAYMILL_CC_TRANS_ORDER_STATUS_ID_DESC", "Include transaction information in this order status level.");
define("MODULE_PAYMENT_PAYMILL_CC_ALLOWED_TITLE", "Accepted countries");
define("MODULE_PAYMENT_PAYMILL_CC_ALLOWED_DESC", "If nothing has been selected, all countries will be accepted");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_PUBLIC_TITLE", "Credit card");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_OWNER", "Card holder");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_NUMBER", "Card number");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_EXPIRY", "Valid until");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_CVC", "CVC");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_CVC_TOOLTIP", "The CVV code or CVC is a security feature of credit cards. It usually is a  three to four digit long number. On VISA credit cards, it is called CVV code. The same code can be found on MasterCard credit cards - where however it is called CVC. CVC is an abbreviation for &quot;Card Validation Code&quot;. CVV code on the other hand is an abbreviation for &quot;Card Validation Value code&quot;. Similar to MasterCard and Visa, other brands such as Diners Club, Discover and JCB contain a three digit number that can usually be found on the back of the credit card. MAESTRO cards exist with and without a three digit CVV. In case a MAESTRO card without a CVV will be used, it is possible to enter 000 to the form instead. American Express uses the CID (card identification number). The CID is a four digit number that can usually be found on the front of the card, top right from the credit card number. ");
define("PAYMILL_10001", "General undefined response.");
define("PAYMILL_10002", "Still waiting on something.");
define("PAYMILL_20000", "General success response.");
define("PAYMILL_40000", "General problem with data.");
define("PAYMILL_40001", "General problem with payment data.");
define("PAYMILL_40100", "Problem with credit card data.");
define("PAYMILL_40101", "Problem with cvv.");
define("PAYMILL_40102", "Card expired or not yet valid.");
define("PAYMILL_40103", "Limit exceeded.");
define("PAYMILL_40104", "Card invalid.");
define("PAYMILL_40105", "Expiry date not valid.");
define("PAYMILL_40106", "Credit card brand required.");
define("PAYMILL_40200", "Problem with bank account data.");
define("PAYMILL_40201", "Bank account data combination mismatch.");
define("PAYMILL_40202", "User authentication failed.");
define("PAYMILL_40300", "Problem with 3d secure data.");
define("PAYMILL_40301", "Currency / amount mismatch");
define("PAYMILL_40400", "Problem with input data.");
define("PAYMILL_40401", "Amount too low or zero.");
define("PAYMILL_40402", "Usage field too long.");
define("PAYMILL_40403", "Currency not allowed.");
define("PAYMILL_50000", "General problem with backend.");
define("PAYMILL_50001", "Country blacklisted.");
define("PAYMILL_50100", "Technical error with credit card.");
define("PAYMILL_50101", "Error limit exceeded.");
define("PAYMILL_50102", "Card declined by authorization system.");
define("PAYMILL_50103", "Manipulation or stolen card.");
define("PAYMILL_50104", "Card restricted");
define("PAYMILL_50105", "Invalid card configuration data.");
define("PAYMILL_50200", "Technical error with bank account.");
define("PAYMILL_50201", "Card blacklisted.");
define("PAYMILL_50300", "Technical error with 3D secure.");
define("PAYMILL_50400", "Decline because of risk issues.");
define("PAYMILL_50500", "General timeout.");
define("PAYMILL_50501", "Timeout on side of the acquirer.");
define("PAYMILL_50502", "Risk management transaction timeout");
define("PAYMILL_50600", "Duplicate transaction.");
define("PAYMILL_FIELD_INVALID_CARD_NUMBER", "Please enter a valid credit card number.");
define("PAYMILL_FIELD_INVALID_CARD_EXP", "Invalid expiration date");
define("PAYMILL_FIELD_INVALID_CARD_CVC", "Invalid CVC");
define("PAYMILL_FIELD_INVALID_CARD_HOLDER", "Please enter the card holder's name.");
define("PAYMILL_INTERNAL_SERVER_ERROR", "The communication with the psp failed.");
define("PAYMILL_INVALID_PUBLIC_KEY", "The public key is invalid.");
define("PAYMILL_INVALID_PAYMENT_DATA", "Paymentmethod, card type currency or country not authorized");
define("PAYMILL_UNKNOWN_ERROR", "Unknown Error");
define("PAYMILL_3DS_CANCELLED", "3-D Secure process has been canceled by the user");
define("PAYMILL_FIELD_INVALID_CARD_EXP_YEAR", "Invalid Expiry Year");
define("PAYMILL_FIELD_INVALID_CARD_EXP_MONTH", "Invalid Expiry Month");
define("PAYMILL_FIELD_INVALID_AMOUNT_INT", "Missing amount for 3-D Secure");
define("PAYMILL_FIELD_INVALID_AMOUNT", "Missing amount for 3-D Secure");
define("PAYMILL_FIELD_INVALID_CURRENCY", "Invalid currency for 3-D Secure");
define("MODULE_PAYMENT_PAYMILL_CC_WEBHOOKS_TITLE", "Enable Webhooks");
define("MODULE_PAYMENT_PAYMILL_CC_WEBHOOKS_DESC", "Authomatically synchronize my Refunds from the PAYMILL Cockpit with my store");
define("MODULE_PAYMENT_PAYMILL_CC_WEBHOOKS_LINK_CREATE", "Create Webhooks");
define("MODULE_PAYMENT_PAYMILL_CC_WEBHOOKS_LINK_REMOVE", "Remove Webhooks");
define("MODULE_PAYMENT_PAYMILL_CC_WEBHOOKS_LINK", "Create Webhooks");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_JANUARY", "January");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_FEBRUARY", "February");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_MARCH", "March");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_APRIL", "April");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_MAY", "May");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_JUNE", "June");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_JULY", "July");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_AUGUST", "August");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_SEPTEMBER", "September");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_OCTOBER", "October");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_NOVEMBER", "November");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_MONTH_DECEMBER", "December");
define("MODULE_PAYMENT_PAYMILL_CC_ZONE_TITLE", "Allowed Zones");
define("MODULE_PAYMENT_PAYMILL_CC_ZONE_DESC", "Please enter the zones individually that should be allowed to use this module (e.g. US, UK (leave blank to allow all zones))");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_EXPIRY_INVALID", "Invalid expiration date");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_CARDNUMBER_INVALID", "Please enter a valid credit card number.");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_CVC_INVALID", "Invalid CVC");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_CREDITCARD_OWNER_INVALID", "Please enter the card holder's name.");
define("PAYMILL_0", "An error has occurred while processing your payment.");
define("MODULE_PAYMENT_PAYMILL_CC_TEXT_TITLE", "PAYMILL Credit card");
define("TEXT_INFO_API_VERSION", "API Version");
define("MODULE_PAYMENT_PAYMILL_CC_STATUS_DESC", "");
define("MODULE_PAYMENT_PAYMILL_CC_ORDER_STATUS_ID_DESC", "");
define("MODULE_PAYMENT_PAYMILL_CC_ACCEPTED_CARDS", "Accepted Credit Cards");
define('PAYMILL_REFUND_BUTTON_TEXT', 'refund order');
define('PAYMILL_REFUND_SUCCESS', 'Order successful refunded.');
define('PAYMILL_REFUND_ERROR', 'Order not successful refunded.');
?>