<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\Factory\Validator;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    CmsMoney\Service\CurrencyListInterface,
    CmsMoney\Validator\CurrencyCode;

class CurrencyCodeFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return CurrencyCode
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        $validator = new CurrencyCode();
        $validator->setCurrencyList($services->get(CurrencyListInterface::class));

        return $validator;
    }
}
