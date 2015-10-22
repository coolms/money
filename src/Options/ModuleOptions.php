<?php 
/**
 * CoolMS2 Money Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/money for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsMoney\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements ModuleOptionsInterface
{
    /**
     * Turn off strict options mode
     *
     * @var bool
     */
    protected $__strictMode__ = false;

    /**
     * @var array
     */
    protected $allowed = [];

    /**
     * @var array
     */
    protected $excluded = [];

    /**
     * @var array
     */
    protected $currencySpecs = [];

    /**
     * @var array|string
     */
    protected $default;

    /**
     * {@inheritDoc}
     */
    public function setAllowed($codes)
    {
    	$this->allowed = (array) $codes;
    	return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowed()
    {
    	return $this->allowed;
    }

    /**
     * {@inheritDoc}
     */
    public function setExcluded($codes)
    {
        $this->excluded = (array) $codes;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getExcluded()
    {
        return $this->excluded;
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrencySpecs(array $specs)
    {
        $this->currencySpecs = $specs;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrencySpecs()
    {
        return $this->currencySpecs;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefault($code)
    {
        $this->default = $code;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefault()
    {
        return $this->default;
    }
}
