<?php
/* --------------------------------------------------------------
$Id: class.importexport.php 872 2014-03-21 14:46:30Z akausch $

Estelco - Ebusiness and more
Copyright (c) 2008 Estelco

--------------------------------------------------------------
based on:
(c) 2003 xtcommerce (import.php 1319 2005-10-23 10:35:15Z mz); www.xt-commerce.com

Released under the GNU General Public License
--------------------------------------------------------------
*/
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
class xtcImport {

    function xtcImport($filename, $time_start = 0) {
        $this->seperator = CSV_SEPERATOR;
        $this->TextSign = CSV_TEXTSIGN;
        if (CSV_SEPERATOR == '')
        $this->seperator = "\t";
        if (CSV_SEPERATOR == '\t')
        $this->seperator = "\t";
        $this->filename = $filename;
        $this->ImportDir = DIR_FS_CATALOG.'import/';
        $this->catDepth = 6;
        $this->languages = $this->get_lang();
        $this->counter = array ();
        $this->mfn = $this->get_mfn();
        $this->errorlog = array ();
        $this->time_start = $time_start==0?time():$time_start;
        $this->debug = false;
        $this->CatTree = array ('ID' => 0);
        // precaching categories in array ?
        $this->CatCache = true;
        $this->FileSheme = array ();
        $this->Groups = xtc_get_customers_statuses();

    }

    /**
    *   generating file layout
    *   @param array $mapping standard fields
    *   @return array
    */
    function generate_map($import = 'products') {

        switch ($import) {

            case 'products':
                // lets define a standard fieldmapping array, with importable fields
                $file_layout = array ('action' => '', // what to do?
                'p_model' => '', // products_model
                'p_id' => '', // products_model
                'p_stock' => '', // products_quantity
                'p_tpl' => '', // products_template
                'p_sorting' => '', // products_sorting
                'p_manufacturer' => '', // manufacturer
                'p_fsk18' => '', // FSK18
                'p_priceNoTax' => '', // Nettoprice
                'p_tax' => '', // taxrate in percent
                'p_status' => '', // products status
                'p_weight' => '', // products weight
                'p_ean' => '', // products ean
                'p_disc' => '', // products discount
                'p_opttpl' => '', // options template
                'p_image' => '', // product image
                'p_vpe' => '', // products VPE
                'p_vpe_status' => '', // products VPE Status
                'p_vpe_value' => '', // products VPE value
                'p_shipping' => '', // product shipping_time
                'p_ekpprice' => '', // product EK
                'p_uvpprice' => '', // product UVP
                );

                // Group Prices
                for ($i = 0; $i < count($this->Groups) - 1; $i ++) {
                    $file_layout = array_merge($file_layout, array ('p_priceNoTax.'.$this->Groups[$i +1]['id'] => ''));
                }

                // Group Permissions
                for ($i = 0; $i < count($this->Groups); $i ++) {
                    $file_layout = array_merge($file_layout, array ('p_groupAcc.'.$this->Groups[$i]['id'] => ''));
                }

                // product images
                for ($i = 1; $i < MO_PICS + 1; $i ++) {
                    $file_layout = array_merge($file_layout, array ('p_image.'.$i => ''));
                }

                // add lang fields
                for ($i = 0; $i < sizeof($this->languages); $i ++) {
                    $file_layout = array_merge($file_layout, array ('p_name.'.$this->languages[$i]['code'] => '', // products_name
                    'p_desc.'.$this->languages[$i]['code'] => '', // products_description
                    'p_shortdesc.'.$this->languages[$i]['code'] => '', // products_short_description
                    'p_keywords.'.$this->languages[$i]['code'] => '', // products_keywords
                    'p_meta_title.'.$this->languages[$i]['code'] => '', // products_meta_title
                    'p_meta_desc.'.$this->languages[$i]['code'] => '', // products_meta_description
                    'p_meta_key.'.$this->languages[$i]['code'] => '', // products_meta_keywords
                    'p_url.'.$this->languages[$i]['code'] => '')); //products_url
                }
                // add categorie fields
                for ($i = 0; $i < $this->catDepth; $i ++)
                $file_layout = array_merge($file_layout, array ('p_cat.'.$i => ''));
                break;

            case 'categories':

                $file_layout = array ('action' => '', // what to do?
                'c_id' => '', // categories_id
                'c_image' => '', // categories_image
                'c_parent' => '', // parent_id
                'c_status' => '', // categories_status
                'c_tpl' => '', // categories_template
                'c_ltpl' => '', // categories_listing_template
                'c_sort' => '', // sort_order
                'c_prod_sort' => '', // products_sorting
                'c_prod_sort2' => '', // product_sorting2
                );

                // Group Permissions
                for ($i = 0; $i < count($this->Groups); $i ++) {
                    $file_layout = array_merge($file_layout, array ('c_groupAcc.'.$this->Groups[$i]['id'] => ''));
                }

                // add lang fields
                for ($i = 0; $i < sizeof($this->languages); $i ++) {
                    $file_layout = array_merge($file_layout, array ('c_name.'.$this->languages[$i]['code'] => '', // categories_name
                    'c_desc.'.$this->languages[$i]['code'] => '', // categories_description
                    'c_title.'.$this->languages[$i]['code'] => '', // categories_heading_title
                    'c_meta_title.'.$this->languages[$i]['code'] => '', // categories_meta_title
                    'c_meta_desc.'.$this->languages[$i]['code'] => '', // categories_meta_description
                    'c_meta_key.'.$this->languages[$i]['code'] => '')); // categories_meta_keywords
                }

                break;

            case 'prod2cat':
                $file_layout = array ('action' => '', // what to do?
                'prod_id' => '', // products_id
                'cat_id' => '', // categories_id
                'prod_name' => '', // products_name
                'cat_name' => '' // categories_name
                );
                break;
        }

        return $file_layout;

    }

    /**
    *   generating mapping layout for importfile
    *   @param array $mapping standard fields
    *   @return array
    */
    function map_file($mapping) {
        if (!file_exists($this->ImportDir.$this->filename)) {
            // error
            return 'error';
        } else {
            // file is ok, creating mapping
            $inhalt = array ();
            $inhalt = file($this->ImportDir.$this->filename);
            // get first line into array
            $content = explode($this->seperator, $inhalt[0]);

            foreach ($mapping as $key => $value) {
                // try to find our field in fieldlayout
                foreach ($content as $key_c => $value_c)
                if ($key == trim($this->RemoveTextNotes($content[$key_c]))) {
                    $mapping[$key] = trim($this->RemoveTextNotes($key_c));
                    $this->FileSheme[$key] = 'Y';
                }

            }
            return $mapping;
        }
    }

    /**
    *   Get installed languages
    *   @return array
    */
    function get_lang() {

        $languages_query = xtc_db_query("select languages_id, name, code, image, directory from ".TABLE_LANGUAGES." order by sort_order");
        while ($languages = xtc_db_fetch_array($languages_query)) {
            $languages_array[] = array ('id' => $languages['languages_id'], 'name' => $languages['name'], 'code' => $languages['code']);
        }

        return $languages_array;
    }

    function import($mapping, $import = 'products') {
        // open file
        $inhalt = file($this->ImportDir.$this->filename);
        $lines = count($inhalt);

        switch ($import) {
            case 'products':
                // walk through file data, and ignore first line
                for ($i = 1; $i < $lines; $i ++) {
                    $line_content = '';

                    // get line content
                    $line_fetch = $this->get_line_content($i, $inhalt, $lines);
                    $line_content = explode($this->seperator, $line_fetch['data']);
                    $i += $line_fetch['skip'];

                    // ok, now crossmap data into array
                    $line_data = $this->generate_map($import);

                    foreach ($mapping as $key => $value)
                    $line_data[$key] = $this->RemoveTextNotes($line_content[$value]);

                    if ($this->debug) {
                        echo '<pre>';
                        print_r($line_data);
                        echo '</pre>';

                    }
                    if ((isset($line_data['action']) && $line_data['action']=='insert') || !isset($line_data['action'])) {
                        if ($line_data['p_model'] != '') {
                            if ($line_data['p_cat.0'] != '' || $this->FileSheme['p_cat.0'] != 'Y') {
                                if ($this->FileSheme['p_cat.0'] != 'Y') {
                                    if ($this->checkModel($line_data['p_model'])) {
                                        $this->insertProduct($line_data, 'update');
                                    } else {
                                        $this->insertProduct($line_data);
                                    }
                                } else {
                                    if ($this->checkModel($line_data['p_model'])) {
                                        $this->insertProduct($line_data, 'update', true);
                                    } else {
                                        $this->insertProduct($line_data, 'insert', true);
                                    }
                                }
                            } else {
                                $this->errorLog[] = '<b>ERROR:</b> no Categorie, line: '.$i.' dataset: '.$line_fetch['data'];
                            }
                        } else {
                            $this->errorLog[] = '<b>ERROR:</b> no Modelnumber, line: '.$i.' dataset: '.$line_fetch['data'];
                        }

                    } elseif ($line_data['action']=='update') {
                        if ($this->checkModel($line_data['p_model'])) {
                            $this->insertProduct($line_data, 'update');
                        } else {
                            $this->insertProduct($line_data);
                            $this->errorLog[] = '<b>ERROR:</b> unknown Modelnumber, line: '.$i.' dataset: '.$line_fetch['data'];
                        }

                    } elseif ($line_data['action']=='delete') {
                        $this->deleteProduct($line_data['p_model']);
                    } elseif ($line_data['action']=='ignore') {
                        $this->counter['prod_ignore']++;
                    } else {
                        $this->errorLog[] = '<b>ERROR:</b> wrong action ("' . $line_data['action'] . '"), line: '.$i.' dataset: '.$line_fetch['data'];
                    }
                }
                break;

            case 'categories':
                // walk through file data, and ignore first line
                for ($i = 1; $i < $lines; $i ++) {
                    $line_content = '';

                    // get line content
                    $line_fetch = $this->get_line_content($i, $inhalt, $lines);
                    $line_content = explode($this->seperator, $line_fetch['data']);
                    $i += $line_fetch['skip'];

                    // ok, now crossmap data into array
                    $line_data = $this->generate_map($import);

                    foreach ($mapping as $key => $value)
                    $line_data[$key] = $this->RemoveTextNotes($line_content[$value]);

                    if ($this->debug) {
                        echo '<pre>';
                        print_r($line_data);
                        echo '</pre>';

                    }
                    if ($line_data['action'] == 'insert') {
                        if ($line_data['c_id'] != '') {
                            if ($this->checkCategory($line_data['c_id'])) {
                                $this->insertCategories($line_data, 'update');
                            } else {
                                $this->insertCategories($line_data);
                            }
                        } else {
                            $this->errorLog[] = '<b>ERROR:</b> no Categories_ID, line: '.$i.' dataset: '.$line_fetch['data'];
                        }
                    } elseif ($line_data['action'] == 'update') {
                        if ($this->checkCategory($line_data['c_id'])) {
                            $this->insertCategories($line_data, 'update');
                        } else {
                            $this->errorLog[] = '<b>ERROR:</b> update not possible, category doesn\'t exist, line: '.$i.' dataset: '.$line_fetch['data'];
                        }
                    } elseif ($line_data['action'] == 'delete') {
                        $this->deleteCategories($line_data['c_id']);
                    } elseif ($line_data['action'] == 'ignore') {
                        $this->counter['cat_ignore']++;
                    } else {
                        $this->errorLog[] = '<b>ERROR:</b> wrong action ("' . $line_data['action'] . '"), line: '.$i.' dataset: '.$line_fetch['data'];
                    }
                }
                break;

            case 'prod2cat':
                // walk through file data, and ignore first line
                for ($i = 1; $i < $lines; $i ++) {
                    $line_content = '';

                    // get line content
                    $line_fetch = $this->get_line_content($i, $inhalt, $lines);
                    $line_content = explode($this->seperator, $line_fetch['data']);
                    $i += $line_fetch['skip'];

                    // ok, now crossmap data into array
                    $line_data = $this->generate_map($import);

                    foreach ($mapping as $key => $value)
                    $line_data[$key] = $this->RemoveTextNotes($line_content[$value]);

                    if ($this->debug) {
                        echo '<pre>';
                        print_r($line_data);
                        echo '</pre>';

                    }
                    if ($line_data['action'] == 'insert') {
                        if ($line_data['prod_id'] != '' && $line_data['cat_id'] != '') {
                            if (!$this->checkProd2Cat($line_data['prod_id'], $line_data['cat_id'])) {
                                $this->insertPtoCconnection($line_data['prod_id'], $line_data['cat_id']);
                            }
                        } else {
                            $this->errorLog[] = '<b>ERROR:</b> no Categories_ID and/or no Products_ID, line: '.$i.' dataset: '.$line_fetch['data'];
                        }
                    } elseif ($line_data['action'] == 'delete') {
                        $this->deletePtoCconnection($line_data['prod_id'], $line_data['cat_id']);
                    } elseif ($line_data['action'] == 'ignore') {
                        $this->counter['p2c_ignore']++;
                    } else {
                        $this->errorLog[] = '<b>ERROR:</b> wrong action ("' . $line_data['action'] . '"), line: '.$i.' dataset: '.$line_fetch['data'];
                    }
                }
                break;
        }
        return array ($this->counter, $this->errorLog, $this->calcElapsedTime($this->time_start));
    }

    /**
    *   Check if a product exists in database, query for model number
    *   @param string $model products modelnumber
    *   @return boolean
    */
    function checkModel($model) {
        $model_query = xtc_db_query("SELECT products_id FROM ".TABLE_PRODUCTS." WHERE products_model='".addslashes($model)."'");
        if (!xtc_db_num_rows($model_query))
        return false;
        return true;
    }
    /**
    *   Check if a product exists in database, query for model number
    *   @param string $model products modelnumber
    *   @return boolean
    */
    function checkCategory($category) {
        // check if categorie exists
        $cat_query = xtc_db_query("SELECT categories_id FROM ".TABLE_CATEGORIES." WHERE categories_id='".addslashes($category)."'");

        if (!xtc_db_num_rows($cat_query))
        return false;
        return true;
    }
    /**
    *   Check if a product exists in database, query for model number
    *   @param string $model products modelnumber
    *   @return boolean
    */
    function checkProd2Cat($product, $category) {
        $prod2cat_query = xtc_db_query("SELECT * FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id='".addslashes($product)."' AND categories_id='".addslashes($category)."'");
        if (!xtc_db_num_rows($prod2cat_query))
        return false;
        return true;
    }

    /**
    *   removing textnotes from a dataset
    *   @param String $data data
    *   @return String cleaned data
    */
    function RemoveTextNotes($data) {
        if (substr($data, -1) == $this->TextSign)
        $data = substr($data, 1, strlen($data) - 2);

        return trim($data);

    }

    /**
    *   Get/create manufacturers ID for a given Name
    *   @param String $manufacturer Manufacturers name
    *   @return int manufacturers ID
    */
    function getMAN($manufacturer) {
        if ($manufacturer == '')
        return;
        if (isset ($this->mfn[$manufacturer]['id']))
        return $this->mfn[$manufacturer]['id'];
        $man_query = xtc_db_query("SELECT manufacturers_id FROM ".TABLE_MANUFACTURERS." WHERE manufacturers_name = '".$manufacturer."'");
        if (!xtc_db_num_rows($man_query)) {
            $manufacturers_array = array ('manufacturers_name' => $manufacturer);
            xtc_db_perform(TABLE_MANUFACTURERS, $manufacturers_array);
            $this->mfn[$manufacturer]['id'] = xtc_db_insert_id();
        } else {
            $man_data = xtc_db_fetch_array($man_query);
            $this->mfn[$manufacturer]['id'] = $man_data['manufacturers_id'];

        }
        return $this->mfn[$manufacturer]['id'];
    }
    /**
    *   Insert a new product into Database
    *   @param array $dataArray Linedata
    *   @param string $mode insert or update flag
    */
    function insertProduct(& $dataArray, $mode = 'insert', $touchCat = false) {

        $products_array = array ('products_model' => $dataArray['p_model']);
        if ($this->FileSheme['p_id'] == 'Y')
        $products_array = array_merge($products_array, array ('products_id' => $dataArray['p_id']));
        if ($this->FileSheme['p_stock'] == 'Y')
        $products_array = array_merge($products_array, array ('products_quantity' => $dataArray['p_stock']));
        if ($this->FileSheme['p_priceNoTax'] == 'Y')
        $products_array = array_merge($products_array, array ('products_price' => $dataArray['p_priceNoTax']));
        if ($this->FileSheme['p_weight'] == 'Y')
        $products_array = array_merge($products_array, array ('products_weight' => $dataArray['p_weight']));
        if ($this->FileSheme['p_status'] == 'Y')
        $products_array = array_merge($products_array, array ('products_status' => $dataArray['p_status']));
        if ($this->FileSheme['p_image'] == 'Y')
        $products_array = array_merge($products_array, array ('products_image' => $dataArray['p_image']));
        if ($this->FileSheme['p_disc'] == 'Y')
        $products_array = array_merge($products_array, array ('products_discount_allowed' => $dataArray['p_disc']));
        if ($this->FileSheme['p_ean'] == 'Y')
        $products_array = array_merge($products_array, array ('products_ean' => $dataArray['p_ean']));
        if ($this->FileSheme['p_tax'] == 'Y')
        $products_array = array_merge($products_array, array ('products_tax_class_id' => $dataArray['p_tax']));
        if ($this->FileSheme['p_opttpl'] == 'Y')
        $products_array = array_merge($products_array, array ('options_template' => $dataArray['p_opttpl']));
        if ($this->FileSheme['p_manufacturer'] == 'Y')
        $products_array = array_merge($products_array, array ('manufacturers_id' => $this->getMAN(trim($dataArray['p_manufacturer']))));
        if ($this->FileSheme['p_fsk18'] == 'Y')
        $products_array = array_merge($products_array, array ('products_fsk18' => $dataArray['p_fsk18']));
        if ($this->FileSheme['p_tpl'] == 'Y')
        $products_array = array_merge($products_array, array ('product_template' => $dataArray['p_tpl']));
        if ($this->FileSheme['p_vpe'] == 'Y')
        $products_array = array_merge($products_array, array ('products_vpe' => $dataArray['p_vpe']));
        if ($this->FileSheme['p_vpe_status'] == 'Y')
        $products_array = array_merge($products_array, array ('products_vpe_status' => $dataArray['p_vpe_status']));
        if ($this->FileSheme['p_vpe_value'] == 'Y')
        $products_array = array_merge($products_array, array ('products_vpe_value' => $dataArray['p_vpe_value']));
        if ($this->FileSheme['p_shipping'] == 'Y')
        $products_array = array_merge($products_array, array ('products_shippingtime' => $dataArray['p_shipping']));
        if ($this->FileSheme['p_ekpprice'] == 'Y')
        $products_array = array_merge($products_array, array ('products_ekpprice' => $dataArray['p_ekpprice']));
        if ($this->FileSheme['p_uvpprice'] == 'Y')
        $products_array = array_merge($products_array, array ('products_uvpprice' => $dataArray['p_uvpprice']));
        if ($this->FileSheme['p_sorting'] == 'Y')
        $products_array = array_merge($products_array, array ('products_sort' => $dataArray['p_sorting']));

        if ($mode == 'insert') {
            $this->counter['prod_new']++;
            xtc_db_perform(TABLE_PRODUCTS, $products_array);
            $products_id = xtc_db_insert_id();
        } else {
            $this->counter['prod_upd']++;
            xtc_db_perform(TABLE_PRODUCTS, $products_array, 'update', 'products_model = \''.addslashes($dataArray['p_model']).'\'');
            $prod_query = xtc_db_query("SELECT products_id FROM ".TABLE_PRODUCTS." WHERE products_model='".addslashes($dataArray['p_model'])."'");
            $prod_data = xtc_db_fetch_array($prod_query);
            $products_id = $prod_data['products_id'];

        }

        // Insert Group Prices.
        for ($i = 1; $i < count($this->Groups); $i ++) {
            // seperate string ::
            if (isset ($dataArray['p_priceNoTax.'.$this->Groups[$i]['id']])) {
                $delete_query = "DELETE FROM personal_offers_by_customers_status_".$this->Groups[$i]['id'] . " WHERE products_id=" . $products_id;
                xtc_db_query($delete_query);
                $prices = $dataArray['p_priceNoTax.'.$this->Groups[$i]['id']];
                $prices = explode('::', $prices);
                for ($ii = 0; $ii < count($prices); $ii ++) {
                    $values = explode(':', $prices[$ii]);
                    $group_array = array ('products_id' => $prod_data['products_id'], 'quantity' => $values[0], 'personal_offer' => $values[1]);

                    xtc_db_perform('personal_offers_by_customers_status_'.$this->Groups[$i]['id'], $group_array);
                }
            }
        }

        // Insert Group Permissions.
        for ($i = 0; $i < count($this->Groups); $i ++) {
            if (isset ($dataArray['p_groupAcc.'.$this->Groups[$i]['id']])) {
                $insert_array = array ('group_permission_'.$this->Groups[$i]['id'] => $dataArray['p_groupAcc.'.$this->Groups[$i]['id']]);
                xtc_db_perform(TABLE_PRODUCTS, $insert_array, 'update', 'products_id = \''.$products_id.'\'');
            }
        }

        if ($touchCat) $this->insertCategory($dataArray, $mode, $products_id);
        for ($i_insert = 0; $i_insert < sizeof($this->languages); $i_insert ++) {
            $prod_desc_array = array ('products_id' => $products_id, 'language_id' => $this->languages[$i_insert]['id']);

            if ($this->FileSheme['p_name.'.$this->languages[$i_insert]['code']] == 'Y')
            $prod_desc_array = array_merge($prod_desc_array, array ('products_name' => addslashes($dataArray['p_name.'.$this->languages[$i_insert]['code']])));
            if ($this->FileSheme['p_desc.'.$this->languages[$i_insert]['code']] == 'Y')
            $prod_desc_array = array_merge($prod_desc_array, array ('products_description' => addslashes($dataArray['p_desc.'.$this->languages[$i_insert]['code']])));
            if ($this->FileSheme['p_shortdesc.'.$this->languages[$i_insert]['code']] == 'Y')
            $prod_desc_array = array_merge($prod_desc_array, array ('products_short_description' => addslashes($dataArray['p_shortdesc.'.$this->languages[$i_insert]['code']])));
            if ($this->FileSheme['p_keywords.'.$this->languages[$i_insert]['code']] == 'Y')
            $prod_desc_array = array_merge($prod_desc_array, array ('products_keywords' => $dataArray['p_keywords.'.$this->languages[$i_insert]['code']]));
            if ($this->FileSheme['p_meta_title.'.$this->languages[$i_insert]['code']] == 'Y')
            $prod_desc_array = array_merge($prod_desc_array, array ('products_meta_title' => $dataArray['p_meta_title.'.$this->languages[$i_insert]['code']]));
            if ($this->FileSheme['p_meta_desc.'.$this->languages[$i_insert]['code']] == 'Y')
            $prod_desc_array = array_merge($prod_desc_array, array ('products_meta_description' => $dataArray['p_meta_desc.'.$this->languages[$i_insert]['code']]));
            if ($this->FileSheme['p_meta_key.'.$this->languages[$i_insert]['code']] == 'Y')
            $prod_desc_array = array_merge($prod_desc_array, array ('products_meta_keywords' => $dataArray['p_meta_key.'.$this->languages[$i_insert]['code']]));
            if ($this->FileSheme['p_url.'.$this->languages[$i_insert]['code']] == 'Y')
            $prod_desc_array = array_merge($prod_desc_array, array ('products_url' => $dataArray['p_url.'.$this->languages[$i_insert]['code']]));

            if ($mode == 'insert') {
                xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $prod_desc_array);
            } else {
                xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $prod_desc_array, 'update', 'products_id = \''.$products_id.'\' and language_id=\''.$this->languages[$i_insert]['id'].'\'');
            }
        }
    }

    /**
    *   Match and insert Categories
    *   @param array $dataArray data array
    *   @param string $mode insert mode
    *   @param int $pID  products ID
    */
    function insertCategory(& $dataArray, $mode = 'insert', $pID) {
        if ($this->debug) {
            echo '<pre>';
            print_r($this->CatTree);
            echo '</pre>';
        }
        $cat = array ();
        $catTree = '';
        for ($i = 0; $i < $this->catDepth; $i ++)
        if (trim($dataArray['p_cat.'.$i]) != '') {
            $cat[$i] = trim($dataArray['p_cat.'.$i]);
            $catTree .= '[\''.addslashes($cat[$i]).'\']';
        }
        $code = '$ID=$this->CatTree'.$catTree.'[\'ID\'];';
        if ($this->debug)
        echo $code;
        eval ($code);

        if (is_int($ID) || $ID == '0') {
            $this->insertPtoCconnection($pID, $ID);
        } else {

            $catTree = '';
            $parTree = '';
            $curr_ID = 0;
            for ($i = 0; $i < count($cat); $i ++) {

                $catTree .= '[\''.addslashes($cat[$i]).'\']';

                $code = '$ID=$this->CatTree'.$catTree.'[\'ID\'];';
                eval ($code);
                if (is_int($ID) || $ID == '0') {
                    $curr_ID = $ID;
                } else {

                    $code = '$parent=$this->CatTree'.$parTree.'[\'ID\'];';
                    eval ($code);
                    // check if categorie exists
                    $cat_query = xtc_db_query("SELECT c.categories_id FROM ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd
                                                WHERE
                                                cd.categories_name='".addslashes($cat[$i])."'
                                                and cd.language_id='".$this->languages[0]['id']."'
                                                and cd.categories_id=c.categories_id
                                                and parent_id='".$parent."'");

                    if (!xtc_db_num_rows($cat_query)) { // insert categorie
                        $categorie_data = array ('parent_id' => $parent, 'categories_status' => 1, 'date_added' => 'now()', 'last_modified' => 'now()');

                        xtc_db_perform(TABLE_CATEGORIES, $categorie_data);
                        $cat_id = xtc_db_insert_id();
                        $this->counter['cat_new']++;
                        $code = '$this->CatTree'.$parTree.'[\''.addslashes($cat[$i]).'\'][\'ID\']='.$cat_id.';';
                        eval ($code);
                        $parent = $cat_id;
                        for ($i_insert = 0; $i_insert < sizeof($this->languages); $i_insert ++) {
                            $categorie_data = array ('language_id' => $this->languages[$i_insert]['id'], 'categories_id' => $cat_id, 'categories_name' => $cat[$i]);
                            xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $categorie_data);

                        }
                    } else {
                        $this->counter['cat_touched']++;
                        $cData = xtc_db_fetch_array($cat_query);
                        $cat_id = $cData['categories_id'];
                        $code = '$this->CatTree'.$parTree.'[\''.addslashes($cat[$i]).'\'][\'ID\']='.$cat_id.';';
                        eval ($code);
                    }

                }
                $parTree = $catTree;
            }
            $this->insertPtoCconnection($pID, $cat_id);
        }

    }

    function insertCategories($line_data, $mode = 'insert') {
        if ($this->debug) {
            echo '<pre>';
            print_r($this->CatTree);
            echo '</pre>';
        }

        if ($mode == 'insert') {
            $categorie_data = array ('parent_id' => $line_data['c_parent'],
            'categories_status' => $line_data['c_status'],
            'categories_image' => $line_data['c_image'],
            'categories_template' => $line_data['c_tpl'],
            'listing_template' => $line_data['c_ltpl'],
            'sort_order' => $line_data['c_sort'],
            'products_sorting' => $line_data['c_prod_sort'],
            'products_sorting2' => $line_data['c_prod_sort2'],
            'date_added' => 'now()',
            'last_modified' => 'now()');
            xtc_db_perform(TABLE_CATEGORIES, $categorie_data);
            $cat_id = xtc_db_insert_id();
            $this->counter['cat_new']++;
            // Insert Group Permissions.
            for ($i = 0; $i < count($this->Groups); $i ++) {
                if (isset ($line_data['c_groupAcc.'.$this->Groups[$i]['id']])) {
                    $insert_array = array ('group_permission_'.$this->Groups[$i]['id'] => $line_data['c_groupAcc.'.$this->Groups[$i]['id']]);
                    xtc_db_perform(TABLE_CATEGORIES, $insert_array, 'update', 'categories_id = \''.$cat_id.'\'');
                }
            }
            for ($i_insert = 0; $i_insert < sizeof($this->languages); $i_insert ++) {
                $categorie_data = array ('language_id' => $this->languages[$i_insert]['id'],
                'categories_id' => $cat_id,
                'categories_name' => $line_data['c_name.'.$this->languages[$i_insert]['code']],
                'categories_description' => $line_data['c_desc.'.$this->languages[$i_insert]['code']],
                'categories_heading_title' => $line_data['c_title.'.$this->languages[$i_insert]['code']],
                'categories_meta_title' => $line_data['c_meta_title.'.$this->languages[$i_insert]['code']],
                'categories_meta_description' => $line_data['c_meta_desc.'.$this->languages[$i_insert]['code']],
                'categories_meta_keywords' => $line_data['c_meta_key.'.$this->languages[$i_insert]['code']]
                );
                xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $categorie_data);
            }
        } elseif ($mode == 'update') {
            $categorie_data = array ('parent_id' => $line_data['c_parent'],
            'categories_status' => $line_data['c_status'],
            'categories_image' => $line_data['c_image'],
            'categories_template' => $line_data['c_tpl'],
            'listing_template' => $line_data['c_ltpl'],
            'sort_order' => $line_data['c_sort'],
            'products_sorting' => $line_data['c_prod_sort'],
            'products_sorting2' => $line_data['c_prod_sort2'],
            'date_added' => 'now()',
            'last_modified' => 'now()');
            xtc_db_perform(TABLE_CATEGORIES, $categorie_data, 'update', "categories_id='" . $line_data['c_id'] . "'");
            $this->counter['cat_upd']++;
            // Insert Group Permissions.
            for ($i = 0; $i < count($this->Groups); $i ++) {
                if (isset ($line_data['c_groupAcc.'.$this->Groups[$i]['id']])) {
                    $insert_array = array ('group_permission_'.$this->Groups[$i]['id'] => $line_data['c_groupAcc.'.$this->Groups[$i]['id']]);
                    xtc_db_perform(TABLE_CATEGORIES, $insert_array, 'update', 'categories_id = \''.$line_data['c_id'].'\'');
                }
            }
            for ($i_insert = 0; $i_insert < sizeof($this->languages); $i_insert ++) {
                $categorie_data = array ('categories_id' => $line_data['c_id'],
                'categories_name' => $line_data['c_name.'.$this->languages[$i_insert]['code']],
                'categories_description' => $line_data['c_desc.'.$this->languages[$i_insert]['code']],
                'categories_heading_title' => $line_data['c_title.'.$this->languages[$i_insert]['code']],
                'categories_meta_title' => $line_data['c_meta_title.'.$this->languages[$i_insert]['code']],
                'categories_meta_description' => $line_data['c_meta_desc.'.$this->languages[$i_insert]['code']],
                'categories_meta_keywords' => $line_data['c_meta_key.'.$this->languages[$i_insert]['code']]
                );
                xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $categorie_data, 'update', "categories_id='" . $line_data['c_id'] . "' and language_id='" . $this->languages[$i_insert]['id'] . "'");
            }
        }
    }

    function deleteProduct($pModel) {
        $prod_id_query = xtc_db_query("SELECT products_id FROM " . TABLE_PRODUCTS . " WHERE products_model='".xtc_db_input($pModel)."'");
        if (xtc_db_num_rows($prod_id_query) > 0) {
            while ($del = xtc_db_fetch_array($prod_id_query)) {
                $this->counter['prod_del']++;
                xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id='".$del['products_id']."'");
                xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_CONTENT." WHERE products_id='".$del['products_id']."'");
                xtc_db_query("DELETE ".TABLE_PRODUCTS_ATTRIBUTES." pa, ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." pad FROM ".TABLE_PRODUCTS_ATTRIBUTES." pa, ".TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD." pad WHERE pa.products_id='".$del['products_id']."' AND pa.products_attributes_id=pad.products_attributes_id");
                xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id='".$del['products_id']."'");
                xtc_db_query("DELETE FROM ".TABLE_PRODUCTS." WHERE products_id='".$del['products_id']."'");
            }
        } else {
            //$this->errorLog[] = '<b>ERROR:</b> unknown Modelnumber: '.$pModel;
        }
    }

    function deleteCategories($cID) {
        $this->counter['cat_del']++;
        $cat_del_query = xtc_db_query("DELETE FROM ".TABLE_CATEGORIES." WHERE categories_id='".$cID."'");
        $cat_del_query = xtc_db_query("DELETE FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id='".$cID."'");
        xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE categories_id='".$cID."'");
    }

    /**
    *   Insert products to categories connection
    *   @param int $pID products ID
    *   @param int $cID categories ID
    */
    function insertPtoCconnection($pID, $cID) {
        if ($cID == 0 || $pID == 0) {
            $this->counter['p2c_ign']++;
            return;
        }
        $prod2cat_query = xtc_db_query("SELECT *
                                        FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                        WHERE
                                        categories_id='".$cID."'
                                        and products_id='".$pID."'");

        if (!xtc_db_num_rows($prod2cat_query)) {
            $this->counter['p2c_new']++;
            $insert_data = array ('products_id' => $pID, 'categories_id' => $cID);

            xtc_db_perform(TABLE_PRODUCTS_TO_CATEGORIES, $insert_data);
        }
    }

    function deletePtoCconnection($pID, $cID) {
        $this->counter['p2c_del']++;
        $prod2cat_query = xtc_db_query("DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
                                        WHERE
                                        categories_id='".$cID."'
                                        and products_id='".$pID."'");
    }

    /**
    *   Parse Inputfile until next line
    *   @param int $line taxrate in percent
    *   @param string $file_content taxrate in percent
    *   @param int $max_lines taxrate in percent
    *   @return array
    */
    function get_line_content($line, $file_content, $max_lines) {
        // get first line
        $line_data = array ();
        $line_data['data'] = $file_content[$line];
        $lc = 1;
        // check if next line got ; in first 50 chars
        while (!strstr(substr($file_content[$line + $lc], 0, 6), 'XTSOL') && $line + $lc <= $max_lines) {
            $line_data['data'] .= $file_content[$line + $lc];
            $lc ++;
        }
        $line_data['skip'] = $lc -1;
        return $line_data;
    }

    /**
    *   Calculate Elapsed time from 2 given Timestamps
    *   @param int $time old timestamp
    *   @return String elapsed time
    */
    function calcElapsedTime($time) {

        // calculate elapsed time (in seconds!)
        $diff = time() - $time;
        $daysDiff = 0;
        $hrsDiff = 0;
        $minsDiff = 0;
        $secsDiff = 0;

        $sec_in_a_day = 60 * 60 * 24;
        while ($diff >= $sec_in_a_day) {
            $daysDiff ++;
            $diff -= $sec_in_a_day;
        }
        $sec_in_an_hour = 60 * 60;
        while ($diff >= $sec_in_an_hour) {
            $hrsDiff ++;
            $diff -= $sec_in_an_hour;
        }
        $sec_in_a_min = 60;
        while ($diff >= $sec_in_a_min) {
            $minsDiff ++;
            $diff -= $sec_in_a_min;
        }
        $secsDiff = $diff;

        return ('(elapsed time '.$hrsDiff.'h '.$minsDiff.'m '.$secsDiff.'s)');

    }

    /**
    *   Get manufacturers
    *   @return array
    */
    function get_mfn() {
        $mfn_query = xtc_db_query("select manufacturers_id, manufacturers_name from ".TABLE_MANUFACTURERS);
        while ($mfn = xtc_db_fetch_array($mfn_query)) {
            $mfn_array[$mfn['manufacturers_name']] = array ('id' => $mfn['manufacturers_id']);
        }
        return $mfn_array;
    }

}

// EXPORT

class xtcExport {

    function xtcExport($filename, $time_start = 0) {
        $this->catDepth = 6;
        $this->languages = $this->get_lang();
        $this->filename = $filename;
        $this->CAT = array ();
        $this->PARENT = array ();
        $this->counter = array ('prod_exp' => 0);
        $this->time_start = $time_start==0?time():$time_start;
        $this->man = $this->getManufacturers();
        $this->TextSign = CSV_TEXTSIGN;
        $this->seperator = CSV_SEPERATOR;
        if (CSV_SEPERATOR == '')
        $this->seperator = "\t";
        if (CSV_SEPERATOR == '\t')
        $this->seperator = "\t";
        $this->Groups = xtc_get_customers_statuses();
    }

    /**
    *   Get installed languages
    *   @return array
    */
    function get_lang() {

        $languages_query = xtc_db_query("select languages_id, name, code, image, directory from ".TABLE_LANGUAGES);
        while ($languages = xtc_db_fetch_array($languages_query)) {
            $languages_array[] = array ('id' => $languages['languages_id'], 'name' => $languages['name'], 'code' => $languages['code']);
        }

        return $languages_array;
    }

    function exportProdFile($start = 0, $old_counter = 0) {
        $timestamp = time();
        if ($start == 0) {
            $fp = fopen(DIR_FS_CATALOG.'export/'.$this->filename, "w+");

            $heading = $this->TextSign.'XTSOL'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'action'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_model'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_id'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_stock'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_sorting'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_shipping'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_ekpprice'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_uvpprice'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_tpl'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_manufacturer'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_fsk18'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_priceNoTax'.$this->TextSign.$this->seperator;
            for ($i = 1; $i < count($this->Groups); $i ++) {
                $heading .= $this->TextSign.'p_priceNoTax.'.$this->Groups[$i]['id'].$this->TextSign.$this->seperator;
            }
            if (GROUP_CHECK == 'true') {
                for ($i = 0; $i < count($this->Groups); $i ++) {
                    $heading .= $this->TextSign.'p_groupAcc.'.$this->Groups[$i]['id'].$this->TextSign.$this->seperator;
                }
            }
            $heading .= $this->TextSign.'p_tax'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_status'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_weight'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_ean'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_disc'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_opttpl'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_vpe'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_vpe_status'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'p_vpe_value'.$this->TextSign.$this->seperator;

            // product images

            for ($i = 1; $i < MO_PICS + 1; $i ++) {
                $heading .= $this->TextSign.'p_image.'.$i.$this->TextSign.$this->seperator;
                ;
            }

            $heading .= $this->TextSign.'p_image'.$this->TextSign;

            // add lang fields
            for ($i = 0; $i < sizeof($this->languages); $i ++) {
                $heading .= $this->seperator.$this->TextSign;
                $heading .= 'p_name.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
                $heading .= $this->TextSign.'p_desc.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
                $heading .= $this->TextSign.'p_shortdesc.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
                $heading .= $this->TextSign.'p_keywords.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
                $heading .= $this->TextSign.'p_meta_title.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
                $heading .= $this->TextSign.'p_meta_desc.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
                $heading .= $this->TextSign.'p_meta_key.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
                $heading .= $this->TextSign.'p_url.'.$this->languages[$i]['code'].$this->TextSign;

            }
            // add categorie fields
            for ($i = 0; $i < $this->catDepth; $i ++)
            $heading .= $this->seperator.$this->TextSign.'p_cat.'.$i.$this->TextSign;

            $heading .= "\n";

            fputs($fp, $heading);
        } else {
            $fp = fopen(DIR_FS_CATALOG.'export/'.$this->filename, "a+");
            $this->counter['prod_exp'] = $old_counter;
        }
        // content
        $export_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS . ($start > 0?" WHERE products_id>=" . (int)$start:'') . ' ORDER BY products_id');
        while ($export_data = xtc_db_fetch_array($export_query)) {
            if ((time() - $timestamp) > CSV_TIME_LIMIT) {
                fclose($fp);
                xtc_redirect(xtc_href_link(FILENAME_CSV_BACKEND, 'action=export&content=products&continue=' . $export_data['products_id'] . '&counter=' . $this->counter['prod_exp'] . '&timestart=' . $this->time_start));
            }
            $this->counter['prod_exp']++;
            $line = $this->TextSign.'XTSOL'.$this->TextSign.$this->seperator;
            $line .= $this->TextSign.CSV_DEFAULT_ACTION.$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_model'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_id'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_quantity'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_sort'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_shippingtime'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_ekpprice'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_uvpprice'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['product_template'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$this->man[$export_data['manufacturers_id']].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_fsk18'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_price'].$this->TextSign.$this->seperator;
            // group prices  Qantity:Price::Quantity:Price
            for ($i = 1; $i < count($this->Groups); $i ++) {
                $price_query = "SELECT * FROM personal_offers_by_customers_status_".$this->Groups[$i]['id']." WHERE products_id = '".$export_data['products_id']."'ORDER BY quantity";
                $price_query = xtc_db_query($price_query);
                $groupPrice = '';
                while ($price_data = xtc_db_fetch_array($price_query)) {
                    if ($price_data['personal_offer'] > 0) {
                        $groupPrice .= $price_data['quantity'].':'.$price_data['personal_offer'].'::';
                    }
                }
                $groupPrice .= ':';
                $groupPrice = str_replace(':::', '', $groupPrice);
                if ($groupPrice == ':')
                $groupPrice = "";
                $line .= $this->TextSign.$groupPrice.$this->TextSign.$this->seperator;

            }

            // group permissions
            if (GROUP_CHECK == 'true') {
                for ($i = 0; $i < count($this->Groups); $i ++) {
                    $line .= $this->TextSign.$export_data['group_permission_'.$this->Groups[$i]['id']].$this->TextSign.$this->seperator;
                }
            }

            $line .= $this->TextSign.$export_data['products_tax_class_id'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_status'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_weight'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_ean'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_discount_allowed'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['options_template'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_vpe'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_vpe_status'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_vpe_value'].$this->TextSign.$this->seperator;

            if (MO_PICS > 0) {
                $mo_query = "SELECT * FROM ".TABLE_PRODUCTS_IMAGES." WHERE products_id='".$export_data['products_id']."'";
                $mo_query = xtc_db_query($mo_query);
                $img = array ();
                while ($mo_data = xtc_db_fetch_array($mo_query)) {
                    $img[$mo_data['image_nr']] = $mo_data['image_name'];
                }

            }

            // product images
            for ($i = 1; $i < MO_PICS + 1; $i ++) {
                if (isset ($img[$i])) {
                    $line .= $this->TextSign.$img[$i].$this->TextSign.$this->seperator;
                } else {
                    $line .= $this->TextSign."".$this->TextSign.$this->seperator;
                }
            }

            $line .= $this->TextSign.$export_data['products_image'].$this->TextSign.$this->seperator;

            for ($i = 0; $i < sizeof($this->languages); $i ++) {
                $lang_query = xtc_db_query("SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE language_id='".$this->languages[$i]['id']."' and products_id='".$export_data['products_id']."'");
                $lang_data = xtc_db_fetch_array($lang_query);
                $lang_data['products_description'] = str_replace("\n", "", $lang_data['products_description']);
                $lang_data['products_short_description'] = str_replace("\n", "", $lang_data['products_short_description']);
                $lang_data['products_description'] = str_replace("\r", "", $lang_data['products_description']);
                $lang_data['products_short_description'] = str_replace("\r", "", $lang_data['products_short_description']);
                $lang_data['products_description'] = str_replace(chr(13), "", $lang_data['products_description']);
                $lang_data['products_short_description'] = str_replace(chr(13), "", $lang_data['products_short_description']);
                $line .= $this->TextSign.$lang_data['products_name'].$this->TextSign.$this->seperator;
                $line .= $this->TextSign.$lang_data['products_description'].$this->TextSign.$this->seperator;
                $line .= $this->TextSign.$lang_data['products_short_description'].$this->TextSign.$this->seperator;
                $line .= $this->TextSign.$lang_data['products_keywords'].$this->TextSign.$this->seperator;
                $line .= $this->TextSign.$lang_data['products_meta_title'].$this->TextSign.$this->seperator;
                $line .= $this->TextSign.$lang_data['products_meta_description'].$this->TextSign.$this->seperator;
                $line .= $this->TextSign.$lang_data['products_meta_keywords'].$this->TextSign.$this->seperator;
                $line .= $this->TextSign.$lang_data['products_url'].$this->TextSign.$this->seperator;
            }
            $cat_query = xtc_db_query("SELECT categories_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id='".$export_data['products_id']."'");
            $cat_data = xtc_db_fetch_array($cat_query);

            $line .= $this->buildCAT($cat_data['categories_id']);
            $line .= $this->TextSign;
            $line .= "\n";
            fputs($fp, $line);
        }

        fclose($fp);
        if (COMPRESS_EXPORT=='true') {
            require_once('includes/functions/pclzip.lib.php');
            $backup_file = DIR_FS_CATALOG.'export/' . $this->filename;
            $archive = new PclZip(DIR_FS_CATALOG.'export/products.zip');
            $archive->create($backup_file);
            unlink($backup_file);
        }

        return array (0 => $this->counter, 1 => '', 2 => $this->calcElapsedTime($this->time_start));
    }

    function exportCatFile() {

        $fp = fopen(DIR_FS_CATALOG.'export/'.$this->filename, "w+");

        $heading = $this->TextSign.'XTSOL'.$this->TextSign.$this->seperator;
        $heading .= $this->TextSign.'action'.$this->TextSign.$this->seperator;
        $heading .= $this->TextSign.'c_id'.$this->TextSign.$this->seperator;
        $heading .= $this->TextSign.'c_image'.$this->TextSign.$this->seperator;
        $heading .= $this->TextSign.'c_parent'.$this->TextSign.$this->seperator;
        $heading .= $this->TextSign.'c_status'.$this->TextSign.$this->seperator;
        $heading .= $this->TextSign.'c_tpl'.$this->TextSign.$this->seperator;
        $heading .= $this->TextSign.'c_ltpl'.$this->TextSign.$this->seperator;
        $heading .= $this->TextSign.'c_sort'.$this->TextSign.$this->seperator;
        $heading .= $this->TextSign.'c_prod_sort'.$this->TextSign.$this->seperator;
        $heading .= $this->TextSign.'c_prod_sort2'.$this->TextSign.$this->seperator;

        // group permissions
        if (GROUP_CHECK == 'true') {
            for ($i = 0; $i < count($this->Groups); $i ++) {
                $heading .= $this->TextSign.'c_groupAcc.'.$this->Groups[$i]['id'].$this->TextSign.$this->seperator;
            }
        }

        // add lang fields

        for ($i = 0; $i < sizeof($this->languages); $i ++) {
            $heading .= $this->TextSign.'c_name.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'c_desc.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'c_title.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'c_meta_title.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'c_meta_desc.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'c_meta_key.'.$this->languages[$i]['code'].$this->TextSign.$this->seperator;
        }


        $heading .= "\n";

        fputs($fp, $heading);

        // content

        $export_query = xtc_db_query("SELECT * FROM ".TABLE_CATEGORIES);

        while ($export_data = xtc_db_fetch_array($export_query)) {

            $this->counter['cat_exp']++;

            $line = $this->TextSign.'XTSOL'.$this->TextSign.$this->seperator;
            $line .= $this->TextSign."ignore".$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['categories_id'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['categories_image'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['parent_id'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['categories_status'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['categories_template'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['listing_template'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['sort_order'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_sorting'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_sorting2'].$this->TextSign.$this->seperator;

            // group permissions
            if (GROUP_CHECK == 'true') {
                for ($i = 0; $i < count($this->Groups); $i ++) {
                    $line .= $this->TextSign.$export_data['group_permission_'.$this->Groups[$i]['id']].$this->TextSign.$this->seperator;
                }
            }

            for ($i = 0; $i < sizeof($this->languages); $i ++) {
                $lang_query = xtc_db_query("SELECT * FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE language_id='".$this->languages[$i]['id']."' and categories_id='".$export_data['categories_id']."'");
                $lang_data = xtc_db_fetch_array($lang_query);
                $lang_data['categories_description'] = str_replace("\n", "", $lang_data['categories_description']);
                $lang_data['categories_description'] = str_replace("\r", "", $lang_data['categories_description']);
                $lang_data['categories_description'] = str_replace(chr(13), "", $lang_data['categories_description']);
                $line .= $this->TextSign.$lang_data['categories_name'].$this->TextSign.$this->seperator;
                $line .= $this->TextSign.$lang_data['categories_description'].$this->TextSign.$this->seperator;
                $line .= $this->TextSign.$lang_data['categories_heading_title'].$this->TextSign.$this->seperator;
                $line .= $this->TextSign.$lang_data['categories_meta_title'].$this->TextSign.$this->seperator;
                $line .= $this->TextSign.$lang_data['categories_meta_description'].$this->TextSign.$this->seperator;
                $line .= $this->TextSign.$lang_data['categories_meta_keywords'].$this->TextSign.$this->seperator;
            }

            $line .= "\n";

            fputs($fp, $line);

        }

        fclose($fp);

        if (COMPRESS_EXPORT=='true') {
            require_once('includes/functions/pclzip.lib.php');
            $backup_file = DIR_FS_CATALOG.'export/' . $this->filename;
            $archive = new PclZip(DIR_FS_CATALOG.'export/categories.zip');
            $archive->create($backup_file);
            unlink($backup_file);
        }


        return array (0 => $this->counter, 1 => '', 2 => $this->calcElapsedTime($this->time_start));

    }

    function exportProdToCatFile() {

        $export_query = xtc_db_query("SELECT * FROM ".TABLE_PRODUCTS_TO_CATEGORIES);

        $count = xtc_db_num_rows($export_query);
        $file = explode(".",$this->filename);
        $ending = $file[1];
        $file = $file[0];
        $i = 0;
        $fp = array();
        while ($count>0) {

            $filenam = $i==0?$file. '.' . $ending:$file.$i.'.'.$ending;
            $fp[$i] = fopen(DIR_FS_CATALOG.'export/'.$filenam, "w+");

            $heading = $this->TextSign.'XTSOL'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'action'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'prod_id'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'cat_id'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'prod_name'.$this->TextSign.$this->seperator;
            $heading .= $this->TextSign.'cat_name'.$this->TextSign;

            $heading .= "\n";

            fputs($fp[$i], $heading);
            $count -= 32000;
            $i++;
        }

        // content


        $row = 0;
        $i = 0;
        while ($export_data = xtc_db_fetch_array($export_query)) {

            $this->counter['prod2cat_exp']++;

            $line = $this->TextSign.'XTSOL'.$this->TextSign.$this->seperator;
            $line .= $this->TextSign."ignore".$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['products_id'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$export_data['categories_id'].$this->TextSign.$this->seperator;
            $prod_name_query = xtc_db_query("SELECT products_name FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE language_id='2' and products_id='".$export_data['products_id']."'");
            $cat_name_query = xtc_db_query("SELECT categories_name FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE language_id='2' and categories_id='".$export_data['categories_id']."'");
            $prod_name_data = xtc_db_fetch_array($prod_name_query);
            $cat_name_data = xtc_db_fetch_array($cat_name_query);
            $line .= $this->TextSign.$prod_name_data['products_name'].$this->TextSign.$this->seperator;
            $line .= $this->TextSign.$cat_name_data['categories_name'].$this->TextSign.$this->seperator;
            $line .= "\n";

            fputs($fp[$i], $line);
            $row++;
            if ($row>=32000) {
                $row=0;
                fclose($fp[$i]);
                $i++;
            }


        }

        fclose($fp[$i]);

        if (COMPRESS_EXPORT=='true') {
            require_once('includes/functions/pclzip.lib.php');
            $backup_file = DIR_FS_CATALOG.'export/' . $this->filename;
            $archive = new PclZip(DIR_FS_CATALOG.'export/prod2cat.zip');
            $archive->create($backup_file);
            unlink($backup_file);
        }


        return array (0 => $this->counter, 1 => '', 2 => $this->calcElapsedTime($this->time_start));

    }

    /**
    *   Calculate Elapsed time from 2 given Timestamps
    *   @param int $time old timestamp
    *   @return String elapsed time
    */
    function calcElapsedTime($time) {

        $diff = time() - $time;
        $daysDiff = 0;
        $hrsDiff = 0;
        $minsDiff = 0;
        $secsDiff = 0;

        $sec_in_a_day = 60 * 60 * 24;
        while ($diff >= $sec_in_a_day) {
            $daysDiff ++;
            $diff -= $sec_in_a_day;
        }
        $sec_in_an_hour = 60 * 60;
        while ($diff >= $sec_in_an_hour) {
            $hrsDiff ++;
            $diff -= $sec_in_an_hour;
        }
        $sec_in_a_min = 60;
        while ($diff >= $sec_in_a_min) {
            $minsDiff ++;
            $diff -= $sec_in_a_min;
        }
        $secsDiff = $diff;

        return ('(elapsed time '.$hrsDiff.'h '.$minsDiff.'m '.$secsDiff.'s)');

    }

    function buildCAT($catID) {

        if (isset ($this->CAT[$catID])) {
            return $this->CAT[$catID];
        } else {
            $cat = array ();
            $tmpID = $catID;

            while ($this->getParent($catID) != 0 || $catID != 0) {
                $cat_select = xtc_db_query("SELECT categories_name FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id='".$catID."' and language_id='".$this->languages[0]['id']."'");
                $cat_data = xtc_db_fetch_array($cat_select);
                $catID = $this->getParent($catID);
                $cat[] = $cat_data['categories_name'];

            }
            $catFiller = '';
            for ($i = $this->catDepth - count($cat); $i > 0; $i --) {
                $catFiller .= $this->TextSign.$this->TextSign.$this->seperator;
            }
            $catFiller .= $this->TextSign;
            $catStr = '';
            for ($i = count($cat); $i > 0; $i --) {
                $catStr .= $this->TextSign.$cat[$i -1].$this->TextSign.$this->seperator;
            }
            $this->CAT[$tmpID] = $catStr.$catFiller;
            return $this->CAT[$tmpID];
        }
    }

    /**
    *   Get the tax_class_id to a given %rate
    *   @return array
    */
    function getTaxRates() // must be optimazed (pre caching array)
    {
        $tax = array ();
        $tax_query = xtc_db_query("Select
                                                                              tr.tax_class_id,
                                                                              tr.tax_rate,
                                                                              ztz.geo_zone_id
                                                                              FROM
                                                                              ".TABLE_TAX_RATES." tr,
                                                                              ".TABLE_ZONES_TO_GEO_ZONES." ztz
                                                                              WHERE
                                                                              ztz.zone_country_id='".STORE_COUNTRY."'
                                                                              and tr.tax_zone_id=ztz.geo_zone_id
                                                                              ");
        while ($tax_data = xtc_db_fetch_array($tax_query)) {

            $tax[$tax_data['tax_class_id']] = $tax_data['tax_rate'];

        }
        return $tax;
    }

    /**
    *   Prefetch Manufactrers
    *   @return array
    */
    function getManufacturers() {
        $man = array ();
        $man_query = xtc_db_query("SELECT
                                                                        manufacturers_name,manufacturers_id
                                                                        FROM
                                                                        ".TABLE_MANUFACTURERS);
        while ($man_data = xtc_db_fetch_array($man_query)) {
            $man[$man_data['manufacturers_id']] = $man_data['manufacturers_name'];
        }
        return $man;
    }

    /**
    *   Return Parent ID for a given categories id
    *   @return int
    */
    function getParent($catID) {
        if (isset ($this->PARENT[$catID])) {
            return $this->PARENT[$catID];
        } else {
            $parent_query = xtc_db_query("SELECT parent_id FROM ".TABLE_CATEGORIES." WHERE categories_id='".$catID."'");
            $parent_data = xtc_db_fetch_array($parent_query);
            $this->PARENT[$catID] = $parent_data['parent_id'];
            return $parent_data['parent_id'];
        }
    }

}
?>