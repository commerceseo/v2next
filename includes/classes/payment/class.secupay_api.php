<?php
/*
define('SECUPAY_HOST', 'api-dist.secupay-ag.de');
define('SECUPAY_URL', 'https://'.SECUPAY_HOST.'/payment/');
define('SECUPAY_PATH', '/payment/');
define('SECUPAY_PORT', 443);
*/

define('SECUPAY_HOST', 'api.secupay.ag');
define('SECUPAY_URL', 'https://'.SECUPAY_HOST.'/payment/');
define('SECUPAY_PATH', '/payment/');
define('SECUPAY_PORT', 443);

// prevent deprecated date() function warning messages
ini_set("date.timezone", "Europe/Berlin");
define('API_VERSION', '2.3');

//diese Funktion existiert in php4 noch nicht und wird daher in diesem Fall erzeugt
if (!function_exists('http_build_query')) {

    function http_build_query($data, $prefix='', $sep='&', $key='') {
        $ret = array();
        foreach ((array) $data as $k => $v) {
            if (is_int($k) && $prefix != null)
                $k = urlencode($prefix . $k);
            if (!empty($key))
                $k = $key . '[' . urlencode($k) . ']';

            if (is_array($v) || is_object($v))
                array_push($ret, http_build_query($v, '', $sep, $k));
            else
                array_push($ret, $k . '=' . urlencode($v));
        }

        if (empty($sep))
            $sep = ini_get('arg_separator.output');
        return implode($sep, $ret);
    }
}

if(!function_exists('json_encode')) {

    function json_encode( $data ) {
        if( is_array($data) || is_object($data) ) {
            $islist = is_array($data) && ( empty($data) || array_keys($data) === range(0,count($data)-1) );

            if( $islist ) {
                $json = '[' . implode(',', array_map('json_encode', $data) ) . ']';
            } else {
                $items = Array();
                foreach( $data as $key => $value ) {
                    $items[] = json_encode("$key") . ':' . json_encode($value);
                }
                $json = '{' . implode(',', $items) . '}';
            }
        } elseif( is_string($data) ) {
            # Escape non-printable or Non-ASCII characters.
            # I also put the \\ character first, as suggested in comments on the 'addclashes' page.
            $string = '"' . addcslashes($data, "\\\"\n\r\t/" . chr(8) . chr(12)) . '"';
            $json    = '';
            $len    = strlen($string);
            # Convert UTF-8 to Hexadecimal Codepoints.
            for( $i = 0; $i < $len; $i++ ) {

                $char = $string[$i];
                $c1 = ord($char);

                # Single byte;
                if( $c1 <128 ) {
                    $json .= ($c1 > 31) ? $char : sprintf("\\u%04x", $c1);
                    continue;
                }

                # Double byte
                $c2 = ord($string[++$i]);
                if ( ($c1 & 32) === 0 ) {
                    $json .= sprintf("\\u%04x", ($c1 - 192) * 64 + $c2 - 128);
                    continue;
                }

                # Triple
                $c3 = ord($string[++$i]);
                if( ($c1 & 16) === 0 ) {
                    $json .= sprintf("\\u%04x", (($c1 - 224) <<12) + (($c2 - 128) << 6) + ($c3 - 128));
                    continue;
                }

                # Quadruple
                $c4 = ord($string[++$i]);
                if( ($c1 & 8 ) === 0 ) {
                    $u = (($c1 & 15) << 2) + (($c2>>4) & 3) - 1;

                    $w1 = (54<<10) + ($u<<6) + (($c2 & 15) << 2) + (($c3>>4) & 3);
                    $w2 = (55<<10) + (($c3 & 15)<<6) + ($c4-128);
                    $json .= sprintf("\\u%04x\\u%04x", $w1, $w2);
                }
            }
        } else {
            # int, floats, bools, null
            $json = strtolower(var_export( $data, true ));
        }
        return $json;
    }
}

if ( !function_exists('json_decode') ){

    function json_decode($json) {
        $comment = false;
        $out = '$x=';

        $json = str_replace('\u00fc','ü',$json);
        $json = str_replace('\u00f6','ö',$json);
        $json = str_replace('\u00e4','ä',$json);

        $json = str_replace('\u00dc','Ü',$json);
        $json = str_replace('\u00d6','Ö',$json);
        $json = str_replace('\u00c4','Ä',$json);

        $json = str_replace('\u00df','ß',$json);

        for ($i=0; $i<strlen($json); $i++) {
            if (!$comment) {
                if (($json[$i] == '{') || ($json[$i] == '[')) {
                    $out .= ' array(';
                } else if (($json[$i] == '}') || ($json[$i] == ']')) {
                    $out .= ')';
                } else if ($json[$i] == ':') {
                    $out .= '=>';
                } else {
                    $out .= $json[$i];
                }
            }
            else $out .= $json[$i];
            if ($json[$i] == '"' && $json[($i-1)]!="\\")    $comment = !$comment;
        }
        eval($out . ';');

        $ausgabe = new stdClass();
        foreach($x as $k => $v){
            $ausgabe->$k = $v;
        }

        return $ausgabe;
    }

}

if ( !function_exists('file_put_contents')){

    function file_put_contents($filename, $data, $flag = false) {
        $mode = ($flag == FILE_APPEND || strtoupper($flag) == 'FILE_APPEND') ? 'a' : 'w';
        $f = @fopen($filename, $mode);
        if ($f === false) {
            return 0;
        } else {
            if (is_array($data)) $data = implode($data);
            fwrite($f, $data);
            fclose($f);
        }
    }

}

if ( !function_exists('seems_utf8')){
	function seems_utf8($Str) {
		for ($i=0; $i<strlen($Str); $i++) {
			if (ord($Str[$i]) < 0x80) continue; # 0bbbbbbb
			else if ((ord($Str[$i]) & 0xE0) == 0xC0) $n=1; # 110bbbbb
			else if ((ord($Str[$i]) & 0xF0) == 0xE0) $n=2; # 1110bbbb
			else if ((ord($Str[$i]) & 0xF8) == 0xF0) $n=3; # 11110bbb
			else if ((ord($Str[$i]) & 0xFC) == 0xF8) $n=4; # 111110bb
			else if ((ord($Str[$i]) & 0xFE) == 0xFC) $n=5; # 1111110b
			else return false; // Does not match any model
	 
			for ($j=0; $j<$n; $j++) { 
				// n bytes matching 10bbbbbb follow ?
				if ((++$i == strlen($Str)) || ((ord($Str[$i]) & 0xC0) != 0x80)) {
					return false;
				}
			}
		}
		return true;
	}
} 
if ( !function_exists('utf8_ensure')){
	function utf8_ensure($data) {
		if (is_string($data)) {
			return seems_utf8($data)? $data: utf8_encode($data); 
		} else if (is_array($data)) {
			foreach ($data as $key => $value) {
				$data[$key] = utf8_ensure($value);
			}
			unset($value);
			unset($key);
		} else if (is_object($data)) {
			foreach ($data as $key => $value) {
				$data->$key = utf8_ensure($value);
			}
			unset($value);
			unset($key);		
		}
		return $data;
	}
}

/**
 * Secupay logging function
 *
 * @param bool true when we want to log
 */
if (!function_exists('secupay_log')) {

    function secupay_log($log) {
//      date_default_timezone_set("Europe/Berlin");
        static $logfile="logfiles/splog.php";
        if(!$log){
            return;
        }
        $date = date("r");
        $x = 0;
        foreach(func_get_args() as $val){
            $x++;
            if ($x == 1)
                continue;
            if (is_string($val) || is_numeric($val)) {
                file_put_contents(DIR_FS_CATALOG . $logfile, "[{$date}] {$val}\n", FILE_APPEND);
            } else {
                file_put_contents(DIR_FS_CATALOG . $logfile, "[{$date}] ".print_r($val, true)."\n", FILE_APPEND);
            }
        }
    }
}


if (!class_exists("secupay_api")) {

    /**
     * Class that handles SecupayApi requests and responses
     */
    class secupay_api
    {
        var $req_format,
            $data,
            $req_function,
            $error,
            $sp_log,
            $language;

        /**
         * Constructor
         *
         * @param array params
         * @param string - the name of the required function to call (init or status/hash) or other
         * @param string format, default application/json
         * @param bool sp_log - true if you want this class to log the request and response data
         */
        function secupay_api($params, $req_function = 'init', $format = 'application/json', $sp_log = false, $language = 'de_DE')
        {
            $this->req_function = $req_function;
            $this->req_format = $format;
            $this->data = $params;
            $this->sp_log = $sp_log;
            $this->language = $language;
        }

        /**
         * Function that creates request and sends it to Secupay
         *
         * @return response
         */
        function request()
        {
            $rc = null;
            if (function_exists("curl_init")) {
                $rc = $this->request_by_curl();
            } else {
                $rc = $this->request_by_socketstream();
            }

            return $rc;
        }

        /**
         * Function that creates request by curl
         *
         * @return object response
         */
        function request_by_curl()
        {
            $_data = json_encode(utf8_ensure($this->data));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, SECUPAY_URL . $this->req_function);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $_data);
            // headers for APIv2
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: '.$this->req_format,
                'Content-Type: application/json',
                'User-Agent: XTC-client 1.0.0',
                'Accept-Language: '.$this->language
            ));
            secupay_log($this->sp_log, 'CURL request for '. SECUPAY_URL . $this->req_function.' in format : '.$this->req_format .' language: '.$this->language);
            secupay_log($this->sp_log, $_data);

            $rcvd = curl_exec($ch);
            secupay_log($this->sp_log, 'Response: ' . $rcvd);

            $this->sent_data = json_encode($_data);
            $this->recvd_data = $rcvd;

            curl_close($ch);
            return $this->parse_answer($this->recvd_data);
        }

        /**
         * Function that parses answer from Secupay
         *
         * @return parsed object
         */
        function parse_answer($ret)
        {
            switch (strtolower($this->req_format)) {
            case 'application/json':
                $answer = json_decode($ret);
                break;
            case 'text/xml':
                $answer = simplexml_load_string($ret);
                break;
            }
            return $answer;
        }

        /**
         * Function that creates request through fsockopen
         *
         * @return object response or false on error
         */
        function request_by_socketstream()
        {
            $_data = json_encode(utf8_ensure($this->data));

            $rcvd = "";
            $rcv_buffer = "";
            $fp = fsockopen('ssl://' . SECUPAY_HOST, SECUPAY_PORT, $errstr, $errno);

            if (!$fp) {
                $this->error = "can't connect to secupay api";
                return false;
            } else {
                $req = "POST ".SECUPAY_PATH . $this->req_function." HTTP/1.1\r\n";
                $req.= "Host: ".SECUPAY_HOST."\r\n";
                $req.= "Content-type: application/json; Charset:UTF8\r\n";
                $req.= "Accept: ".$this->req_format."\r\n";
                $req.= "User-Agent: XTC-client 1.0.0\r\n";
                $req.= "Accept-Language: ".$this->language."\r\n";
                $req.= "Content-Length: ". strlen($_data). "\r\n";
                $req.= "Connection: close\r\n\r\n";
                $req.= $_data;

                fputs($fp, $req);
            }
            secupay_log($this->sp_log, 'SocketStream request for '. SECUPAY_HOST . SECUPAY_PATH . $this->req_function.' in format : '.$this->req_format  .' language: '.$this->language);
            secupay_log($this->sp_log, $_data);

            while (!feof($fp)) {
                $rcv_buffer = fgets($fp, 128);
                $rcvd .= $rcv_buffer;
            }
            fclose($fp);

            secupay_log($this->sp_log, 'Response data:');
            secupay_log($this->sp_log, $rcvd);

            $pos = strpos($rcvd, "\r\n\r\n");
            $rcvd = substr($rcvd, $pos + 4);

            $this->sent_data = $_data;
            $this->recvd_data = $rcvd;

            return $this->parse_answer($this->recvd_data);
        }
    }
}