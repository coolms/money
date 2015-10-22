<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\Factory;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    CmsMoney\Currencies,
    CmsMoney\Options\ModuleOptions,
    CmsMoney\Options\ModuleOptionsInterface,
    CmsMoney\Service\CurrencyList,
    CmsMoney\Service\CurrencyListInterface;

class CurrencyListFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return CurrencyListInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $options ModuleOptionsInterface */
        $options = $serviceLocator->get(ModuleOptions::class);

        if ($specs = $options->getCurrencySpecs()) {
            foreach ($specs as $spec) {
                Currencies::register($spec);
            }
        }

        return new CurrencyList(
            $options->getAllowed(),
            $options->getExcluded(),
            $options->getDefault()
        );
    }
}
