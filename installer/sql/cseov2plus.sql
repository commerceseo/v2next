DROP TABLE IF EXISTS address_book;
CREATE TABLE address_book (
address_book_id INT NOT NULL auto_increment,
customers_id INT NOT NULL,
entry_gender char(1) NOT NULL,
entry_company VARCHAR(32),
entry_firstname VARCHAR(32) NOT NULL,
entry_lastname VARCHAR(32) NOT NULL,
entry_street_address VARCHAR(64) NOT NULL,
entry_suburb VARCHAR(32),
entry_postcode VARCHAR(10) NOT NULL,
entry_city VARCHAR(32) NOT NULL,
entry_state VARCHAR(32),
entry_country_id INT DEFAULT '0' NOT NULL,
entry_zone_id INT DEFAULT '0' NOT NULL,
address_date_added datetime DEFAULT '0000-00-00 00:00:00',
address_last_modified datetime DEFAULT '0000-00-00 00:00:00',
address_class VARCHAR( 32 ) NOT NULL,
PRIMARY KEY (address_book_id),
KEY idx_address_book_customers_id (customers_id),
KEY entry_country_id (entry_country_id),
KEY entry_zone_id (entry_zone_id)
);

DROP TABLE IF EXISTS boxes;
CREATE TABLE boxes (
id INT(11) NOT NULL auto_increment,
box_name VARCHAR(32) NOT NULL,
position VARCHAR(16) NOT NULL,
sort_id INT(4) NOT NULL,
status INT(1) NOT NULL DEFAULT '1',
box_type VARCHAR(16) NOT NULL,
file_flag INT(2) NOT NULL,
mobile INT(1) NOT NULL DEFAULT '1',
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS boxes_names;
CREATE TABLE boxes_names (
id INT(11) NOT NULL auto_increment,
box_name VARCHAR(32) NOT NULL,
box_title VARCHAR(128) NOT NULL,
box_desc text NOT NULL,
language_id INT(2) NOT NULL,
status INT(1) NOT NULL DEFAULT '1',
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS boxes_positions;
CREATE TABLE boxes_positions (
id INT(11) NOT NULL auto_increment,
position_name VARCHAR(16) NOT NULL,
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS boxes_styles;
CREATE TABLE boxes_styles (
id INT(11) NOT NULL auto_increment,
box_name VARCHAR(32) NOT NULL,
border_color VARCHAR(6) NOT NULL,
background_content VARCHAR(32) NOT NULL,
color_content VARCHAR(6) NOT NULL,
background_head VARCHAR(32) NOT NULL,
color_head VARCHAR(6) NOT NULL,
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS customers_memo;
CREATE TABLE customers_memo (
memo_id INT(11) NOT NULL auto_increment,
customers_id INT(11) NOT NULL DEFAULT '0',
memo_date date NOT NULL DEFAULT '0000-00-00',
memo_title text NOT NULL,
memo_text text NOT NULL,
poster_id INT(11) NOT NULL DEFAULT '0',
PRIMARY KEY (memo_id)
);

DROP TABLE IF EXISTS products_xsell;
CREATE TABLE products_xsell (
ID INT(10) NOT NULL auto_increment,
products_id INT(10) NOT NULL DEFAULT '1',
products_xsell_grp_name_id INT(10) NOT NULL DEFAULT '1',
xsell_id INT(10) NOT NULL DEFAULT '1',
sort_order INT(10) NOT NULL DEFAULT '1',
PRIMARY KEY (ID),
KEY products_id (products_id),
KEY products_id_2 (products_xsell_grp_name_id,products_id)
);

DROP TABLE IF EXISTS products_xsell_grp_name;
CREATE TABLE products_xsell_grp_name (
products_xsell_grp_name_id INT(10) NOT NULL,
xsell_sort_order INT(10) NOT NULL DEFAULT '0',
language_id smallint(6) NOT NULL DEFAULT '0',
groupname VARCHAR(255) NOT NULL DEFAULT ''
);

DROP TABLE IF EXISTS campaigns;
CREATE TABLE campaigns (
campaigns_id INT(11) NOT NULL auto_increment,
campaigns_name VARCHAR(32) NOT NULL DEFAULT '',
campaigns_refID VARCHAR(64) DEFAULT NULL,
campaigns_leads INT(11) NOT NULL DEFAULT '0',
date_added datetime DEFAULT NULL,
last_modified datetime DEFAULT NULL,
PRIMARY KEY (campaigns_id),
KEY IDX_CAMPAIGNS_NAME (campaigns_name)
);

DROP TABLE IF EXISTS campaigns_ip;
CREATE TABLE campaigns_ip (
user_ip VARCHAR( 15 ) NOT NULL ,
time DATETIME NOT NULL ,
campaign VARCHAR( 32 ) NOT NULL
);

DROP TABLE IF EXISTS address_format;
CREATE TABLE address_format (
address_format_id INT NOT NULL auto_increment,
address_format VARCHAR(128) NOT NULL,
address_summary VARCHAR(48) NOT NULL,
PRIMARY KEY (address_format_id)
);

DROP TABLE IF EXISTS database_version;
CREATE TABLE database_version (
version VARCHAR(32) NOT NULL
);

DROP TABLE IF EXISTS admin_access;
CREATE TABLE admin_access (
customers_id VARCHAR(32) NOT NULL DEFAULT '0',
configuration INT(1) NOT NULL DEFAULT '0',
modules INT(1) NOT NULL DEFAULT '0',
countries INT(1) NOT NULL DEFAULT '0',
currencies INT(1) NOT NULL DEFAULT '0',
zones INT(1) NOT NULL DEFAULT '0',
geo_zones INT(1) NOT NULL DEFAULT '0',
tax_classes INT(1) NOT NULL DEFAULT '0',
tax_rates INT(1) NOT NULL DEFAULT '0',
accounting INT(1) NOT NULL DEFAULT '0',
backup INT(1) NOT NULL DEFAULT '0',
cache INT(1) NOT NULL DEFAULT '0',
server_info INT(1) NOT NULL DEFAULT '0',
whos_online INT(1) NOT NULL DEFAULT '0',
languages INT(1) NOT NULL DEFAULT '0',
define_language INT(1) NOT NULL DEFAULT '0',
orders_status INT(1) NOT NULL DEFAULT '0',
shipping_status INT(1) NOT NULL DEFAULT '0',
module_export INT(1) NOT NULL DEFAULT '0',
filemanager INT(1) NOT NULL DEFAULT '0',
database_manager INT(1) NOT NULL DEFAULT '0',
customers INT(1) NOT NULL DEFAULT '0',
create_account INT(1) NOT NULL DEFAULT '0',
customers_status INT(1) NOT NULL DEFAULT '0',
orders INT(1) NOT NULL DEFAULT '0',
campaigns INT(1) NOT NULL DEFAULT '0',
print_packingslip INT(1) NOT NULL DEFAULT '0',
print_order INT(1) NOT NULL DEFAULT '0',
popup_memo INT(1) NOT NULL DEFAULT '0',
coupon_admin INT(1) NOT NULL DEFAULT '0',
listcategories INT(1) NOT NULL DEFAULT '0',
listproducts INT(1) NOT NULL DEFAULT '0',
gv_queue INT(1) NOT NULL DEFAULT '0',
gv_mail INT(1) NOT NULL DEFAULT '0',
gv_sent INT(1) NOT NULL DEFAULT '0',
validproducts INT(1) NOT NULL DEFAULT '0',
validcategories INT(1) NOT NULL DEFAULT '0',
mail INT(1) NOT NULL DEFAULT '0',
emails INT(1) NOT NULL DEFAULT '0',
categories INT(1) NOT NULL DEFAULT '0',
new_attributes INT(1) NOT NULL DEFAULT '0',
janolaw INT(1) NOT NULL DEFAULT '0',
delete_cache INT(1) NOT NULL DEFAULT '0',
products_attributes INT(1) NOT NULL DEFAULT '0',
price_change INT(1) NOT NULL DEFAULT '0',
manufacturers INT(1) NOT NULL DEFAULT '0',
reviews INT(1) NOT NULL DEFAULT '0',
specials INT(1) NOT NULL DEFAULT '0',
stats_products_expected INT(1) NOT NULL DEFAULT '0',
stats_products_viewed INT(1) NOT NULL DEFAULT '0',
stats_products_purchased INT(1) NOT NULL DEFAULT '0',
stats_customers INT(1) NOT NULL DEFAULT '0',
stats_sales_report INT(1) NOT NULL DEFAULT '0',
stats_stock_warning INT(1) NOT NULL DEFAULT '0',
stats_stock_warning_print INT(1) NOT NULL DEFAULT '0',
stats_campaigns INT(1) NOT NULL DEFAULT '0',
stats_keywords_all INT(1) NOT NULL DEFAULT '0',
stats_keywords_all_print INT(1) NOT NULL DEFAULT '0',
banner_manager INT(1) NOT NULL DEFAULT '0',
banner_statistics INT(1) NOT NULL DEFAULT '0',
module_newsletter INT(1) NOT NULL DEFAULT '0',
start INT(1) NOT NULL DEFAULT '0',
box_manager INT(1) NOT NULL DEFAULT '0',
content_manager INT(1) NOT NULL DEFAULT '0',
content_preview INT(1) NOT NULL DEFAULT '0',
credits INT(1) NOT NULL DEFAULT '0',
blacklist INT(1) NOT NULL DEFAULT '0',
news_ticker INT(1) NOT NULL DEFAULT '0',
orders_edit INT(1) NOT NULL DEFAULT '0',
popup_image INT(1) NOT NULL DEFAULT '0',
csv_backend INT(1) NOT NULL DEFAULT '0',
products_vpe INT(1) NOT NULL DEFAULT '0',
products_parameters INT(1) NOT NULL DEFAULT '0',
products_parameters_edit INT(1) NOT NULL DEFAULT '0',
cross_sell_groups INT(1) NOT NULL DEFAULT '0',
fck_wrapper INT(1) NOT NULL DEFAULT '0',
paypal INT( 1 ) NOT NULL DEFAULT '0',
customers_sik INT( 1 ) NOT NULL DEFAULT '0',
novalnet INT( 1 ) NOT NULL DEFAULT '0',
group_prices INT( 1 ) NOT NULL DEFAULT '0',
customers_aquise INT( 1 ) NOT NULL DEFAULT '0',
customers_aquise_request INT( 1 ) NOT NULL DEFAULT '0',
orders_overview INT( 1 ) NOT NULL DEFAULT '0',
orders_overview_print INT( 1 ) NOT NULL DEFAULT '0',
recover_cart_sales INT( 1 ) NOT NULL DEFAULT '0',
stats_recover_cart_sales INT( 1 ) NOT NULL DEFAULT '0',
module_newsletter_products INT( 1 ) NOT NULL DEFAULT '0',
close_cart_new_order INT( 1 ) NOT NULL DEFAULT '0',
product_listings INT( 1 ) NOT NULL DEFAULT '0',
css_styler INT( 1 ) NOT NULL DEFAULT '0',
slimstat INT( 1 ) NOT NULL DEFAULT '0',
orders_pdf_profiler INT(1) NOT NULL DEFAULT '0',
create_pdf INT(1) NOT NULL DEFAULT '0',
create_pdf_download INT(1) NOT NULL DEFAULT '0',
personal_links INT(1) NOT NULL DEFAULT '0',
blog INT(1) NOT NULL DEFAULT '0',
google_sitemap INT(1) NOT NULL DEFAULT '0',
product_filter INT(1) NOT NULL DEFAULT '0',
module_order_products INT(1) NOT NULL DEFAULT '0',
mobile INT(1) NOT NULL DEFAULT '0',
module_install INT(1) NOT NULL DEFAULT '0',
module_system INT(1) NOT NULL DEFAULT '0',
accessories INT(1) NOT NULL DEFAULT '0',
global_products_price INT(1) NOT NULL DEFAULT '0',
attribute_manager INT(1) NOT NULL DEFAULT '0',
products_expected INT(1) NOT NULL DEFAULT '0',
cseo_language_button INT(1) NOT NULL DEFAULT '0',
cseo_ids INT(1) NOT NULL DEFAULT '0',
cseo_antispam INT(1) NOT NULL DEFAULT '0',
recover_wish_list INT(1) NOT NULL DEFAULT '0',
removeoldpics INT(1) NOT NULL DEFAULT '0',
cseo_redirect INT(1) NOT NULL DEFAULT '0',
cseo_center_security INT(1) NOT NULL DEFAULT '0',
cseo_product_export INT(1) NOT NULL DEFAULT '0',
xajax_dispatcher INT(1) NOT NULL DEFAULT '0',
haendlerbund INT(1) NOT NULL DEFAULT '0',
cseo_imageprocessing INT(1) NOT NULL DEFAULT '0',
global_search INT(1) NOT NULL DEFAULT '0',
comments_orders INT(1) NOT NULL DEFAULT '0',
cseo_checkout_sort INT(1) NOT NULL DEFAULT '0',
cseo_main_sort INT(1) NOT NULL DEFAULT '0',
cseo_logo INT(1) NOT NULL DEFAULT '0',
csv_import_export INT(1) NOT NULL DEFAULT '0',
shipping_tracking INT(1) NOT NULL DEFAULT '0',
blog_edit_block INT(1) NOT NULL DEFAULT '0',
cseo_gallery_manager INT(1) NOT NULL DEFAULT '0',
cseo_rma INT(1) NOT NULL DEFAULT '0',
specials_gratis INT(1) NOT NULL DEFAULT '0',
it_recht_kanzlei INT(1) NOT NULL DEFAULT '0',
magnalister INT(1) NOT NULL DEFAULT '0',
PRIMARY KEY (customers_id)
);

DROP TABLE IF EXISTS customers_sik;
CREATE TABLE customers_sik (
customers_id INT(11) NOT NULL auto_increment,
customers_cid VARCHAR(32) DEFAULT NULL,
customers_vat_id VARCHAR(20) DEFAULT NULL,
customers_vat_id_status INT(2) NOT NULL DEFAULT '0',
customers_warning VARCHAR(32) DEFAULT NULL,
customers_status INT(5) NOT NULL DEFAULT '1',
customers_gender char(1) NOT NULL,
customers_firstname VARCHAR(32) NOT NULL,
customers_lastname VARCHAR(32) NOT NULL,
customers_dob datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
customers_email_address VARCHAR(96) NOT NULL,
customers_DEFAULT_address_id INT(11) NOT NULL,
customers_telephone VARCHAR(32) NOT NULL,
customers_fax VARCHAR(32) DEFAULT NULL,
customers_password VARCHAR(50) NOT NULL,
customers_newsletter char(1) DEFAULT NULL,
customers_newsletter_mode char(1) NOT NULL DEFAULT '0',
member_flag char(1) NOT NULL DEFAULT '0',
delete_user char(1) NOT NULL DEFAULT '1',
account_type INT(1) NOT NULL DEFAULT '0',
password_request_key VARCHAR(32) NOT NULL,
payment_unallowed VARCHAR(255) NOT NULL,
shipping_unallowed VARCHAR(255) NOT NULL,
refferers_id INT(5) NOT NULL DEFAULT '0',
customers_date_added datetime DEFAULT '0000-00-00 00:00:00',
customers_last_modified datetime DEFAULT '0000-00-00 00:00:00',
datensg datetime DEFAULT NULL,
login_tries VARCHAR( 2 ) NOT NULL DEFAULT '0',
login_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (customers_id)
);

DROP TABLE IF EXISTS customers_wishlist;
CREATE TABLE customers_wishlist (
customers_basket_id INT(11) NOT NULL auto_increment,
customers_id INT(11) NOT NULL DEFAULT '0',
products_id INT( 11 ) NOT NULL,
customers_basket_quantity INT(2) NOT NULL DEFAULT '0',
final_price decimal(15,4) NOT NULL DEFAULT '0.0000',
customers_basket_date_added VARCHAR(8) DEFAULT NULL,
PRIMARY KEY (customers_basket_id),
KEY products_id (products_id)
);

DROP TABLE IF EXISTS customers_wishlist_attributes;
CREATE TABLE customers_wishlist_attributes (
customers_basket_attributes_id INT(11) NOT NULL auto_increment,
customers_id INT(11) NOT NULL DEFAULT '0',
products_id INT(11) NOT NULL,
products_options_id INT(11) NOT NULL DEFAULT '0',
products_options_value_id INT(11) NOT NULL DEFAULT '0',
PRIMARY KEY (customers_basket_attributes_id),
KEY products_id (products_id)
);

DROP TABLE IF EXISTS banktransfer;
CREATE TABLE banktransfer (
orders_id INT(11) NOT NULL DEFAULT '0',
banktransfer_owner VARCHAR(64) DEFAULT NULL,
banktransfer_number VARCHAR(24) DEFAULT NULL,
banktransfer_bankname VARCHAR(255) DEFAULT NULL,
banktransfer_blz VARCHAR(8) DEFAULT NULL,
banktransfer_status INT(11) DEFAULT NULL,
banktransfer_prz char(2) DEFAULT NULL,
banktransfer_fax char(2) DEFAULT NULL,
KEY orders_id(orders_id)
);

DROP TABLE IF EXISTS news_ticker;
CREATE TABLE news_ticker (
id INT(11) NOT NULL auto_increment,
ticker_text text NOT NULL,
language_id INT(2) NOT NULL,
status INT(1) NOT NULL DEFAULT '0',
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS banners;
CREATE TABLE banners (
banners_id INT NOT NULL auto_increment,
banners_title VARCHAR(64) NOT NULL,
banners_url VARCHAR(255) NOT NULL,
banners_image VARCHAR(64) NOT NULL,
banners_group VARCHAR(10) NOT NULL,
banners_html_text text,
expires_impressions INT(7) DEFAULT '0',
expires_date datetime DEFAULT NULL,
date_scheduled datetime DEFAULT NULL,
date_added datetime NOT NULL,
date_status_change datetime DEFAULT NULL,
status INT(1) DEFAULT '1' NOT NULL,
PRIMARY KEY (banners_id),
KEY status (status,banners_group)
);

DROP TABLE IF EXISTS banners_history;
CREATE TABLE banners_history (
banners_history_id INT NOT NULL auto_increment,
banners_id INT NOT NULL,
banners_shown INT(5) NOT NULL DEFAULT '0',
banners_clicked INT(5) NOT NULL DEFAULT '0',
banners_history_date datetime NOT NULL,
PRIMARY KEY (banners_history_id)
);

DROP TABLE IF EXISTS categories;
CREATE TABLE categories (
categories_id INT NOT NULL auto_increment,
categories_image VARCHAR(64),
categories_nav_image VARCHAR( 64 ),
categories_footer_image VARCHAR( 64 ),
parent_id INT DEFAULT '0' NOT NULL,
section INT DEFAULT '0' NOT NULL,
categories_status TINYINT (1) DEFAULT '1' NOT NULL,
categories_main_status TINYINT (1) DEFAULT '1' NOT NULL,
categories_content_status TINYINT (1) DEFAULT '0' NOT NULL,
categories_template VARCHAR(64),
group_permission_0 TINYINT(1) NOT NULL,
group_permission_1 TINYINT(1) NOT NULL,
group_permission_2 TINYINT(1) NOT NULL,
group_permission_3 TINYINT(1) NOT NULL,
listing_template VARCHAR(64) NOT NULL,
sort_order INT(3) DEFAULT '0' NOT NULL,
products_sorting VARCHAR(32),
products_sorting2 VARCHAR(32),
date_added datetime,
last_modified datetime,
categories_col_top TINYINT(1) NOT NULL DEFAULT 1,
categories_col_left TINYINT(1) NOT NULL DEFAULT 1,
categories_col_right TINYINT(1) NOT NULL DEFAULT 1,
categories_col_bottom TINYINT(1) NOT NULL DEFAULT 1,
PRIMARY KEY (categories_id),
KEY idx_categories_parent_id (parent_id),
KEY categories_id (categories_id,parent_id,categories_status,sort_order),
KEY parent_id (parent_id,categories_status,sort_order),
KEY categories_status (categories_status)
);

DROP TABLE IF EXISTS categories_description;
CREATE TABLE categories_description (
categories_id INT DEFAULT '0' NOT NULL,
language_id INT DEFAULT '1' NOT NULL,
categories_name VARCHAR(64) NOT NULL,
categories_heading_title VARCHAR(255) NOT NULL,
categories_description TEXT NOT NULL,
categories_short_description TEXT NOT NULL,
categories_description_footer TEXT NULL,
categories_pic_alt VARCHAR(128) NOT NULL,
categories_pic_footer_alt VARCHAR(128) NOT NULL,
categories_pic_nav_alt VARCHAR(128) NOT NULL,
categories_meta_title VARCHAR(128) NOT NULL,
categories_meta_description VARCHAR(255) NOT NULL,
categories_meta_keywords VARCHAR(255) NOT NULL,
categories_url_alias VARCHAR(64) NULL,
categories_google_taxonomie TEXT NULL,
categories_contents INT(11) NOT NULL DEFAULT 0,
categories_blogs INT(11) NOT NULL DEFAULT 0,
slider_set INT( 64 ) NOT NULL,
PRIMARY KEY (categories_id, language_id),
KEY idx_categories_name (categories_name),
FULLTEXT (categories_name),
FULLTEXT (categories_description),
FULLTEXT (categories_description_footer)
);

DROP TABLE IF EXISTS configuration;
CREATE TABLE configuration (
configuration_id INT NOT NULL auto_increment,
configuration_key VARCHAR(64) NOT NULL,
configuration_value text NOT NULL,
configuration_group_id INT NOT NULL,
sort_order INT(5) NULL,
last_modified datetime NULL,
date_added datetime NOT NULL,
use_function VARCHAR(255) NULL,
set_function VARCHAR(255) NULL,
PRIMARY KEY (configuration_id),
KEY idx_configuration_group_id (configuration_group_id)
);

DROP TABLE IF EXISTS configuration_group;
CREATE TABLE configuration_group (
configuration_group_id INT NOT NULL auto_increment,
configuration_group_title VARCHAR(64) NOT NULL,
configuration_group_description VARCHAR(255) NOT NULL,
sort_order INT(5) NULL,
visible INT(1) DEFAULT '1' NULL,
PRIMARY KEY (configuration_group_id)
);

DROP TABLE IF EXISTS counter;
CREATE TABLE counter (
startdate char(8),
counter INT(12)
);

DROP TABLE IF EXISTS counter_history;
CREATE TABLE counter_history (
month char(8),
counter INT(12)
);

DROP TABLE IF EXISTS countries;
CREATE TABLE countries (
countries_id INT NOT NULL auto_increment,
countries_name VARCHAR(64) NOT NULL,
countries_iso_code_2 char(2) NOT NULL,
countries_iso_code_3 char(3) NOT NULL,
address_format_id INT NOT NULL,
status INT(1) DEFAULT '1' NULL,
PRIMARY KEY (countries_id),
KEY IDX_COUNTRIES_NAME (countries_name)
);

DROP TABLE IF EXISTS currencies;
CREATE TABLE currencies (
currencies_id INT NOT NULL auto_increment,
title VARCHAR(32) NOT NULL,
code char(3) NOT NULL,
symbol_left VARCHAR(12),
symbol_right VARCHAR(12),
decimal_point char(1),
thousands_point char(1),
decimal_places char(1),
value float(13,8),
last_updated datetime NULL,
PRIMARY KEY (currencies_id)
);

DROP TABLE IF EXISTS customers;
CREATE TABLE customers (
customers_id INT NOT NULL auto_increment,
customers_cid VARCHAR(32),
customers_vat_id VARCHAR (20),
customers_vat_id_status INT(2) DEFAULT '0' NOT NULL,
customers_warning VARCHAR(32),
customers_status INT(5) DEFAULT '1' NOT NULL,
customers_gender char(1) NOT NULL,
customers_firstname VARCHAR(32) NOT NULL,
customers_lastname VARCHAR(32) NOT NULL,
customers_dob datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
customers_email_address VARCHAR(96) NOT NULL,
customers_default_address_id INT NOT NULL,
customers_telephone VARCHAR(32) NOT NULL,
customers_fax VARCHAR(32),
customers_password VARCHAR(40) NOT NULL,
customers_newsletter char(1),
customers_newsletter_mode char( 1 ) DEFAULT '0' NOT NULL,
member_flag char(1) DEFAULT '0' NOT NULL,
delete_user char(1) DEFAULT '1' NOT NULL,
account_type INT(1) NOT NULL DEFAULT '0',
password_request_key VARCHAR(32) NOT NULL,
payment_unallowed VARCHAR(255) NOT NULL,
shipping_unallowed VARCHAR(255) NOT NULL,
refferers_id VARCHAR(32) DEFAULT '0' NOT NULL,
customers_date_added datetime DEFAULT '0000-00-00 00:00:00',
customers_last_modified datetime DEFAULT '0000-00-00 00:00:00',
datensg DATETIME NULL,
login_tries VARCHAR( 2 ) NOT NULL DEFAULT '0',
login_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (customers_id)
);

DROP TABLE IF EXISTS customers_basket;
CREATE TABLE customers_basket (
customers_basket_id INT NOT NULL auto_increment,
customers_id INT NOT NULL,
products_id INT(11) NOT NULL,
customers_basket_quantity INT(2) NOT NULL,
final_price decimal(15,4) NOT NULL,
customers_basket_date_added char(8),
checkout_site enum('cart','shipping','payment','confirm') NOT NULL DEFAULT 'cart',
language VARCHAR(32) DEFAULT NULL,
PRIMARY KEY (customers_basket_id),
KEY products_id (products_id)
);

DROP TABLE IF EXISTS customers_basket_attributes;
CREATE TABLE customers_basket_attributes (
customers_basket_attributes_id INT NOT NULL auto_increment,
customers_id INT NOT NULL,
products_id INT(11) NOT NULL,
products_options_id INT NOT NULL,
products_option_ft VARCHAR(50) NOT NULL,
products_options_value_id INT NOT NULL,
PRIMARY KEY (customers_basket_attributes_id),
KEY products_id (products_id)
);

DROP TABLE IF EXISTS customers_info;
CREATE TABLE customers_info (
customers_info_id INT NOT NULL,
customers_info_date_of_last_logon datetime,
customers_info_number_of_logons INT(5),
customers_info_date_account_created datetime,
customers_info_date_account_last_modified datetime,
global_product_notifications INT(1) DEFAULT '0',
PRIMARY KEY (customers_info_id)
);

DROP TABLE IF EXISTS customers_ip;
CREATE TABLE customers_ip (
customers_ip_id INT(11) NOT NULL auto_increment,
customers_id INT(11) NOT NULL DEFAULT '0',
customers_ip VARCHAR(15) NOT NULL DEFAULT '',
customers_ip_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
customers_host VARCHAR(255) NOT NULL DEFAULT '',
customers_advertiser VARCHAR(30) DEFAULT NULL,
customers_referer_url VARCHAR(255) DEFAULT NULL,
PRIMARY KEY (customers_ip_id),
KEY customers_id (customers_id)
);

DROP TABLE IF EXISTS customers_status;
CREATE TABLE customers_status (
customers_status_id INT(11) NOT NULL DEFAULT '0',
language_id INT(11) NOT NULL DEFAULT '1',
customers_status_name VARCHAR(32) NOT NULL DEFAULT '',
customers_status_public INT(1) NOT NULL DEFAULT '1',
customers_status_min_order INT(7) DEFAULT NULL,
customers_status_max_order INT(7) DEFAULT NULL,
customers_status_image VARCHAR(64) DEFAULT NULL,
customers_status_discount decimal(4,2) DEFAULT '0',
customers_status_ot_discount_flag char(1) NOT NULL DEFAULT '0',
customers_status_ot_discount decimal(4,2) DEFAULT '0',
customers_status_graduated_prices VARCHAR(1) NOT NULL DEFAULT '0',
customers_status_show_price INT(1) NOT NULL DEFAULT '1',
customers_status_show_price_tax INT(1) NOT NULL DEFAULT '1',
customers_status_add_tax_ot INT(1) NOT NULL DEFAULT '0',
customers_status_payment_unallowed VARCHAR(255) NOT NULL,
customers_status_shipping_unallowed VARCHAR(255) NOT NULL,
customers_status_discount_attributes INT(1) NOT NULL DEFAULT '0',
customers_fsk18 INT(1) NOT NULL DEFAULT '1',
customers_fsk18_display INT(1) NOT NULL DEFAULT '1',
customers_status_write_reviews INT(1) NOT NULL DEFAULT '1',
customers_status_read_reviews INT(1) NOT NULL DEFAULT '1',
PRIMARY KEY (customers_status_id,language_id),
KEY idx_orders_status_name (customers_status_name)
);

DROP TABLE IF EXISTS customers_status_history;
CREATE TABLE customers_status_history (
customers_status_history_id INT(11) NOT NULL auto_increment,
customers_id INT(11) NOT NULL DEFAULT '0',
new_value INT(5) NOT NULL DEFAULT '0',
old_value INT(5) DEFAULT NULL,
date_added datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
customer_notified INT(1) DEFAULT '0',
PRIMARY KEY (customers_status_history_id)
);

DROP TABLE IF EXISTS languages;
CREATE TABLE languages (
languages_id INT NOT NULL auto_increment,
name VARCHAR(32) NOT NULL,
code char(5) NOT NULL,
image VARCHAR(64),
directory VARCHAR(32),
sort_order INT(3),
language_charset TEXT NOT NULL,
status INT(1) NOT NULL DEFAULT '0',
status_admin INT(1) NOT NULL DEFAULT '0',
PRIMARY KEY (languages_id),
KEY IDX_LANGUAGES_NAME (name)
);

DROP TABLE IF EXISTS manufacturers;
CREATE TABLE manufacturers (
manufacturers_id INT NOT NULL auto_increment,
manufacturers_name VARCHAR(64) NOT NULL,
manufacturers_image VARCHAR(64),
date_added datetime NULL,
last_modified datetime NULL,
PRIMARY KEY (manufacturers_id),
KEY IDX_MANUFACTURERS_NAME (manufacturers_name),
KEY manufacturers_id (manufacturers_id,manufacturers_name)
);

DROP TABLE IF EXISTS manufacturers_info;
CREATE TABLE manufacturers_info (
manufacturers_id int(11) NOT NULL,
languages_id int(11) NOT NULL,
manufacturers_description text NOT NULL,
manufacturers_meta_title VARCHAR(100) NOT NULL,
manufacturers_meta_description VARCHAR(255) NOT NULL,
manufacturers_meta_keywords VARCHAR(255) NOT NULL,
manufacturers_url VARCHAR(255) NOT NULL,
url_clicked int(5) NOT NULL DEFAULT '0',
date_last_click datetime DEFAULT NULL,
KEY manufacturers_id (manufacturers_id,languages_id)
);

DROP TABLE IF EXISTS newsletters;
CREATE TABLE newsletters (
newsletters_id INT NOT NULL auto_increment,
title VARCHAR(255) NOT NULL,
content text NOT NULL,
module VARCHAR(255) NOT NULL,
date_added datetime NOT NULL,
date_sent datetime,
status INT(1),
locked INT(1) DEFAULT '0',
PRIMARY KEY (newsletters_id)
);

DROP TABLE IF EXISTS newsletter_recipients;
CREATE TABLE newsletter_recipients (
mail_id INT(11) NOT NULL auto_increment,
customers_email_address VARCHAR(96) NOT NULL DEFAULT '',
customers_id INT(11) NOT NULL DEFAULT '0',
customers_status INT(5) NOT NULL DEFAULT '0',
customers_firstname VARCHAR(32) NOT NULL DEFAULT '',
customers_lastname VARCHAR(32) NOT NULL DEFAULT '',
mail_status INT(1) NOT NULL DEFAULT '0',
mail_key VARCHAR(32) NOT NULL DEFAULT '',
date_added datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (mail_id)
);

DROP TABLE IF EXISTS newsletters_history;
CREATE TABLE newsletters_history (
news_hist_id INT(11) NOT NULL DEFAULT '0',
news_hist_cs INT(11) NOT NULL DEFAULT '0',
news_hist_cs_date_sent date DEFAULT NULL,
PRIMARY KEY (news_hist_id)
);

DROP TABLE IF EXISTS orders;
CREATE TABLE orders (
orders_id INT NOT NULL auto_increment,
customers_id INT NOT NULL,
customers_cid VARCHAR(32),
customers_vat_id VARCHAR (20),
customers_status INT(11),
customers_status_name VARCHAR(32) NOT NULL,
customers_status_image VARCHAR (64),
customers_status_discount decimal (4,2),
customers_name VARCHAR(64) NOT NULL,
customers_firstname VARCHAR(64) NOT NULL,
customers_lastname VARCHAR(64) NOT NULL,
customers_company VARCHAR(32),
customers_street_address VARCHAR(64) NOT NULL,
customers_suburb VARCHAR(32),
customers_city VARCHAR(32) NOT NULL,
customers_postcode VARCHAR(10) NOT NULL,
customers_state VARCHAR(32),
customers_country VARCHAR(32) NOT NULL,
customers_telephone VARCHAR(32) NOT NULL,
customers_email_address VARCHAR(96) NOT NULL,
customers_address_format_id INT(5) NOT NULL,
delivery_name VARCHAR(64) NOT NULL,
delivery_firstname VARCHAR(64) NOT NULL,
delivery_lastname VARCHAR(64) NOT NULL,
delivery_company VARCHAR(32),
delivery_street_address VARCHAR(64) NOT NULL,
delivery_suburb VARCHAR(32),
delivery_city VARCHAR(32) NOT NULL,
delivery_postcode VARCHAR(10) NOT NULL,
delivery_state VARCHAR(32),
delivery_country VARCHAR(32) NOT NULL,
delivery_country_iso_code_2 char(2) NOT NULL,
delivery_address_format_id INT(5) NOT NULL,
billing_name VARCHAR(64) NOT NULL,
billing_firstname VARCHAR(64) NOT NULL,
billing_lastname VARCHAR(64) NOT NULL,
billing_company VARCHAR(32),
billing_street_address VARCHAR(64) NOT NULL,
billing_suburb VARCHAR(32),
billing_city VARCHAR(32) NOT NULL,
billing_postcode VARCHAR(10) NOT NULL,
billing_state VARCHAR(32),
billing_country VARCHAR(32) NOT NULL,
billing_country_iso_code_2 char(2) NOT NULL,
billing_address_format_id INT(5) NOT NULL,
payment_method VARCHAR(32) NOT NULL,
cc_type VARCHAR(20),
cc_owner VARCHAR(64),
cc_number VARCHAR(64),
cc_expires VARCHAR(4),
cc_start VARCHAR(4) DEFAULT NULL,
cc_issue VARCHAR(3) DEFAULT NULL,
cc_cvv VARCHAR(4) DEFAULT NULL,
comments text,
last_modified datetime,
date_purchased datetime,
orders_status INT(5) NOT NULL,
orders_date_finished datetime,
currency char(3),
currency_value decimal(14,6),
account_type INT(1) DEFAULT '0' NOT NULL,
payment_class VARCHAR(32) NOT NULL,
shipping_method VARCHAR(128) NOT NULL,
shipping_class VARCHAR(32) NOT NULL,
shipping_cost VARCHAR(5) NOT NULL,
customers_ip VARCHAR(32) NOT NULL,
language VARCHAR(32) NOT NULL,
afterbuy_success INT(1) DEFAULT'0' NOT NULL,
afterbuy_id INT(32) DEFAULT '0' NOT NULL,
refferers_id VARCHAR(32) NOT NULL,
conversion_type INT(1) DEFAULT '0' NOT NULL,
orders_ident_key VARCHAR(128),
edebit_transaction_id VARCHAR(32),
edebit_gutid VARCHAR(32),
ibn_billnr INT NOT NULL,
ibn_billdate DATE NOT NULL,
ibn_pdfnotifydate DATE NOT NULL,
order_tracking_id VARCHAR(255),
order_delivery_id INT(5),
PRIMARY KEY (orders_id),
KEY customers_status (customers_status)
);


DROP TABLE IF EXISTS orders_products;
CREATE TABLE orders_products (
orders_products_id INT NOT NULL auto_increment,
orders_id INT NOT NULL,
products_id INT NOT NULL,
products_model VARCHAR(64),
products_name VARCHAR(64) NOT NULL,
products_price decimal(15,4) NOT NULL,
products_discount_made decimal(4,2) DEFAULT NULL,
products_shipping_time VARCHAR(255) DEFAULT NULL,
final_price decimal(15,4) NOT NULL,
products_tax decimal(7,4) NOT NULL,
products_quantity INT(2) NOT NULL,
allow_tax INT(1) NOT NULL,
product_type INT(1) NOT NULL DEFAULT '1',
PRIMARY KEY (orders_products_id),
KEY idx_orders_id (orders_id),
KEY idx_products_id (products_id)
);

DROP TABLE IF EXISTS orders_status;
CREATE TABLE orders_status (
orders_status_id INT DEFAULT '0' NOT NULL,
language_id INT DEFAULT '1' NOT NULL,
orders_status_name VARCHAR(32) NOT NULL,
orders_status_color VARCHAR( 6 ) NOT NULL,
PRIMARY KEY (orders_status_id, language_id),
KEY idx_orders_status_name (orders_status_name)
);

DROP TABLE IF EXISTS shipping_status;
CREATE TABLE shipping_status (
shipping_status_id INT DEFAULT '0' NOT NULL,
language_id INT DEFAULT '1' NOT NULL,
shipping_status_name VARCHAR(32) NOT NULL,
shipping_status_image VARCHAR(32) NOT NULL,
info_link_active TINYINT NOT NULL DEFAULT '1',
PRIMARY KEY (shipping_status_id, language_id),
KEY idx_shipping_status_name (shipping_status_name),
KEY language_id (language_id)
);

DROP TABLE IF EXISTS search_queries_all;
CREATE TABLE search_queries_all (
search_id INT(11) NOT NULL auto_increment,
search_text tinytext ,
search_result VARCHAR(255) NOT NULL,
PRIMARY KEY (search_id)
);

DROP TABLE IF EXISTS search_queries_sorted;
CREATE TABLE search_queries_sorted (
search_id smallint(6) not null auto_increment,
search_text tinytext not null ,
search_count INT(11) default '0' NOT NULL,
search_result VARCHAR(255) NOT NULL,
PRIMARY KEY (search_id)
);

DROP TABLE IF EXISTS orders_status_history;
CREATE TABLE orders_status_history (
orders_status_history_id INT NOT NULL auto_increment,
orders_id INT NOT NULL,
orders_status_id INT(5) NOT NULL,
date_added datetime NOT NULL,
customer_notified INT(1) DEFAULT '0',
comments TEXT,
PRIMARY KEY (orders_status_history_id)
);

DROP TABLE IF EXISTS orders_products_attributes;
CREATE TABLE orders_products_attributes (
orders_products_attributes_id INT NOT NULL auto_increment,
orders_id INT NOT NULL,
orders_products_id INT NOT NULL,
products_attributes_id INT NOT NULL,
sortorder INT NOT NULL,
products_options VARCHAR(32) NOT NULL,
products_options_values VARCHAR(64) NOT NULL,
options_values_price decimal(15,4) NOT NULL,
price_prefix CHAR(1) NOT NULL,
products_attributes_model VARCHAR( 255 ) NULL,
attributes_shippingtime VARCHAR( 255 ) NOT NULL,
PRIMARY KEY (orders_products_attributes_id)
);

DROP TABLE IF EXISTS orders_products_download;
CREATE TABLE orders_products_download (
orders_products_download_id INT NOT NULL auto_increment,
orders_id INT NOT NULL DEFAULT '0',
orders_products_id INT NOT NULL DEFAULT '0',
orders_products_filename VARCHAR(255) NOT NULL DEFAULT '',
download_maxdays INT(2) NOT NULL DEFAULT '0',
download_count INT(2) NOT NULL DEFAULT '0',
download_ip VARCHAR(15) NOT NULL DEFAULT '0',
download_time DATETIME NOT NULL,
PRIMARY KEY (orders_products_download_id)
);

DROP TABLE IF EXISTS orders_total;
CREATE TABLE orders_total (
orders_total_id INT NOT NULL auto_increment,
orders_id INT NOT NULL,
title VARCHAR(255) NOT NULL,
text VARCHAR(255) NOT NULL,
value decimal(15,4) NOT NULL,
class VARCHAR(32) NOT NULL,
sort_order INT NOT NULL,
PRIMARY KEY (orders_total_id),
KEY idx_orders_total_orders_id (orders_id)
);

DROP TABLE IF EXISTS paypal;
CREATE TABLE paypal (
paypal_ipn_id int(11) NOT NULL auto_increment,
xtc_order_id int(11) NOT NULL default '0',
txn_type VARCHAR(32) NOT NULL default '',
reason_code VARCHAR(15) default NULL,
payment_type VARCHAR(7) NOT NULL default '',
payment_status VARCHAR(17) NOT NULL default '',
pending_reason VARCHAR(14) default NULL,
invoice VARCHAR(64) default NULL,
mc_currency char(3) NOT NULL default '',
first_name VARCHAR(32) NOT NULL default '',
last_name VARCHAR(32) NOT NULL default '',
payer_business_name VARCHAR(64) default NULL,
address_name VARCHAR(32) default NULL,
address_street VARCHAR(64) default NULL,
address_city VARCHAR(32) default NULL,
address_state VARCHAR(32) default NULL,
address_zip VARCHAR(10) default NULL,
address_country VARCHAR(64) default NULL,
address_status VARCHAR(11) default NULL,
payer_email VARCHAR(96) NOT NULL default '',
payer_id VARCHAR(32) NOT NULL default '',
payer_status VARCHAR(10) NOT NULL default '',
payment_date datetime NOT NULL default '0001-01-01 00:00:00',
business VARCHAR(96) NOT NULL default '',
receiver_email VARCHAR(96) NOT NULL default '',
receiver_id VARCHAR(32) NOT NULL default '',
txn_id VARCHAR(40) NOT NULL default '',
parent_txn_id VARCHAR(17) default NULL,
num_cart_items tinyint(4) NOT NULL default '1',
mc_gross decimal(7,2) NOT NULL default '0.00',
mc_fee decimal(7,2) NOT NULL default '0.00',
mc_shipping decimal(7,2) NOT NULL default '0.00',
payment_gross decimal(7,2) default NULL,
payment_fee decimal(7,2) default NULL,
settle_amount decimal(7,2) default NULL,
settle_currency char(3) default NULL,
exchange_rate decimal(4,2) default NULL,
notify_version decimal(2,1) NOT NULL default '0.0',
verify_sign VARCHAR(128) NOT NULL default '',
last_modified datetime NOT NULL default '0001-01-01 00:00:00',
date_added datetime NOT NULL default '0001-01-01 00:00:00',
memo text,
mc_authorization decimal(7,2) NOT NULL,
mc_captured decimal(7,2) NOT NULL,
PRIMARY KEY (paypal_ipn_id,txn_id),
KEY xtc_order_id (xtc_order_id)
);

DROP TABLE if EXISTS paypal_status_history;
CREATE TABLE paypal_status_history (
payment_status_history_id int(11) NOT NULL auto_increment,
paypal_ipn_id int(11) NOT NULL default '0',
txn_id VARCHAR(64) NOT NULL default '',
parent_txn_id VARCHAR(64) NOT NULL default '',
payment_status VARCHAR(17) NOT NULL default '',
pending_reason VARCHAR(64) default NULL,
mc_amount decimal(7,2) NOT NULL,
date_added datetime NOT NULL default '0001-01-01 00:00:00',
PRIMARY KEY (payment_status_history_id),
KEY paypal_ipn_id (paypal_ipn_id)
);

DROP TABLE IF EXISTS orders_recalculate;
CREATE TABLE orders_recalculate (
orders_recalculate_id INT(11) NOT NULL auto_increment,
orders_id INT(11) NOT NULL DEFAULT '0',
n_price decimal(15,4) NOT NULL DEFAULT '0.0000',
b_price decimal(15,4) NOT NULL DEFAULT '0.0000',
tax decimal(15,4) NOT NULL DEFAULT '0.0000',
tax_rate decimal(7,4) NOT NULL DEFAULT '0.0000',
class VARCHAR(32) NOT NULL DEFAULT '',
PRIMARY KEY (orders_recalculate_id)
);

DROP TABLE IF EXISTS products;
CREATE TABLE products (
products_id INT NOT NULL auto_increment,
products_ean VARCHAR(128),
products_isbn VARCHAR(128),
products_upc VARCHAR(128),
products_g_identifier VARCHAR(128) NULL DEFAULT  'TRUE',
products_brand_name VARCHAR(128),
products_g_availability VARCHAR(128),
products_g_shipping_status INT(10),
products_quantity INT(4) NOT NULL,
products_shippingtime INT(4) NOT NULL,
products_model VARCHAR(64),
products_manufacturers_model VARCHAR(64),
group_permission_0 TINYINT(1) NOT NULL,
group_permission_1 TINYINT(1) NOT NULL,
group_permission_2 TINYINT(1) NOT NULL,
group_permission_3 TINYINT(1) NOT NULL,
products_sort INT(4) NOT NULL DEFAULT '0',
products_image VARCHAR(254),
products_price decimal(15,4) NOT NULL,
products_ekpprice decimal(15,4) NOT NULL,
products_uvpprice decimal(15,4) NOT NULL,
products_discount_allowed decimal(5,2) DEFAULT '0.00' NOT NULL,
products_date_added datetime NOT NULL,
products_last_modified datetime,
products_date_available datetime,
products_weight decimal(10,3) DEFAULT '0.00' NOT NULL,
products_status TINYINT(1) NOT NULL,
products_tax_class_id INT NOT NULL,
product_template VARCHAR (64),
options_template VARCHAR (64),
manufacturers_id INT NULL,
products_ordered INT NOT NULL DEFAULT '0',
products_fsk18 INT(1) NOT NULL DEFAULT '0',
products_vpe INT(11) NOT NULL,
products_vpe_status INT(1) NOT NULL DEFAULT '0',
products_vpe_value decimal(15,4) NOT NULL,
products_startpage INT(1) NOT NULL DEFAULT '0',
products_startpage_sort INT(4) NOT NULL DEFAULT '0',
products_zustand VARCHAR(10) NOT NULL DEFAULT 'neu',
products_movie_embeded_code TEXT NULL,
products_movie_height INT(4) NULL,
products_movie_width INT(4) NULL,
products_movie_youtube_id VARCHAR(32) NOT NULL DEFAULT '',
products_movie_on_server VARCHAR(255) NOT NULL DEFAULT '',
products_col_top TINYINT(1) NOT NULL DEFAULT 1,
products_col_left TINYINT(1) NOT NULL DEFAULT 1,
products_col_right TINYINT(1) NOT NULL DEFAULT 1,
products_col_bottom TINYINT(1) NOT NULL DEFAULT 1,
products_cartspecial INT(1) NOT NULL DEFAULT 0,
products_buyable INT(1) NOT NULL DEFAULT 1,
products_sperrgut TINYINT(1) NOT NULL DEFAULT 0,
products_shipping_costs VARCHAR(64) NOT NULL,
products_forbidden_payment TEXT,
products_forbidden_shipping TEXT,
products_master int(1) NOT NULL DEFAULT 0,
products_master_article VARCHAR(64) NOT NULL,
products_slave_in_list int(1) NOT NULL DEFAULT 0,
stock_mail int(1) unsigned NOT NULL DEFAULT 0,
products_promotion_status int(1) NOT NULL default 0,
products_promotion_product_title int(1) NOT NULL default 0,
products_promotion_product_desc int(1) NOT NULL default 0,
products_treepodia_activate int(1) NOT NULL default 1,
products_minorder int(5) NULL,
products_maxorder int(5) NULL,
products_only_request int(1) NOT NULL default '0',
products_rel int(1) NOT NULL default '1',
products_google_gender VARCHAR( 128 ) NULL,
products_google_age_group VARCHAR( 128 ) NULL,
products_google_color VARCHAR( 128 ) NULL,
products_google_size VARCHAR( 128 ) NULL,
product_type TINYINT( 1 ) NOT NULL DEFAULT '1',
PRIMARY KEY (products_id),
KEY idx_products_date_added (products_date_added),
KEY products_id (products_id,products_status,products_date_added),
KEY products_status (products_status,products_id,products_date_added),
KEY products_status_2 (products_status,products_id,products_price),
KEY products_status_3 (products_status,products_ordered,products_id),
KEY products_status_4 (products_status,products_model,products_id),
KEY products_id_2 (products_id,products_startpage,products_status,products_startpage_sort),
KEY products_date_available (products_date_available,products_id),
KEY products_quantity (products_quantity),
KEY products_sort (products_sort),
KEY products_tax_class_id (products_tax_class_id),
KEY manufacturers_id (manufacturers_id),
KEY products_startpage (products_startpage), 
KEY model (products_model)
);

DROP TABLE IF EXISTS products_attributes;
CREATE TABLE products_attributes (
products_attributes_id INT NOT NULL auto_increment,
products_id INT NOT NULL,
options_id INT NOT NULL,
options_values_id INT NOT NULL,
options_values_price decimal(15,4) NOT NULL,
options_values_scale_price VARCHAR(128) NOT NULL,
price_prefix char(1) NOT NULL,
attributes_model VARCHAR(64) NULL,
attributes_stock INT(4) NULL,
options_values_weight decimal(15,4) NOT NULL,
weight_prefix char(1) NOT NULL,
sortorder INT(11) NULL,
attributes_ean VARCHAR( 128 ) NULL DEFAULT NULL,
attributes_vpe_status INT( 1 ) NOT NULL DEFAULT  '0',
attributes_vpe INT( 11 ) NOT NULL,
attributes_vpe_value DECIMAL( 15, 4 ) NOT NULL,
attributes_shippingtime INT( 4 ) NOT NULL,
PRIMARY KEY (products_attributes_id),
KEY products_id (products_id,options_id,options_values_id,sortorder),
KEY idx_products_id (products_id),
KEY idx_options (options_id, options_values_id),
KEY sortorder (sortorder),
FULLTEXT (attributes_model)
);

DROP TABLE IF EXISTS products_attributes_download;
CREATE TABLE products_attributes_download (
products_attributes_id INT NOT NULL,
products_attributes_filename VARCHAR(255) NOT NULL DEFAULT '',
products_attributes_maxdays INT(2) DEFAULT '0',
products_attributes_maxcount INT(2) DEFAULT '0',
PRIMARY KEY (products_attributes_id)
);

DROP TABLE IF EXISTS products_description;
CREATE TABLE products_description (
products_id INT NOT NULL auto_increment,
language_id INT NOT NULL DEFAULT '1',
products_name VARCHAR(128) NOT NULL DEFAULT '',
products_description TEXT NULL,
products_short_description TEXT NULL,
products_zusatz_description TEXT NULL,
products_cart_description TEXT NULL,
products_google_taxonomie TEXT NULL,
products_taxonomie TEXT,
products_img_alt VARCHAR(128) NULL,
products_keywords VARCHAR(255) DEFAULT NULL,
products_meta_title VARCHAR(128) NULL,
products_meta_description TEXT NULL,
products_meta_keywords TEXT NULL,
products_url VARCHAR(255) DEFAULT NULL,
products_viewed INT(5) DEFAULT '0',
products_tag_cloud VARCHAR(32) NULL,
products_url_alias VARCHAR(128) NULL,
products_promotion_title VARCHAR(128) NULL,
products_promotion_image VARCHAR(64) NULL,
products_promotion_desc text NULL,
products_treepodia_catch_phrase_1 VARCHAR(255) DEFAULT NULL,
products_treepodia_catch_phrase_2 VARCHAR(255) DEFAULT NULL,
products_treepodia_catch_phrase_3 VARCHAR(255) DEFAULT NULL,
products_treepodia_catch_phrase_4 VARCHAR(255) DEFAULT NULL,
products_treepodia_youtube_keyword1 VARCHAR(255) NULL,
products_treepodia_youtube_keyword2 VARCHAR(255) NULL,
products_treepodia_youtube_keyword3 VARCHAR(255) NULL,
products_treepodia_youtube_keyword4 VARCHAR(255) NULL,
url_text VARCHAR(255) NULL,
url_md5 VARCHAR(32) NULL,
url_old_text VARCHAR(255) NULL,
PRIMARY KEY (products_id,language_id),
KEY products_name (products_name),
KEY language_id (language_id,products_keywords),
KEY language_id_2 (language_id,products_name),
KEY urlidx (url_text, url_md5),
FULLTEXT (products_name),
FULLTEXT (products_description),
FULLTEXT (products_short_description),
FULLTEXT (products_zusatz_description)
);

DROP TABLE IF EXISTS products_images;
CREATE TABLE products_images (
image_id INT NOT NULL auto_increment,
products_id INT NOT NULL,
image_nr SMALLINT NOT NULL,
image_name VARCHAR(254) NOT NULL,
alt_langID_1 VARCHAR(64) NOT NULL,
alt_langID_2 VARCHAR(64) NOT NULL,
PRIMARY KEY (image_id),
KEY products_images (products_id)
);

DROP TABLE IF EXISTS products_listings;
CREATE TABLE products_listings (
list_name varchar(32) NOT NULL,
col int(2) NOT NULL DEFAULT '3',
p_img int(1) NOT NULL DEFAULT '1',
p_name int(1) NOT NULL DEFAULT '1',
p_price int(1) NOT NULL DEFAULT '1',
b_details int(1) NOT NULL DEFAULT '1',
b_order int(1) NOT NULL DEFAULT '1',
b_wishlist int(1) NOT NULL DEFAULT '0',
p_reviews int(1) NOT NULL DEFAULT '0',
p_stockimg int(1) NOT NULL DEFAULT '0',
p_vpe int(1) NOT NULL DEFAULT '0',
p_model int(1) NOT NULL DEFAULT '0',
p_manu_img int(1) NOT NULL DEFAULT '0',
p_manu_name int(1) NOT NULL DEFAULT '0',
p_short_desc int(1) NOT NULL DEFAULT '1',
p_short_desc_lenght int(1) NOT NULL DEFAULT '75',
p_long_desc int(1) NOT NULL DEFAULT '0',
p_long_desc_lenght int(1) NOT NULL DEFAULT '200',
list_type varchar(10) NOT NULL DEFAULT 'list',
list_head varchar(32) NOT NULL,
p_staffel int(1) NOT NULL DEFAULT '0',
p_attribute int(1) NOT NULL DEFAULT '0',
p_buy int(1) NOT NULL DEFAULT '0',
p_weight int(1) NOT NULL DEFAULT '0',
PRIMARY KEY (list_name),
KEY products_listings (list_name)
);

DROP TABLE IF EXISTS products_notifications;
CREATE TABLE products_notifications (
products_id INT NOT NULL,
customers_id INT NOT NULL,
date_added datetime NOT NULL,
PRIMARY KEY (products_id, customers_id)
);

DROP TABLE IF EXISTS products_parameters;
CREATE TABLE products_parameters (
parameters_id INT(11) NOT NULL auto_increment,
group_id INT(11) NOT NULL default '0',
products_id INT(11) NOT NULL default '0',
sort_order INT(3) NOT NULL default '0',
PRIMARY KEY (parameters_id)
);

DROP TABLE IF EXISTS products_parameters_description;
CREATE TABLE products_parameters_description (
parameters_id INT(11) NOT NULL,
language_id INT(11) NOT NULL,
parameters_name TEXT NOT NULL,
parameters_value TEXT NOT NULL,
PRIMARY KEY (parameters_id,language_id)
);

DROP TABLE IF EXISTS products_parameters_groups;
CREATE TABLE products_parameters_groups (
group_id INT(11) NOT NULL auto_increment,
sort_order INT(3) NOT NULL,
PRIMARY KEY (group_id)
);

DROP TABLE IF EXISTS products_parameters_groups_description;
CREATE TABLE products_parameters_groups_description (
group_id INT(11) NOT NULL,
language_id INT(11) NOT NULL,
group_name VARCHAR(64) NOT NULL,
PRIMARY KEY (group_id,language_id)
);

DROP TABLE IF EXISTS products_options;
CREATE TABLE products_options (
products_options_id INT NOT NULL DEFAULT '0',
language_id INT NOT NULL DEFAULT '1',
products_options_name VARCHAR(32) NOT NULL DEFAULT '',
products_options_sortorder int(11) DEFAULT NULL,
PRIMARY KEY (products_options_id,language_id)
);

DROP TABLE IF EXISTS products_options_values;
CREATE TABLE products_options_values (
products_options_values_id INT NOT NULL DEFAULT '0',
language_id INT NOT NULL DEFAULT '1',
products_options_values_name VARCHAR(64) NOT NULL DEFAULT '',
products_options_values_desc TEXT NOT NULL DEFAULT '',
products_options_values_image VARCHAR(64) NOT NULL DEFAULT '',
products_options_hex_image VARCHAR( 7 ) NOT NULL,
PRIMARY KEY (products_options_values_id,language_id),
KEY products_options_values_name (products_options_values_name,language_id),
FULLTEXT (products_options_values_name)
);

DROP TABLE IF EXISTS products_options_values_to_products_options;
CREATE TABLE products_options_values_to_products_options (
products_options_values_to_products_options_id INT NOT NULL auto_increment,
products_options_id INT NOT NULL,
products_options_values_id INT NOT NULL,
PRIMARY KEY (products_options_values_to_products_options_id),
KEY products_options_id (products_options_id),
KEY products_options_values_id (products_options_values_id)
);

DROP TABLE IF EXISTS products_graduated_prices;
CREATE TABLE products_graduated_prices (
products_id INT(11) NOT NULL DEFAULT '0',
quantity INT(11) NOT NULL DEFAULT '0',
unitprice decimal(15,4) NOT NULL DEFAULT '0.0000',
KEY products_id (products_id)
);

DROP TABLE IF EXISTS products_to_categories;
CREATE TABLE products_to_categories (
products_id INT NOT NULL,
categories_id INT NOT NULL,
PRIMARY KEY (products_id,categories_id),
KEY categories_id (categories_id,products_id),
KEY categories_id_2 (categories_id)
);

DROP TABLE IF EXISTS products_vpe;
CREATE TABLE products_vpe (
products_vpe_id INT(11) NOT NULL DEFAULT '0',
language_id INT(11) NOT NULL DEFAULT '0',
products_vpe_name VARCHAR(32) NOT NULL DEFAULT ''
);

DROP TABLE IF EXISTS reviews;
CREATE TABLE reviews (
reviews_id INT NOT NULL auto_increment,
products_id INT NOT NULL,
customers_id int,
customers_name VARCHAR(64) NOT NULL,
reviews_rating INT(1),
date_added datetime,
last_modified datetime,
reviews_read INT(5) NOT NULL DEFAULT '0',
reviews_status INT(1) NOT NULL DEFAULT '0',
PRIMARY KEY (reviews_id)
);

DROP TABLE IF EXISTS reviews_description;
CREATE TABLE reviews_description (
reviews_id INT NOT NULL,
languages_id INT NOT NULL,
reviews_text text NOT NULL,
PRIMARY KEY (reviews_id, languages_id)
);

DROP TABLE IF EXISTS sessions;
CREATE TABLE sessions (
sesskey VARCHAR(32) NOT NULL,
expiry INT(11) NOT NULL,
value text NOT NULL,
PRIMARY KEY (sesskey),
KEY sesskey (sesskey,expiry)
);

DROP TABLE IF EXISTS specials;
CREATE TABLE specials (
specials_id INT NOT NULL auto_increment,
products_id INT NOT NULL,
specials_quantity INT(4) NOT NULL,
specials_new_products_price decimal(15,4) NOT NULL,
specials_date_added datetime,
specials_last_modified datetime,
expires_date datetime,
date_status_change datetime,
status INT(1) NOT NULL DEFAULT '1',
PRIMARY KEY (specials_id),
KEY products_id (products_id,status,specials_date_added),
KEY status (status,expires_date)
);

DROP TABLE IF EXISTS tax_class;
CREATE TABLE tax_class (
tax_class_id INT NOT NULL auto_increment,
tax_class_title VARCHAR(128) NOT NULL,
tax_class_description VARCHAR(255) NOT NULL,
last_modified datetime NULL,
date_added datetime NOT NULL,
PRIMARY KEY (tax_class_id)
);

DROP TABLE IF EXISTS tax_rates;
CREATE TABLE tax_rates (
tax_rates_id INT NOT NULL auto_increment,
tax_zone_id INT NOT NULL,
tax_class_id INT NOT NULL,
tax_priority INT(5) DEFAULT 1,
tax_rate decimal(7,4) NOT NULL,
tax_description VARCHAR(255) NOT NULL,
last_modified datetime NULL,
date_added datetime NOT NULL,
PRIMARY KEY (tax_rates_id),
KEY tax_zone_id (tax_zone_id),
KEY tax_class_id (tax_class_id,tax_priority)
);

DROP TABLE IF EXISTS geo_zones;
CREATE TABLE geo_zones (
geo_zone_id INT NOT NULL auto_increment,
geo_zone_name VARCHAR(32) NOT NULL,
geo_zone_description VARCHAR(255) NOT NULL,
last_modified datetime NULL,
date_added datetime NOT NULL,
PRIMARY KEY (geo_zone_id)
);

DROP TABLE IF EXISTS whos_online;
CREATE TABLE whos_online (
customer_id int,
full_name VARCHAR(64) NOT NULL,
session_id VARCHAR(128) NOT NULL,
ip_address VARCHAR(15) NOT NULL,
time_entry VARCHAR(14) NOT NULL,
time_last_click VARCHAR(14) NOT NULL,
last_page_url VARCHAR(255) NOT NULL,
http_referer VARCHAR(255) NOT NULL,
user_agent VARCHAR(255) NOT NULL,
user_language VARCHAR( 6 ) NOT NULL,
KEY customer_id (customer_id),
KEY session_id (session_id),
KEY time_last_click (time_last_click)
);

DROP TABLE IF EXISTS whos_online_year;
CREATE TABLE whos_online_year (
whos_online_id int(11) NOT NULL auto_increment,
year int(4) NOT NULL default '0',
month int(2) NOT NULL default '0',
referer_url VARCHAR(250) default NULL,
count int(11) NOT NULL default '0',
PRIMARY KEY (whos_online_id)
); 

DROP TABLE IF EXISTS whos_online_month;
CREATE TABLE whos_online_month (
whos_online_id int(11) NOT NULL auto_increment,
day smallint(2) NOT NULL default '0',
referer_url VARCHAR(250) default NULL,
count int(11) NOT NULL default '0',
PRIMARY KEY (whos_online_id)
);

DROP TABLE IF EXISTS zones;
CREATE TABLE zones (
zone_id INT NOT NULL auto_increment,
zone_country_id INT NOT NULL,
zone_code VARCHAR(32) NOT NULL,
zone_name VARCHAR(32) NOT NULL,
PRIMARY KEY (zone_id)
);

DROP TABLE IF EXISTS zones_to_geo_zones;
CREATE TABLE zones_to_geo_zones (
association_id INT NOT NULL auto_increment,
zone_country_id INT NOT NULL,
zone_id INT NULL,
geo_zone_id INT NULL,
last_modified datetime NULL,
date_added datetime NOT NULL,
PRIMARY KEY (association_id),
KEY zone_id (zone_id),
KEY geo_zone_id (geo_zone_id),
KEY zone_country_id (zone_country_id,zone_id)
);

DROP TABLE IF EXISTS content_manager;
CREATE TABLE content_manager (
content_id INT(11) NOT NULL auto_increment,
categories_id INT(11) NOT NULL DEFAULT '0',
parent_id INT(11) NOT NULL DEFAULT '0',
group_ids TEXT,
languages_id INT(11) NOT NULL DEFAULT '0',
content_title text NOT NULL,
content_heading text NOT NULL,
content_text text NOT NULL,
sort_order INT(4) NOT NULL DEFAULT '0',
file_flag INT(1) NOT NULL DEFAULT '0',
content_file VARCHAR(64) NOT NULL DEFAULT '',
content_status INT(1) NOT NULL DEFAULT '0',
content_group INT(11) NOT NULL,
content_delete INT(1) NOT NULL DEFAULT '1',
content_meta_title text,
content_meta_description text,
content_meta_keywords text,
content_url_alias VARCHAR(64) NULL,
content_out_link TEXT NULL,
content_link_target VARCHAR(6) NULL,
content_link_type VARCHAR(8) NULL,
content_col_top TINYINT(1) NOT NULL DEFAULT 1,
content_col_left TINYINT(1) NOT NULL DEFAULT 1,
content_col_right TINYINT(1) NOT NULL DEFAULT 1,
content_col_bottom TINYINT(1) NOT NULL DEFAULT 1,
slider_set INT( 64 ) NOT NULL,
last_modified DATETIME NOT NULL,
PRIMARY KEY (content_id),
KEY languages_id (languages_id,file_flag,content_status,sort_order),
KEY content_id (content_id,languages_id),
KEY content_group (content_group,languages_id)
);

DROP TABLE IF EXISTS media_content;
CREATE TABLE media_content (
file_id INT(11) NOT NULL auto_increment,
old_filename text NOT NULL,
new_filename text NOT NULL,
file_comment text NOT NULL,
PRIMARY KEY (file_id)
);

DROP TABLE IF EXISTS products_content;
CREATE TABLE products_content (
content_id INT(11) NOT NULL auto_increment,
products_id INT(11) NOT NULL DEFAULT '0',
group_ids TEXT,
content_name VARCHAR(32) NOT NULL DEFAULT '',
content_file VARCHAR(64) NOT NULL,
content_link text NOT NULL,
languages_id INT(11) NOT NULL DEFAULT '0',
content_read INT(11) NOT NULL DEFAULT '0',
file_comment text NOT NULL,
PRIMARY KEY (content_id)
);

DROP TABLE IF EXISTS module_newsletter;
CREATE TABLE module_newsletter (
newsletter_id INT(11) NOT NULL auto_increment,
title text NOT NULL,
bc text NOT NULL,
cc text NOT NULL,
date datetime DEFAULT NULL,
status INT(1) NOT NULL DEFAULT '0',
body text NOT NULL,
personalize char(3) NOT NULL DEFAULT '0',
greeting INT(1) NOT NULL DEFAULT '0',
gift char(3) NOT NULL DEFAULT '0',
ammount VARCHAR(10) NOT NULL DEFAULT '0',
product_list INT(11) NOT NULL DEFAULT '0',
PRIMARY KEY (newsletter_id)
);

DROP TABLE IF EXISTS newsletter_product_list;
CREATE TABLE newsletter_product_list (
id INT(11) NOT NULL auto_increment,
list_name char(64) NOT NULL DEFAULT '',
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS newsletter_products;
CREATE TABLE newsletter_products (
id INT(11) NOT NULL auto_increment,
accessories_id INT(11) NOT NULL DEFAULT '0',
product_id INT(11) NOT NULL DEFAULT '0',
PRIMARY KEY (id)
);

DROP TABLE if exists cm_file_flags;
CREATE TABLE cm_file_flags (
file_flag INT(11) NOT NULL,
file_flag_name VARCHAR(32) NOT NULL,
PRIMARY KEY (file_flag)
);

DROP TABLE if EXISTS coupon_email_track;
CREATE TABLE coupon_email_track (
unique_id INT(11) NOT NULL auto_increment,
coupon_id INT(11) NOT NULL DEFAULT '0',
customer_id_sent INT(11) NOT NULL DEFAULT '0',
sent_firstname VARCHAR(32) DEFAULT NULL,
sent_lastname VARCHAR(32) DEFAULT NULL,
emailed_to VARCHAR(32) DEFAULT NULL,
date_sent datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (unique_id)
);

DROP TABLE if EXISTS coupon_gv_customer;
CREATE TABLE coupon_gv_customer (
customer_id INT(5) NOT NULL DEFAULT '0',
amount decimal(8,4) NOT NULL DEFAULT '0.0000',
PRIMARY KEY (customer_id),
KEY customer_id (customer_id)
);

DROP TABLE if EXISTS coupon_gv_queue;
CREATE TABLE coupon_gv_queue (
unique_id INT(5) NOT NULL auto_increment,
customer_id INT(5) NOT NULL DEFAULT '0',
order_id INT(5) NOT NULL DEFAULT '0',
amount decimal(8,4) NOT NULL DEFAULT '0.0000',
date_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
ipaddr VARCHAR(32) NOT NULL DEFAULT '',
release_flag char(1) NOT NULL DEFAULT 'N',
PRIMARY KEY (unique_id),
KEY uid (unique_id,customer_id,order_id)
);

DROP TABLE if EXISTS coupon_redeem_track;
CREATE TABLE coupon_redeem_track (
unique_id INT(11) NOT NULL auto_increment,
coupon_id INT(11) NOT NULL DEFAULT '0',
customer_id INT(11) NOT NULL DEFAULT '0',
redeem_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
redeem_ip VARCHAR(32) NOT NULL DEFAULT '',
order_id INT(11) NOT NULL DEFAULT '0',
PRIMARY KEY (unique_id)
);

DROP TABLE if EXISTS coupons;
CREATE TABLE coupons (
coupon_id INT(11) NOT NULL auto_increment,
coupon_type char(1) NOT NULL DEFAULT 'F',
coupon_code VARCHAR(32) NOT NULL DEFAULT '',
coupon_amount decimal(8,4) NOT NULL DEFAULT '0.0000',
coupon_minimum_order decimal(8,4) NOT NULL DEFAULT '0.0000',
coupon_start_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
coupon_expire_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
uses_per_coupon INT(5) NOT NULL DEFAULT '1',
uses_per_user INT(5) NOT NULL DEFAULT '0',
restrict_to_products VARCHAR(255) DEFAULT NULL,
restrict_to_categories VARCHAR(255) DEFAULT NULL,
restrict_to_customers text,
coupon_active char(1) NOT NULL DEFAULT 'Y',
date_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
date_modified datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (coupon_id)
);

DROP TABLE IF EXISTS coupons_description;
CREATE TABLE coupons_description (
coupon_id INT(11) NOT NULL DEFAULT '0',
language_id INT(11) NOT NULL DEFAULT '0',
coupon_name VARCHAR(32) NOT NULL DEFAULT '',
coupon_description text,
KEY coupon_id (coupon_id)
);

DROP TABLE IF EXISTS personal_offers_by_customers_status_;
DROP TABLE IF EXISTS personal_offers_by_customers_status_0;
DROP TABLE IF EXISTS personal_offers_by_customers_status_1;
DROP TABLE IF EXISTS personal_offers_by_customers_status_2;
DROP TABLE IF EXISTS personal_offers_by_customers_status_3;

DROP TABLE IF EXISTS scart;
CREATE TABLE scart (
scartid INT( 11 ) NOT NULL AUTO_INCREMENT,
customers_id INT( 11 ) NOT NULL UNIQUE,
dateadded VARCHAR( 8 ) NOT NULL,
datemodified VARCHAR( 8 ) NOT NULL,
PRIMARY KEY ( scartid )
);

DROP TABLE IF EXISTS staffel_to_templates;
CREATE TABLE staffel_to_templates (
template_id INT(5) NOT NULL,
quantity INT(5) DEFAULT NULL,
personal_offer decimal(15,4) DEFAULT NULL
);

DROP TABLE if EXISTS staffel_templates;
CREATE TABLE staffel_templates (
template_id INT(5) NOT NULL auto_increment,
template_name VARCHAR(255) NOT NULL,
PRIMARY KEY ( template_id )
);


DROP TABLE if EXISTS tag_to_product;
CREATE TABLE tag_to_product (
id INT(11) NOT NULL auto_increment,
pID INT(10) NOT NULL DEFAULT '0',
lID INT(2) NOT NULL DEFAULT '0',
tag VARCHAR(64) NOT NULL DEFAULT '0',
PRIMARY KEY (id),
FULLTEXT (tag),
INDEX (id,pID)
);


DROP TABLE IF EXISTS emails;
CREATE TABLE emails (
id int(2) NOT NULL AUTO_INCREMENT,
email_name VARCHAR(64) NOT NULL,
languages_id int(2) NOT NULL,
email_address VARCHAR(64) NOT NULL,
email_address_name VARCHAR(64) NOT NULL,
email_replay_address VARCHAR(64) NOT NULL,
email_replay_address_name VARCHAR(64) NOT NULL,
email_subject VARCHAR(64) NOT NULL,
email_forward VARCHAR(64) NOT NULL,
email_content_html text NOT NULL,
email_content_text text NOT NULL,
email_backup_html text NOT NULL,
email_backup_text text NOT NULL,
email_timestamp int(10) NOT NULL,
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS emails_order_products_list;
CREATE TABLE emails_order_products_list (
id INT(11) NOT NULL auto_increment,
list_name char(64) NOT NULL DEFAULT '',
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS emails_order_products;
CREATE TABLE emails_order_products (
id INT(11) NOT NULL auto_increment,
accessories_id INT(11) NOT NULL DEFAULT '0',
product_id INT(11) NOT NULL DEFAULT '0',
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS orders_pdf_profile;
CREATE TABLE orders_pdf_profile (
id int(11) unsigned NOT NULL AUTO_INCREMENT,
languages_id int(2) NOT NULL,
pdf_key VARCHAR(64) NOT NULL,
pdf_value TEXT,
type VARCHAR(32) NOT NULL,
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS orders_pdf;
CREATE TABLE orders_pdf (
id int(11) unsigned NOT NULL AUTO_INCREMENT,
order_id int(6) NOT NULL,
pdf_bill_nr int(5) NOT NULL,
bill_name VARCHAR(128) NOT NULL,
customer_notified int(1) NOT NULL,
notified_date date NOT NULL,
pdf_generate_date date NOT NULL,
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS commerce_seo_url_names;
CREATE TABLE commerce_seo_url_names (
id INT(4) NOT NULL auto_increment,
file_name VARCHAR(64) NOT NULL,
file_name_php VARCHAR(32) NOT NULL,
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS commerce_seo_url_personal_links;
CREATE TABLE commerce_seo_url_personal_links (
link_id INT(4) NOT NULL auto_increment,
url_text VARCHAR(128) NOT NULL,
file_name VARCHAR(64) NOT NULL,
language_id INT(2) NOT NULL,
PRIMARY KEY (link_id)
);

DROP TABLE IF EXISTS products_to_filter;
CREATE TABLE products_to_filter (
products_id int(11) NOT NULL,
filter_id int(11) NOT NULL,
PRIMARY KEY (products_id,filter_id)
);

DROP TABLE IF EXISTS product_filter_categories;
CREATE TABLE product_filter_categories (
id int(11) NOT NULL default '0',
language_id int(11) NOT NULL default '0',
titel VARCHAR(150) NOT NULL default '',
status int(1) NOT NULL default '0',
position int(11) NOT NULL default '0',
categories_ids VARCHAR(64) NOT NULL default '',
PRIMARY KEY (id,language_id)
);

DROP TABLE IF EXISTS product_filter_items;
CREATE TABLE product_filter_items (
id int(11) NOT NULL default '0',
language_id int(11) NOT NULL default '0',
filter_categories_id int(11) NOT NULL default '0',
title VARCHAR(150) NOT NULL default '',
name VARCHAR(200) NOT NULL default '',
description text NOT NULL,
status int(1) NOT NULL default '0',
position int(11) NOT NULL default '0',
PRIMARY KEY (id,language_id)
);

DROP TABLE IF EXISTS accessories;
CREATE TABLE accessories (
id int(11) NOT NULL auto_increment,
head_product_id int(11) NOT NULL,
PRIMARY KEY (id)
);


DROP TABLE IF EXISTS accessories_products;
CREATE TABLE accessories_products (
id int(11) NOT NULL AUTO_INCREMENT,
accessories_id int(11) NOT NULL,
product_id int(11) NOT NULL,
sort_order int(11) NOT NULL,
PRIMARY KEY (id)
);


DROP TABLE IF EXISTS am_config;
CREATE TABLE am_config (
am_config_id int(11) NOT NULL auto_increment,
am_type VARCHAR(10) NOT NULL,
am_class VARCHAR(2) NOT NULL,
am_title VARCHAR(30) NOT NULL,
am_db_field VARCHAR(30) NOT NULL,
PRIMARY KEY (am_config_id)
);

DROP TABLE IF EXISTS attr_profile;
CREATE TABLE attr_profile (
attr_profile_item_id int(11) NOT NULL auto_increment,
products_id VARCHAR(64) default NULL,
options_id int(11) NOT NULL,
options_values_id int(11) NOT NULL,
options_values_price decimal(15,4) NOT NULL,
price_prefix char(1) NOT NULL,
attributes_model VARCHAR(64) default NULL,
attributes_ean VARCHAR(128) default NULL,
attributes_stock int(4) default NULL,
options_values_weight decimal(15,4) NOT NULL,
weight_prefix char(1) NOT NULL,
sortorder int(11) default NULL,
attributes_vpe_status INT( 1 ) NOT NULL DEFAULT '0',
attributes_vpe INT( 11 ) NOT NULL,
attributes_vpe_value DECIMAL( 15, 4 ) NOT NULL,
attributes_shippingtime INT( 4 ) NOT NULL,
PRIMARY KEY (attr_profile_item_id)
);



DROP TABLE IF EXISTS addon_database;
CREATE TABLE addon_database (
configuration_id int(11) NOT NULL AUTO_INCREMENT,
configuration_key VARCHAR(128) NOT NULL,
configuration_value VARCHAR(255) NOT NULL,
PRIMARY KEY (configuration_id)
);


DROP TABLE IF EXISTS addon_filenames;
CREATE TABLE addon_filenames (
configuration_id int(11) NOT NULL AUTO_INCREMENT,
configuration_key VARCHAR(128) NOT NULL,
configuration_value VARCHAR(255) NOT NULL,
PRIMARY KEY (configuration_id)
);


DROP TABLE IF EXISTS addon_languages;
CREATE TABLE addon_languages (
configuration_id int(11) NOT NULL AUTO_INCREMENT,
configuration_key VARCHAR(128) NOT NULL,
configuration_value VARCHAR(255) NOT NULL,
languages_id int(11) NOT NULL,
PRIMARY KEY (configuration_id)
);


DROP TABLE IF EXISTS commerce_seo_redirect;
CREATE TABLE commerce_seo_redirect (
id INT(11) NOT NULL AUTO_INCREMENT,
old_url VARCHAR(255) NULL ,
new_url VARCHAR(255) NULL,
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS commerce_seo_404_stats;
CREATE TABLE commerce_seo_404_stats (
id INT(11) NOT NULL AUTO_INCREMENT,
search_key VARCHAR(255) NOT NULL ,
referrer VARCHAR(255) NOT NULL,
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS cseo_antispam;
CREATE TABLE cseo_antispam (
id int(10) NOT NULL AUTO_INCREMENT,
question text NOT NULL,
answer VARCHAR(255) NOT NULL,
language_id int(1) NOT NULL,
UNIQUE KEY id (id)
);

DROP TABLE IF EXISTS cseo_lang_button;
CREATE TABLE cseo_lang_button (
id int(10) NOT NULL AUTO_INCREMENT,
button VARCHAR(255) NOT NULL,
buttontext text NOT NULL,
language_id int(1) NOT NULL,
UNIQUE KEY id (id)
);

DROP TABLE IF EXISTS admin_navigation;
CREATE TABLE admin_navigation (
id int(3) NOT NULL AUTO_INCREMENT,
name VARCHAR(255) DEFAULT NULL,
title VARCHAR(255) DEFAULT NULL,
subsite VARCHAR(255) DEFAULT NULL,
filename VARCHAR(255) DEFAULT NULL,
gid int(5) DEFAULT NULL,
languages_id int(2) DEFAULT '2',
nav_set VARCHAR(255) DEFAULT NULL,
sort int(3) NOT NULL DEFAULT '1',
PRIMARY KEY (id)
);

DROP TABLE IF EXISTS cseo_configuration;
CREATE TABLE cseo_configuration (
cseo_configuration_id int(11) NOT NULL AUTO_INCREMENT,
cseo_key varchar(255) NOT NULL DEFAULT '',
cseo_value text NOT NULL,
cseo_group_id int(11) NOT NULL DEFAULT '0',
cseo_sort_order int(5) NOT NULL DEFAULT '0',
PRIMARY KEY (cseo_configuration_id),
KEY cseo_key (cseo_key)
);

DROP TABLE IF EXISTS mail_templates;
CREATE TABLE mail_templates (
id int(11) NOT NULL auto_increment,
title varchar(100) NOT NULL,
mail_text text NOT NULL,
PRIMARY KEY  (id)
);


DROP TABLE IF EXISTS intrusions;
CREATE TABLE intrusions (
  name varchar(128) NOT NULL,
  badvalue varchar(255) NOT NULL,
  page varchar(255) NOT NULL,
  tags varchar(255) NOT NULL,
  ip varchar(128) NOT NULL,
  ip2 varchar(128) NOT NULL,
  impact varchar(255) NOT NULL,
  origin varchar(255) NOT NULL,
  created date NOT NULL
);

DROP TABLE IF EXISTS commerce_seo_url;
CREATE TABLE commerce_seo_url (
  url_id int(32) NOT NULL AUTO_INCREMENT,
  url_md5 varchar(32) NOT NULL DEFAULT '',
  url_text varchar(255) NOT NULL DEFAULT '',
  products_id int(11) DEFAULT NULL,
  categories_id int(11) DEFAULT NULL,
  blog_id int(11) DEFAULT NULL,
  blog_cat int(11) DEFAULT NULL,
  content_group int(11) DEFAULT NULL,
  language_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (url_id),
  KEY url_text (url_id,url_text)
);

DROP TABLE IF EXISTS orders_pdf_delivery;
CREATE TABLE orders_pdf_delivery (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  order_id int(6) NOT NULL,
  pdf_delivery_nr int(5) NOT NULL,
  delivery_name varchar(128) NOT NULL,
  customer_notified int(1) NOT NULL,
  notified_date date NOT NULL,
  pdf_generate_date date NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS shipping_tracking;
CREATE TABLE shipping_tracking (
shipping_tracking_id INT DEFAULT '0' NOT NULL,
language_id INT DEFAULT '1' NOT NULL,
shipping_tracking_name VARCHAR(32) NOT NULL,
shipping_tracking_url TEXT NOT NULL,
shipping_tracking_text TEXT NOT NULL,
PRIMARY KEY (shipping_tracking_id, language_id),
KEY idx_shipping_tracking_name (shipping_tracking_name),
KEY language_id (language_id)
);

DROP TABLE IF EXISTS blog_cat_images;
CREATE TABLE blog_cat_images (
  id int(11) NOT NULL AUTO_INCREMENT,
  cat_id int(11) NOT NULL DEFAULT '0',
  image_nr int(11) NOT NULL DEFAULT '0',
  image varchar(125) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS blog_categories;
CREATE TABLE blog_categories (
  id int(11) NOT NULL AUTO_INCREMENT,
  categories_id int(5) NOT NULL,
  parent_id int(11) NOT NULL DEFAULT 0,
  tmid int(11) NOT NULL DEFAULT 0,
  sort_order int(11) NOT NULL DEFAULT 0,
  tmselect varchar(50) NOT NULL DEFAULT 'false',
  group_ids text NOT NULL,
  language_id int(11) NOT NULL DEFAULT 0,
  titel varchar(150) NOT NULL,
  description text NOT NULL,
  short_description text NOT NULL,
  status int(1) NOT NULL DEFAULT 0,
  position int(11) NOT NULL DEFAULT 0,
  date VARCHAR(10) NOT NULL,
  update_date varchar(10) NOT NULL,
  meta_title text,
  meta_desc text,
  meta_key text,
  PRIMARY KEY (id,language_id)
);

DROP TABLE IF EXISTS blog_com_comments;
CREATE TABLE blog_com_comments (
  id int(11) NOT NULL AUTO_INCREMENT,
  com_id int(11) NOT NULL DEFAULT '0',
  description text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  date varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS blog_comment;
CREATE TABLE blog_comment (
  id int(3) NOT NULL AUTO_INCREMENT,
  blog_id int(11) NOT NULL,
  name varchar(150) NOT NULL DEFAULT '',
  text text,
  date varchar(10) NOT NULL,
  comment_status int(1) NOT NULL DEFAULT '0',
  comment_rating int(1) DEFAULT NULL,
  comment_read int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS blog_item_article;
CREATE TABLE blog_item_article (
  id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) NOT NULL DEFAULT '0',
  art_id int(11) DEFAULT '0',
  position int(11) DEFAULT '0',
  language_id int(11) DEFAULT '0',
  name varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS blog_item_images;
CREATE TABLE blog_item_images (
  id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) NOT NULL DEFAULT '0',
  slid_id int(11) NOT NULL DEFAULT '0',
  position int(11) NOT NULL DEFAULT '0',
  image_nr int(11) NOT NULL DEFAULT '0',
  image text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  width varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'auto',
  height varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'auto',
  floating varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'left',
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS blog_item_item;
CREATE TABLE blog_item_item (
  id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) NOT NULL DEFAULT '0',
  bitem_id int(11) DEFAULT '0',
  position int(11) DEFAULT '0',
  language_id int(11) DEFAULT '0',
  name varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS blog_item_kat;
CREATE TABLE blog_item_kat (
  id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) NOT NULL DEFAULT '0',
  kat_id int(11) NOT NULL DEFAULT '0',
  position int(11) NOT NULL DEFAULT '0',
  language_id int(11) NOT NULL DEFAULT '0',
  name varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS blog_item_text;
CREATE TABLE blog_item_text (
  id int(11) NOT NULL AUTO_INCREMENT,
  tblock_id int(11) NOT NULL DEFAULT '0',
  item_id int(11) DEFAULT '0',
  language_id int(11) DEFAULT '0',
  position int(11) DEFAULT '0',
  description text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS blog_items;
CREATE TABLE blog_items (
  id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(5) NOT NULL,
  language_id int(11) NOT NULL DEFAULT '0',
  categories_id int(11) NOT NULL DEFAULT '0',
  title varchar(150) NOT NULL DEFAULT '',
  name varchar(200) NOT NULL DEFAULT '',
  description text NOT NULL,
  group_ids text NOT NULL,
  shortdesc text NOT NULL,
  status int(1) NOT NULL DEFAULT '0',
  position int(11) NOT NULL DEFAULT '0',
  date varchar(10) NOT NULL,
  date_update varchar(10) NOT NULL,
  date2 date NOT NULL,
  date_release DATETIME NULL,
  date_out DATETIME NULL,
  meta_title text,
  meta_keywords text,
  meta_description text,
  lenght int(5) DEFAULT NULL,
  item_viewed int(10) NOT NULL DEFAULT '0',
  blog_image VARCHAR( 254 ) NULL,
  PRIMARY KEY (id,language_id)
);

DROP TABLE IF EXISTS blog_settings;
CREATE TABLE blog_settings (
  id int(11) NOT NULL AUTO_INCREMENT,
  blog_key varchar(20) DEFAULT NULL,
  wert varchar(20) DEFAULT NULL,
  type varchar(20) NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS blog_start;
CREATE TABLE blog_start (
  id int(1) NOT NULL DEFAULT '0',
  language_id int(11) NOT NULL DEFAULT '0',
  description text NOT NULL,
  group_ids text NOT NULL,
  meta_title text NOT NULL,
  meta_description text NOT NULL,
  meta_keywords text NOT NULL,
  date datetime NOT NULL,
  PRIMARY KEY (id,language_id)
);

DROP TABLE IF EXISTS blog_start_images;
CREATE TABLE blog_start_images (
  id int(11) NOT NULL AUTO_INCREMENT,
  start_id int(11) NOT NULL DEFAULT '1',
  image_nr int(11) NOT NULL DEFAULT '0',
  image varchar(125) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS cseo_share;
CREATE TABLE IF NOT EXISTS cseo_share (
  id int(10) NOT NULL AUTO_INCREMENT,
  share text NOT NULL,
  count int(10) NOT NULL,
  site varchar(255) NOT NULL,
  time int(14) NOT NULL,
  UNIQUE KEY id (id)
);

DROP TABLE IF EXISTS cseo_slider_gallery;
CREATE TABLE cseo_slider_gallery (
  slider_id int(11) NOT NULL AUTO_INCREMENT,
  slider_title varchar(255) NOT NULL,
  slider_url varchar(255) NOT NULL,
  slider_url_2 varchar(255) NOT NULL,
  slider_url_3 varchar(255) NOT NULL,
  slider_url_4 varchar(255) NOT NULL,
  slider_url_5 varchar(255) NOT NULL,
  slider_image varchar(64) NOT NULL,
  slider_image_2 varchar(64) NOT NULL,
  slider_image_3 varchar(255) NOT NULL,
  slider_image_4 varchar(255) NOT NULL,
  slider_image_5 varchar(255) NOT NULL,
  slider_link_text varchar(255) NOT NULL,
  slider_link_text_2 varchar(255) NOT NULL,
  slider_link_text_3 varchar(255) NOT NULL,
  slider_link_text_4 varchar(255) NOT NULL,
  slider_link_text_5 varchar(255) NOT NULL,
  slider_desc text,
  slider_desc_2 text,
  slider_desc_3 text,
  slider_desc_4 text,
  slider_desc_5 text,
  slider_text text,
  date_added datetime NOT NULL,
  date_status_change datetime DEFAULT NULL,
  status int(1) NOT NULL DEFAULT '1',
  fullsize int(1) NOT NULL DEFAULT '1',
  language_id int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (slider_id)
);


DROP TABLE IF EXISTS orders_products_properties;
CREATE TABLE IF NOT EXISTS orders_products_properties (
  orders_products_properties_id int(10) unsigned NOT NULL auto_increment,
  orders_products_id int(10) unsigned default NULL,
  products_properties_combis_id int(10) unsigned default NULL,
  properties_name varchar(255) NOT NULL,
  values_name varchar(255) NOT NULL,
  properties_price_type varchar(8) NOT NULL,
  properties_price decimal(16,4) NOT NULL,
  PRIMARY KEY  (orders_products_properties_id)
);

DROP TABLE IF EXISTS products_properties_admin_select;
CREATE TABLE  products_properties_admin_select (
  products_properties_admin_select_id int(11) NOT NULL auto_increment,
  products_id int(11) NOT NULL,
  properties_id int(11) NOT NULL,
  properties_values_id int(11) NOT NULL,
  PRIMARY KEY  (products_properties_admin_select_id),
  KEY products_id (products_id)
);

DROP TABLE IF EXISTS products_properties_combis;
CREATE TABLE products_properties_combis (
  products_properties_combis_id int(10) unsigned NOT NULL auto_increment,
  products_id int(10) unsigned NOT NULL,
  sort_order int(10) unsigned NOT NULL,
  combi_model varchar(64) NOT NULL,
  combi_quantity_type enum('','plus','minus','fix') NULL,
  combi_quantity int(10) unsigned NOT NULL,
  combi_shipping_status_id int(11) NOT NULL,
  combi_weight decimal(15,4) NOT NULL,
  combi_price_type enum('plus','minus','fix') NOT NULL,
  combi_price decimal(15,4) NOT NULL,
  combi_image varchar(255) NOT NULL,
  products_vpe_id int(11) NOT NULL,
  vpe_value decimal(16,4) NOT NULL,
  PRIMARY KEY  (products_properties_combis_id),
  KEY products_properties_combis_id (products_properties_combis_id,products_id,sort_order),
  KEY products_id (products_id,sort_order)
);

DROP TABLE IF EXISTS products_properties_combis_values;
CREATE TABLE products_properties_combis_values (
  products_properties_combis_values_id int(10) unsigned NOT NULL auto_increment,
  products_properties_combis_id int(10) unsigned NOT NULL,
  properties_values_id int(10) unsigned default NULL,
  PRIMARY KEY  (products_properties_combis_values_id),
  KEY products_properties_combis_values_id (products_properties_combis_values_id,products_properties_combis_id,properties_values_id),
  KEY products_properties_combis_id (products_properties_combis_id,properties_values_id),
  KEY properties_values_id (properties_values_id,products_properties_combis_id)
);

DROP TABLE IF EXISTS products_properties_index;
CREATE TABLE IF NOT EXISTS products_properties_index (
  products_id int(10) NOT NULL,
  language_id int(10) NOT NULL,
  properties_id int(10) NOT NULL,
  products_properties_combis_id int(10) default '0',
  properties_values_id int(10) default NULL,
  properties_name varchar(255) default NULL,
  properties_sort_order int(10) NOT NULL,
  values_name varchar(255) default NULL,
  value_sort_order int(10) default NULL,
  KEY products_id (products_id,language_id,products_properties_combis_id),
  KEY products_id_2 (products_id,language_id,properties_id)
);

DROP TABLE IF EXISTS properties;
CREATE TABLE properties (
  properties_id int(10) unsigned NOT NULL auto_increment,
  sort_order int(10) unsigned NOT NULL,
  PRIMARY KEY  (properties_id),
  KEY properties_id (properties_id,sort_order)
);

DROP TABLE IF EXISTS properties_description;
CREATE TABLE properties_description (
  properties_description_id int(10) unsigned NOT NULL auto_increment,
  properties_id int(10) unsigned NOT NULL,
  language_id int(10) unsigned NOT NULL,
  properties_name varchar(255) NOT NULL,
  properties_admin_name varchar(255) NOT NULL,
  PRIMARY KEY  (properties_description_id),
  KEY properties_id (properties_id,language_id)
);

DROP TABLE IF EXISTS properties_values;
CREATE TABLE properties_values (
  properties_values_id int(10) unsigned NOT NULL auto_increment,
  properties_id int(10) unsigned NOT NULL,
  sort_order int(10) unsigned NOT NULL,
  value_model varchar(64) NOT NULL,
  value_price_type enum('plus','minus','fix') NOT NULL,
  value_price decimal(9,4) NOT NULL,
  PRIMARY KEY  (properties_values_id),
  KEY properties_values_id (properties_values_id,properties_id,sort_order),
  KEY properties_id (properties_id,sort_order)
);

DROP TABLE IF EXISTS properties_values_description;
CREATE TABLE properties_values_description (
  properties_values_description_id int(10) unsigned NOT NULL auto_increment,
  properties_values_id int(10) unsigned NOT NULL,
  language_id int(10) unsigned NOT NULL,
  values_name varchar(255) NOT NULL,
  values_image varchar(255) NOT NULL,
  PRIMARY KEY  (properties_values_description_id),
  KEY properties_values_description_id (properties_values_description_id,properties_values_id,language_id),
  KEY properties_values_id (properties_values_id,language_id)
);

DROP TABLE IF EXISTS rma;
CREATE TABLE rma (
rma_id int(11) NOT NULL auto_increment,
customers_id int(11) NOT NULL default '0',
orders_id int(11) NOT NULL default '0',
products_id int(11) NOT NULL default '0',
products_ean varchar(20) default NULL,
reason_id int(11) NOT NULL default '0',
description longtext NOT NULL,
rma_date datetime NOT NULL default '0000-00-00 00:00:00',
pickup smallint(1) default NULL,
shipping_time varchar(10) default NULL,
rma_status_id tinyint(1) NOT NULL default '1',
cost_estimate smallint(1) default NULL,
PRIMARY KEY (rma_id)
);

DROP TABLE IF EXISTS rma_comments;
CREATE TABLE rma_comments (
rma_comments_id int(11) NOT NULL auto_increment,
rma_id int(11) NOT NULL default '0',
rma_status_id tinyint(2) NOT NULL default '0',
comments text NOT NULL,
edit_date datetime NOT NULL default '0000-00-00 00:00:00',
PRIMARY KEY (rma_comments_id)
);

DROP TABLE IF EXISTS rma_reason;
CREATE TABLE rma_reason (
rma_reason_id int(11) NOT NULL default '0',
language_id int(11) NOT NULL default '1',
rma_reason_name varchar(64) NOT NULL default '',
PRIMARY KEY (rma_reason_id,language_id),
KEY idx_rma_reason_name (rma_reason_name)
);

DROP TABLE IF EXISTS rma_status;
CREATE TABLE rma_status (
rma_status_id int(11) NOT NULL default '0',
language_id int(11) NOT NULL default '1',
rma_status_name varchar(64) NOT NULL default '',
PRIMARY KEY (rma_status_id,language_id),
KEY idx_rma_status_name (rma_status_name)
);

DROP TABLE IF EXISTS rma_templates;
CREATE TABLE rma_templates (
rma_template_id int(11) NOT NULL default '0',
language_id int(11) NOT NULL default '1',
rma_template_name varchar(64) NOT NULL default '',
rma_template_text TEXT NOT NULL default '',
PRIMARY KEY (rma_template_id,language_id),
KEY idx_rma_template_name (rma_template_name)
);

DROP TABLE IF EXISTS specials_gratis;
CREATE TABLE specials_gratis (
  specials_gratis_id int(11) NOT NULL AUTO_INCREMENT,
  products_id int(11) NOT NULL DEFAULT '0',
  specials_gratis_quantity int(15) NOT NULL DEFAULT '0',
  specials_gratis_new_products_price decimal(15,4) NOT NULL DEFAULT '0.0000',
  specials_gratis_min_price decimal(15,4) NOT NULL DEFAULT '0.0000',
  specials_gratis_max_value int(11) NOT NULL DEFAULT '1',
  specials_gratis_ab_value int(11) NOT NULL DEFAULT '1',
  categories_id int(11) NOT NULL DEFAULT '1',
  manufacturers_id int(1) NOT NULL DEFAULT '1',
  specials_gratis_date_added datetime DEFAULT NULL,
  specials_gratis_last_modified datetime DEFAULT NULL,
  expires_date datetime DEFAULT NULL,
  date_status_change datetime DEFAULT NULL,
  status int(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (specials_gratis_id),
  KEY products_id (products_id,status,specials_gratis_date_added),
  KEY status (status,expires_date)
);

DROP TABLE IF EXISTS specials_gratis_description;
CREATE TABLE specials_gratis_description (
  specials_gratis_id INT DEFAULT '0' NOT NULL,
  specials_gratis_description text NOT NULL,
  language_id int(11) NOT NULL,
  FULLTEXT (specials_gratis_description)
);

DROP TABLE IF EXISTS withdrawals;
CREATE TABLE withdrawals (
  withdrawal_id int(11) NOT NULL AUTO_INCREMENT,
  order_id int(11) NOT NULL,
  customer_id int(11) NOT NULL,
  customer_gender varchar(16) NOT NULL,
  customer_firstname varchar(255) NOT NULL,
  customer_lastname varchar(255) NOT NULL,
  customer_street_address varchar(255) NOT NULL,
  customer_postcode varchar(255) NOT NULL,
  customer_city varchar(255) NOT NULL,
  customer_country varchar(255) NOT NULL,
  customer_email varchar(255) NOT NULL,
  order_date varchar(12) NOT NULL,
  delivery_date varchar(12) NOT NULL,
  withdrawal_date varchar(12) NOT NULL,
  withdrawal_content text NOT NULL,
  date_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  created_by_admin tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (withdrawal_id)
);

