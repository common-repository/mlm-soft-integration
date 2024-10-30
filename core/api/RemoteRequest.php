<?php

namespace MLMSoft\core\api;

use Exception;

class RemoteRequest
{
    const CONTENT_TYPE_JSON = 'application/json';

    /**
     * @var string
     */
    private $url;

    /**
     * @var null|resource
     */
    private $handle = null;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var array
     */
    private $info;

    /**
     * @var int
     */
    private $timeout = 0;

    /**
     * @var string
     */
    private $contentType;

    /**
     * CurlRequest constructor.
     *
     * @param string $url
     */
    public function __construct($url = '')
    {
        $this->url = $url;
        $this->handle = curl_init();
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return RemoteRequest
     * @throws Exception
     */
    public function setOption($name, $value)
    {
        if (!curl_setopt($this->handle, $name, $value)) {
            throw new Exception(__CLASS__ . ': ' . curl_error($this->handle), 500);
        }
        return $this;
    }

    /**
     * @param string $login
     * @param string $password
     * @return $this
     * @throws Exception
     */
    public function setAuth($login, $password)
    {
        return $this->setOption(CURLOPT_USERPWD, "$login:$password");
    }

    /**
     * @param $port
     *
     * @return RemoteRequest
     * @throws Exception
     */
    public function setPort($port)
    {
        return $this->setOption(CURLOPT_PORT, $port);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function execute()
    {
        $this->setOption(CURLOPT_URL, $this->url);
        $this->setOption(CURLOPT_HTTPHEADER, $this->headers);
        if ($this->timeout) {
            $this->setOption(CURLOPT_TIMEOUT, $this->timeout);
        }
        $response = curl_exec($this->handle);
        $this->info = curl_getinfo($this->handle);
        if ($response !== false) {
            return (strpos($this->getInfo('content_type'), 'application/json') === 0)
                ? json_decode($response, true)
                : $response;
        }
        throw new Exception(__CLASS__ . ' error #' . curl_errno($this->handle) . ': ' . curl_error($this->handle), 500);
    }

    /**
     * @param bool|string $option
     *
     * @return mixed
     */
    public function getInfo($option = false)
    {
        if ($this->info) {
            if ($option === false) {
                return $this->info;
            } elseif (isset($this->info[$option])) {
                return $this->info[$option];
            }
        }
        return null;
    }

    /**
     * @return int|null
     */
    public function getResponseCode()
    {
        return $this->getInfo('http_code');
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        if ($this->handle) {
            curl_close($this->handle);
        }
        $this->handle = null;
    }

    /**
     * Method POST
     *
     * @param array $fields
     * @param bool $returnTransfer
     *
     * @return mixed
     * @throws Exception
     */
    public function post(array $fields = [], $returnTransfer = true)
    {
        $this->setOption(CURLOPT_POST, true);
        if ($this->contentType == 'application/json') {
            $this->setOption(CURLOPT_POSTFIELDS, json_encode($fields));
        } else {
            $this->setOption(CURLOPT_POSTFIELDS, http_build_query($fields, '', '&'));
        }
        $this->setOption(CURLOPT_RETURNTRANSFER, (bool)$returnTransfer);
        return $this->execute();
    }

    /**
     * Method PUT
     *
     * @param array $fields
     * @param bool $returnTransfer
     *
     * @return mixed
     * @throws Exception
     */
    public function put(array $fields = [], $returnTransfer = true)
    {
        $this->setOption(CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($this->contentType == self::CONTENT_TYPE_JSON) {
            $this->setOption(CURLOPT_POSTFIELDS, json_encode($fields));
        } else {
            $this->setOption(CURLOPT_POSTFIELDS, http_build_query($fields, '', '&'));
        }
        $this->setOption(CURLOPT_RETURNTRANSFER, (bool)$returnTransfer);
        return $this->execute();
    }

    /**
     * Method DELETE
     *
     * @param array $fields
     *
     * @return mixed
     * @throws Exception
     */
    public function delete(array $fields = [])
    {
        $this->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');
        return $this->post($fields);
    }

    /**
     * Method GET
     *
     * @param array $fields
     * @param bool $returnTransfer
     *
     * @return mixed
     * @throws Exception
     */
    public function get(array $fields = [], $returnTransfer = true)
    {
        $this->setOption(CURLOPT_POST, false);
        $this->setOption(CURLOPT_RETURNTRANSFER, (bool)$returnTransfer);
        if (!empty($fields)) {
            $this->url .= '?' . http_build_query($fields);
        }
        return $this->execute();
    }

    /**
     * @param array $headers
     *
     * @return $this
     */
    public function addHeaders(array $headers)
    {
        $array = [];
        foreach ($headers as $field => $value) {
            $array[] = rtrim($field, ':') . ': ' . $value;
        }
        $this->headers = array_merge($this->headers, $array);
        return $this;
    }

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this->addHeaders([
            'Content-Type' => $contentType,
        ]);
    }

    /**
     * @param string $url
     *
     * @return RemoteRequest
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param int $timeout
     *
     * @return RemoteRequest
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @param string $scheme
     * @param string $host
     * @param string $action
     * @param array $params
     *
     * @return string
     */
    public static function buildUrl($scheme = '', $host = '', $action = '', $params = [])
    {
        $result = ($scheme ? trim($scheme, '://') . '://' : '') . trim($host, '/') . '/' . trim($action, '/');
        if ($params) {
            $result .= '?' . http_build_query($params);
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}