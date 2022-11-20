<?php

namespace easedevs\yii2\turnstile;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\validators\Validator;

class TurnstileInputValidator extends Validator
{
    /** @var boolean Whether to skip this validator if the input is empty. */
    public $skipOnEmpty = false;

    /** @var string The shared key secret */
    public $secret;

    public $siteVerifyUrl;

    public $sendRemoteIp = false;

    /** @var \yii\httpclient\Client */
    public $httpClient;

    /** @var bool */
    protected $isValid;

    /** @var string */
    public $configComponentName = 'turnstile';

    public function __construct(
        $secret = null,
        $siteVerifyUrl = null,
        Client $httpClient = null,
        $config
    )
    {
        if ($secret && !$this->secret) {
            $this->secret = $secret;
        }
        if ($siteVerifyUrl && !$this->siteVerifyUrl) {
            $this->siteVerifyUrl = $siteVerifyUrl;
        }
        if ($httpClient && !$this->httpClient) {
            $this->httpClient = $httpClient;
        }

        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        $this->configureComponent();
    }

    /**
     * @param string|array $value
     * @return array|null
     * @throws Exception
     */
    protected function validateValue($value)
    {
        if ($this->isValid === null) {
            if (!$value) {
                $this->isValid = false;
            } else {
                $response = $this->getResponse($value);
                if (!isset($response['success'])) {
                    throw new Exception('Invalid site verify response.');
                }

                $this->isValid = $response['success'] === true;
            }
        }

        if (!$this->isValid) {
            $this->message = $this->message ?: Yii::t(
                'yii',
                'Please verify that you are human.'
            );
        }

        return $this->isValid ? null : [$this->message, []];
    }

    /**
     * @param string $value
     * @return array
     * @throws Exception
     * @throws \yii\base\InvalidParamException
     */
    protected function getResponse($value)
    {
        $data = [
            'secret' => $this->secret,
            'response' => $value,
        ];
        if ($this->sendRemoteIp) {
            $data['remoteip'] = \Yii::$app->request->userIP;
        }

        $response = $this->httpClient
            ->createRequest()
            ->setMethod('POST')
            ->setUrl($this->siteVerifyUrl)
            ->setData($data)
            ->send();
        if (!$response->isOk) {
            throw new Exception('Could not connect to Turnstile server: ' . $response->statusCode);
        }

        return $response->data;
    }

    /**
     * @throws InvalidConfigException
     */
    protected function configureComponent()
    {
        /** @var TurnstileConfig $turnstileConfig */
        $turnstileConfig = \Yii::$app->get($this->configComponentName, false);

        if (!$this->secret) {
            if ($turnstileConfig && $turnstileConfig->secret) {
                $this->secret = $turnstileConfig->secret;
            } else {
                throw new InvalidConfigException('Required `secret` parameter is not defined.');
            }
        }
        if (!$this->siteVerifyUrl) {
            if ($turnstileConfig && $turnstileConfig->siteVerifyUrl) {
                $this->siteVerifyUrl = $turnstileConfig->siteVerifyUrl;
            }
        }

        if (!$this->httpClient) {
            if ($turnstileConfig && $turnstileConfig->httpClient) {
                $this->httpClient = $turnstileConfig->httpClient;
            } else {
                $this->httpClient = new Client();
            }
        }
    }
}
