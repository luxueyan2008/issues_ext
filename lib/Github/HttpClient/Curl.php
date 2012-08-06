<?php

/**
 * Performs requests on GitHub API. API documentation should be self-explanatory.
 *
 * @author    Thibault Duplessis <thibault.duplessis at gmail dot com>
 * @license   MIT License
 */
class Github_HttpClient_Curl extends Github_HttpClient
{
    /**
     * Send a request to the server, receive a response
     *
     * @param  string   $url          Request url
     * @param  array    $parameters    Parameters
     * @param  string   $httpMethod    HTTP method to use
     * @param  array    $options       Request options
     *
     * @return string   HTTP response
     */
    public function doRequest($url, array $parameters = array(), $httpMethod = 'GET', array $options = array())
    {
        $curlOptions = array();

        if ($options['login']) {
            switch ($options['auth_method']) {
                case Github_Client::AUTH_HTTP_PASSWORD:
                    $curlOptions += array(
                        CURLOPT_USERPWD => $options['login'].':'.$options['secret'],
                    );
                    break;
                case Github_Client::AUTH_HTTP_TOKEN:
                    $curlOptions += array(
                        CURLOPT_USERPWD => $options['login'].'/token:'.$options['secret'],
                    );
                    break;
                case Github_Client::AUTH_URL_TOKEN:
                default:
                    $parameters = array_merge(array(
                        'login' => $options['login'],
                        'token' => $options['secret']
                            ), $parameters);
                    break;
            }
        }

        if (!empty($parameters)) {
            $queryString = utf8_encode(http_build_query($parameters, '', '&'));

            if ('GET' === $httpMethod) {
                $url .= '?'.$queryString;
            } else {
                $curlOptions += array(
                    CURLOPT_POSTFIELDS => json_encode($parameters),
                );
            }
        }

        $curlValue = true;
        switch($httpMethod) {
            case 'GET':
                $curlMethod = CURLOPT_HTTPGET;
                break;
            case 'POST':
                $curlMethod = CURLOPT_POST;
                break;
            case 'HEAD':
                $curlMethod = CURLOPT_CUSTOMREQUEST;
                $curlValue = "HEAD";
                break;
            case 'PUT':
                $curlMethod = CURLOPT_CUSTOMREQUEST;
                $curlValue = "PUT";
                break;
            case 'DELETE':
                $curlMethod = CURLOPT_CUSTOMREQUEST;
                $curlValue = "DELETE";
                break;
            case 'PATCH':
                // since PATCH is new the end points accept as POST
                $curlMethod = CURLOPT_POST;
                break;
            default:
                throw new Github_HttpClient_Exception('Method currently not supported');
        }

        $curlOptions += array(
            CURLOPT_URL => $url,
            CURLOPT_PORT => $options['http_port'],
            CURLOPT_USERAGENT => $options['user_agent'],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Authorization:Basic dWRldjpnaXRodWJmb3J1bWVuZ2Rldg=='),
            CURLOPT_TIMEOUT => $options['timeout'],
            $curlMethod => $curlValue
        );

        $response = $this->doCurlCall($curlOptions);

        if (!in_array($response['headers']['http_code'], array(0, 200, 201, 204))) {
            throw new Github_HttpClient_Exception(null, (int) $response['headers']['http_code']);
        }

        if ($response['errorNumber'] != '') {
            throw new Github_HttpClient_Exception('error '.$response['errorNumber']);
        }

        return $response['response'];
    }

    protected function doCurlCall(array $curlOptions)
    {
        $curl = curl_init();

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);
        $headers = curl_getinfo($curl);
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        curl_close($curl);

        return compact('response', 'headers', 'errorNumber', 'errorMessage');
    }
}
