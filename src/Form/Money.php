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

use Traversable,
    Zend\Form\Exception,
    CmsCommon\Form\InputFilterProviderFieldset,
    CmsCommon\Stdlib\ArrayUtils,
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
                'name'  => 'amount',
                'type'  => 'MoneyAmount',
                'label' => 'Amount',
            ]);
        }

        if (!$this->has('currency')) {
            $this->add([
                'name'  => 'currency',
                'type'  => 'CurrencySelect',
                'label' => 'Currency',
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (isset($options['locale'])) {
            $this->setLocale($options['locale']);
        }

        if (isset($options['max'])) {
            $this->setMax($options['max']);
        }

        if (isset($options['min'])) {
            $this->setMin($options['min']);
        }

        if (isset($options['step'])) {
            $this->setStep($options['step']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        if ($value instanceof MoneyObject) {
            $this->get('amount')->setValue($value->getAmount());
            $this->get('currency')->setValue($value->getCurrency());

            return $this;
        }

        if ($value instanceof \Traversable) {
            $value = ArrayUtils::iteratorToArray($value, false);
        }

        if (is_array($value)) {
            foreach ($value as $name => $val) {
                if ($this->has($name)) {
                    $this->get($name)->setValue($val);
                }
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function populateValues($data)
    {
        if ($data instanceof Traversable) {
            $data = ArrayUtils::iteratorToArray($data, true);
        }

        if (!is_array($data)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable set of data; received "%s"',
                __METHOD__,
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }

        if (!isset($data['amount']) || !isset($data['currency'])) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array with money amount and currency specified; received "%s"',
                __METHOD__,
                print_r($data, true)
            ));
        }

        $money = new MoneyObject($data['amount'], $data['currency']);
        if (null === ($precision = $this->getOption('precision'))) {
            $precision = MoneyObject::PRECISION_CURRENCY;
        }

        $data['amount'] = $money->setPrecision($precision)->toUnits();

        return parent::populateValues($data);
    }

    /**
     * @param number $max
     * @return self
     */
    public function setMax($max)
    {
        $this->get('amount')->setMax($max);
        return $this;
    }

    /**
     * @return number
     */
    public function getMax()
    {
        return $this->get('amount')->getMax();
    }

    /**
     * @param number $min
     * @return self
     */
    public function setMin($min)
    {
        $this->get('amount')->setMin($min);
        return $this;
    }

    /**
     * @return number
     */
    public function getMin()
    {
        return $this->get('amount')->getMin();
    }

    /**
     * @param number $step
     * @return self
     */
    public function setStep($step)
    {
        $this->get('amount')->setStep($step);
        return $this;
    }

    /**
     * @return number
     */
    public function getStep()
    {
        return $this->get('amount')->getStep();
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
