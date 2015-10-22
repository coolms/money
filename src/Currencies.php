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

use Zend\Json\Json;

/**
 * List of supported currencies
 */
abstract class Currencies
{
    /**
     * List of known currency specifications
     *
     * @var array
     */
    private static $specifications = [];

    /**
     * Checks whether a currency is allowed in the current context
     *
     * @param string|CurrencyInterface $currency
     * @return bool
     */
    public static function exists($currency)
    {
        if ($currency instanceof CurrencyInterface) {
            $currency = $currency->getIsoCode();
        }

        self::loadSpecifications();
        return array_key_exists(strtolower($currency), self::$specifications);
    }

    /**
     * @param array $currencySpec
     */
    public static function register(array $currencySpec)
    {
        if (isset($currencySpec['code'])) {
            $code = strtolower($currencySpec['code']);
            unset($currencySpec['code']);
        } elseif (isset($currencySpec['iso_code'])) {
            $code = strtolower($currencySpec['iso_code']);
        } else {
            throw new Exception\InvalidArgumentException("Currency code couldn't be found whithin specification");
        }

        self::loadSpecifications();
        self::$specifications[$code] = $currencySpec;
    }

    /**
     * Retrieves list of supported currencies
     *
     * @return array
     */
    public static function getSpecifications()
    {
        self::loadSpecifications();
        return self::$specifications;
    }

    /**
     * @param string $code
     * @return array
     */
    public static function getSpecification($code)
    {
        $code = strtolower($code);
        $specs = self::getSpecifications();
        if (isset($specs[$code])) {
            return $specs[$code];
        }
    }

    /**
     * Loads currencies specs
     */
    private static function loadSpecifications()
    {
        if (!self::$specifications) {
            $content = file_get_contents(__DIR__ . '/../data/currencies.json');
            self::$specifications = Json::decode($content, Json::TYPE_ARRAY);
        }
    }
}
