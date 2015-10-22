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

use JsonSerializable,
    Locale,
    NumberFormatter,
    Serializable,
    Zend\Filter\StaticFilter,
    Zend\Filter\Word\CamelCaseToUnderscore,
    Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * Currency Value Object
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
class Currency implements CurrencyInterface, JsonSerializable, Serializable
{
    /**
     * @var string
     */
    protected $id;

    /**
     * Currency ISO 4217 alpha code
     *
     * @var string
     */
    protected $isoCode;

    /**
     * Currency display name
     *
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $symbol;

    /**
     * @var array
     */
    protected $alternateSymbols = [];

    /**
     * @var null|string
     */
    protected $disambiguateSymbol = null;

    /**
     * @var string
     */
    protected $subunit;

    /**
     * @var int
     */
    protected $subunitToUnit;

    /**
     * @var int
     */
    protected $fractionDigits;

    /**
     * @var bool
     */
    protected $symbolFirst;

    /**
     * @var string
     */
    protected $htmlEntity;

    /**
     * @var string
     */
    protected $decimalMark;

    /**
     * @var string
     */
    protected $thousandsSeparator;

    /**
     * Currency ISO 4217 numeric code
     *
     * @var number
     */
    protected $isoNumeric;

    /**
     * @var int
     */
    protected $smallestDenomination;

    /**
     * __construct
     *
     * @param string $code
     * @param string $locale
     *
     * @throws Exception\UnexpectedValueException
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($code = null, $locale = null)
    {
        if (null === $code) {
            $formatter = new NumberFormatter((string) $locale ?: Locale::getDefault(), NumberFormatter::CURRENCY);
            $code = $formatter->getSymbol(NumberFormatter::INTL_CURRENCY_SYMBOL);
        }

        if (!is_string($code)) {
            throw new Exception\UnexpectedValueException('Currency code should be a string');
        }

        if (!Currencies::exists($code)) {
            throw new Exception\InvalidArgumentException('Currency with "' . $code . '" code does not exist!');
        }

        $this->id = strtolower($code);
        $this->fromArray(Currencies::getSpecification($this->id));
    }

    /**
     * @param string $code
     * @param string $locale
     * @return self
     */
    public static function create($code = null, $locale = null)
    {
        return new static($code, $locale);
    }

    /**
     * @param string $method
     * @param array $args
     * @return self
     */
    public static function __callStatic($method, $args)
    {
        return new static($method);
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * {@inheritDoc}
     */
    public function getAlternateSymbols()
    {
        return $this->alternateSymbols;
    }

    /**
     * {@inheritDoc}
     */
    public function getDisambiguateSymbol()
    {
        return $this->disambiguateSymbol;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubunit()
    {
        return $this->subunit;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubunitToUnit()
    {
        return $this->subunitToUnit;
    }

    /**
     * {@inheritDoc}
     */
    public function getFractionDigits()
    {
        if (null === $this->fractionDigits) {
            if ($this->subunitToUnit > 1) {
                if ($this->subunitToUnit % 10 === 0) {
                    $this->fractionDigits = strlen(strval($this->subunitToUnit - 1));
                } else {
                    $formatter = new NumberFormatter('@currency=' . $this->isoCode, NumberFormatter::CURRENCY);
                    $this->fractionDigits = (int) $formatter->getAttribute(NumberFormatter::FRACTION_DIGITS);
                }
            } else {
                $this->fractionDigits = 0;
            }
        }

        return $this->fractionDigits;
    }

    /**
     * {@inheritDoc}
     */
    public function isSymbolFirst()
    {
        return $this->symbolFirst;
    }

    /**
     * {@inheritDoc}
     */
    public function getHtmlEntity()
    {
        return $this->htmlEntity;
    }

    /**
     * {@inheritDoc}
     */
    public function getDecimalMark()
    {
        return $this->decimalMark;
    }

    /**
     * {@inheritDoc}
     */
    public function getThousandsSeparator()
    {
        return $this->thousandsSeparator;
    }

    /**
     * {@inheritDoc}
     */
    public function getIsoNumeric()
    {
        return $this->isoNumeric;
    }

    /**
     * {@inheritDoc}
     */
    public function getSmallestDenomination()
    {
        return $this->smallestDenomination;
    }

    /**
     * @param array $spec
     */
    protected function fromArray(array $spec)
    {
        foreach ($spec as $name => $value) {
            $property = lcfirst(StaticFilter::execute($name, UnderscoreToCamelCase::class));
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $arrayCopy = [];
        foreach (get_object_vars($this) as $property => $value) {
            if (is_scalar($value) || is_array($value)) {
                $getter = 'get' . ucfirst($property);
                if (method_exists($this, $getter)) {
                    $property = strtolower(StaticFilter::execute($property, CamelCaseToUnderscore::class));
                    $arrayCopy[$property] = $this->$getter();
                }
            }
        }

        return $arrayCopy;
    }

    /**
     * {@inheritDoc}
     */
    public function equals(CurrencyInterface $currency)
    {
        return $this->toArray() == $currency->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $unserialized = unserialize($serialized);
        $this->fromArray($unserialized);
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getIsoCode();
    }
}
