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

trait CurrencyListAwareTrait
{
    /**
     * @var CurrencyListInterface
     */
    protected $currencyList;

    /**
     * @return CurrencyListInterface
     */
    public function getCurrencyList()
    {
        return $this->currencyList;
    }

    /**
     * @param CurrencyListInterface $list
     * @return self
     */
    public function setCurrencyList(CurrencyListInterface $list)
    {
        $this->currencyList = $list;
        return $this;
    }
}
