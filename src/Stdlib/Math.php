<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2015 Łukasz Biały
 * @license   https://github.com/keiosweb/moneyright/blob/master/LICENSE.md
 */

namespace CmsMoney\Stdlib;

use Litipk\BigNumbers\Decimal,
    CmsMoney\Exception\InvalidArgumentException;

/**
 * Class Math
 * Provides arbitrary precision rounding using BCMath extension
 *
 * @see https://github.com/keiosweb/moneyright/blob/master/src/Math.php
 */
abstract class Math
{
    /**
     * @const int HALF - rounding point
     */
    const HALF = 5;

    const ROUND_HALF_UP     = PHP_ROUND_HALF_UP;
    const ROUND_HALF_DOWN   = PHP_ROUND_HALF_DOWN;
    const ROUND_HALF_EVEN   = PHP_ROUND_HALF_EVEN;
    const ROUND_HALF_ODD    = PHP_ROUND_HALF_ODD;

    /**
     * @var array
     */
    private static $roundingModes = [
        'ROUND_HALF_DOWN'   => self::ROUND_HALF_DOWN,
        'ROUND_HALF_EVEN'   => self::ROUND_HALF_EVEN,
        'ROUND_HALF_ODD'    => self::ROUND_HALF_ODD,
        'ROUND_HALF_UP'     => self::ROUND_HALF_UP,
    ];

    /**
     * @param string $number
     * @param int $precision
     * @return bool
     */
    final private static function isFirstDecimalAfterPrecisionTrailedByZeros($number, $precision)
    {
        $secondPlaceAfterPrecision = strpos($number, '.') + $precision + 2;
        $remainingDecimals = substr($number, $secondPlaceAfterPrecision);
        return bccomp($remainingDecimals, '0', 64) === 1;
    }

    /**
     * @param string $number
     * @param int $precision
     * @return string
     */
    final private static function getHalfUpValue($number, $precision)
    {
        $sign = self::getSign($number);
        return $sign . '0.' . str_repeat('0', $precision) . '5';
    }

    /**
     * @param string $number
     * @param int $precision
     * @return string
     */
    final private static function truncate($number, $precision)
    {
        return bcadd($number, '0', $precision);
    }

    /**
     * @param int $firstDecimalAfterPrecision
     * @param string $number
     * @param int $precision
     * @return string
     */
    final private static function roundNotTied($firstDecimalAfterPrecision, $number, $precision)
    {
        if ($firstDecimalAfterPrecision > self::HALF) {
            return self::bcRoundHalfUp($number, $precision);
        } else {
            return self::truncate($number, $precision);
        }
    }

    /**
     * @param string $number
     * @param int $precision
     * @param int $roundingMode
     * @return string
     * @throws InvalidArgumentException
     */
    final private static function roundTied($number, $precision, $roundingMode)
    {
        if (self::isFirstDecimalAfterPrecisionTrailedByZeros($number, $precision)) {
            $result = self::bcRoundHalfUp($number, $precision);
        } else {
            switch ($roundingMode) {
                case self::ROUND_HALF_DOWN:
                    $result = self::truncate($number, $precision);
                    break;
                case self::ROUND_HALF_EVEN:
                    $result = self::getEvenRoundedResult($number, $precision);
                    break;
                case self::ROUND_HALF_ODD:
                    $result = self::getOddRoundedResult($number, $precision);
                    break;
                default:
                    throw new InvalidArgumentException(sprintf(
                        'Rounding mode should be %s',
                        implode(' | ', array_map(
                            function($constant) {
                                return __CLASS__ . '::' . $constant;
                            },
                            array_keys(self::$roundingModes)
                        ))
                    ));
            }
        }

        return $result;
    }

    /**
     * @param string $number
     * @return string
     */
    final private static function getSign($number)
    {
        if (bccomp('0', $number, 64) == 1) {
            return '-';
        } else {
            return '';
        }
    }

    /**
     * @param string $number
     * @param int $precision
     * @return int
     */
    final private static function getEvenOddDigit($number, $precision)
    {
        list($integers, $decimals) = explode('.', $number);
        if ($precision === 0) {
            return (int)substr($integers, -1);
        } else {
            return (int)$decimals[$precision - 1];
        }
    }

    /**
     * @param string $number
     * @param int $precision
     * @return string
     */
    final private static function getOddRoundedResult($number, $precision)
    {
        if (self::getEvenOddDigit($number, $precision) % 2) { // odd
            return self::truncate($number, $precision);
        } else { // even
            return self::truncate(self::bcRoundHalfUp($number, $precision), $precision);
        }
    }

    /**
     * @param string $number
     * @param int $precision
     * @return string
     */
    final private static function getEvenRoundedResult($number, $precision)
    {
        if (self::getEvenOddDigit($number, $precision) % 2) { // odd
            return self::bcRoundHalfUp($number, $precision);
        } else { // even
            return self::truncate($number, $precision);
        }
    }

    /**
     * @param string $number
     * @param int $precision
     * @return int
     */
    final private static function getFirstDecimalAfterPrecision($number, $precision)
    {
        $decimals = explode('.', $number)[1];
        $firstDecimalAfterPrecision = (int)substr($decimals, $precision, 1);
        return $firstDecimalAfterPrecision;
    }

    /**
     * Round decimals from 5 up, less than 5 down
     *
     * @param string $number
     * @param int $precision
     * @return string
     */
    final private static function bcRoundHalfUp($number, $precision)
    {
        return self::truncate(bcadd($number, self::getHalfUpValue($number, $precision), $precision + 1), $precision);
    }

    /**
     * @param number $precision
     * @return int
     */
    final private static function normalizePrecision($precision)
    {
        return ($precision < 0) ? 0 : (int) $precision;
    }

    /**
     * @param string $number
     * @return bool
     */
    final private static function isNotDecimalString($number)
    {
        return strpos($number, '.') === false;
    }

    /**
     * @param string $result
     * @param int $precision
     * @return string
     */
    final private static function normalizeZero($result, $precision)
    {
        if ($result[0] === '-') {
            if (bccomp(substr($result, 1), '0', $precision) === 0) {
                return '0';
            }
        }

        return $result;
    }

    /**
     * BCRound implementation
     *
     * @param number|Decimal $number
     * @param int $precision
     * @param int $roundingMode
     * @return Decimal
     * @throws InvalidArgumentException
     */
    final public static function bcround($number, $precision, $roundingMode = self::ROUND_HALF_UP)
    {
        if ($number instanceof Decimal) {
            $number = (string) $number;
        }

        $precision = self::normalizePrecision($precision);
        if (self::isNotDecimalString($number)) {
            return Decimal::create($number, $precision);
        }

        if ($roundingMode === self::ROUND_HALF_UP) {
            return Decimal::create(self::bcRoundHalfUp($number, $precision), $precision);
        }

        $firstDecimalAfterPrecision = self::getFirstDecimalAfterPrecision($number, $precision);
        if ($firstDecimalAfterPrecision === self::HALF) {
            $result = self::roundTied($number, $precision, $roundingMode);
        } else {
            $result = self::roundNotTied($firstDecimalAfterPrecision, $number, $precision);
        }

        /*
         * Arbitrary precision arithmetic allows for '-0.0' which is not equal to '0.0' if compared with bccomp.
         * We have no use for this behaviour, so negative numbers have to be checked if they are minus zero,
         * so we can convert them into unsigned zero and return that.
         */
        $result = self::normalizeZero($result, $precision);

        return Decimal::create($result, $precision);
    }
}
