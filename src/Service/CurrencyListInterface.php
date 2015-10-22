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

/**
 * Implement this to provide a list of allowed currencies
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
interface CurrencyListInterface
{
    /**
     * @return array
     */
    public function getCodes();

    /**
     * @return array<\CmsMoney\CurrencyInterface>
     */
    public function getCurrencies();

    /**
     * @param string|\CmsMoney\CurrencyInterface $code
     * @return self
     */
    public function allow($code);

    /**
     * @param array $codes
     * @return self
     */
    public function setAllowed(array $codes);

    /**
     * Checks whether a currency is allowed in the current context
     *
     * @param string|\CmsMoney\CurrencyInterface|\CmsMoney\Money $currency
     * @return bool
     */
    public function isAllowed($code);

    /**
     * @param string|\CmsMoney\CurrencyInterface $code
     * @return self
     */
    public function exclude($code);

    /**
     * @param array $codes
     * @return self
     */
    public function setExcluded(array $codes);

    /**
     * @param array|null|string $default
     * @return self
     */
    public function setDefault($default);

    /**
     * @param string $locale
     * @return \CmsMoney\CurrencyInterface
     */
    public function getDefault($locale = null);
}
