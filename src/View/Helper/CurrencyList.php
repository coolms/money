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

use Zend\View\Helper\AbstractHelper,
    CmsMoney\Service\CurrencyListAwareTrait,
    CmsMoney\Service\CurrencyListInterface;

/**
 * View helper decorator for \CmsMoney\Service\CurrencyList
 */
class CurrencyList extends AbstractHelper
{
    use CurrencyListAwareTrait;

    /**
     * __construct
     *
     * @param CurrencyListInterface $list
     */
    public function __construct(CurrencyListInterface $list)
    {
        $this->setCurrencyList($list);
    }

    /**
     * @return CurrencyListInterface
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * Proxy the CurrencyList service
     *
     * @param  string $method
     * @param  array  $argv
     * @return mixed
     */
    public function __call($method, $argv)
    {
        return call_user_func_array([$this->getCurrencyList(), $method], $argv);
    }
}
