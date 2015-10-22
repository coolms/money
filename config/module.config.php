<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney;

return [
    'cmsmoney' => [
        'allowed' => [],
        'excluded' => [],
        'default' => null,
        'currency_specs' => [],
    ],
    'controllers' => [
        'invokables' => [
            'CmsMoney\Controller\Admin' => 'CmsMoney\Mvc\Controller\AdminController',
            'CmsMoney\Controller\Index' => 'CmsMoney\Mvc\Controller\IndexController',
        ],
    ],
    'form_elements' => [
        'aliases' => [
            'CurrencySelect' => 'CmsMoney\Form\Element\CurrencySelect',
            'Money' => 'CmsMoney\Form\Money',
        ],
        'factories' => [
            'CmsMoney\Form\Element\CurrencySelect' => 'CmsMoney\Factory\Form\Element\CurrencySelectFactory',
            'CmsMoney\Form\Money' => 'CmsMoney\Factory\Form\MoneyFactory',
        ],
        'invokables' => [
            'MoneyAmount' => 'CmsMoney\Form\Element\MoneyAmount',
        ],
    ],
    'router' => [
        'routes' => [
            'cms-admin' => [
                'child_routes' => [
                    'money' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/money[/:controller[/:action[/:id]]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z\-]*',
                                'action' => '[a-zA-Z\-]*',
                                'id' => '[a-zA-Z0-9\-]*',
                            ],
                            'defaults' => [
                                '__NAMESPACE__' => 'CmsMoney\Controller',
                                'controller' => 'Admin',
                                'action' => 'index',
                            ],
                        ],
                    ],
                ],
            ],
            'cms-money' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/money',
                    'defaults' => [
                        '__NAMESPACE__' => 'CmsMoney\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ],
                    'may_terminate' => true,
                    'child_routes' => [
                        'default' => [
                            'type' => 'Segment',
                            'options' => [
                                'route' => '[:action[/:id]]',
                                'constraints' => [
                                    'action' => '[a-zA-Z\-]*',
                                    'id' => '[a-zA-Z]{0,3}',
                                ],
                                'defaults' => [
                                    '__NAMESPACE__' => 'CmsMoney\Controller',
                                    'controller' => 'Index',
                                    'action' => 'index',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'CmsMoney\Options\ModuleOptionsInterface' => 'CmsMoney\Options\ModuleOptions',
            'CmsMoney\Service\CurrencyListInterface' => 'CmsMoney\Service\CurrencyList',
        ],
        'invokables' => [
            'CmsMoney\Service\CurrencyExchangerInterface' => 'CmsMoney\Service\CurrencyExchanger',
        ],
        'factories' => [
            'CmsMoney\Options\ModuleOptions' => 'CmsMoney\Factory\ModuleOptionsFactory',
            'CmsMoney\Service\CurrencyList'  => 'CmsMoney\Factory\CurrencyListFactory',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'moneyFormat' => 'CmsMoney\View\Helper\MoneyFormat',
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'cms-money/money-fieldset' => __DIR__ . '/../view/cms-money/money-fieldset.phtml',
        ],
        'template_path_stack' => [
            __NAMESPACE__ => __DIR__ . '/../view',
        ],
    ],
];
