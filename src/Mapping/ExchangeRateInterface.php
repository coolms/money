<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\Mapping;

use CmsMoney\CurrencyInterface,
    CmsMoney\ExchangeRateInterface as BaseExchangeRateInterface;

/**
 * Exchange Rate Interface
 */
interface ExchangeRateInterface extends BaseExchangeRateInterface
{
    /**
     * Set currency to exchange from
     *
     * @param CurrencyInterface $currency
     */
    public function setFromCurrency(CurrencyInterface $currency);

    /**
     * Set currency to exchange to
     *
     * @param CurrencyInterface $currency
     */
    public function setToCurrency(CurrencyInterface $currency);

    /**
     * Set exchange rate
     *
     * @param float $rate
     */
    public function setExchangeRate($rate);
}
