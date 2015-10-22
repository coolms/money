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

/**
 * Currency Interface
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
interface CurrencyInterface
{
    /**
     * Returns the currency id
     *
     * @return string
     */
    public function getId();

    /**
     * Returns the currency ISO 2417 code
     *
     * @return string
     */
    public function getIsoCode();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getSymbol();

    /**
     * @return array
     */
    public function getAlternateSymbols();

    /**
     * @return string
     */
    public function getDisambiguateSymbol();

    /**
     * @return string
     */
    public function getSubunit();

    /**
     * @return int
     */
    public function getSubunitToUnit();

    /**
     * @return int
     */
    public function getFractionDigits();

    /**
     * @return bool
     */
    public function isSymbolFirst();

    /**
     * @return string
     */
    public function getHtmlEntity();

    /**
     * @return string
     */
    public function getDecimalMark();

    /**
     * @return string
     */
    public function getThousandsSeparator();

    /**
     * @return number
     */
    public function getIsoNumeric();

    /**
     * @return int
     */
    public function getSmallestDenomination();

    /**
     * @param CurrencyInterface
     * @return bool
     */
    public function equals(CurrencyInterface $currency);

    /**
     * @return array
     */
    public function toArray();
}
