<?php
/*
 * This file is part of the sfDependentSelect package.
 * (c) 2010 Sergio Flores <sercba@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
/**
 * sfDependentSelectArraySource class for array data sources
 *
 * @package    symfony
 * @subpackage sfDependentSelectPlugin
 * @author     Sergio Flores <sercba@gmail.com>
 */ 
class sfDependentSelectArraySource extends sfDependentSelectSource
{
    protected
        $_callable;
        
    public function getValues($refValue = null)
    {
        $array = call_user_func($this->_callable);
        
        if ($refValue) {
            $array = $array[$refValue];
        }
        
        return $array;
    }
    
    public function getRefValue($value)
    {
        $refValue = null;
        $values = $this->getValues();
        
        foreach ($values as $iGroup => $iOption) {
            foreach ($iOption as $iValue => $iText) {
                if ($iValue == $value) {
                    $refValue =  $iGroup;
                }
            }
        }
        
        return $refValue;
    }
}
