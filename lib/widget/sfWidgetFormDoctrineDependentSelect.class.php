<?php
/*
 * This file is part of the sfDependentSelect package.
 * (c) 2010 Sergio Flores <sercba@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormDoctrineDependentSelect represents an select widget rendered by
 * SelectDependiente javascript class optimized for doctrine objects.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Sergio Flores <sercba@gmail.com>
 */
class sfWidgetFormDoctrineDependentSelect extends sfWidgetFormObjectDependentSelect
{
   /**
    * Constructor.
    *
    * @param array $options     An array of options
    * @param array $attributes  An array of default HTML attributes
    *
    * @see sfWidgetForm
    */
    public function __construct($options = array(), $attributes = array())
    {
        $options['source_class'] = 'sfDependentSelectDoctrineSource';

        parent::__construct($options, $attributes);
    }

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
        $this->addOption('table_method', '');
        
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
        $this->setSourceParam('table_method', $this->getOption('table_method'));
        
        return parent::render($name, $value, $attributes, $errors);
    }
}
