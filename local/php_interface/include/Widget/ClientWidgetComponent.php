<?php

namespace Widget;

class ClientWidgetComponent
{
    /**
     * @property string $secretKey
     */
    public $secretKey = '4fqCSFFhURzk9g7u';

    /**
     * @property string $token
     */
    public $token = null;


    /**
     * @property string $code
     */
    private $code = null;

    /**
     * @property string $userId
     */
    private $userId;

    /**
     * @property string $userIp
     */
    private $userIp;

    /**
     * @property integer $responseCode
     */
    private $responseCode;

    /**
     * @property string $url
     */
    private $url;

    private $result;

    /**
     * @property string $widgetUrl
     */
    private $widgetUrl = 'http://widget.deals';


    public function getToken()
    {
        $secretKey = $this->secretKey;

        $this->url = sprintf('%s/api/customer/%s', $this->widgetUrl, $secretKey);

        if ($this->token) {
            $this->url .= '/' . $this->token;
        }

        $fields = [
            'customer_id' => $this->userId,
            'customer_ip' => $this->userIp,
        ];

        $this->postRequest($fields);

        if ($this->checkResponseCode()) {
            $this->token = $this->result['token'];
        }

        return $this;
    }

    public function getWidgetReference($type)
    {
        $this->url = sprintf('%s/api/customer/%s/%d', $this->widgetUrl, $this->token, $type);

        $this->request(function ($ch) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        });
        return $this;
    }

    public function clickPromo()
    {

        $this->url = sprintf('%s/api/promo/%s/%s', $this->widgetUrl, $this->token->token, $this->code);
        $fields = [
            'customer_id' => $this->getUserId(),
            'customer_ip' => $this->getUserIp(),
        ];

        $this->postRequest($fields);
        if ($this->checkResponseCode()) {
            $this->code = $this->result['code'];
        }

        return $this;
    }

    public function usePromo()
    {
        $this->url = sprintf('%s/api/promo/%s/%s', $this->widgetUrl, $this->token->token, $this->code);
        $fields = [
            'customer_id' => $this->userId,
            'customer_ip' => $this->userIp,
        ];
        $this->putRequest($fields);

        return $this;
    }

    public function activatePromo()
    {
        $this->url = sprintf('%s/api/activate/%s/%s', $this->widgetUrl, $this->token->token, $this->code);

        $fields = [
            'customer_id' => $this->userId,
            'customer_ip' => $this->userIp,
        ];

        $this->putRequest($fields);

        return $this;
    }

    public function prolongationPromo()
    {
        $this->url = sprintf('%s/api/activate/%s/%s', $this->widgetUrl, $this->token, $this->code);
        $fields = [

        ];
        $this->postRequest($fields);
        return $this;
    }

    private function request($callback, $data = null)
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "cache-control: no-cache",
            "content-type: application/json",
            "charset=utf-8",
        ]);
        $callback($ch, $data);
        $result = curl_exec($ch);
        $this->responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        $this->result = json_decode($result, true);
    }

    private function putRequest($fields)
    {
        $this->request(function ($ch, $fields) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        }, $fields);
    }

    private function postRequest($fields)
    {
        $this->request(function ($ch, $fields) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        }, $fields);
    }

    public function checkResponseCode()
    {
        return $this->responseCode === 200;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getWidgetHtml()
    {

    }

    public function saveToken()
    {
        file_put_contents('widget_token.json', json_encode(['token' => $this->token]));
        return $this;
    }

    public function loadToken()
    {
        $array = json_decode(file_get_contents('widget_token.json'), true);
        $this->token = $array['token'];
        return $this;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    private function getUserIp()
    {
        return $this->userIp;
    }

    private function getUserId()
    {
        return $this->userId;
    }

    public function setUserIp($ip)
    {
        $this->userIp = $ip;
    }

    public function setUserId($id)
    {
        $this->userId = $id;
    }

    public function getResponseCode()
    {
        return $this->responseCode;
    }
}
