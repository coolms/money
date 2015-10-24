<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\Factory\View\Helper;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    CmsMoney\Service\CurrencyListInterface,
    CmsMoney\View\Helper\CurrencyList;

class CurrencyListFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return CurrencyList
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        return new CurrencyList($services->get(CurrencyListInterface::class));
    }
}
