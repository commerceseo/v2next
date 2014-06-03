
INSERT INTO configuration (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) 
VALUES 
(NULL, 'WK_CSS_BUTTON_BACKGROUND_PIC', '', 23, 3, NULL, '2013-05-06 09:55:05', NULL, 'xtc_cfg_pull_down_css_wk_bg_pic_sets(');
INSERT INTO configuration (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) 
VALUES 
(NULL, 'WK_CSS_BUTTON_HOVER_BACKGROUND_PIC', '', 23, 3, NULL, '2013-05-06 09:55:05', NULL, 'xtc_cfg_pull_down_css_wk_bg_pic_hover_sets(');

ALTER TABLE admin_access ADD cseo_logo INT( 1 ) NOT NULL DEFAULT '0';
UPDATE admin_access SET cseo_logo = 1 WHERE module_export = 1;
ALTER TABLE attr_profile ADD attributes_vpe_status INT( 1 ) NOT NULL DEFAULT '0', ADD attributes_vpe INT( 11 ) NOT NULL , ADD attributes_vpe_value DECIMAL( 15, 4 ) NOT NULL ;
ALTER TABLE orders_pdf_profile CHANGE pdf_value pdf_value TEXT;

INSERT INTO admin_navigation VALUES(NULL, 'cseo_logo', 'Logo Manager', 'seo_config', 'cseo_logo.php', NULL, 2, NULL, 11);
INSERT INTO admin_navigation VALUES(NULL, 'cseo_logo', 'Logo Manager', 'seo_config', 'cseo_logo.php', NULL, 1, NULL, 11);

ALTER TABLE products CHANGE products_g_identifier products_g_identifier VARCHAR( 128 ) NULL DEFAULT  'TRUE';

INSERT INTO configuration VALUES (NULL, 'CUSTOMER_CID_FORM', 'date', 5, 8, NULL, NOW(), NULL, "xtc_cfg_select_option(array('date', 'custom', 'num'),");
INSERT INTO configuration VALUES (NULL, 'CUSTOMER_CID_FORM_CUSTOM', '', 5, 9, NULL, NOW(), NULL, '');
UPDATE configuration SET configuration_group_id  =  '5', sort_order =  '10' WHERE configuration_key = 'TRUSTED_SHOP_CREATE_ACCOUNT_DS';

INSERT INTO configuration VALUES (NULL, 'ATTRIBUTE_STOCK_CHECK_DISPLAY', 'false', 9, 12, NULL, NOW(), NULL, "xtc_cfg_select_option(array('true', 'false'),");
INSERT INTO commerce_seo_url_names VALUES (NULL, 'FILENAME_BLOG', 'blog.php');
ALTER TABLE blog_comment ADD comment_status INT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE blog_comment ADD comment_rating INT( 1 ) DEFAULT NULL;
ALTER TABLE blog_comment ADD comment_read INT( 5 ) NOT NULL DEFAULT '0';
INSERT INTO blog_settings (id ,blog_key ,wert ) VALUES (NULL, 'blog_rate', 'ja');
INSERT INTO blog_settings (id ,blog_key ,wert ) VALUES (NULL, 'blog_captcha', 'ja');
INSERT INTO addon_database VALUES (NULL, 'TABLE_BLOG_COMMENT', 'blog_comment');
ALTER TABLE blog_categories ADD COLUMN short_description TEXT NOT NULL AFTER description;

INSERT INTO addon_filenames VALUES (NULL ,  'FILENAME_DOWNLOAD_PDF_BILL',  'download_pdf_bill.php');

DELETE FROM configuration WHERE configuration_key = 'PRODUCT_LISTING_MANU_NAME';
DELETE FROM configuration WHERE configuration_key = 'PRODUCT_LISTING_MANU_IMG';
DELETE FROM configuration WHERE configuration_key = 'PRODUCT_LISTING_VPE';
DELETE FROM configuration WHERE configuration_key = 'PRODUCT_LISTING_MODEL';

ALTER TABLE orders_status ADD orders_status_color VARCHAR( 6 ) NOT NULL ;

INSERT INTO configuration VALUES (NULL, 'BLOG_MAIN_SORT', 'latest', 1000, 15, NULL, NOW(), '', "xtc_cfg_select_option(array('latest', 'oldest', 'random'),");
INSERT INTO configuration VALUES (NULL, 'MAIN_BLOG_MAXVALUE', 5, 1000, 15, NULL, NOW(), NULL, NULL);

ALTER TABLE configuration CHANGE  configuration_value configuration_value TEXT;
DELETE FROM configuration WHERE configuration_key = 'GOOGLE_ANONYM_ON';
INSERT INTO configuration VALUES (NULL, 'GOOGLE_ANAL_CODE_BASE', '', 361, 14, NULL, NOW(), '', 'xtc_cfg_textarea(');
INSERT INTO configuration VALUES (NULL, 'GENERAL_SCRIPT_ADDON', '', 361, 15, NULL, NOW(), '', 'xtc_cfg_textarea(');

ALTER TABLE products_attributes ADD attributes_shippingtime INT( 4 ) NOT NULL;
ALTER TABLE attr_profile ADD attributes_shippingtime INT( 4 ) NOT NULL;
ALTER TABLE products_options_values ADD products_options_hex_image VARCHAR( 7 ) NOT NULL ;
INSERT INTO configuration (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'STOCK_LEVEL_SHIPPINGTIME', 'False', 9, 13, NULL, '0000-00-00 00:00:00', NULL, 'xtc_cfg_select_option(array(''True'', ''False''),');
INSERT INTO configuration (configuration_id, configuration_key, configuration_value, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'STOCK_LEVEL_SHIPPINGTIME_ID', '3', 9, 14, NULL, '2011-08-02 14:17:52', NULL, NULL);
ALTER TABLE orders_products_attributes ADD attributes_shippingtime VARCHAR( 255 ) NOT NULL ;

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

ALTER TABLE orders_products_attributes ADD sortorder INT( 11 ) NULL ;
ALTER TABLE orders_products_attributes ADD products_attributes_id INT( 11 ) NULL ;

DELETE FROM commerce_seo_url_names WHERE file_name = 'FILENAME_BLOG';
ALTER TABLE  products ADD  products_uvpprice DECIMAL( 15, 4 ) NOT NULL AFTER  products_ekpprice ;

INSERT INTO admin_navigation (id, name, title, subsite, filename, gid, languages_id, nav_set, sort) VALUES (NULL, 'configuration', 'Treepodia', 'seo_config', 'configuration.php', 1004, 1, NULL, 1);
INSERT INTO admin_navigation (id, name, title, subsite, filename, gid, languages_id, nav_set, sort) VALUES (NULL, 'configuration', 'Treepodia', 'seo_config', 'configuration.php', 1004, 2, NULL, 1);

ALTER TABLE products_description ADD products_cart_description TEXT NULL AFTER products_zusatz_description;

INSERT INTO configuration VALUES (NULL, 'MODULE_CUSTOMERS_PDF_INVOICE_STATUS', 'false', 1, 34, NULL, '', NOW(), "xtc_cfg_select_option(array('true', 'false'),");
INSERT INTO configuration VALUES (NULL, 'MODULE_CUSTOMERS_PDF_INVOICE_MAIL_STATUS', 'false', 1, 35, NULL, '', NOW(), "xtc_cfg_select_option(array('true', 'false'),");
INSERT INTO orders_pdf_profile (id, languages_id, pdf_key, pdf_value, type) VALUES (NULL, '2', 'TEXT_PDF_INVOICE_TEXT', 'Bitte überweisen Sie den Rechnungsbetrag innerhalb von 7 Tagen auf untenstehendes Konto mit Angabe der Rechnungsnummer.', 'textarea');
INSERT INTO orders_pdf_profile (id, languages_id, pdf_key, pdf_value, type) VALUES (NULL, '1', 'TEXT_PDF_INVOICE_TEXT', 'Bitte überweisen Sie den Rechnungsbetrag innerhalb von 7 Tagen auf untenstehendes Konto mit Angabe der Rechnungsnummer.', 'textarea');

