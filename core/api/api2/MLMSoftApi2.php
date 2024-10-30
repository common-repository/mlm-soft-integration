<?php

namespace MLMSoft\core\api\api2;

use Exception;
use MLMSoft\core\api\api2\models\Api2Response;
use MLMSoft\core\MLMSoftOptions;
use MLMSoft\core\MLMSoftPlugin;

class MLMSoftApi2
{
    public const API2_URL_PREFIX = '/api2/online-office';
    public const DOCUMENT_POLLING_MAX_ITERATIONS = 20;
    public const DOCUMENT_POLLING_TIMEOUT = 500000;
    public const APP_ID = 1;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $token;

    /**
     * MLMSoftApi2 constructor.
     * @param $options MLMSoftOptions
     */
    public function __construct($options)
    {
        $this->token = $options->api2token;
        $url = $options->projectUrl;
        $url = trim($url, '/');
        $this->url = $url . self::API2_URL_PREFIX;
    }

    /**
     * @param $endpoint string
     * @param $params array
     * @return Api2Response
     */
    public function execGet($endpoint, array $params = array())
    {
        $url = $this->url . '/' . trim($endpoint, '/') . (count($params) > 0 ? ('?' . http_build_query($params)) : '');
        $params = array(
            'headers' => $this->makeHeaders($params),
            'method' => 'GET',
        );
        return $this->sendRequest($url, $params);
    }

    /**
     * @param $endpoint string
     * @param $params array
     * @return Api2Response
     */
    public function execPost($endpoint, array $params = array())
    {
        $url = $this->url . '/' . trim($endpoint, '/');
        $params = array(
            'headers' => $this->makeHeaders($params),
            'method' => 'POST',
            'body' => json_encode($params)
        );
        return $this->sendRequest($url, $params);
    }

    /**
     * @param $endpoint string
     * @param $params array
     * @return Api2Response
     */
    public function execDelete($endpoint, array $params = array())
    {
        $url = $this->url . '/' . trim($endpoint, '/');
        $params = array(
            'headers' => $this->makeHeaders($params),
            'method' => 'DELETE',
            'body' => json_encode($params)
        );
        return $this->sendRequest($url, $params);
    }

    /**
     * @param $documentId
     * @return Api2Response
     */
    private function getDocumentResult($documentId)
    {
        for ($i = 1; $i <= self::DOCUMENT_POLLING_MAX_ITERATIONS; $i++) {
            usleep(self::DOCUMENT_POLLING_TIMEOUT);
            $response = $this->execGet('/document/get', array('id' => $documentId));
            $payload = $response->getPrimaryPayload();
            $context = $payload->document->context;

            $logVariables = array(
                '%id' => $documentId,
                '%iteration' => $i,
                '%action' => $context->entity . '\\' . $context->action,
                '%result' => !empty($payload->document->result) ? 'received' : 'none',
            );
            if (!$response->isPrimarySuccess()) {
                error_log(
                    'Error getting result for document '
                    . print_r($logVariables + $response->getStdErrorLogParams(), true)
                );
            }
            if (!empty($payload->document->result)) {
                return $payload->document->result;
            }
        }
        return new Api2Response([
            'errorCode' => -1,
            'errorMessage' => MLMSoftPlugin::translate('Document {{documentId}} not processed', ['documentId' => $documentId])
        ]);
    }

    /**
     * @param $url string
     * @param $params array
     * @return Api2Response
     */
    private function sendRequest($url, $params)
    {
        $reqRes = wp_remote_request($url, $params);
        $reqRes = $this->checkResponse($reqRes);

        $response = new Api2Response($reqRes);
        $payload = $response->getPrimaryPayload();

        if (!empty($payload->queueRequest) && !empty($payload->documentId)) {
            $reqRes = $this->getDocumentResult((int)$payload->documentId);
            $response = new Api2Response($reqRes);
        }
        return $response;
    }

    private function checkResponse($reqRes)
    {
        $error = false;
        if (is_wp_error($reqRes)) {
            $error = $reqRes->get_error_message();
            error_log("Something went wrong: $error");
            die();
        }
        if (!isset($reqRes['body'])) {
            $error = MLMSoftPlugin::translate('response body is not defined');
            error_log("Something went wrong: $error");
            die();
        }

        $res = [];

        try {
            $res = json_decode($reqRes['body']);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        if ($error) {
            error_log("Something went wrong: $error");
            die();
        }

        return $res;
    }

    private function makeHeaders($params)
    {
        return array(
            'Content-Type' => 'application/json',
            'App-Id' => self::APP_ID,
            'Security-Key' => $this->calcSign($params)
        );
    }

    private function calcSign($params)
    {
        ksort($params);
        return md5(http_build_query($params) . $this->token);
    }
}