<?php
/*
 * This file is part of the sfDependentSelect package.
 * (c) 2010 Sergio Flores <sercba@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
/**
 * sfDependentSelectDoctrineSource class for doctrine objects data sources
 *
 * @package    symfony
 * @subpackage sfDependentSelectPlugin
 * @author     Sergio Flores <sercba@gmail.com>
 */ 
class sfDependentSelectDoctrineSource extends sfDependentSelectObjectSource
{
    protected
        $_tableMethod;

    public function getObjects($fk = null)
    {
        if ( ! $this->_tableMethod) {
            
            $query = Doctrine_Core::getTable($this->_model)->createQuery();
            if (is_array($order = $this->_orderBy)) {
                $query->addOrderBy($order[0] . ' ' . $order[1]);
            }
            if ($fk) {
                $refColumn = str_replace('get_', '', sfInflector::underscore($this->_refMethod));
                $query->addWhere("{$refColumn} = ?", $fk);
            }
            $objects = $query->execute();
            
        } else {
        
            $tableMethod = $this->_tableMethod;
            $results = Doctrine_Core::getTable($this->_model)->$tableMethod($fk);
            if ($results instanceof Doctrine_Query) {
                $objects = $results->execute();
            }
            else if ($results instanceof Doctrine_Collection) {
                $objects = $results;
            }
            else if ($results instanceof Doctrine_Record) {
                $objects = new Doctrine_Collection($this->_model);
                $objects[] = $results;
            } else {
                $objects = array();
            }
        }

        return $objects;
    }
    
    public function getObject($pk)
    {
        return Doctrine_Core::getTable($this->_model)->find($pk);
    }
}
