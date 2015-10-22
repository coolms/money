<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney;

/**
 * Exchange Rate Interface
 */
interface ExchangeRateInterface
{
    /**
     * Exchange Money from one currency to another
     *
     * @param Money $money
     * @param int   $roundingMode
     * @return Money
     */
    public function exchange(Money $money, $roundingMode);

    /**
     * Returns the currency to exchange from
     *
     * @return CurrencyInterface
     */
    public function getFromCurrency();

    /**
     * Returns the currency to exchange to
     *
     * @return CurrencyInterface
     */
    public function getToCurrency();

    /**
     * Returns the exchange rate
     *
     * @return float
     */
    public function getExchangeRate();
}
