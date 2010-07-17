<?php
/*
 * This file is part of the sfDependentSelect package.
 * (c) 2010 Sergio Flores <sercba@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
/**
 * sfDependentSelectArraySource abstract class for objects based data sources
 *
 * @package    symfony
 * @subpackage sfDependentSelectPlugin
 * @author     Sergio Flores <sercba@gmail.com>
 */ 
abstract class sfDependentSelectObjectSource extends sfDependentSelectSource
{
    protected
        $_model,
        $_method,
        $_valueMethod,
        $_refMethod,
        $_orderBy;
        
    abstract protected function getObjects($fk = null);    
    abstract public function getObject($pk);
    
    public function getValues($refValue = null)
    {
        $values = array();
        $objects = $this->getObjects($refValue);
        $method = $this->_method;
        $keyMethod = $this->_valueMethod;
        $groupMethod = $this->_refMethod;

        if ($refValue || ! $groupMethod) {
            foreach ($objects as $object) {
              $values[$object->$keyMethod()] = $object->$method();
            }         
        } else {
            foreach ($objects as $object) {
              $values[$object->$groupMethod()][$object->$keyMethod()] = $object->$method();
            }           
        }

        return $values;
    }
    
    public function getRefValue($value)
    {
        $refValue = null;
        $object = $this->getObject($value);
        
        if ($object) {
            $refValue = call_user_func(array($object, $this->_refMethod));
        }
        
        return $refValue;        
    }                 
}
