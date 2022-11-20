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

use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Yii2 TurnstileInput from Cloudflare widget
 */
class TurnstileInput extends InputWidget
{
    const THEME_AUTO = 'auto';
    const THEME_LIGHT = 'light';
    const THEME_DARK = 'dark';

    const SIZE_NORMAL = 'normal';
    const SIZE_COMPACT = 'compact';

    const DIV_CLASS_DEFAULT = 'cf-turnstile';

    public $siteKey;
    public $jsApiUrl;

    /** @var string */
    public $configComponentName = 'turnstile';

    public $theme;
    public $size;
    public $tabIndex;

    /** @var array Additional html widget options, such as `class`. */
    public $widgetOptions = [];

    /**
     * @param $siteKey
     * @param null $jsApiUrl
     * @param array $config
     */
    public function __construct($siteKey = null, $jsApiUrl = null, $config = [])
    {
        if ($siteKey && !$this->siteKey) {
            $this->siteKey = $siteKey;
        }
        if ($jsApiUrl && !$this->jsApiUrl) {
            $this->jsApiUrl = $jsApiUrl;
        }

        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        $this->configureComponent();
    }

    /**
     * @throws InvalidConfigException
     */
    protected function configureComponent()
    {
        /** @var TurnstileConfig $turnstileConfig */
        $turnstileConfig = \Yii::$app->get($this->configComponentName, false);

        if (!$this->siteKey) {
            if ($turnstileConfig && $turnstileConfig->siteKey) {
                $this->siteKey = $turnstileConfig->siteKey;
            } else {
                throw new InvalidConfigException('Required `siteKey` parameter is not defined.');
            }
        }
        if (!$this->jsApiUrl) {
            if ($turnstileConfig && $turnstileConfig->jsApiUrl) {
                $this->jsApiUrl = $turnstileConfig->jsApiUrl;
            }
        }
    }

    public function run()
    {
        parent::run();

        $this->view->registerJsFile(
            $this->jsApiUrl,
            ['position' => $this->view::POS_HEAD, 'async' => true, 'defer' => true]
        );

        echo Html::tag('div', '', $this->buildDivOptions());
    }

    protected function buildDivOptions()
    {
        $divOptions = [
            'class' => self::DIV_CLASS_DEFAULT,
            'data-sitekey' => $this->siteKey,
        ];
        if (isset($this->widgetOptions['class'])) {
            $divOptions['class'] .= ' ' . $this->widgetOptions['class'];
            unset($this->widgetOptions['class']);
        }

        $divOptions += $this->widgetOptions;

        if ($this->theme) {
            $divOptions['data-theme'] = $this->theme;
        }

        if ($this->size) {
            $divOptions['data-size'] = $this->size;
        }

        if ($this->tabIndex) {
            $divOptions['data-tabindex'] = $this->tabIndex;
        }

        $divOptions['data-response-field-name'] = $this->getResponseFieldName();

        return $divOptions;
    }

    protected function getResponseFieldName()
    {
        if (isset($this->widgetOptions['data-response-field-name'])) {
            return $this->widgetOptions['data-response-field-name'];
        }

        if ($this->hasModel()) {
            return Html::getInputName($this->model, $this->attribute);
        }

        return $this->name;
    }
}
