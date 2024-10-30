<?php

namespace MLMSoft\core\api\api3;


use Exception;
use HttpException;
use MLMSoft\core\api\RemoteRequest;
use MLMSoft\core\MLMSoftOptions;
use MLMSoft\core\MLMSoftPlugin;

class MLMSoftApi3
{
    public const API3_URL_PREFIX = '/api3';
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var MLMSoftOptions
     */
    private $options;

    /**
     * MLMSoftApi2 constructor.
     * @param $options MLMSoftOptions
     */
    public function __construct($options)
    {
        $this->options = $options;
        $this->login = $options->api3Login;
        $this->password = $options->api3Password;
        $this->accessToken = $options->_api3AccessToken;
        $this->refreshToken = $options->_api3RefreshToken;

        $url = $options->projectUrl;
        $url = trim($url, '/');
        $this->url = $url . self::API3_URL_PREFIX;
    }

    /**
     * @param $url
     * @param array $params
     * @param string $customToken
     * @return array|bool|object
     * @throws HttpException
     * @throws Exception
     */
    public function get($url, $params = [], $customToken = '')
    {
        return $this->send('get', $url, $params, $customToken);
    }

    /**
     * @param $url
     * @param $data
     * @param array $params
     * @param string $customToken
     * @return array|bool|object
     * @throws HttpException
     * @throws Exception
     */
    public function post($url, $data, $params = [], $customToken = '')
    {
        $resultUrl = add_query_arg($params, $url);
        return $this->send('post', $resultUrl, $data, $customToken);
    }

    /**
     * @param $url
     * @param $data
     * @param array $params
     * @param string $customToken
     * @return array|bool|object
     * @throws HttpException
     * @throws Exception
     */
    public function put($url, $data, $params = [], $customToken = '')
    {
        $resultUrl = add_query_arg($params, $url);
        return $this->send('put', $resultUrl, $data, $customToken);
    }

    /**
     * @param $url
     * @param array $params
     * @param string $customToken
     * @return array|bool|object
     * @throws HttpException
     * @throws Exception
     */
    public function delete($url, $params = [], $customToken = '')
    {
        return $this->send('delete', $url, $params, $customToken);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param string $customToken
     * @return array | object | boolean
     * @throws HttpException
     * @throws Exception
     */
    public function send($method, $url, $params = [], $customToken = '')
    {
        $method = mb_strtolower($method);

        $request = $this->prepareRequest($url, $customToken);

        if (!method_exists($request, $method)) {
            throw new Exception(MLMSoftPlugin::translate('Method not exists'));
        }

        $response = $request->$method($params);

        if ($response && isset($response['error']['code']) && $response['error']['code'] == 401 && !$customToken) {
            $this->refreshToken();
            $request = $this->prepareRequest($url, $customToken);
            $response = $request->$method($params);
        }

        if (!$response) {
            error_log('API error: Response is empty');
            throw new Exception(MLMSoftPlugin::translate('Response is empty'));
        }
        if (is_string($response)) {
            error_log('API error: Response not recognized: ' . $response);
            throw new Exception(MLMSoftPlugin::translate('Response not recognized: {{response}}', ['response' => $response]));
        }
        if (!$response['success']) {
            if (isset($response['error'], $response['error']['description'])) {
                throw new Exception($response['error']['description']);
            }
            throw new Exception(json_encode($response));
        }
        return $response['payload'] ?? true;
    }

    /**
     * @param $type string
     * @param $payload object | array
     * @return array|object|null
     * @throws HttpException
     */
    public function createDocument($type, $payload)
    {
        return $this->send('post', 'document/create', [
            'documentType' => $type,
            'payload' => $payload
        ]);
    }

    /**
     * @throws HttpException
     * @throws Exception
     */
    private function refreshToken()
    {
        if ($this->refreshToken) {
            $request = $this->prepareRequest('auth/refresh-token');
            $response = $request->post([
                'token' => $this->refreshToken
            ]);
            if ($response['success']) {
                $this->setTokens($response['payload']['accessToken'], $response['payload']['refreshToken']);
                return;
            }
        }
        $request = $this->prepareRequest('auth/login');
        $response = $request->post([
            'login' => $this->login,
            'password' => $this->password,
            'networkAccount' => false
        ]);
        if (!$response['success']) {
            throw new Exception(isset($response['error']) ? $response['error']['description'] : json_encode($response));
        }
        $this->setTokens($response['payload']['accessToken'], $response['payload']['refreshToken']);
    }

    /**
     * @param string $uri
     * @param string $customToken
     * @return RemoteRequest
     */
    private function prepareRequest($uri, $customToken = '')
    {
        $request = new RemoteRequest();
        $request->setUrl($this->buildUrl($uri));
        $request->setContentType(RemoteRequest::CONTENT_TYPE_JSON);
        if ($customToken) {
            $request->addHeaders([
                'Authorization' => 'Bearer ' . $customToken
            ]);
        } else if (!empty($this->accessToken)) {
            $request->addHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken
            ]);
        }
        return $request;
    }

    /**
     * @param string $access
     * @param string $refresh
     */
    private function setTokens($access, $refresh)
    {
        $this->accessToken = $access;
        $this->refreshToken = $refresh;
        $this->options->_api3AccessToken = $access;
        $this->options->_api3RefreshToken = $refresh;
    }

    /**
     * @param string $addr
     * @return string
     */
    private function buildUrl($addr)
    {
        return $this->url . '/' . $addr;
    }
}
