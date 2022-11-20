<?php

namespace easedevs\yii2\turnstile\tests;

use easedevs\yii2\turnstile\TurnstileInput;
use PHPUnit\Framework\TestCase;
use yii\helpers\ArrayHelper;

class TurnstileInputTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockWebApplication();
    }

    protected function mockWebApplication($config = [], $appClass = '\yii\web\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'aliases' => [
                '@bower' => '@vendor/bower-asset',
                '@npm' => '@vendor/npm-asset',
            ],
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'asdfasdfasdfasdf',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                    'isConsoleRequest' => false,
                ],
            ],
        ], $config));
    }

    public function testDivOutput()
    {
        $result = TurnstileInput::widget([
            'name' => 'turnstilefield',
            'siteKey' => 'asdf',
        ]);
        $this->assertEquals('<div class="cf-turnstile" data-sitekey="asdf" data-response-field-name="turnstilefield"></div>', $result);

        $result = TurnstileInput::widget([
            'name' => 'turnstilefield',
            'siteKey' => 'asdf',
            'widgetOptions' => [
                'class' => 'customclass',
            ]
        ]);
        $this->assertEquals('<div class="cf-turnstile customclass" data-sitekey="asdf" data-response-field-name="turnstilefield"></div>', $result);
    }

}
