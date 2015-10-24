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

use Zend\Form\Element\Select,
    Zend\Stdlib\ArrayUtils,
    CmsMoney\Currency,
    CmsMoney\Service\CurrencyListInterface,
    CmsMoney\Service\CurrencyList;

class CurrencySelect extends Select
{
    /**
     * Configured List of allowed currencies
     *
     * @var CurrencyListInterface
     */
    protected $currencyList;

    /**
     * Default attributes
     *
     * @var array
     */
    protected $attrubutes = [
        'required' => true,
    ];

    /**
     * Default options
     *
     * @var array
     */
    protected $options = [
        'display_names' => false,
    ];

    /**
     * __construct
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name = null, $options = [])
    {
        if (null === $name) {
            $name = 'currency-select';
        }

        parent::__construct($name, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function getValueOptions()
    {
        if (!count($this->valueOptions)) {
            $currencies = $this->getCurrencyList()->getCurrencies();
            $options = parent::getValueOptions();
            foreach ($currencies as $currency) {
                $name = $this->getDisplayNames() ? $currency->getName() : $currency->getIsoCode();
                $options[$currency->getIsoCode()] = $name;
            }

            $this->setValueOptions($options);
        }

        return parent::getValueOptions();
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        if ($this->isMultiple()) {
            if ($value instanceof \Traversable) {
                $value = ArrayUtils::iteratorToArray($value);
            } elseif (!$value) {
                return parent::setValue([]);
            } elseif (!is_array($value)) {
                $value = (array) $value;
            }

            return parent::setValue(array_map(Currency::class . '::create', $value));
        }

        return parent::setValue(Currency::create($value));
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        if (null === ($value = parent::getValue())) {
            return (string) $this->getCurrencyList()->getDefault();
        }

        return $value;
    }

    /**
     * Set Option whether to display names or codes
     *
     * @param  bool $flag
     * @return self
     */
    public function setDisplayNames($flag)
    {
        $this->setOption('display_names', (bool) $flag);
        return $this;
    }

    /**
     * Return display names option
     *
     * @return bool
     */
    public function getDisplayNames()
    {
        return $this->getOption('display_names');
    }

    /**
     * {@inheritDoc}
     */
    protected function getValidator()
    {
        if (null === $this->validator) {
            $this->validator = [
                'name' => 'CurrencyCode',
            ];
        }

        return $this->validator;
    }

    /**
     * Set validator to return with input spec
     *
     * @param  array|\Zend\Validator\ValidatorInterface $validator
     * @return self
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getInputSpecification()
    {
        $inputSpec = parent::getInputSpecification();

        $defaultFilters = [
            ['name' => 'StringToUpper'],
            ['name' => 'StringTrim'],
        ];

        if (isset($inputSpec['filters'])) {
            array_unshift($inputSpec['filters'], $defaultFilters);
        } else {
            $inputSpec['filters'] = $defaultFilters;
        }

        return $inputSpec;
    }

    /**
     * Set Currency list to check allowed currencies against
     *
     * @param  CurrencyListInterface $list
     * @return self
     */
    public function setCurrencyList(CurrencyListInterface $list)
    {
        $this->currencyList = $list;
        return $this;
    }

    /**
     * Return the currency list for checking allowed currencies
     *
     * Lazy loads one if none set
     *
     * @return CurrencyListInterface
     */
    public function getCurrencyList()
    {
        if (null === $this->currencyList) {
            $this->setCurrencyList(new CurrencyList);
        }

        return $this->currencyList;
    }
}
