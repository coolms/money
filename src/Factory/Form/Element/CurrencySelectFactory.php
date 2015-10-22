<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\Factory\Form\Element;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    CmsMoney\Form\Element\CurrencySelect,
    CmsMoney\Options\ModuleOptions,
    CmsMoney\Options\ModuleOptionsInterface,
    CmsMoney\Service\CurrencyList;

class CurrencySelectFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return CurrencySelect
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        /* @var $options ModuleOptionsInterface */
        $options = $services->get(ModuleOptions::class);

        $element = new CurrencySelect('currency-select');
        $element->setCurrencyList($services->get(CurrencyList::class));

        return $element;
    }
}
