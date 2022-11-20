<?php

/**
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://github.com/easedevs/yii2-turnstile-validator
 * @copyright Copyright (c) 2022 Ease Tech LLC
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace easedevs\yii2\turnstile;

class TurnstileConfig
{
    public $siteKey;
    public $secret;
    public $jsApiUrl = 'https://challenges.cloudflare.com/turnstile/v0/api.js';
    public $siteVerifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    public $httpClient;
}
