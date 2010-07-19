<?php
/*
 * This file is part of the sfDependentSelect package.
 * (c) 2010 Sergio Flores <sercba@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
/**
 * sfDependentSelectDoctrineSource class for propel objects data sources
 *
 * @package    symfony
 * @subpackage sfDependentSelectPlugin
 * @author     Sergio Flores <sercba@gmail.com>
 */ 
class sfDependentSelectPropelSource extends sfDependentSelectObjectSource
{
    protected
        $_peerMethod = 'doSelect';

    public function getObjects($fk = null)
    {
        $class = constant($this->_model.'::PEER');
        $criteria = new Criteria();
        
        if (is_array($order = $this->_orderBy)) {
          $method = sprintf('add%sOrderByColumn', 0 === strpos(strtoupper($order[1]), 'ASC') ? 'Ascending' : 'Descending');
          $criteria->$method(call_user_func(array($class, 'translateFieldName'), $order[0], BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME));
        }
        if ($fk) {
          $criteria->add(call_user_func(array($class, 'translateFieldName'), str_replace('get_', '', sfInflector::underscore($this->_refMethod)), BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME), $fk);
        }
        
        return call_user_func(array($class, $this->_peerMethod), $criteria);   
    }
    
    public function getObject($pk)
    {
        $class = constant($this->_model.'::PEER');
        $criteria = new Criteria();
        $colName = call_user_func(array($class, 'translateFieldName'), substr($this->_valueMethod, 3), BasePeer::TYPE_PHPNAME, BasePeer::TYPE_COLNAME);
        $criteria->add($colName, $pk);
        return call_user_func(array($class, 'doSelectOne'), $criteria);        
    } 
}
