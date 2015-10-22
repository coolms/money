<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\Service;

use CmsMoney\Money;

interface CurrencyExchangerInterface
{
    /**
     * Retrieve the exchange rate, and multiplies it to the $amount parameter.
     *
     * @param Money $money The amount to exchange
     * @param string|\CmsMoney\CurrencyInterface $targetCode Currency code according to the format of 3 uppercase characters
     * @return Money
     */
    public function exchange(Money $amount, $targetCurrency);
}
