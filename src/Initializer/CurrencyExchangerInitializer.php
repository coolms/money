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
    CmsMoney\Service\CurrencyExchangerAwareInterface,
    CmsMoney\Service\CurrencyExchangerInterface;

class CurrencyExchangerInitializer implements InitializerInterface
{
	/**
	 * {@inheritDoc}
	 *
	 * @return CurrencyExchangerInterface
	 */
	public function initialize($instance, ServiceLocatorInterface $serviceLocator)
	{
        if ($instance instanceof CurrencyExchangerAwareInterface) {
            if ($serviceLocator instanceof AbstractPluginManager) {
            	$serviceLocator = $serviceLocator->getServiceLocator();
            }

            $exchanger = $serviceLocator->get(CurrencyExchangerInterface::class);
            $instance->setCurrencyExchanger($exchanger);
        }
	}
}
