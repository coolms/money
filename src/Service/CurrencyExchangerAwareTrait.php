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

trait CurrencyExchangerAwareTrait
{
    /**
     * @var CurrencyExchangerInterface
     */
    protected $currencyExchanger;

    /**
     * @return CurrencyExchangerInterface
     */
    public function getCurrencyExchanger()
    {
        return $this->currencyExchanger;
    }

    /**
     * @param CurrencyExchangerInterface $exchanger
     * @return self
     */
    public function setCurrencyExchanger(CurrencyExchangerInterface $exchanger)
    {
        $this->currencyExchanger = $exchanger;
        return $this;
    }
}
