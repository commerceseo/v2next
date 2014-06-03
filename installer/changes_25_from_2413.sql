ALTER TABLE  categories_description ADD  categories_contents INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  categories_description ADD  categories_blogs INT( 11 ) NOT NULL DEFAULT  '0';

INSERT INTO configuration VALUES (NULL, 'CSV_TIME_LIMIT', '28', '20', '4', NULL, NOW( ) , NULL, NULL);
INSERT INTO configuration VALUES (NULL, 'CSV_DEFAULT_ACTION', 'ignore', '20', '5', NULL, NOW( ), NULL , 'xtc_cfg_select_option(array(\'ignore\', \'insert\'),');
INSERT INTO configuration VALUES (NULL, 'CHECKOUT_LOGIN_ALLOW', 'true', 333, 0, NULL, NOW(), NULL, "xtc_cfg_select_option(array('true', 'false'),");

UPDATE configuration SET configuration_group_id =  '5', sort_order =  '10' WHERE configuration_key = 'TRUSTED_SHOP_PASSWORD_EMAIL';

INSERT INTO configuration VALUES (NULL, 'ACCOUNT_AGE_VERIFICATION', 'false', 5, 12, NULL, NOW(), NULL, "xtc_cfg_select_option(array('true', 'false'),");
INSERT INTO configuration VALUES (NULL, 'ACCOUNT_MIN_AGE', '18', 5, 13, NULL, NOW(), NULL, '');

ALTER TABLE products ADD products_forbidden_shipping TEXT ; 

ALTER TABLE admin_access ADD csv_import_export INT( 1 ) NOT NULL DEFAULT '0';
UPDATE admin_access SET csv_import_export = 1;

INSERT INTO admin_navigation VALUES(NULL, 'csv_import_export', 'Erweiterter Import/Export', 'tools', 'csv_import_export.php', NULL, 2, NULL, 11);
INSERT INTO admin_navigation VALUES(NULL, 'csv_import_export', 'Advanced Import/Export', 'tools', 'csv_import_export.php', NULL, 1, NULL, 11);

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
ALTER TABLE admin_access ADD shipping_tracking INT( 1 ) NOT NULL DEFAULT '0';
UPDATE admin_access SET shipping_tracking = 1;

INSERT INTO admin_navigation VALUES(NULL, 'shipping_tracking', 'Versand Tracking', 'config', 'shipping_tracking.php', NULL, 2, NULL, 11);
INSERT INTO admin_navigation VALUES(NULL, 'shipping_tracking', 'Shipping Tracking', 'config', 'shipping_tracking.php', NULL, 1, NULL, 11);
INSERT INTO shipping_tracking VALUES 
(1, 1, 'DPD', '<a href="http://extranet.dpd.de/cgi-bin/delistrack?pknr={$SHIPPING_TRACKING_ID}">http://extranet.dpd.de/cgi-bin/delistrack?pknr={$SHIPPING_TRACKING_ID}</a>', 'Wir haben das Paket mit DPD verschickt. Das Paket hat die Tracking-Nummer <strong>{$SHIPPING_TRACKING_ID}</strong>.<br /><br />Sie können den <em>Paketstatus</em> über folgende Internetseite verfolgen:<br />'), 
(2, 1, 'DHL', '<a href="http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc={$SHIPPING_TRACKING_ID}">http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc={$SHIPPING_TRACKING_ID}</a>', 'Wir haben das Paket mit DHL verschickt. Das Paket hat die Tracking-Nummer <strong>{$SHIPPING_TRACKING_ID}</strong>.<br /><br />Sie können den <em>Paketstatus</em> über folgende Internetseite verfolgen:<br />'), 
(3, 1, 'DP', '<a href="https://www.deutschepost.de/sendungsstatus/bzl/sendung/simpleQueryResult.do?sendungsnummer={$SHIPPING_TRACKING_ID}">https://www.deutschepost.de/sendungsstatus/bzl/sendung/simpleQueryResult.do?sendungsnummer={$SHIPPING_TRACKING_ID}</a>', 'Wir haben das Paket mit der deutschen Post verschickt. Das Paket hat die Tracking-Nummer <strong>{$SHIPPING_TRACKING_ID}</strong>.<br /><br />Sie können den <em>Paketstatus</em> über folgende Internetseite verfolgen:<br />'); 

INSERT INTO shipping_tracking VALUES 
(1, 2, 'DPD', '<a href="http://extranet.dpd.de/cgi-bin/delistrack?pknr={$SHIPPING_TRACKING_ID}">http://extranet.dpd.de/cgi-bin/delistrack?pknr={$SHIPPING_TRACKING_ID}</a>', 'Wir haben das Paket mit DPD verschickt. Das Paket hat die Tracking-Nummer <strong>{$SHIPPING_TRACKING_ID}</strong>.<br /><br />Sie können den <em>Paketstatus</em> über folgende Internetseite verfolgen:<br />'), 
(2, 2, 'DHL', '<a href="http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc={$SHIPPING_TRACKING_ID}">http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc={$SHIPPING_TRACKING_ID}</a>', 'Wir haben das Paket mit DHL verschickt. Das Paket hat die Tracking-Nummer <strong>{$SHIPPING_TRACKING_ID}</strong>.<br /><br />Sie können den <em>Paketstatus</em> über folgende Internetseite verfolgen:<br />'), 
(3, 2, 'DP', '<a href="https://www.deutschepost.de/sendungsstatus/bzl/sendung/simpleQueryResult.do?sendungsnummer={$SHIPPING_TRACKING_ID}">https://www.deutschepost.de/sendungsstatus/bzl/sendung/simpleQueryResult.do?sendungsnummer={$SHIPPING_TRACKING_ID}</a>', 'Wir haben das Paket mit der deutschen Post verschickt. Das Paket hat die Tracking-Nummer <strong>{$SHIPPING_TRACKING_ID}</strong>.<br /><br />Sie können den <em>Paketstatus</em> über folgende Internetseite verfolgen:<br />'); 

INSERT INTO configuration VALUES (NULL, 'DEFAULT_SHIPPING_TRACKING_ID', '1', 6, 0, NULL, NOW(), NULL, NULL);
ALTER TABLE  orders ADD  order_tracking_id VARCHAR( 255 ) NULL ;
ALTER TABLE  orders ADD  order_delivery_id INT( 5 ) NULL ;
INSERT INTO configuration VALUES (NULL, 'ATTRIBUTE_REQUIRED', 'false', 17, 12, NULL, NOW(), NULL, "xtc_cfg_select_option(array('true', 'false'),");

ALTER TABLE  blog_categories ADD  tmid INT( 11 ) NOT NULL DEFAULT  '0' AFTER  parent_id ;
ALTER TABLE  blog_categories ADD  sort_order INT( 11 ) NOT NULL DEFAULT  '0' AFTER  tmid ;
ALTER TABLE  blog_categories ADD  tmselect VARCHAR( 50 ) NOT NULL DEFAULT  'false' AFTER  sort_order ;
ALTER TABLE  blog_categories ADD  group_ids TEXT NOT NULL AFTER  tmselect ;

DROP TABLE IF EXISTS blog_cat_images;
CREATE TABLE IF NOT EXISTS blog_cat_images (
  id int(11) NOT NULL AUTO_INCREMENT,
  cat_id int(11) NOT NULL DEFAULT '0',
  image_nr int(11) NOT NULL DEFAULT '0',
  image varchar(125) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS blog_com_comments;
CREATE TABLE IF NOT EXISTS blog_com_comments (
  id int(11) NOT NULL AUTO_INCREMENT,
  com_id int(11) NOT NULL DEFAULT '0',
  description text COLLATE utf8_unicode_ci NOT NULL,
  date varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE  blog_items ADD  group_ids TEXT NOT NULL AFTER  description ;
ALTER TABLE  blog_items ADD  item_viewed INT(10) NOT NULL DEFAULT  '0';
DROP TABLE IF EXISTS blog_item_article;
CREATE TABLE IF NOT EXISTS blog_item_article (
  id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) NOT NULL DEFAULT '0',
  art_id int(11) DEFAULT '0',
  position int(11) DEFAULT '0',
  language_id int(11) DEFAULT '0',
  name varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS blog_item_images;
CREATE TABLE IF NOT EXISTS blog_item_images (
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
CREATE TABLE IF NOT EXISTS blog_item_item (
  id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) NOT NULL DEFAULT '0',
  bitem_id int(11) DEFAULT '0',
  position int(11) DEFAULT '0',
  language_id int(11) DEFAULT '0',
  name varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS blog_item_kat;
CREATE TABLE IF NOT EXISTS blog_item_kat (
  id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) NOT NULL DEFAULT '0',
  kat_id int(11) NOT NULL DEFAULT '0',
  position int(11) NOT NULL DEFAULT '0',
  language_id int(11) NOT NULL DEFAULT '0',
  name varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (id)
);

DROP TABLE IF EXISTS blog_item_text;
CREATE TABLE IF NOT EXISTS blog_item_text (
  id int(11) NOT NULL AUTO_INCREMENT,
  tblock_id int(11) NOT NULL DEFAULT '0',
  item_id int(11) DEFAULT '0',
  language_id int(11) DEFAULT '0',
  position int(11) DEFAULT '0',
  description text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY (id)
);

ALTER TABLE  blog_settings ADD  type VARCHAR( 20 ) NOT NULL ;

DELETE FROM blog_settings;
INSERT INTO blog_settings (id, blog_key, wert, type) VALUES
	(1, 'social', 'ja', 'radio'),
	(2, 'comments', 'ja', 'radio'),
	(3, 'rate', 'ja', 'radio'),
	(4, 'register_user', 'ja', 'radio'),
	(5, 'session_rate', 'nein', 'radio'),
	(6, 'blog_nav_ajax', 'ja', 'radio'),
	(7, 'blog_captcha', 'ja', 'radio'),
	(8, 'pic_start', '6', 'text'),
	(9, 'pic_cat', '8', 'text'),
	(10, 'pic_item', '13', 'text');

ALTER TABLE  blog_start ADD  group_ids TEXT NOT NULL AFTER  description ,
ADD  meta_title TEXT NOT NULL AFTER  group_ids ,
ADD  meta_description TEXT NOT NULL AFTER  meta_title ,
ADD  meta_keywords TEXT NOT NULL AFTER  meta_description ;

DROP TABLE IF EXISTS blog_start_images;
CREATE TABLE IF NOT EXISTS blog_start_images (
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

ALTER TABLE admin_access ADD blog_edit_block INT( 1 ) NOT NULL DEFAULT '0';
UPDATE admin_access SET blog_edit_block = 1 WHERE customers_id = '1';

DELETE FROM configuration WHERE configuration_key = 'HTTP_CACHING';
INSERT INTO cseo_configuration (cseo_configuration_id, cseo_key, cseo_value, cseo_group_id, cseo_sort_order) VALUES (NULL, 'CSEO_LOGO', 'logo.png', 0, 0);

INSERT INTO configuration VALUES (NULL, 'ACCOUNT_TELEFON', 'true', 5, 12, NULL, NOW(), NULL,"xtc_cfg_select_option(array('true', 'false'),");
INSERT INTO configuration VALUES (NULL, 'ACCOUNT_FAX', 'false', 5, 13, NULL, NOW(), NULL,"xtc_cfg_select_option(array('true', 'false'),");
INSERT INTO configuration VALUES (NULL, 'GOOGLE_ANALYTICS_ANONYMI', 'true', 361, 2, NULL, NOW(), NULL, "xtc_cfg_select_option(array('true', 'false'),");
INSERT INTO configuration VALUES (NULL, 'GOOGLE_ANALYTICS_DOMAIN', '', 361, 4, NULL, NOW(), NULL, '');

DELETE FROM configuration WHERE configuration_key = 'GOOGLE_ANAL_CODE_BASE';
DELETE FROM configuration WHERE configuration_key = 'ETRACKER_ON';
DELETE FROM configuration WHERE configuration_key = 'ETRACKER_CODE';
UPDATE configuration SET use_function = NULL WHERE configuration_key = 'GOOGLE_ANAL_CODE';

INSERT INTO configuration VALUES (NULL, 'PRODUCT_LIST_VIEW_AS', 'true', 8, 4, NULL, NOW(), NULL, "xtc_cfg_select_option(array('true', 'false'),");
INSERT INTO configuration VALUES (NULL, 'PRODUCT_LIST_VIEW_PER_SITE', 'true', 8, 5, NULL, NOW(), NULL, "xtc_cfg_select_option(array('true', 'false'),");

ALTER TABLE admin_access ADD cseo_gallery_manager INT( 1 ) NOT NULL DEFAULT '0';
UPDATE admin_access SET cseo_gallery_manager = 1;

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


INSERT INTO configuration VALUES (NULL, 'CSS_FRONTEND_BACKGROUND', '', 23, 31, NULL, NOW(), NULL, NULL);
INSERT INTO configuration VALUES (NULL, 'CSS_FRONTEND_BACKGROUND_1', '', 23, 32, NULL, NOW(), NULL, NULL);
INSERT INTO configuration VALUES (NULL, 'CSS_FRONTEND_BACKGROUND_2', '', 23, 33, NULL, NOW(), NULL, NULL);
INSERT INTO configuration VALUES (NULL, 'CSS_FRONTEND_BOX_HEADER_BACKGROUND', '', 23, 34, NULL, NOW(), NULL, NULL);
ALTER TABLE  boxes ADD  mobile INT( 1 ) NOT NULL DEFAULT  '1' AFTER  file_flag ;
ALTER TABLE  content_manager ADD  slider_set INT( 64 ) NOT NULL;

ALTER TABLE categories_description ADD slider_set INT( 64 ) NOT NULL;

INSERT INTO admin_navigation VALUES(NULL, 'cseo_gallery_manager', 'Slider Manager', 'tools', 'cseo_gallery_manager.php', NULL, 2, NULL, 12);
INSERT INTO admin_navigation VALUES(NULL, 'cseo_gallery_manager', 'Slider Manager', 'tools', 'cseo_gallery_manager.php', NULL, 1, NULL, 12);

UPDATE database_version SET version = 'commerce:SEO v2next 2.5.2 CE';
