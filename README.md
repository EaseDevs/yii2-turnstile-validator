# yii2-turnstile-validator
Yii2 validator for Cloudflare's Turnstile CAPTCHA alternative

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
$ php composer.phar require "easedevs/yii2-turnstile-validator" "*"
```

or add

```
"easedevs/yii2-turnstile-validator": "*"
```

to the `require` section of your `composer.json` file.

In your config, add a component configuration:
```php
    'turnstile' => [
        'class' => 'easedevs\yii2\turnstile\TurnstileConfig',
        'siteKey' => '_YOUR_SITE_KEY_FROM_CLOUDFLARE_TURNSTILE_',
        'secret' => '_YOUR_SECRET_FROM_CLOUDFLARE_TURNSTILE_',
    ],
```

## Usage

Using as an `ActiveField` widget:

```php
use easedevs\yii2\turnstile\TurnstileInput;

echo $form->field($model, 'captcha')->widget(TurnstileInput::class, [
    'size' => TurnstileInput::SIZE_COMPACT,
]);
```

Using as a simple widget:

```php
use easedevs\yii2\turnstile\TurnstileInput;

echo TurnstileInput::widget([
    'name' => 'captcha',
    'size' => TurnstileInput::SIZE_COMPACT,
]);
```

Using validator in a model for verification of the result on the server:

```php
use easedevs\yii2\turnstile\TurnstileInputValidator;

class Account extends Model
{
    public $captcha;

    public function rules()
    {
        return [
            [['captcha'], 'string'],
            [['captcha'], TurnstileInputValidator::class],
        ];
    }
}
```
