<?php
/*
 * This file is part of the sfDependentSelect package.
 * (c) 2010 Sergio Flores <sercba@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfActionsDependentSelect for remote calls
 *
 * @package    symfony
 * @subpackage action
 * @author     Sergio Flores <sercba@gmail.com>
 */
abstract class sfActionsDependentSelect extends sfActions
{
    /**
     * Executes _ajax action
     *
     * @param sfRequest $request A request object
     */
    public function execute_ajax(sfWebRequest $request)
    {
        $sourceClass = $request->getParameter('_ds_source_class');
        $source = new $sourceClass($request->getParameter('_ds_source_params'));

        if ('true' == $request->getParameter('_ds_get_ref_value')) {
        
            if (method_exists($this, sprintf('getRefValueFor%s', $request->getParameter('_ds_id')))) {
                $data = $this->$method($request, $source);
            } else {
                $data = $source->getRefValue($request->getParameter('_ds_ref'));
            }
            
        } else {
        
            if (method_exists($this, sprintf('getValuesFor%s', $request->getParameter('_ds_id')))) {
                $data = $this->$method($request, $source);
            } else {
                $data = $source->getValues($request->getParameter('_ds_ref'));
            }
        }
        
        return $this->renderText(json_encode($data));
    }
}
