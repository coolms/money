<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\Service;

use Locale,
    ResourceBundle,
    CmsMoney\Currencies,
    CmsMoney\Currency,
    CmsMoney\Exception,
    CmsMoney\Money;

/**
 * List of supported currencies
 */
class CurrencyList implements CurrencyListInterface, \Iterator, \Countable
{
    /**
     * An array of allowed currency codes.
     * By default all currencies are allowed
     *
     * @var array
     */
    private $allowed = [];

    /**
     * An array of excluded currency codes.
     * By default all currencies are allowed
     *
     * @var array
     */
    private $excluded = [];

    /**
     * @var array|null|string
     */
    private $default;

    /**
     * @var array
     */
    private $codes;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * Constructor populates allowed codes with defaults and optionally filters those passed as an argument
     *
     * @param  array|null|string $allowed  An array of currency codes to allow
     * @param  array|null|string $excluded An array of currency codes to remove from the allowed list, or remove from the defaults if no allow is set
     * @param  array|null|string $default
     */
    public function __construct($allowed = null, $excluded = null, $default = null)
    {
        if (null !== $allowed) {
            $this->setAllowed((array) $allowed);
        }

        if (null !== $excluded) {
            $this->setExcluded((array) $excluded);
        }

        if (null !== $default) {
            $this->setDefault($default);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getCodes()
    {
        if (null === $this->codes) {
            $specs = Currencies::getSpecifications();
            $currencies = array_map('strtoupper', array_keys($specs));

            if ($this->allowed) {
                $currencies = array_intersect($currencies, $this->allowed);
            }

            if ($this->excluded) {
                $currencies = array_diff($currencies, $this->excluded);
            }

            $this->codes = array_values($currencies);
        }

        return $this->codes;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencies()
    {
        return array_map(Currency::class . '::create', $this->getCodes());
    }

    /**
     * {@inheritDoc}
     */
    public function setAllowed(array $codes)
    {
        $codes = array_map([$this, 'assertValidCode'], $codes);
        $this->allowed = array_unique($codes);
        $this->excluded = array_diff($this->excluded, $this->allowed);
        $this->codes = null;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function allow($code)
    {
        $code = $this->assertValidCode($code);
        $this->allowed[] = $code;
        $this->excluded = array_diff($this->excluded, [$code]);
        $this->codes = null;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isAllowed($code)
    {
        $code = $this->assertValidCode($code);
        return in_array($code, $this->getCodes());
    }

    /**
     * {@inheritDoc}
     */
    public function setExcluded(array $codes)
    {
        $codes = array_map([$this, 'assertValidCode'], $codes);
        $this->excluded = array_unique($codes);
        $this->codes = null;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function exclude($code)
    {
        $this->excluded[] = $this->assertValidCode($code);
        $this->codes = null;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefault($default)
    {
        if (is_string($default)) {
            $default = $this->assertValidCode($default);
        } elseif (is_array($default)) {
            foreach ($default as $locale => $code) {
                unset($default[$locale]);
                $locale = $this->assertValidLocale($locale);
                $default[$locale] = $this->assertValidCode($code);
            }
        } elseif (null !== $default) {
            throw new Exception\InvalidArgumentException(sprintf(
                'First argument is expected to be a type of array, null or string; %s given',
                is_object($default) ? get_class($default) : gettype($default)
            ));
        }

        $this->default = $default;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefault($locale = null)
    {
        if (is_string($this->default) && $this->isAllowed($this->default)) {
            return Currency::create($this->default);
        }

        if (!$this->default) {
            $currency = new Currency(null, $locale);
            if ($this->isAllowed($currency->getIsoCode())) {
                return $currency;
            }
        } elseif (is_array($this->default)) {
            $default = $locale ?: Locale::getDefault();
            foreach ($this->default as $locale => $code) {
                if ($default === $locale && $this->isAllowed($code)) {
                    return Currency::create($code);
                }
            }
        }

        return Currency::create($this->getCodes()[0]);
    }

    /**
     * Ensure the given code is a known valid code
     *
     * @param  string|Currency|Money        $code
     * @return string                       Normalized code
     * @throws Exception\InvalidArgumentException
     * @throws Exception\UnknownCurrencyException
     */
    protected function assertValidCode($code)
    {
        if ($code instanceof Money) {
            $code = $code->getCurrency();
        }

        if ($code instanceof Currency) {
            return (string) $code;
        }

        if (!is_string($code)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Currency code should be a string; %s given',
                gettype($code)
            ));
        }

        $code = trim(strtoupper($code));
        if (!Currencies::exists($code)) {
            throw new Exception\UnknownCurrencyException(sprintf(
                '%s is not a valid ISO 4217 Currency code',
                $code
            ));
        }

        return $code;
    }

    /**
     * {@inheritDoc}
     */
    protected function assertValidLocale($locale)
    {
        $locale = Locale::canonicalize($locale);

        if (!in_array($locale, ResourceBundle::getLocales(''))) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Locale %s could not be found',
                $locale
            ));
        }

        return $locale;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return Currency::create($this->getCodes()[$this->position]);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return isset($this->getCodes()[$this->position]);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->getCodes());
    }
}
