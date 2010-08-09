<?php
/*
 * This file is part of the sfDependentSelect package.
 * (c) 2010 Sergio Flores <sercba@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormObjectDependentSelect represents an select widget rendered by
 * SelectDependiente javascript class optimized for objects.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Sergio Flores <sercba@gmail.com>
 */
abstract class sfWidgetFormObjectDependentSelect extends sfWidgetFormDependentSelect
{
    /**
     * Configures the current widget.
     *
     * Available options details in 
     * http://www.symfony-project.org/plugins/sfDependentSelectPlugin
     *
     * @param array $options     An array of options
     * @param array $attributes  An array of default HTML attributes
     *
     * @see sfWidgetForm
     */
    protected function configure($options = array(), $attributes = array())
    {
        $this->addRequiredOption('model');
        $this->addOption('method', '__toString');
        $this->addOption('key_method', 'getId');
        $this->addOption('ref_method', '');        
        $this->addOption('order_by', '');
        
        parent::configure($options, $attributes);
    }

    /**
     * @param  string $name        The element name
     * @param  string $value       The date displayed in this widget
     * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
     * @param  array  $errors      An array of errors for the field
     *
     * @return string An HTML tag string
     *
     * @see sfWidgetForm
     */   
    public function render($name, $value = null, $attributes = array(), $errors = array())
    {
        if ( ! $this->getOption('ref_method') && $this->getOption('depends')) {
            if ($this->isModel($this->getOption('depends'))) {
                $this->setOption('ref_method', 'get' . $this->getOption('depends') . 'Id');
            } else {
                $this->setOption('ref_method', 'get' . ucfirst($this->getOption('depends')));
            }
        }
    
        $this->setSourceParam(array(
            'model'        => $this->getOption('model'),
            'method'       => $this->getOption('method'),
            'value_method' => $this->getOption('key_method'),
            'ref_method'   => $this->getOption('ref_method'),
            'order_by'     => $this->getOption('order_by'),
        ));
    
        return parent::render($name, $value, $attributes, $errors);
    }
    
    protected function generateJavascriptVar($baseName, $var = null)
    {
        if ($this->isModel($this->getOption('depends'))) {
          $var = $this->getOption('ref_method');
          if (substr($var, 0, 3) === 'get') {
            $var = substr($var, 3);
          }
        }

        return parent::generateJavascriptVar($baseName, $var);  
    }
    
    protected function isModel($str)
    {
        return ctype_upper(substr($str, 0, 1));
    }     
}
