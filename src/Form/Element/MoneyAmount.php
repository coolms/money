<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\Form\Element;

use CmsCommon\Form\Element\Number;

class MoneyAmount extends Number
{
    /**
     * Locale string used for interpreting inputted numbers
     *
     * @var string
     */
    protected $locale;

    /**
     * {@inheritDoc}
     */
    protected function getValidators()
    {
        return [
            [
                'name' => 'IsFloat',
                'options' => [
                    'locale' => $this->getLocale(),
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getInputSpecification()
    {
        $inputSpec = parent::getInputSpecification();

        $inputSpec['required'] = true;
        $inputSpec['filters'] = [
            ['name' => 'StringTrim'],
            [
                'name' => 'NumberParse',
                'options' => [
                    'style' => \NumberFormatter::DECIMAL,
                    'type' => \NumberFormatter::TYPE_DOUBLE,
                    'locale' => $this->getLocale(),
                ],
            ],
        ];

        return $inputSpec;
    }

    /**
     * Sets the locale option
     *
     * @param  string|null    $locale
     * @return self
     */
    public function setLocale($locale = null)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Returns the locale option
     *
     * @return string
     */
    public function getLocale()
    {
        if (null === $this->locale) {
            return \Locale::getDefault();
        }

        return $this->locale;
    }
}
