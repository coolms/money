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

use Serializable,
    JsonSerializable,
    Litipk\BigNumbers\Decimal,
    CmsMoney\Stdlib\Math;

/**
 * Money Value Object
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
class Money implements JsonSerializable, Serializable
{
    const PRECISION_CURRENCY = 0;
    const PRECISION_COMMON   = 2;
    const PRECISION_GAAP     = 4;

    const ROUND_HALF_UP     = Math::ROUND_HALF_UP;
    const ROUND_HALF_DOWN   = Math::ROUND_HALF_DOWN;
    const ROUND_HALF_EVEN   = Math::ROUND_HALF_EVEN;
    const ROUND_HALF_ODD    = Math::ROUND_HALF_ODD;

    /**
     * @var array
     */
    private $roundingModes = [
        'ROUND_HALF_DOWN'   => self::ROUND_HALF_DOWN,
        'ROUND_HALF_EVEN'   => self::ROUND_HALF_EVEN,
        'ROUND_HALF_ODD'    => self::ROUND_HALF_ODD,
        'ROUND_HALF_UP'     => self::ROUND_HALF_UP,
    ];

    /**
     * Internal value
     *
     * @var Decimal
     */
    protected $amount;

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var int
     */
    private $precision;

    /**
     * @var int
     */
    private static $innerPrecision = self::PRECISION_GAAP;

    /**
     * @param number|Decimal            $amount   Decimal, expressed in the smallest units of $currency (eg cents)
     * @param string|CurrencyInterface  $currency
     * @throws Exception\InvalidArgumentException If amount is not integer
     */
    public function __construct($amount = 0, $currency = null)
    {
        $this->currency = $this->normalizeCurrency($currency);
        $this->amount = $this->normalizeAmount($amount);
    }

    /**
     * Convenience factory method for a Money object
     *
     * <code>
     * $fiveDollar = Money::USD(500);
     * </code>
     *
     * @param string $method
     * @param array  $arguments
     * @return Money
     */
    public static function __callStatic($method, $arguments)
    {
        return new static($arguments[0], $method);
    }

    /**
     * Returns a new Money instance based on the current one using the Currency
     *
     * @param int $amount
     * @return self
     */
    private function newInstance($amount)
    {
        return new static($amount, $this->currency);
    }

    /**
     * @param int $precision
     */
    public static function setInnerPrecision($precision)
    {
        self::assertPrecision($precision);
        self::$innerPrecision = $precision;
    }

    /**
     * @return int
     */
    private function getInnerPrecision()
    {
        if (!self::$innerPrecision) {
            return (int) $this->currency->getFractionDigits();
        }

        return self::$innerPrecision;
    }

    /**
     * @param int $precision
     * @return self
     */
    public function setPrecision($precision)
    {
        self::assertPrecision($precision);
        $this->precision = $precision;

        return $this;
    }

    /**
     * @return int
     */
    private function getPrecision()
    {
        if (null === $this->precision) {
            return $this->getInnerPrecision();
        }

        return $this->precision;
    }

    /**
     * Checks whether a Money has the same Currency as this
     *
     * @param Money $money
     * @return bool
     */
    public function isSameCurrency(Money $money)
    {
        return $this->currency->equals($money->currency);
    }

    /**
     * Asserts that a Money has the same currency as this
     *
     * @param Money $money
     * @throws Exception\InvalidArgumentException If $other has a different currency
     */
    private function assertSameCurrency(Money $money)
    {
        if (!$this->isSameCurrency($money)) {
            throw new Exception\InvalidArgumentException('Currencies must be identical');
        }
    }

    /**
     * Checks whether the value represented by this object equals to the other
     *
     * @param Money $money
     * @return bool
     */
    public function equals(Money $money)
    {
        return $this->isSameCurrency($money) &&
            $this->amount->equals($money->amount, $this->getInnerPrecision());
    }

    /**
     * Returns an integer less than, equal to, or greater than zero
     * if the value of this object is considered to be respectively
     * less than, equal to, or greater than the other
     *
     * @param Money $money
     * @return int
     */
    public function compare(Money $money)
    {
        $this->assertSameCurrency($money);
        return $this->amount->comp($money->amount, $this->getInnerPrecision());
    }

    /**
     * Checks whether the value represented by this object is greater than the other
     *
     * @param Money $money
     * @return bool
     */
    public function greaterThan(Money $money)
    {
        return 1 == $this->compare($money);
    }

    /**
     * @param Money $money
     * @return bool
     */
    public function greaterThanOrEqual(Money $money)
    {
        return 0 >= $this->compare($money);
    }

    /**
     * Checks whether the value represented by this object is less than the other
     *
     * @param Money $money
     * @return bool
     */
    public function lessThan(Money $money)
    {
        return -1 == $this->compare($money);
    }

    /**
     * @param Money $money
     * @return bool
     */
    public function lessThanOrEqual(Money $money)
    {
        return 0 <= $this->compare($money);
    }

    /**
     * @param float|int|string|Decimal $amount
     * @return Decimal
     */
    protected function normalizeAmount($amount)
    {
        $innerPrecision = $this->getInnerPrecision();

        if (!$amount instanceof Decimal) {
            $amount = Decimal::create($amount ?: 0, $innerPrecision);
        }

        $this->currency = $this->normalizeCurrency($this->currency);
        $subunitToUnit = $this->currency->getSubunitToUnit();
        if (is_int(func_get_arg(0)) && $subunitToUnit > 1) {
            $subunitToUnit = Decimal::create($subunitToUnit, $innerPrecision);
            $amount = $amount->div($subunitToUnit, $innerPrecision);
        }

        return $amount->round($innerPrecision);
    }

    /**
     * @param null|string|CurrencyInterface $currency
     * @return CurrencyInterface
     */
    protected function normalizeCurrency($currency)
    {
        if (null === $currency) {
            $currency = new Currency;
        } elseif (is_string($currency)) {
            $currency = new Currency($currency);
        }

        return $currency;
    }

    /**
     * @return Decimal
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Returns the value represented by this object
     *
     * @return Decimal
     */
    public function toSubunits($roundingMode = self::ROUND_HALF_UP)
    {
        $this->assertRoundingMode($roundingMode);

        $precision = $this->getPrecision();
        if (null === $roundingMode) {
            $amount = $this->amount->round($precision);
        } else {
            $amount = Math::bcround($this->amount, $precision, $roundingMode);
        }

        $subunitToUnit = $this->currency->getSubunitToUnit();
        if ($subunitToUnit > 1) {
            $subunitToUnit = Decimal::create($subunitToUnit, $precision);
            $amount = $amount->mul($subunitToUnit, $precision);
        }

        return $amount->asInteger();
    }

    /**
     * Useful for payment systems that don't use high precision
     *
     * @param int $roundingMode
     * @return Decimal
     */
    public function toUnits($roundingMode = self::ROUND_HALF_UP)
    {
        $this->assertRoundingMode($roundingMode);
        $precision = $this->getPrecision();

        if (null === $roundingMode) {
            return $this->amount->round($precision);
        }

        return Math::bcround($this->amount, $precision, $roundingMode);
    }

    /**
     * Returns the currency of this object
     *
     * @return CurrencyInterface
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Asserts that integer remains integer after arithmetic operations
     *
     * @param  numeric $amount
     * @throws Exception\UnexpectedValueException If $amount isn't integer
     */
    private function assertInteger($amount)
    {
        if (!is_int($amount)) {
            throw new Exception\UnexpectedValueException('The result of arithmetic operation is not an integer');
        }
    }

    /**
     * Returns a new Money object that represents
     * the sum of this and an other Money object
     *
     * @param Money $money
     * @return Money
     */
    public function add(Money $money)
    {
        $this->assertSameCurrency($money);
        $amount = $this->amount->add($money->amount, $this->getInnerPrecision());
        return $this->newInstance($amount);
    }

    /**
     * Returns a new Money object that represents
     * the difference of this and an other Money object
     *
     * @param Money $money
     * @return Money
     */
    public function subtract(Money $money)
    {
        $this->assertSameCurrency($money);
        $amount = $this->amount->sub($money->amount, $this->getInnerPrecision());
        return $this->newInstance($amount);
    }

    /**
     * Asserts that the operand is integer or float
     *
     * @param numeric $operand
     * @throws Exception\InvalidArgumentException If $operand is neither integer nor float
     */
    private function assertOperand($operand)
    {
        if (!is_int($operand) && !is_float($operand)) {
            throw new Exception\InvalidArgumentException('Operand should be an integer or a float');
        }
    }

    /**
     * @param numeric $operand
     * @return Decimal
     */
    private function castOperand($operand)
    {
        $this->assertOperand($operand);
        return Decimal::create($operand, $this->getInnerPrecision());
    }

    /**
     * @param unknown $precision
     * @throws Exception\InvalidArgumentException If $operand is neither integer nor null
     */
    private static function assertPrecision($precision)
    {
        if (null !== $precision && !is_int($precision)) {
            throw new Exception\InvalidArgumentException('Precision should be an integer');
        }
    }

    /**
     * @param number $precision
     * @return int
     */
    private function castPrecision($precision)
    {
        $this->assertPrecision($precision);
        if (!$precision) {
            $precision = null === $precision ? $this->getInnerPrecision() : $this->getPrecision();
        }

        return $this->castInteger($precision);
    }

    /**
     * Asserts that an integer value didn't become something else
     *
     * @param numeric $int
     * @throws Exception\OverflowException If integer overflow occured
     * @throws Exception\UnderflowException If integer underflow occured
     */
    private function assertIntegerBounds($int)
    {
        if ($int > PHP_INT_MAX) {
            throw new Exception\OverflowException;
        } elseif ($int < ~PHP_INT_MAX) {
            throw new Exception\UnderflowException;
        }
    }

    /**
     * Casts an argument to integer ensuring that an overflow/underflow did not occur
     *
     * @param numeric $int
     * @return int
     */
    private function castInteger($int)
    {
        $this->assertIntegerBounds($int);
        return (int)$int;
    }

    /**
     * Asserts that rounding mode is a valid integer value
     *
     * @param int $roundingMode
     * @throws Exception\InvalidArgumentException If $roundingMode is not valid
     */
    private function assertRoundingMode($roundingMode)
    {
        if ($roundingMode !== null && !in_array($roundingMode, $this->roundingModes)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Rounding mode should be %s',
                implode(' | ', array_map(
                    function($constant) {
                        return __CLASS__ . '::' . $constant;
                    },
                    array_keys($this->roundingModes)
                ))
            ));
        }
    }

    /**
     * Returns a new Money object that represents
     * the multiplied value by the given factor
     *
     * @param numeric $multiplier
     * @param int $roundingMode
     * @return Money
     */
    public function multiply($multiplier, $roundingMode = self::ROUND_HALF_UP, $precision = self::PRECISION_GAAP)
    {
        $this->assertRoundingMode($roundingMode);
        $multiplier = $this->castOperand($multiplier);
        $precision = $this->castPrecision($precision);

        $amount = $this->amount->mul($multiplier, $this->getInnerPrecision() + 1);

        if (null === $roundingMode) {
            $amount = $amount->round($precision);
        } else {
            $amount = Math::bcround($amount, $precision, $roundingMode);
        }

        return $this->newInstance($amount);
    }

    /**
     * Returns a new Money object that represents
     * the divided value by the given factor
     *
     * @param numeric $divisor
     * @param int $roundingMode
     * @param int $precision
     * @throws Exception\InvalidArgumentException If division by zero accured
     * @return Money
     */
    public function divide($divisor, $roundingMode = self::ROUND_HALF_UP, $precision = self::PRECISION_GAAP)
    {
        $this->assertRoundingMode($roundingMode);
        $divisor = $this->castOperand($divisor);
        $precision = $this->castPrecision($precision);

        $innerPrecision = $this->getInnerPrecision();
        if ($divisor->isZero($innerPrecision)) {
            throw new Exception\InvalidArgumentException('Division by zero');
        }

        $amount = $this->amount->div($divisor, $innerPrecision + 1);

        if (null === $roundingMode) {
            $amount = $amount->round($precision);
        } else {
            $amount = Math::bcround($amount, $precision, $roundingMode);
        }

        return $this->newInstance($amount);
    }

    /**
     * Allocate the money according to a list of ratios
     *
     * @param array $ratios
     * @param int $precision
     * @return Money[]
     */
    public function allocate(array $ratios, $precision = self::PRECISION_CURRENCY)
    {
        $precision = $this->castPrecision($precision);

        $remainder  = $this->amount;
        $results    = [];
        $total      = Decimal::create(array_sum($ratios), $precision);

        foreach ($ratios as $ratio) {
            $share = $this->amount
                ->mul(Decimal::create($ratio, $precision), $precision)
                ->div($total, $precision);

            $results[] = $this->newInstance($share);
            $remainder = $remainder->sub($share, $precision);
        }

        $minValue = Decimal::create(
            $precision ? '0.' . str_repeat('0', $precision - 1) . '1' : 1
        );

        for ($i = 0; !$remainder->isZero($precision); $i++) {
            if ($remainder->isPositive()) {
                $remainder = $remainder->sub($minValue, $precision);
                $results[$i]->amount = $results[$i]->amount->add($minValue, $precision);
            } else {
                $remainder = $remainder->add($minValue, $precision);
                $results[$i]->amount = $results[$i]->amount->sub($minValue, $precision);
            }
        }

        return $results;
    }

    /**
     * Allocate the money among N targets
     *
     * @param int $n
     * @param int $precision
     * @throws Exception\InvalidArgumentException If number of targets is not an integer
     * @return Money[]
     */
    public function allocateTo($n, $precision = self::PRECISION_CURRENCY)
    {
        if (!is_int($n)) {
            throw new Exception\InvalidArgumentException('Number of targets must be an integer');
        }

        return $this->allocate(array_fill(0, $n, 1), $precision);
    }

    /**
     * @param CurrencyInterface $targetCurrency
     * @param float|int $exchangeRate
     * @param int $roundingMode
     * @return Money
     */
    public function exchange(CurrencyInterface $targetCurrency, $exchangeRate, $roundingMode = self::ROUND_HALF_UP)
    {
        $this->assertRoundingMode($roundingMode);
        $exchangeRate = $this->castOperand($exchangeRate);
        $amount = $this->amount->mul($exchangeRate, $this->getInnerPrecision());
        return new static($amount, $targetCurrency);
    }

    /**
     * Checks if the value represented by this object is zero
     *
     * @return bool
     */
    public function isZero()
    {
        return $this->amount->isZero($this->getInnerPrecision());
    }

    /**
     * Checks if the value represented by this object is positive
     *
     * @return bool
     */
    public function isPositive()
    {
        return $this->amount->isPositive();
    }

    /**
     * Checks if the value represented by this object is negative
     *
     * @return bool
     */
    public function isNegative()
    {
        return $this->amount->isNegative();
    }

    /**
     * @param string $string
     * @return self
     */
    public static function fromString($string)
    {
        
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getAmount();
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
            'amount' => (string) $this->amount,
            'currency' => serialize($this->currency),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $unserialized = unserialize($serialized);

        $this->amount = Decimal::create($unserialized['amount']);
        $this->currency = unserialize($unserialized['currency']);
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'amount'    => (string) $this->amount,
            'currency'  => $this->currency,
        ];
    }
}
