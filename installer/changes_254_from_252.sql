
INSERT INTO configuration VALUES (NULL, 'ADDPAGINATION', 'true', 16, 4, NULL, NOW(), NULL, 'xtc_cfg_select_option(array(''true'', ''false''),');

INSERT INTO configuration VALUES (NULL, 'AJAXJQUERYUI', 'true', 1000, 18, NULL, NOW(), NULL, 'xtc_cfg_select_option(array(''true'', ''false''),');
INSERT INTO configuration VALUES (NULL, 'AJAXCOLORBOX', 'true', 1000, 19, NULL, NOW(), NULL, 'xtc_cfg_select_option(array(''true'', ''false''),');
INSERT INTO configuration VALUES (NULL, 'AJAXFLEXNAV', 'true', 1000, 20, NULL, NOW(), NULL, 'xtc_cfg_select_option(array(''true'', ''false''),');
INSERT INTO configuration VALUES (NULL, 'AJAXJZOOM', 'true', 1000, 21, NULL, NOW(), NULL, 'xtc_cfg_select_option(array(''true'', ''false''),');
INSERT INTO configuration VALUES (NULL, 'AJAXRESPTABS', 'false', 1000, 22, NULL, NOW(), NULL, 'xtc_cfg_select_option(array(''true'', ''false''),');
INSERT INTO configuration VALUES (NULL, 'AJAXJYOUTUBE', 'false', 1000, 23, NULL, NOW(), NULL, 'xtc_cfg_select_option(array(''true'', ''false''),');
INSERT INTO configuration VALUES (NULL, 'AJAXRESPSLIDE', 'true', 1000, 24, NULL, NOW(), NULL, 'xtc_cfg_select_option(array(''true'', ''false''),');
INSERT INTO configuration VALUES (NULL, 'AJAXUNSLIDER', 'false', 1000, 25, NULL, NOW(), NULL, 'xtc_cfg_select_option(array(''true'', ''false''),');
INSERT INTO configuration VALUES (NULL, 'AJAXBOOTSTRAP', 'true', 1000, 26, NULL, NOW(), NULL, 'xtc_cfg_select_option(array(''true'', ''false''),');
ALTER TABLE blog_items ADD date_release DATETIME NULL;
ALTER TABLE blog_items ADD date_out DATETIME NULL;
ALTER TABLE products_attributes ADD options_values_scale_price VARCHAR(128) NOT NULL AFTER options_values_price;

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
INSERT INTO rma_reason VALUES (1, 1, 'Product defectively'), (1, 2,
'Ware defekt'),
(2, 1, 'Wrong delivery'),
(2, 2, 'Falschlieferung');


DROP TABLE IF EXISTS rma_status;
CREATE TABLE rma_status (
rma_status_id int(11) NOT NULL default '0',
language_id int(11) NOT NULL default '1',
rma_status_name varchar(64) NOT NULL default '',
PRIMARY KEY (rma_status_id,language_id),
KEY idx_rma_status_name (rma_status_name)
);
INSERT INTO rma_status VALUES (1, 1, 'Open'),
(1, 2, 'Offen'),
(2, 1, 'In Processing'),
(2, 2, 'In Bearbeitung'),
(3, 1, 'Sent'),
(3, 2, 'Versendet');

DROP TABLE IF EXISTS rma_templates;
CREATE TABLE rma_templates (
rma_template_id int(11) NOT NULL default '0',
language_id int(11) NOT NULL default '1',
rma_template_name varchar(64) NOT NULL default '',
rma_template_text TEXT NOT NULL default '',
PRIMARY KEY (rma_template_id,language_id),
KEY idx_rma_template_name (rma_template_name)
);
INSERT INTO rma_templates VALUES 
(1, 2, 'Vorlage1', 'Das ist mein Text ...'),
(1, 1, 'Template1', 'Das ist mein Text ...'),
(2, 2, 'Vorlage2', 'Das ist mein Text Numero 2 ...'),
(2, 1, 'Template2', 'Das ist mein Text Numero 2 ...'),
(3, 2, 'Vorlage3', 'Das ist mein Text Numero 4 ...'),
(3, 1, 'Template3', 'Das ist mein Text Numero 4 ...');

ALTER TABLE  products ADD  products_maxorder INT( 5 ) NULL AFTER  products_minorder ;
INSERT INTO configuration ( configuration_id , configuration_key , configuration_value , configuration_group_id , sort_order , last_modified , date_added , use_function , set_function ) 
VALUES ('','RMA_CHOOSE_PRODUCTS_OBLIGATION', 'true', 1005, 5, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'), 
('','ENTRY_RMA_ERROR_MESSAGE_MIN_LENGTH', '50', 1005, 9, NULL, '0000-00-00 00:00:00', NULL, NULL), 
('','RMA_PRODUCTS_EAN_OBLIGATION', 'false', 1005, 7, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'), 
('','RMA_MODUL_ON', 'true', 1005, 1, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'), 
('','RMA_PRODUCTS_EAN_SHOW', 'true', 1005, 6, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'), 
('','RMA_ERROR_MESSAGE_SHOW', 'true', 1005, 8, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'), 
('','RMA_CHOOSE_REASON_OBLIGATION', 'true', 1005, 10, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'), 
('','RMA_PICK_UP_SHOW', 'true', 1005, 14, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),'), 
('','RMA_COST_ESTIMATE_SHOW', 'true', 1005, 16, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''true'', ''false''),');


INSERT INTO configuration ( configuration_id , configuration_key , configuration_value , configuration_group_id , sort_order , last_modified , date_added , use_function , set_function ) 
VALUES (NULL, 'PRODUCT_IMAGE_NO_ENLARGE_UNDER_DEFAULT', 'false', '4', 1, NULL, NOW(), NULL, 'xtc_cfg_select_option(array(\'true\', \'false\'), ');

ALTER TABLE admin_access ADD cseo_rma INT( 1 ) NOT NULL DEFAULT '0';
UPDATE admin_access SET cseo_rma = 1;

INSERT INTO admin_navigation VALUES(NULL, 'cseo_rma', 'Wiederrufsanfragen', 'customers', 'cseo_rma.php', NULL, 2, NULL, 11);
INSERT INTO admin_navigation VALUES(NULL, 'cseo_rma', 'Wiederrufsanfragen', 'customers', 'cseo_rma.php', NULL, 1, NULL, 11);

ALTER TABLE admin_access ADD COLUMN specials_gratis INT(1) NOT NULL DEFAULT '0';
UPDATE admin_access SET specials_gratis=1 WHERE  customers_id='1' LIMIT 1;

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

INSERT INTO admin_navigation (id, name, title, subsite, filename, gid, languages_id, nav_set, sort) VALUES (NULL, 'specials_gratis', 'Gratisartikel', 'products', 'specials_gratis.php', NULL, 2, NULL, 5);
INSERT INTO admin_navigation (id, name, title, subsite, filename, gid, languages_id, nav_set, sort) VALUES (NULL, 'specials_gratis', 'Specials-Free', 'products', 'specials_gratis.php', NULL, 1, NULL, 5);
INSERT INTO configuration VALUES (NULL, 'CHECKOUT_ATTACH', 'false', 333, 30, NULL, NOW(), NULL, "xtc_cfg_select_option(array('true', 'false'),");
INSERT INTO configuration VALUES (NULL, 'CHECKOUT_ATTACH_FILE1', '', 333, 31, NULL, NOW(), NULL, NULL);
INSERT INTO configuration VALUES (NULL, 'CHECKOUT_ATTACH_FILE2', '', 333, 32, NULL, NOW(), NULL, NULL);
INSERT INTO configuration VALUES (NULL, 'PARTNER_SELLER_ACTIVE', 'false', 19, 100, NULL, NOW(), NULL, "xtc_cfg_select_option(array('true', 'false'),");
INSERT INTO configuration VALUES (NULL, 'PARTNER_SELLER_PATH', '', 19, 101, NULL, NOW( ) , NULL, NULL);
INSERT INTO configuration VALUES (NULL, 'PARTNER_SELLER_PWD', '', 19, 102, NULL, NOW( ) , NULL, NULL);
INSERT INTO configuration VALUES (NULL, 'PARTNER_SELLER_CHK', '', 19, 103, NULL, NOW( ) , NULL, NULL);
INSERT INTO configuration VALUES (NULL, 'GRATISARTIKEL_OPTION', 'select', 17, 100, NULL, NOW(), NULL, "xtc_cfg_select_option(array('select', 'radio'),");
UPDATE database_version SET version = 'commerce:SEO v2next 2.5.4 CE';

