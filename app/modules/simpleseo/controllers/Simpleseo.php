<?php
/**
 * Module Simple SEO
 * created by Branislav Zvolenský
 * 27.11.2010
 *
 * class: module controller
 */
class Simpleseo implements IContentModule
{
    
    function __construct()
    {
        include(dirname(__FILE__) . '/../models/SimpleseoModel.php');
        if(@is_file(dirname(__FILE__) . '/../languages/' . Application::$language['mach_name'] . '.inc'))
            include(dirname(__FILE__) . '/../languages/' . Application::$language['mach_name'] . '.inc');
        else
            include(dirname(__FILE__) . '/../languages/english.inc');
    }
    
    function handleShow($id)
    {
        $obj = new SimpleseoModel();
        
        $row = $obj->find($id);
        
        if($row) {
            if(trim($row['keywords']) != ''){
                $found = false;
                foreach(Application::$metas as $idx => $meta) {
                    if(isset($meta['name']) && $meta['name'] == 'keywords') {
                        Application::$metas[$idx]['content'] = $row['keywords'];
                        $found = true;
                    }
                }
                if(!$found) Application::$metas[] = array('name'=>'keywords', 'content'=>$row['keywords']);
            }
            
            if(trim($row['description']) != ''){
                $found = false;
                foreach(Application::$metas as $idx => $meta) {
                    if(isset($meta['name']) && $meta['name'] == 'description') {
                        Application::$metas[$idx]['content'] = $row['description'];
                        $found = true;
                    }
                }
                if(!$found) Application::$metas[] = array('name'=>'description', 'content'=>$row['description']);
            }
            
            if(trim($row['robots']) != ''){
                $found = false;
                foreach(Application::$metas as $idx => $meta) {
                    if(isset($meta['name']) && $meta['name'] == 'robots') {
                        Application::$metas[$idx]['content'] = $row['robots'];
                        $found = true;
                    }
                }
                if(!$found) Application::$metas[] = array('name'=>'robots', 'content'=>$row['robots']);
            }
        }
    }
    
    function handleEdit($id)
    {
        $obj = new SimpleseoModel();
        
        $row = $obj->find($id);
        
        if($row) {
            return $this->createFormInputs($row);
        }
        else {
            return $this->createFormInputs();
        }
    }
    
    function handleSave($id, $values)
    {
        $obj = new SimpleseoModel();
        
        $result = $obj->save($id, $values);
    }
    
    function handleAdd()
    {
        return $this->createFormInputs();
    }
    
    function handleSaveNew($id, $values)
    {
        $obj = new SimpleseoModel();
        
        $result = $obj->saveNew($id, $values);
    }
    
    function handleDelete($id)
    {
        //$obj = new SimpleseoModel();
        
        //$result = $obj->delete($id);
    }
    
    private function createFormInputs($values = null)
    {
        $keywords = Html::elem('input')->id('formFieldSimpleseoKeywords')->setClass('frm-text')->style('width: 100%');
        $keywords->type = 'text';
        $keywords->name = 'simpleseo_keywords';
        $keywords->value = ($_POST ? $_POST['simpleseo_keywords'] : ($values ? $values['keywords'] : '') );
        $keywords->size = '256';
        $kwLabel = Html::elem('div')->setClass('frm-label')->setCont(Html::elem('label')->setCont(SIMPLE_SEO_KEYWORDS_LABEL));
        $kwDesc = Html::elem('div')->setClass('frm-item-description')->setCont(SIMPLE_SEO_KEYWORDS_DESC);
        $kwItem = Html::elem('div')->setClass('frm-control')->setCont($kwDesc . $keywords);
        $formInputs[] = Html::elem('div')->setClass('form-element')->setCont($kwLabel . $kwItem);
        
        $description = Html::elem('input')->id('formFieldSimpleseoDescription')->setClass('frm-text')->style('width: 100%');
        $description->type = 'text';
        $description->name = 'simpleseo_description';
        $description->value = ($_POST ? $_POST['simpleseo_description'] : ($values ? $values['description'] : '') );
        $description->size = '256';
        $desLabel = Html::elem('div')->setClass('frm-label')->setCont(Html::elem('label')->setCont(SIMPLE_SEO_DESCRIPTION_LABEL));
        $desDesc = Html::elem('div')->setClass('frm-item-description')->setCont(SIMPLE_SEO_DESCRIPTION_DESC);
        $desItem = Html::elem('div')->setClass('frm-control')->setCont($desDesc . $description);
        $formInputs[] = Html::elem('div')->setClass('form-element')->setCont($desLabel . $desItem);
        
        $robots = Html::elem('input')->id('formFieldSimpleseoRobots')->setClass('frm-text')->style('width: 100%');
        $robots->type = 'text';
        $robots->name = 'simpleseo_robots';
        $robots->value = ($_POST ? $_POST['simpleseo_robots'] : ($values ? $values['robots'] : '') );
        $robots->size = '128';
        $robLabel = Html::elem('div')->setClass('frm-label')->setCont(Html::elem('label')->setCont(SIMPLE_SEO_ROBOTS_LABEL));
        $robDesc = Html::elem('div')->setClass('frm-item-description')->setCont(SIMPLE_SEO_ROBOTS_DESC);
        $robItem = Html::elem('div')->setClass('frm-control')->setCont($robDesc . $robots);
        $formInputs[] = Html::elem('div')->setClass('form-element')->setCont($robLabel . $robItem);
        
        $frmElements = '';
        foreach($formInputs as $formInput) {
            $frmElements .= $formInput;
        }
        
        $legend = Html::elem('legend')->setCont(SIMPLE_SEO_SETTINGS)->setClass('simleseo_fieldset_legend');
        
        $fieldset = Html::elem('fieldset')->setClass('simleseo_fieldset')->setCont($legend . $frmElements);
        
        return $fieldset;
    }
}
