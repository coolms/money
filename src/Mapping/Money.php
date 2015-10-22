<?php
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\Mapping;

use CmsMoney\Money as BaseMoney;

class Money extends BaseMoney
{
    /**
     * @param number $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $this->normalizeAmount($amount);
    }

    /**
     * @param string $code
     */
    public function setCurrency($code)
    {
        $this->currency = $this->normalizeCurrency($code);
    }
}
