<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\Form;

use CmsCommon\Form\InputFilterProviderFieldset,
    CmsMoney\Mapping\Money as MoneyObject;

class Money extends InputFilterProviderFieldset
{
    /**
     * Default options
     *
     * @var array
     */
    protected $options = [
        'partial' => 'cms-money/money-fieldset',
    ];

    /**
     * @var string
     */
    protected $allowedObjectBindingClass = MoneyObject::class;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        if (!$this->has('amount')) {
            $this->add([
                'name' => 'amount',
                'type' => 'MoneyAmount',
                'label' => 'Amount',
            ]);
        }

        if (!$this->has('currency')) {
            $this->add([
                'name' => 'currency',
                'type' => 'CurrencySelect',
                'label' => 'Currency',
            ]);
        }
    }

    /**
     * Sets the locale option
     *
     * @param  string|null    $locale
     * @return self
     */
    public function setLocale($locale = null)
    {
        $this->get('amount')->setLocale($locale);
        return $this;
    }

    /**
     * Returns the locale option
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->get('amount')->getLocale();
    }
}
