<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\Initializer;

use Zend\ServiceManager\AbstractPluginManager,
    Zend\ServiceManager\InitializerInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    CmsMoney\Service\CurrencyListAwareInterface,
    CmsMoney\Service\CurrencyListInterface;

class CurrencyListInitializer implements InitializerInterface
{
	/**
	 * {@inheritDoc}
	 *
	 * @return CurrencyListInterface
	 */
	public function initialize($instance, ServiceLocatorInterface $serviceLocator)
	{
        if ($instance instanceof CurrencyListAwareInterface) {
            if ($serviceLocator instanceof AbstractPluginManager) {
            	$serviceLocator = $serviceLocator->getServiceLocator();
            }

            $exchanger = $serviceLocator->get(CurrencyListInterface::class);
            $instance->setCurrencyList($exchanger);
        }
	}
}
