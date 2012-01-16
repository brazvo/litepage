<?php
/**
 * Module Autoalias
 * created by Branislav Zvolenský
 * 27.11.2010
 *
 * class: module controller
 */
class Autoalias implements IContentModule
{
    
    function __construct()
    {
        include_once(dirname(__FILE__) . '/models/AutoaliasModel.php');
        if(@is_file(dirname(__FILE__) . '/languages/' . Application::$language['mach_name'] . '.inc'))
            include_once(dirname(__FILE__) . '/languages/' . Application::$language['mach_name'] . '.inc');
        else
            include_once(dirname(__FILE__) . '/languages/english.inc');
    }
    
    function handleShow($id)
    {
        
    }
    
    function handleEdit($id, $values)
    {
        $allowed = Application::getPermisionForAction(Application::$logged['role'], 'autoalias', 'edit');
        
        if($allowed) {
            
            // backward compatibility
            if(!empty ($values) ) {
                $this->handleSave($id, $values);                                        
            }
            
            $obj = new AutoaliasModel();
            $row = $obj->find($id);
            if($row) {
                return $this->createFormInputs($row);
            }
            else {
                return $this->createFormInputs();
            }
        }
        else {
            return '';
        }
    }
    
    function handleSave($id, $values)
    {
        $obj = new AutoaliasModel();
        
        $result = $obj->save($id, $values);
    }
    
    function handleAdd($id, $values)
    {
        if(!Application::getPermisionForAction(Application::$logged['role'], 'autoalias', 'add')) {
            return '';
        }
		// backward compatibility
		if(!empty ($values) ) {
			$this->handleSaveNew($id, $values);                                        
		}
        return $this->createFormInputs();
    }
    
    function handleSaveNew($newid, $values)
    {
        $obj = new AutoaliasModel();
        
        $result = $obj->saveNew($newid, $values);
    }
    
    function handleDelete($id)
    {
        
    }
    
    private function createFormInputs($values = null)
    {
        $autoalias = Html::elem('input')->id('formFieldAutoalias')->setClass('frm-checkbox');
        $autoalias->type = 'checkbox';
        $autoalias->name = 'autoalias_on';
        $autoalias->value = 1;
        $checked = $values['autoalias_on'] ? 'checked' : null;
        if($checked) $autoalias->checked = $checked;
/*		
		$pathalias = Html::elem('input')->id('formFieldPathAlias')->setClass('frm-text');
		$pathalias->type = 'text';
		$pathalias->name = 'path_alias';
		$pathalias->value = $values['path_alias'];
*/		
        $kwLabel = Html::elem('span')->setClass('frm-label')->setCont(Html::elem('label')->setCont(AUTOALIAS_LABEL));
        $kwDesc = Html::elem('div')->setClass('frm-item-description')->setCont(AUTOALIAS_DESC);
        $kwItem = Html::elem('div')->setClass('frm-control')->setCont($kwDesc . $autoalias . $kwLabel);
        $formInput = Html::elem('div')->setClass('form-element')->setCont($kwItem);
        
        $legend = Html::elem('legend')->setCont(AUTOALIAS_SETTINGS)->setClass('autoalias_fieldset_legend');
        
        $fieldset = Html::elem('fieldset')->setClass('autoalias_fieldset')->setCont($legend . $formInput);
        
        $script = "<script type=\"text/javascript\">
/* <![CDATA[ */
jQuery(document).ready(function($){

if( $('#formFieldAutoalias').is(':checked') ) {
    $('input[name=\"path_alias\"]').attr('disabled', 'disabled');
}
else {
    $('input[name=\"path_alias\"]').removeAttr('disabled');
}

$('#formFieldAutoalias').click(
    function(){
        if( $(this).is(':checked') ) {
            $('input[name=\"path_alias\"]').attr('disabled', 'disabled');
        }
        else {
            $('input[name=\"path_alias\"]').removeAttr('disabled');
        }
    }
);
});
$('#formFieldAutoalias').click(
    function(){
        if( $(this).is(':checked') ) {
            $('input[name=\"path_alias\"]').attr('disabled', 'disabled');
        }
        else {
            $('input[name=\"path_alias\"]').removeAttr('disabled');
        }
    }
);
/* ]]> */            
</script>";
        
        return $fieldset . $script;
    }
}
