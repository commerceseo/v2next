<?php

/**
 * Shopvoting for XT:Commerce SP2.1
 *
 * @license GPLv2
 * @author Hans-Peter Sausen
 * @copyright 2010 www.web4design.de
 * @version 1.1
 */
class Shopvoting {

    const TABLE_BEWERTUNG = "bewertung";
    const TABLE_BEWERTUNG_CONFIG = "bewertung_config";
    const TABLE_CUSTOMERS_STATUS = "customers_status";
    const TABLE_ORDERS = "orders";
    const COLUMN_SHOPRATING = "bewertung_shoprating";
    const COLUMN_SEITE = "bewertung_seite";
    const COLUMN_VERSAND = "bewertung_versand";
    const COLUMN_SERVICE = "bewertung_service";
    const COLUMN_WARE = "bewertung_ware";
    const COLUMN_SEND_ADMIN_MAIL = "send_admin_email";
    const COLUMN_SEND_REMIND_MAIL = "send_remind_email";
    const COLUMN_CUSTOMERS_READ = "customer_group_read";
    const COLUMN_CUSTOMERS_WRITE = "customer_group_write";
    const COLUMN_CUSTOMERS_CAPTCHA = "customer_group_captcha";
    const COLUMN_ENTRY_FRONTEND = "entry_per_page_frontend";
    const COLUMN_VOTING_STATUS = "voting_module_aktive";
    const FILENAME_BEWERTUNGSSEITE = "shop-bewertungen.php";
    const FILENAME_BEWERTUNGSSEITE_SCHREIBEN = "shop-bewertungen-schreiben.php";
    const FILENAME_BEWERTUNGEN_VERWALTEN = "bewertungen_verwalten.php";
    const FILENAME_BEWERTUNGEN_VERWALTEN_CONFIG = "bewertungen_verwalten_config.php";
    const FILENAME_BEWERTUNGEN_MAIL = "bewertungen_mail.php";
    const POSITIV_RATES = ">=4";
    const NEUTRAL_RATES = "=3";
    const NEGATIVE_RATES = "<=2";

    public $admin_email;
    public $send_admin_email;
    public $customer_group_read;
    public $customer_group_write;
    public $customer_group_captcha;
    public $entry_per_page_frontend;
    public $front_page_character;
    public $required_name;
    public $required_order_id;
    public $required_order_id_email;
    public $required_comment;
    public $orders_id;
    public $voting_module_aktive;
    public $activate_votings;
    public $language_id;
    public $main_path;
    public $img_path;
    public $admin_path;
    public $config = array();

    public function __construct() {
        $this->_readConfig();
    }

    protected function _readConfig() {
        $config = xtc_db_fetch_array(xtc_db_query("SELECT * FROM " . Shopvoting::TABLE_BEWERTUNG_CONFIG));

        $this->admin_email = $config['admin_email'];
        $this->send_admin_email = $config['send_admin_email'];
        $this->customer_group_read = $config['customer_group_read'];
        $this->customer_group_write = $config['customer_group_write'];
        $this->customer_group_captcha = $config['customer_group_captcha'];
        $this->entry_per_page_frontend = $config['entry_per_page_frontend'];
        $this->front_page_character = $config['front_page_character'];
        $this->required_name = $config['required_name'];
        $this->required_order_id = $config['required_order_id'];
        $this->required_order_id_email = $config['required_order_id_email'];
        $this->required_comment = $config['required_comment'];
        $this->orders_id = $config['orders_id'];
        $this->voting_module_aktive = $config['voting_module_aktive'];
        $this->activate_votings = $config['activate_votings'];
        $this->language_id = (int) $_SESSION['languages_id'];
        $this->main_path = "";
        $this->img_path = "templates/" . CURRENT_TEMPLATE . "/" . $this->main_path . "img/shopwertung/";
        $this->admin_path = "../admin/";

        if ($this->entry_per_page_frontend == 0) {
            $this->entry_per_page_frontend = 10;
        }
    }

    public function getGroupAccess($checkString, $customers_id) {
        $checkArray = explode(',', $checkString);
        $checks = 0;
        if ($checkArray[0] != '') {
            foreach ($checkArray as $key => $value) {
                if ($customers_id == $value) {
                    $checks = 1;
                    break;
                }
            }
        }
        return $checks;
    }

    public function getCountSingleVotes($condition, $column) {
        $select_count = xtc_db_query("
            SELECT
                count(" . xtc_db_input($column) . ") as crating                                        
            FROM
                " . self::TABLE_BEWERTUNG . "
            WHERE
                bewertung_sprache = '" . xtc_db_input($this->language_id) . "'
            AND
                bewertung_status = 1
            AND
                " . xtc_db_input($column) . " " . xtc_db_input($condition));

        $anzahl = xtc_db_fetch_array($select_count);
        return $anzahl['crating'];
    }

    public function getCountDetailVotes($condition) {
        $select_count = xtc_db_query("
            SELECT
                count(" . xtc_db_input($condition) . ") as gesamtanzahl                                        
            FROM
                " . self::TABLE_BEWERTUNG . "
            WHERE
                bewertung_sprache = '" . xtc_db_input($this->language_id) . "'
            AND
                bewertung_status = 1
            AND
                " . xtc_db_input($condition) . " > 0");

        $anzahl = xtc_db_fetch_array($select_count);
        return $anzahl['gesamtanzahl'];
    }

    public function getPercentage($counts) {
        $select_count = xtc_db_query("
            SELECT
                " . xtc_db_input($counts) . " * 100 / count(bewertung_shoprating) as prozent                                        
            FROM
                " . self::TABLE_BEWERTUNG . "
            WHERE
                bewertung_sprache = '" . xtc_db_input($this->language_id) . "'
            AND
                bewertung_status = 1");

        $anzahl = xtc_db_fetch_array($select_count);
        return number_format($anzahl['prozent'], 2, ".", "");
    }

    public function getAverage($column) {
        $select_count = xtc_db_query("
            SELECT
                sum(" . xtc_db_input($column) . ") / count(" . xtc_db_input($column) . ") as durchschnitt                                        
            FROM
                " . self::TABLE_BEWERTUNG . "
            WHERE
                bewertung_sprache = '" . xtc_db_input($this->language_id) . "'
            AND
                bewertung_status = 1
            AND
                " . xtc_db_input($column) . " > 0");

        $anzahl = xtc_db_fetch_array($select_count);
        return number_format($anzahl['durchschnitt'], 2, ".", "");
    }

    public function getStar($valuation) {
        $fullstars = array();

        $stars = floor($valuation);
        $fractal = substr($valuation - $stars, 2);
        if (strlen($fractal) <= 1) {
            $fractal .= "0";
        }

        for ($count = 0; $count < $stars; $count++) {
            $desc = $count + 1;
            $fullstars[] = xtc_image($this->img_path . "star_1.png", STERN_BEWERTUNGEN . ' ' . $desc) . " ";
        }


        if ($fractal <= 99 AND $fractal > 75) {
            $fullstars[] = xtc_image($this->img_path . "star_5.png", STERN_STUCK . ' 0.' . $fractal) . " ";
        } elseif ($fractal <= 75 AND $fractal > 50) {
            $fullstars[] = xtc_image($this->img_path . "star_4.png", STERN_STUCK . ' 0.' . $fractal) . " ";
        } elseif ($fractal <= 50 AND $fractal > 33) {
            $fullstars[] = xtc_image($this->img_path . "star_3.png", STERN_STUCK . ' 0.' . $fractal) . " ";
        } elseif ($fractal <= 33 AND $fractal > 0) {
            $fullstars[] = xtc_image($this->img_path . "star_2.png", STERN_STUCK . ' 0.' . $fractal) . " ";
        }

        while (count($fullstars) < 5) {
            $fullstars[] = xtc_image($this->img_path . "/star_6.png", STERN_KEIN) . " ";
        }

        return $starsout = implode('', $fullstars);
    }

    public function getReviewSQL($frontend, $show = '') {
        $where_string = '';

        if ($frontend == true) {
            switch ($show) {
                case "positive":
                    $contain = "AND " . self::COLUMN_SHOPRATING . " " . self::POSITIV_RATES;
                    break;
                case "neutrale":
                    $contain = "AND " . self::COLUMN_SHOPRATING . " " . self::NEUTRAL_RATES;
                    break;
                case "negative":
                    $contain = "AND " . self::COLUMN_SHOPRATING . " " . self::NEGATIVE_RATES;
                    break;
                default:
                    $contain = "";
            }

            $where_string = "
                WHERE
                    bewertung_sprache = '" . xtc_db_input($this->language_id) . "'
                AND
                    bewertung_status = 1
                    " . xtc_db_input($contain);
        }

        $vote_query = "SELECT *
                       FROM
                           " . self::TABLE_BEWERTUNG . "
                           " . $where_string . "
                       ORDER BY
                           bewertung_id DESC";

        return $vote_query;
    }

    public function getReviewVotes($page, $vote_split) {
        $module_data = array();
        $vote_all = array();

        if ($vote_split->number_of_rows > 0) {
            $vote_query = xtc_db_query($vote_split->sql_query);
            while ($votingArray = xtc_db_fetch_array($vote_query)) {
                $voting_data[] = array(
                    'DATUM' => date('d.m.Y H:i', strtotime($votingArray['bewertung_datum'])),
                    'AUTHOR' => xtc_db_prepare_input($votingArray['bewertung_vorname']) . ' ' . (substr($votingArray['bewertung_nachname'], 0, 1)),
                    'AUTHORLST' => strlen($votingArray['bewertung_nachname']),
                    'TEXT' => xtc_db_prepare_input(nl2br($votingArray['bewertung_text'])),
                    'KOMMENTAR' => xtc_db_prepare_input(nl2br($votingArray['bewertung_kommentar'])),
                    'RATING' => xtc_image($this->img_path . 'sterne_' . $votingArray[self::COLUMN_SHOPRATING] . '.gif', sprintf($votingArray[self::COLUMN_SHOPRATING], $votingArray[self::COLUMN_SHOPRATING])));
            }
        }
        return $voting_data;
    }

    public function getLoginCustomersValues($customers_id) {
        $customers_values = xtc_db_fetch_array(xtc_db_query(
                        "SELECT
                 customers_email_address,
                 customers_firstname,
                 customers_lastname
             FROM
                 " . TABLE_CUSTOMERS . "
             WHERE
                 customers_id = " . xtc_db_input($customers_id))
        );
        return $customers_values;
    }

    public function getRadioButtons($rating = '', $name) {
        for ($i = 1; $i <= 5; $i++) {
            switch ($i) {
                case 1:
                    $wertung = WERTUNG1;
                    break;
                case 2:
                    $wertung = WERTUNG2;
                    break;
                case 3:
                    $wertung = WERTUNG3;
                    break;
                case 4:
                    $wertung = WERTUNG4;
                    break;
                case 5:
                    $wertung = WERTUNG5;
                    break;
            }

            if ($rating == $i) {
                $radio .= '<input type="radio" class="star" name="' . $name . '" value="' . $i . '" title="' . $wertung . '" checked="checked" />';
            } else {
                $radio .= '<input type="radio" class="star" name="' . $name . '" value="' . $i . '" title="' . $wertung . '" />';
            }
        }
        return $radio;
    }

    protected function _sendAdminMail($vars_array) {
        if ($vars_array['voting_customers_firstname'] == "") {
            $firstname = GAST;
        } else {
            $firstname = $vars_array['voting_customers_firstname'];
        }
        $mailinhalt = MAIL_BEWERTUNG_VON . " " . $firstname . " " . $vars_array['voting_customers_lastname'] . "<br />" .
                MAIL_BEWERTUNG_ABSENDER . " " . $vars_array['voting_customers_email'] . "<br /><br />" .
                MAIL_BEWERTUNG_KOMMENTAR . "<br />" . nl2br($vars_array['kommentar']) . "<br /><br />" .
                MAIL_BEWERTUNG_SHOP . " " . $vars_array['ratingshop'] . " <br />" .
                MAIL_BEWERTUNG_WARE . " " . $vars_array['ratingware'] . " <br />" .
                MAIL_BEWERTUNG_VERSAND . " " . $vars_array['ratingversand'] . " <br />" .
                MAIL_BEWERTUNG_SERVICE . " " . $vars_array['ratingservice'] . " <br />" .
                MAIL_BEWERTUNG_SEITE . " " . $vars_array['ratingseite'] . " <br />";

        xtc_php_mail($bewertungs_mail, SHOPBEWERTUNG_FORMULAR, $this->admin_email, $this->admin_email, '', $bewertungs_mail, '', $path_to_attachement, $path_to_more_attachements, SHOPBEWERTUNG_ERHALTEN, $mailinhalt, $mailinhalt);
    }

    public function getErrorCheck($vars_array) {
        $error_array = array();

        if (!xtc_validate_email($vars_array['voting_customers_email']) || strlen($vars_array['voting_customers_email']) < 6) {
            $error_array[] = MAILERROR;
        }

        if ($vars_array['ratingshop'] <= 0) {
            $error_array[] = RATINGERROR;
        }

        if ($vars_array['captchacheck'] == 1) {
            if ($vars_array['vvcode'] != $_SESSION['vvcode']) {
                $error_array[] = CAPTCHAERROR;
            }
        }

        if ($this->required_name == 1) {
            if (strlen($vars_array['voting_customers_firstname']) <= 0 || strlen($vars_array['voting_customers_lastname']) <= 0) {
                $error_array[] = NAMEERROR;
            }
        }

        if ($this->required_order_id == 1) {
            if ($this->required_order_id_email == 1) {
                if ($this->_getOrdersIdInfoMail($vars_array['orders_id']) != $vars_array['voting_customers_email']) {
                    $error_array[] = ORDERIDERROREMAIL;
                }
            } else {
                if ($this->_getOrdersIdInfo($vars_array['orders_id']) == 0) {
                    $error_array[] = ORDERIDERROR;
                }
            }
        }

        if ($this->required_comment == 1) {
            if (strlen($vars_array['kommentar']) <= 0) {
                $error_array[] = COMMENTERROR;
            }
        }

        $error_string = "";

        foreach ($error_array as $key) {
            if (count($error_array) >= 1) {
                $error_string .= $key . "<br />";
            } else {
                $error_string .= $key;
            }
        }
        return $error_string;
    }

    public function setDbVoting($vars_array) {
        if ($vars_array['voting_customers_firstname'] == "") {
            $firstname = GAST;
        } else {
            $firstname = $vars_array['voting_customers_firstname'];
        }

        if ($this->activate_votings == 1) {
            $activate = 1;
        } else {
            $activate = 0;
        }

        $insert_sql = array(
            'bewertung_shoprating' => $vars_array['ratingshop'],
            'bewertung_seite' => $vars_array['ratingseite'],
            'bewertung_versand' => $vars_array['ratingversand'],
            'bewertung_ware' => $vars_array['ratingware'],
            'bewertung_service' => $vars_array['ratingservice'],
            'bewertung_vorname' => $firstname,
            'bewertung_nachname' => $vars_array['voting_customers_lastname'],
            'bewertung_kundenid' => $vars_array['bewertung_kundenid'],
            'bewertung_datum' => 'now()',
            'bewertungs_ip' => $vars_array['bewertungs_ip'],
            'bewertung_sprache' => $vars_array['bewertung_sprache'],
            'bewertung_status' => $activate,
            'orders_id' => $vars_array['orders_id'],
            'bewertungs_email' => $vars_array['voting_customers_email'],
            'bewertung_text' => $vars_array['kommentar'],
        );

        xtc_db_perform(self::TABLE_BEWERTUNG, $insert_sql, 'insert');

        if ($this->send_admin_email == 1) {
            $this->_sendAdminMail($vars_array);
        }
    }

    protected function _getOrdersIdInfoMail($orders_id) {
        $email = xtc_db_fetch_array(xtc_db_query("SELECT customers_email_address  FROM " . self::TABLE_ORDERS . " WHERE orders_id = '" . xtc_db_input($orders_id) . "'"));
        return $email['customers_email_address'];
    }

    protected function _getOrdersIdInfo($orders_id) {
        $counts = xtc_db_fetch_array(xtc_db_query("SELECT count(orders_id) as anzahl FROM " . self::TABLE_ORDERS . " WHERE orders_id = '" . xtc_db_input($orders_id) . "'"));
        return $counts['anzahl'];
    }

    public function setAdminVoting() {
        $update_sql = array(
            'bewertung_shoprating' => (int) $_POST['dropshopb'],
            'bewertung_seite' => (int) $_POST['dropseiteb'],
            'bewertung_versand' => (int) $_POST['dropversandb'],
            'bewertung_ware' => (int) $_POST['dropwareb'],
            'bewertung_service' => (int) $_POST['dropserviceb'],
            'bewertung_text' => $_POST['bewertungstext'],
            'bewertung_kommentar' => $_POST['adminkommentar'],
            'bewertung_datum' => $_POST['admindatum'],
        );
        xtc_db_perform(self::TABLE_BEWERTUNG, $update_sql, 'update', "bewertung_id = '" . xtc_db_input((int) $_POST['sid']) . "'");
    }

    public function setAdminConfig() {
        $update_sql = array(
            'admin_email' => trim($_POST['email']),
            'send_admin_email' => (int) $_POST['email_send'],
            'customer_group_read' => $this->groupImport($_POST['kdlesen']),
            'customer_group_write' => $this->groupImport($_POST['kdschreiben']),
            'entry_per_page_frontend' => (int) $_POST['pro_seite'],
            'required_name' => (int) $_POST['required_name'],
            'required_order_id' => (int) $_POST['required_order_id'],
            'required_order_id_email' => (int) $_POST['required_order_id_email'],
            'required_comment' => (int) $_POST['required_comment'],
            'front_page_character' => (int) $_POST['front_page_character'],
            'customer_group_captcha' => $this->groupImport($_POST['kdcaptcha']),
            'activate_votings' => (int) $_POST['activate_votings'],
            'voting_module_aktive' => (int) $_POST['bewertung_aktiv'],
        );

        xtc_db_perform(self::TABLE_BEWERTUNG_CONFIG, $update_sql, 'update', "id = '0'");
    }

    public function groupImport($checkarray) {
        if (count($checkarray) > 0) {
            $sqlimport = implode(',', $checkarray);
        } else {
            $sqlimport = '';
        }
        return $sqlimport;
    }

    public function setUpdateDeleteVoting($action, $sid = '') {
        switch ($action) {
            case deaktivieren:
                if (count($_POST['bcheck']) > 0) {
                    foreach ($_POST['bcheck'] as $key) {
                        xtc_db_query("UPDATE " . self::TABLE_BEWERTUNG . " SET bewertung_status = '0' WHERE bewertung_id = " . xtc_db_input((int) $key));
                    }
                }
                break;
            case aktivieren:
                if (count($_POST['bcheck']) > 0) {
                    foreach ($_POST['bcheck'] as $key) {
                        xtc_db_query("UPDATE " . self::TABLE_BEWERTUNG . " SET bewertung_status = '1' WHERE bewertung_id = " . xtc_db_input((int) $key));
                    }
                }
                break;
            case delete:
                if (count($_POST['bcheck']) > 0) {
                    foreach ($_POST['bcheck'] as $key) {
                        xtc_db_query("DELETE FROM " . self::TABLE_BEWERTUNG . "  WHERE bewertung_id = " . xtc_db_input((int) $key));
                    }
                }
                break;
            case deletesingle:
                xtc_db_query("DELETE FROM " . self::TABLE_BEWERTUNG . "  WHERE bewertung_id = " . xtc_db_input((int) $sid));
                break;
        }
    }

    public function setVotingStatus($status, $id) {
        xtc_db_query("UPDATE " . self::TABLE_BEWERTUNG . " SET bewertung_status = '" . xtc_db_input((int) $status) . "' WHERE bewertung_id = " . xtc_db_input((int) $id));
    }

    public function getLanguageFlag($id) {
        $flagsql = xtc_db_fetch_array(xtc_db_query("SELECT directory FROM languages WHERE languages_id = '" . xtc_db_input($id) . "'"));
        return $flagsql['directory'];
    }

    public function getDropdownSelected($rating) {
        $optionstring = "";
        for ($i = 0; $i <= 5; $i++) {
            if ($rating == $i) {
                $optionstring .= '<option value="' . $i . '" selected="selected">' . $i . '</option>';
            } else {
                $optionstring .= '<option value="' . $i . '">' . $i . '</option>';
            }
        }
        return $optionstring;
    }

    protected function _getCustomerGroup() {
        $group_sql = xtc_db_query("SELECT customers_status_id,customers_status_name FROM " . self::TABLE_CUSTOMERS_STATUS . " WHERE  language_id = " . (int) $_SESSION['languages_id']);

        while ($group = xtc_db_fetch_array($group_sql)) {
            $customers_group[] = array(
                "ID" => $group['customers_status_id'],
                "NAME" => $group['customers_status_name'],
            );
        }
        return $customers_group;
    }

    public function getCheckedBoxes($id, $checkarray) {
        foreach ($checkarray as $key) {
            if ($key == $id) {
                $checker = 'checked="checked"';
            }
        }
        return $checker;
    }

    public function getCheckboxGroup($name, $selectgroup) {
        $groups = $this->_getCustomerGroup();
        $boxstring = "";
        for ($i = 0; $i < count($groups); $i++) {
            $boxstring .="<br /><input type=\"checkbox\" name=\"" . $name . "\" value=\"" . $groups[$i]['ID'] . "\" " .
                    $this->getCheckedBoxes($groups[$i]['ID'], explode(",", $selectgroup)) . " /> " . $groups[$i]['NAME'];
        }
        return $boxstring;
    }

}
