<?php
/*
 * This file is part of the sfDependentSelect package.
 * (c) 2010 Sergio Flores <sercba@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormDependentSelect represents an select widget rendered by
 * SelectDependiente javascript class.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Sergio Flores <sercba@gmail.com>
 */
class sfWidgetFormDependentSelect extends sfWidgetForm
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
        $this->addOption('depends', '');
        $this->addOption('add_empty', true);
        // ajax
        $this->addOption('ajax', false);
        $this->addOption('cache', true);
        $this->addOption('params', array());
        $this->addOption('url', sfContext::getInstance()->getController()->genUrl('sfDependentSelectAuto/_ajax'));
        // source
        $this->addRequiredOption('source_class', '');
        $this->addOption('source_params', array());
        
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
        $group = '';
        $sourceClass = $this->getOption('source_class');
        $source = new $sourceClass($this->getOption('source_params'));
        
        if ($this->getOption('ajax')) {
            $values = array();
        } else {
            $values = $source->getValues();
            if (is_array($values) && ! is_array(current($values))) {
                $values = array('unique' => $values);
                $group = 'unique';
            }
        }
    
        if ($this->getOption('depends')) {
            $this->setOption('depends', $this->generateJavascriptVar($name, $this->getOption('depends')));
        }
    
        $config = array(
            'id'          => $this->generateId($name),
            'opciones'    => $values,
            'vacio'       => true === $this->getOption('add_empty') ? '' : $this->getOption('add_empty'),
            'ajax'        => $this->getOption('ajax'),
            'url'         => $this->getOption('url'),
            'cache'       => $this->getOption('cache'),
            'dependiente' => $this->getOption('depends'),
            'varref'      => '_ds_ref',
            'varsoloref'  => '_ds_get_ref_value',
            'params'      => array_merge($this->getOption('params'), array(
                '_ds_id'            => $this->generateId($name),
                '_ds_source_class'  => $sourceClass,                                    
                '_ds_source_params' => $this->getOption('source_params'),
            )),            
        );

        $jsConfig = json_encode($config);
        $jsVar = $this->generateJavascriptVar($name);
        $jsGroup = '';
        if (strlen($value) && ! $group) {
            $jsGroup = $source->getRefValue($value);
        } elseif ( ! $this->getOption('depends')) {
            $jsGroup = $group;
        }
        if (strlen($jsGroup)) {
            $jsGroup = "'{$jsGroup}'";
        }
        
        $js = "var $jsVar = new SelectDependiente({$jsConfig}).mostrar({$jsGroup})"
            . (strlen($value) ? ".seleccionar('{$value}');" : ';');
        
        $script = $this->renderContentTag('script', $js, array('type' => 'text/javascript'));
        $widget = $this->renderContentTag('select', null, array_merge(array('name' => $name), $attributes));
        
        return $widget . $script;
    }

    protected function generateJavascriptVar($baseName, $var = null)
    {
        if ( ! $var) {
            $jsName = $baseName;
        } else {   
            $base = preg_match('/(.*)\[.*\]$/i', $baseName, $matches) ? $matches[1] : '';
            $jsName = $base . '[' . sfInflector::underscore($var) . ']';
        }
        
        $jsVar = $this->generateId($jsName);

        return $jsVar;
    }
    
    protected function setSourceParam($var, $val = null)
    {
        if ( ! is_array($var)) {
            $var = array($var => $val);
        }
        $this->setOption('source_params', array_merge($this->getOption('source_params'), $var));
    }
    
    public function getJavascripts()
    {
        $jsConfig = sfConfig::get('app_sfDependentSelectPlugin_js', 'minimized');
        
        if ($jsConfig) {
            $jsName = 'SelectDependiente';
            if ('minimized' === $jsConfig) {
                $jsName .= '.min.js';
            }
            return array("/sfDependentSelectPlugin/js/{$jsName}");
        }
        
        return array();
    }
}
