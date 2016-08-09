INSERT INTO addon_filenames VALUES (NULL,'FILENAME_PRODUCTS_PROMOTION', 'products_promotion.php');
INSERT INTO addon_filenames VALUES (NULL,'FILENAME_CSEO_PRODUCT_EXPORT', 'cseo_product_export.php');
INSERT INTO addon_filenames VALUES (NULL,'FILENAME_DOWNLOAD_PDF_BILL', 'download_pdf_bill.php');

INSERT INTO addon_languages VALUES (NULL,'PP_HEADER_TITLE','Produkt Promotion',2);
INSERT INTO addon_languages VALUES (NULL,'PP_CONFIG_GLOBAL_ON','Promotion für dieses Produkt aktivieren?',2);
INSERT INTO addon_languages VALUES (NULL,'PP_CONFIG_PRODUCT_TITLE_ON','Artikelname als Promotion Titel?',2);
INSERT INTO addon_languages VALUES (NULL,'PP_CONFIG_PRODUCT_DESCRIPTION_ON','Artikelbeschreibung als Promotiontext?',2);
INSERT INTO addon_languages VALUES (NULL,'PP_TEXT_TITLE','Promotion Titel (max.100 Zeichen)',2);
INSERT INTO addon_languages VALUES (NULL,'PP_TEXT_IMAGE','Promotion Grafik einfügen',2);
INSERT INTO addon_languages VALUES (NULL,'PP_TEXT_DELETE','Grafik löschen?',2);
INSERT INTO addon_languages VALUES (NULL,'PP_TEXT_DESCRIPTION','Promotion Beschreibung/Text',2);
INSERT INTO addon_languages VALUES (NULL,'PP_TEXT_WARNING', '<b>Sie m&uuml;ssen das Modul noch unter Konfiguration > Zusatzmodule > Produktpromotion aktivieren.</b>',2);
INSERT INTO addon_languages VALUES (NULL,'MODULE_PRODUCT_PROMOTION_STATUS_TITLE' , 'Produktpromotion aktivieren',2);
INSERT INTO addon_languages VALUES (NULL,'MODULE_PRODUCT_PROMOTION_STATUS_DESC' , 'Aktivieren Sie diese Funktion, wenn Sie auf der Startseite des Shops ausgew&auml;hlte Produkte besonders hervorheben/promoten m&ouml;chten.<br /> In der Produktmaske k&ouml;nnen Sie Titel, Beschreibung und speziell angefertigte Grafiken zuweisen, sowie Produktanzeige deaktivieren, ohne bestehende Angaben l&ouml;schen zu m&uuml;ssen.',2);
INSERT INTO addon_languages VALUES (NULL,'PP_HEADER_TITLE','Product Promotion v2.3',1);
INSERT INTO addon_languages VALUES (NULL,'PP_CONFIG_GLOBAL_ON','Show Promotion for this Product?',1);
INSERT INTO addon_languages VALUES (NULL,'PP_CONFIG_PRODUCT_TITLE_ON','Promotion Title by Productname?',1);
INSERT INTO addon_languages VALUES (NULL,'PP_CONFIG_PRODUCT_DESCRIPTION_ON','Promotion Description by Productdescription?',1);
INSERT INTO addon_languages VALUES (NULL,'PP_TEXT_TITLE','Promotion Title (max.100 Indications)',1);
INSERT INTO addon_languages VALUES (NULL,'PP_TEXT_IMAGE','Promotion Image',1);
INSERT INTO addon_languages VALUES (NULL,'PP_TEXT_DELETE','Del Image?',1);
INSERT INTO addon_languages VALUES (NULL,'PP_TEXT_DESCRIPTION','Promotion Description/Text',1);
INSERT INTO addon_languages VALUES (NULL,'PP_TEXT_WARNING', '<b>You must enable the module is still under Configuration > Plug-ins > Product Promotion.</b>',1);
INSERT INTO addon_languages VALUES (NULL,'MODULE_PRODUCT_PROMOTION_STATUS_TITLE' , 'Show Productpromotion',1);
INSERT INTO addon_languages VALUES (NULL,'MODULE_PRODUCT_PROMOTION_STATUS_DESC' , 'Activate this function, if you particularly promotion on the starting side of the Shops selected products liked. On the product mask you can assign title, description and particularly made diagrams, as well as deactivate product announcement, without having to delete existing data.',1);
INSERT INTO addon_languages VALUES (NULL,'BOX_COMMENTS_ORDERS', 'Kommentar-Vorlagen', '2');
INSERT INTO addon_languages VALUES (NULL,'BOX_COMMENTS_ORDERS', 'Comment templates', '1');


DROP TABLE IF EXISTS log_configuration;
CREATE TABLE log_configuration (
  log_group_id int(11) NOT NULL,
  log_level_id int(11) NOT NULL,
  log_output_type_id int(11) NOT NULL,
  log_output_id int(11) NOT NULL,
  PRIMARY KEY (log_group_id,log_level_id,log_output_type_id,log_output_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 1, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 1, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 1, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 1, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 2, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 3, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 3, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 3, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 3, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 3, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 3, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 4, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 4, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 4, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 4, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 4, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 1, 4, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 1, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 1, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 1, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 1, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 2, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 3, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 3, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 3, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 3, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 3, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 3, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 4, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 4, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 4, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 4, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 4, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 2, 4, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 3, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 3, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 3, 1, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 3, 1, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 3, 3, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 3, 3, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 3, 3, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 3, 3, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 3, 4, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 3, 4, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 3, 4, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (1, 3, 4, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 1, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 1, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 1, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 1, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 1, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 1, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 3, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 3, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 3, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 3, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 3, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 3, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 3, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 3, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 4, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 4, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 4, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 4, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 4, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 4, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 4, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 1, 4, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 1, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 1, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 1, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 1, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 1, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 1, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 3, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 3, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 3, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 3, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 3, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 3, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 3, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 3, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 4, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 4, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 4, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 4, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 4, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 4, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 4, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 2, 4, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 3, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 3, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 3, 1, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 3, 1, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 3, 3, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 3, 3, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 3, 3, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 3, 3, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 3, 4, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 3, 4, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 3, 4, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (2, 3, 4, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 1, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 1, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 1, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 1, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 1, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 1, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 2, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 3, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 3, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 3, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 3, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 3, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 3, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 3, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 3, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 4, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 4, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 4, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 4, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 4, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 4, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 4, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 1, 4, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 2, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 2, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 2, 1, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 2, 1, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 2, 1, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 2, 1, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 2, 4, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 2, 4, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 2, 4, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 2, 4, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 2, 4, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 2, 4, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 3, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (3, 3, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 1, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 1, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 1, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 1, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 1, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 1, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 2, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 3, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 3, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 3, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 3, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 3, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 3, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 3, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 3, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 4, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 4, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 4, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 4, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 4, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 4, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 4, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 1, 4, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 2, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 2, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 2, 1, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 2, 1, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 2, 1, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 2, 1, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 2, 4, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 2, 4, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 2, 4, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 2, 4, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 2, 4, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 2, 4, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 3, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (4, 3, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 1, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 1, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 1, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 1, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 1, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 1, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 3, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 3, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 3, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 3, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 3, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 3, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 3, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 3, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 4, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 4, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 4, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 4, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 4, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 4, 6);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 4, 7);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 1, 4, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 1, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 1, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 1, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 1, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 1, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 3, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 3, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 3, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 3, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 3, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 3, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 4, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 4, 2);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 4, 3);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 4, 4);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 4, 5);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 2, 4, 8);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 3, 1, 1);
INSERT INTO log_configuration (log_group_id, log_level_id, log_output_type_id, log_output_id) VALUES (5, 3, 4, 1);

DROP TABLE IF EXISTS log_groups;
CREATE TABLE log_groups (
  log_group_id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  PRIMARY KEY (log_group_id),
  UNIQUE KEY name (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;


INSERT INTO log_groups (log_group_id, name) VALUES (1, 'error_handler');
INSERT INTO log_groups (log_group_id, name) VALUES (2, 'security');
INSERT INTO log_groups (log_group_id, name) VALUES (3, 'payment');
INSERT INTO log_groups (log_group_id, name) VALUES (4, 'shipping');
INSERT INTO log_groups (log_group_id, name) VALUES (5, 'widgets');


DROP TABLE IF EXISTS log_levels;
CREATE TABLE log_levels (
  log_level_id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  PRIMARY KEY (log_level_id),
  UNIQUE KEY name (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;


INSERT INTO log_levels (log_level_id, name) VALUES (1, 'error');
INSERT INTO log_levels (log_level_id, name) VALUES (2, 'warning');
INSERT INTO log_levels (log_level_id, name) VALUES (3, 'notice');


DROP TABLE IF EXISTS log_output_types;
CREATE TABLE log_output_types (
  log_output_type_id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  PRIMARY KEY (log_output_type_id),
  UNIQUE KEY name (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;


INSERT INTO log_output_types (log_output_type_id, name) VALUES (1, 'file');
INSERT INTO log_output_types (log_output_type_id, name) VALUES (2, 'screen');
INSERT INTO log_output_types (log_output_type_id, name) VALUES (3, 'mail');
INSERT INTO log_output_types (log_output_type_id, name) VALUES (4, 'html_file');


DROP TABLE IF EXISTS log_outputs;
CREATE TABLE log_outputs (
  log_output_id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  PRIMARY KEY (log_output_id),
  UNIQUE KEY name (name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;


INSERT INTO log_outputs (log_output_id, name) VALUES (1, 'output');
INSERT INTO log_outputs (log_output_id, name) VALUES (2, 'filepath');
INSERT INTO log_outputs (log_output_id, name) VALUES (3, 'backtrace');
INSERT INTO log_outputs (log_output_id, name) VALUES (4, 'request_data');
INSERT INTO log_outputs (log_output_id, name) VALUES (5, 'code_snippet');
INSERT INTO log_outputs (log_output_id, name) VALUES (6, 'class_data');
INSERT INTO log_outputs (log_output_id, name) VALUES (7, 'function_data');
INSERT INTO log_outputs (log_output_id, name) VALUES (8, 'session_data');