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

use JsonSerializable;

/**
 * Exchange Rate
 */
class ExchangeRate implements ExchangeRateInterface, JsonSerializable
{
    /**
     * Currency to convert from
     *
     * @var CurrencyInterface
     */
    protected $fromCurrency;

    /**
     * Currency to convert to
     *
     * @var CurrencyInterface
     */
    protected $toCurrency;

    /**
     * @var float
     */
    protected $exchangeRate;

    /**
     * __construct
     *
     * @param CurrencyInterface $fromCurrency
     * @param CurrencyInterface $toCurrency
     * @param float    $exchangeRate
     * @throws Exception\InvalidArgumentException If conversion ratio is not numeric
     */
    public function __construct(CurrencyInterface $fromCurrency, CurrencyInterface $toCurrency, $exchangeRate)
    {
        if(!is_numeric($exchangeRate)) {
            throw new Exception\InvalidArgumentException('Exchange rate must be numeric');
        }

        $this->fromCurrency = $fromCurrency;
        $this->toCurrency   = $toCurrency;
        $this->exchangeRate = (float) $exchangeRate;
    }

    /**
     * Creates a new Exchange Rate based on "EUR/USD 1.2500" form representation
     *
     * @param string $iso String representation of the form "EUR/USD 1.2500"
     * @throws Exception\InvalidArgumentException Format of $iso is invalid
     * @return self
     */
    public static function createFromIso($iso)
    {
        $currency   = '([A-Z]{2,3})';
        $ratio      = '([0-9]*\.?[0-9]+)';
        $pattern    = "#$currency/$currency $ratio#";
        $matches    = [];

        if (!preg_match($pattern, $iso, $matches)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    "Can't create currency pair from ISO string '%s', format of string is invalid",
                    $iso
                )
            );
        }

        return new static(new Currency($matches[1]), new Currency($matches[2]), $matches[3]);
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\InvalidArgumentException If $money's currency is not equal to the currency to exchange from
     */
    public function exchange(Money $money, $roundingMode = Money::ROUND_HALF_UP)
    {
        if ($money->getCurrency() != $this->getFromCurrency()) {
            throw new Exception\InvalidArgumentException('The Money has the wrong currency');
        }

        return $money->exchange($this->getToCurrency(), $this->getExchangeRate(), $roundingMode);
    }

    /**
     * {@inheritDoc}
     */
    public function getFromCurrency()
    {
        return $this->fromCurrency;
    }

    /**
     * {@inheritDoc}
     */
    public function getToCurrency()
    {
        return $this->toCurrency;
    }

    /**
     * {@inheritDoc}
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'fromCurrency'  => $this->getFromCurrency(),
            'toCurrency'    => $this->getToCurrency(),
            'exchangeRate'  => $this->getExchangeRate(),
        ];
    }
}
