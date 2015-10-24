<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\View\Helper;

use Zend\I18n\View\Helper\CurrencyFormat,
    Zend\View\Helper\AbstractHelper,
    CmsMoney\Money;

/**
 * View helper for Money formatting
 */
class MoneyFormat extends AbstractHelper
{
    /**
     * @var CurrencyFormat
     */
    protected $currencyFormatter;

    /**
     * @var string
     */
    protected $defaultCurrencyFormatter = 'currencyFormat';

    /**
     * @var CurrencyList
     */
    protected $currencyListHelper;

    /**
     * @var string
     */
    protected $defaultCurrencyListHelper = 'currencyList';

    /**
     * @param  Money  $money
     * @param  string $locale i.e. en_US or null to leave us to try and work it out
     * @param  bool   $showDecimals Defaults to true
     * @param  string $pattern A pattern accepted by \NumberFormatter used to format the currency
     * @link http://www.icu-project.org/apiref/icu4c/classDecimalFormat.html#details
     *
     * @return string|self
     */
    public function __invoke(
        Money $money  = null,
        $locale       = null,
        $showDecimals = null,
        $pattern      = null
    ) {
        if (0 === func_num_args()) {
            return $this;
        }

        if (null === $money) {
            $money = new Money(0, $this->getCurrencyListHelper()->getDefault());
        }

        return $this->render($money, $locale, $showDecimals, $pattern);
    }

    /**
     * Format a number
     *
     * @param  Money  $money
     * @param  string $locale
     * @param  bool   $showDecimals
     * @param  string $pattern
     * @return string
     */
    protected function render(
        Money $money,
        $locale,
        $showDecimals,
        $pattern
    ) {
        $currencyFormatter = $this->getCurrencyFormatter();
        return $currencyFormatter(
            (string) $money->getAmount(),
            (string) $money->getCurrency(),
            $showDecimals,
            $locale,
            $pattern
        );
    }

    /**
     * @return CurrencyFormat
     */
    protected function getCurrencyFormatter()
    {
        if ($this->currencyFormatter) {
            return $this->currencyFormatter;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->currencyFormatter = $this->view->plugin($this->defaultCurrencyFormatter);
        }

        if (!$this->currencyFormatter instanceof CurrencyFormat) {
            $this->setCurrencyFormatter(new CurrencyFormat());
            $this->currencyFormatter->setView($this->getView());
        }

        return $this->currencyFormatter;
    }

    /**
     * @param CurrencyFormat $formatter
     * @return self
     */
    public function setCurrencyFormatter(CurrencyFormat $formatter)
    {
        $this->currencyFormatter = $formatter;
        return $this;
    }

    /**
     * @return CurrencyList
     */
    protected function getCurrencyListHelper()
    {
        if ($this->currencyListHelper) {
            return $this->currencyListHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->currencyListHelper = $this->view->plugin($this->defaultCurrencyListHelper);
        }

        return $this->currencyListHelper;
    }

    /**
     * @param CurrencyList $helper
     * @return self
     */
    public function setCurrencyListHelper(CurrencyList $helper)
    {
        $this->currencyListHelper = $helper;
        return $this;
    }
}
