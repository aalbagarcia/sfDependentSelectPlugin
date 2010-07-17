<?php
/*
 * This file is part of the sfDependentSelect package.
 * (c) 2010 Sergio Flores <sercba@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
/**
 * sfDependentSelectSource abstract class for data sources
 *
 * @package    symfony
 * @subpackage sfDependentSelectPlugin
 * @author     Sergio Flores <sercba@gmail.com>
 */ 
abstract class sfDependentSelectSource
{
    abstract public function getValues($refValue = null);
    abstract public function getRefValue($value);

    public function __construct($params = array())
    {
        $this->fromArray($params);
    }
    
    public function fromArray($params)
    {
        foreach ($this as $prop => $value) {
            $optName = sfInflector::underscore(str_replace('_', '', $prop));
            if (isset($params[$optName])) {
                $this->$prop = $params[$optName];
            }
        }
    }
}
