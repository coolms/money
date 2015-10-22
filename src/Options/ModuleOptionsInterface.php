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

interface ModuleOptionsInterface
{
    /**
     * @param array|string $codes
     * @return self
     */
    public function setAllowed($codes);

    /**
     * @return array
     */
    public function getAllowed();

    /**
     * @param array|string $codes
     * @return self
     */
    public function setExcluded($codes);

    /**
     * @return array
     */
    public function getExcluded();

    /**
     * @param array $specs
     */
    public function setCurrencySpecs(array $specs);

    /**
     * @return array
     */
    public function getCurrencySpecs();

    /**
     * @param array|null|string $code
     * @return self
     */
    public function setDefault($code);

    /**
     * @return array|null|string
     */
    public function getDefault();
}
