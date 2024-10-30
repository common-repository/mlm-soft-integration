<?php

namespace MLMSoft\traits;

use Exception;
use MLMSoft\core\MLMSoftDebug;
use MLMSoft\core\models\api\MLMSoftApiResponse;

trait AjaxApiTrait
{
    protected $handlers = [];

    protected function addHandler($name, $method)
    {
        $this->handlers[$name] = $method;
    }

    protected function initPublic($endpoint)
    {
        add_action('wp_ajax_nopriv_' . $endpoint, [$this, 'apiEndpoint'], 10, 1);
    }

    protected function initAdmin($endpoint, $initPublicIfDebug = false)
    {
        add_action('wp_ajax_' . $endpoint, [$this, 'apiEndpoint'], 10, 1);
        if ($initPublicIfDebug && MLMSoftDebug::isDebug()) {
            $this->initPublic($endpoint);
        }
    }

    public function apiEndpoint()
    {
        $handlerName = $_REQUEST['handler'];
        if (!isset($this->handlers[$handlerName])) {
            $this->sendError('Handler not found');
        }
        $handler = $this->handlers[$handlerName];
        if (!method_exists($handler[0], $handler[1])) {
            $this->sendError('Method not exists');
        }

        $data = file_get_contents('php://input');
        $data = json_decode($data, true);

        $validationResult = $this->validate($data);

        if ($validationResult) {
            $this->sendError($validationResult);
        }

        try {
            $response = call_user_func($handler, $data);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(MLMSoftApiResponse::getError($e->getMessage()));
            wp_die();
        }

        header('Content-Type: application/json');
        echo json_encode(MLMSoftApiResponse::getSuccess($response));
        wp_die();
    }

    /**
     * @param $body
     * @return string | null
     */
    protected function validate($body)
    {
        return '';
    }

    protected function sendError($error)
    {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(MLMSoftApiResponse::getError($error));
        wp_die();
    }
}