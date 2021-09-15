<?php
/**
 * Created by PhpStorm.
 * User: tuyenvv
 * Date: 2/11/19
 * Time: 6:13 PM
 */

namespace Kiotviet\Kiotviet;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HttpClient
{

    /**
     * HttpClient constructor.
     * @throws \Exception
     */
    public function __construct()
    {
    }

    /**
     * @param $method
     * @param $url
     * @param $params
     * @param $accessToken
     * @param $retailer
     * @param array $headers
     * @param string $bodyType
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function doRequest($method, $url, $params, $accessToken, $retailer, $headers = [], $bodyType = '')
    {
        $client = new Client();

        $options = [];


        $options['headers'] = [
            'Retailer' => $retailer,
            'Authorization' => 'Bearer ' . $accessToken
        ];

        if (sizeof($headers) > 0) {
            $options['headers'] = array_merge($options['headers'], $headers);
        }

        if ($method == 'GET') {
            $options['query'] = $params;
        } else {
            $options['form_params'] = $params;
        }

        if ($bodyType == 'json') {
            $options['json'] = $params;
            $options['headers'] ['Content-Type'] = 'application/json';
        }

        try {
            $response = $client->request($method, $url, $options);
        } catch (GuzzleException $e) {
            return $this->responseError($e->getMessage(), 'Lỗi kết nối tới Kiotviet: ' . $e->getMessage());
        }

        $response = $response->getBody()->getContents();
        $response = json_decode($response, true);

        return $this->responseSuccess($response);
    }

    public function responseSuccess($data)
    {
        return [
            'status' => 'success',
            'data' => $data,
            'message' => 'Done!'
        ];
    }

    public function responseError($errors, $message)
    {
        return [
            'status' => 'error',
            'data' => null,
            'error' => $errors,
            'message' => $message
        ];
    }
}