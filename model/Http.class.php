<?php
require_once('HttpResponse.class.php');
class Http {

    private $http;

    private $header;
	
    public function __construct() {
        $this->http = curl_init();
        curl_setopt($this->http, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->http, CURLOPT_BINARYTRANSFER, true);
        //curl_setopt($this->http, CURLINFO_HEADER_OUT, true);
        $this->header = array();
    }

    public function __destruct() {
        $this->clear();
    }

    public function clear() {
        curl_close($this->http);
    }

    public function set_header($key, $value) {
        $this->header[] = $key.": ".$value;
    }

    public function get($url) {
        return $this->_get_response($url);
    }

   public function post($url, $data='', $cookie=null) {
        curl_setopt($this->http, CURLOPT_POST, true);
        curl_setopt($this->http, CURLOPT_POSTFIELDS, $data);
		if ($cookie) {
			curl_setopt($this->http, CURLOPT_COOKIEFILE, $cookie);
			curl_setopt($this->http, CURLOPT_COOKIEJAR, $cookie);
		}
        return $this->_get_response($url);
    }

    private function _get_response($url) {
        curl_setopt($this->http, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($this->http, CURLOPT_URL, $url);
        curl_setopt($this->http, CURLOPT_CONNECTTIMEOUT, 1);

        $text = curl_exec($this->http);
        $status = $this->_get_response_status();
        // print_r($text); die();
        return new HttpResponseTest($status, $text);
    }

    private function _get_response_status() {
        return curl_getinfo($this->http, CURLINFO_HTTP_CODE);
        $this->clear();
    }

    public static function get_url_encode($data = array()) {
        $parts = array();
        while (list($key, $value) = each($data)) {
            if (is_array($value) || is_object($value)) {
                while (list($x, $y) = each($value)) {
                    $parts[] = urlencode($key).'[]='.urlencode($y);
                }
            } else {
                $parts[] = urlencode($key).'='.urlencode($value);
            }
        }
        return join('&', $parts);
    }


}
	
?>
