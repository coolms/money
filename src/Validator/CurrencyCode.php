<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\Validator;

use Zend\Validator\AbstractValidator,
    CmsMoney\Service\CurrencyList,
    CmsMoney\Service\CurrencyListInterface;

/**
 * Currency Code Validator
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
class CurrencyCode extends AbstractValidator
{
    /**
     * Regex to test currency code
     */
    const PATTERN = '/^[A-Z]{3}$/';

    /**
     * Error: Not String
     */
    const INVALID = 'notString';

    /**
     * Error: Fails Regex
     */
    const NOT_MATCH = 'noMatch';

    /**
     * Error: Code does not exist, or is denied by currency list config
     */
    const NOT_FOUND = 'notFound';

    /**
     * Configured List of allowed currencies
     *
     * @var CurrencyList
     */
    protected $currencyList;

    /**
     * Error Message Templates
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "Invalid type given. String expected",
        self::NOT_MATCH => "Invalid currency code value. 3 uppercase letters expected",
        self::NOT_FOUND => "The currency code provided does not match any known or allowed currency codes",
    );

    /**
     * Whether the value is valid
     *
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);
        if (!preg_match(self::PATTERN, $value)) {
            $this->error(self::NOT_MATCH);
            return false;
        }

        if (!$this->getCurrencyList()->isAllowed($value)) {
            $this->error(self::NOT_FOUND);
        }

        return count($this->getMessages()) === 0;
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
        if (!$this->currencyList) {
            $this->setCurrencyList(new CurrencyList());
        }

        return $this->currencyList;
    }
}
