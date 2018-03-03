<?php
namespace Awolacademy\Webinarjam;

class BaseJandjeJam {

    protected $api_key;
    protected $verify_ssl = true;

    /**
     * Create a new instance
     * @param string $api_key Your WebinarJam API key
     */
    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    public function getWebinar($method, $args=array(), $timeout=10)
    {
        return $this->makeRequest('getwebinar', $method, $args, $timeout);
    }

    public function getAllWebinars($method, $args=array(), $timeout=10)
    {
        return $this->makeRequest('getallwebinars', $method, $args, $timeout);
    }

    public function registerToWebinar($method, $args=array(), $timeout=10)
    {
        return $this->makeRequest('registertowebinar', $method, $args, $timeout);
    }

    /**
     * Performs the underlying HTTP request. Not very exciting
     * @param  string $http_verb   The HTTP verb to use: get, post, put, patch, delete
     * @param  string $method       The API method to be called
     * @param  array  $args         Assoc array of parameters to be passed
     * @return array|boolean        Assoc array of decoded result
     * @throws
     */
    protected function makeRequest($http_verb, $method, $args=array(), $timeout=10)
    {

        if (function_exists('curl_init') && function_exists('curl_setopt')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/vnd.api+json',
                'Content-Type: application/vnd.api+json', $this->api_key));
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            switch($http_verb) {
                case 'getwebinar':
                    $url = $this->endpoint.'/'.$method.'?api_key='.$this->api_key.'&webinar_id='.$args['webinar_id'];
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    break;

                case 'getallwebinars':
                    $url = $this->endpoint.'/'.$method.'?api_key='.$this->api_key;
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    break;

                case 'registertowebinar':
                    $url = $this->endpoint.'/'.$method.'?'.http_build_query(array_merge($args, ['api_key' => $this->api_key]));
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    break;
            }


            $result = curl_exec($ch);

            if (!curl_errno($ch)) {
                switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                    case 200:  # OK
                    break;
                default:
                    $info = curl_getinfo($ch);
                    if (function_exists('log_message')) {
                        log_message('system', sprintf('WebinarJAM Failure: %s %s', print_r($info, true), print_r($result, true)));
                    }
                    throw new WebinarJamException(sprintf('WJAM HTTP Failure: %s', $http_code), $http_code);
                }
            }

            curl_close($ch);
        } else {
            throw new \Exception("cURL support is required, but can't be found.");
        }

        return $result ? json_decode($result, true) : false;
    }

}