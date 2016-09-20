<?php
function post($url,$params, $headers = array()) {
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    $result = curl_exec($ch);
	    curl_close($ch);
        return $result;
    } else {
        echo 'curl should be installed.';
    }
}

class UserClient {
    //private $api_url = 'http://api.userengine.com:9988/interface.php';
    private $api_url = 'http://127.0.0.1:9310/interface.php';

    private $token = null;

    function __construct($access_token, $url = null) {
        if ($url) $this->api_url = $url;
            $this->token = $access_token;
    }
    
    function __destruct() {}
   
    public function setToken($access_token) {
        $this->token = $access_token;
    }

    public function getToken() {
        return $this->token;
    }
    
    public function setURL($url) {
        $this->api_url = $url;
    }

    public function getURL() {
        return $this->api_url;
    }

    public function __call($method,$args) {
        $post_data = array();
        if ( count($args) > 0 ) {
            $arguments = $args[0];
            foreach($arguments as $arg=>$v) {
                $post_data[$arg] = $v;
            }
        }
        $headers = array('Authorization:TOKEN|'.$this->token);
        $request_url = $this->api_url;
        if (substr($request_url, -1) != '?') {
            $request_url = $request_url.'?';
        }

        $tmparr = explode('_',$method);
        $module = $tmparr[0];
        $func = $tmparr[1];
        $request_url .= '_M_='.$module.'&_F_='.$func;
        return post($request_url,$post_data,$headers);
    }
}


