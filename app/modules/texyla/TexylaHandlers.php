<?php
/**
 * Module Texyla
 * created by Branislav Zvolenský
 * 27.11.2010
 *
 * class: module controller
 */
class TexylaHandlers
{
    
    private $controller;
    
    private $template;
    
    function __construct()
    {

        include_once(dirname(__FILE__) . '/models/TexylaModel.php');
        if(@is_file(dirname(__FILE__) . '/languages/' . Application::$language['mach_name'] . '.inc'))
            include_once(dirname(__FILE__) . '/languages/' . Application::$language['mach_name'] . '.inc');
        else
            include_once(dirname(__FILE__) . '/languages/english.inc');
    }
    
    public function startUp($oController, $id)
    {
        $this->controller = $oController;
        $this->template = $oController->getTemplate();
    }
    
    function handleShow()
    {
        
    }
    
    function handleEdit()
    {
        $return['header'] = $this->generateHeaderHtml();
       
        return $return;
    }
    
    function handleAdd()
    {
        $return['header'] = $this->generateHeaderHtml();
       
        return $return;
    }
    
    private function generateHeaderHtml()
    {
        
        $textareas = db::fetchAll("SELECT * FROM texyla");

        $role = Application::$logged['role'];

        $lang = Application::$language['code'];
        
        $sHtml = '';
        
        if( $textareas ) {
        
            $this->template->CSS->add('app/modules/texyla/css/jush.css');
            $this->template->CSS->add('app/modules/texyla/css/style.css');

            $this->template->JS->add('app/modules/texyla/js/1_jush.js');
            $this->template->JS->add('app/modules/texyla/js/2_texyla.js');
            $this->template->JS->add('app/modules/texyla/js/ajaxupload.js');
            $this->template->JS->add('app/modules/texyla/js/buttons.js');
            $this->template->JS->add('app/modules/texyla/js/dom.js');
            $this->template->JS->add('app/modules/texyla/js/selection.js');
            $this->template->JS->add('app/modules/texyla/js/texy.js');
            $this->template->JS->add('app/modules/texyla/js/view.js');

            if(@is_file(MOD_DIR."/texyla/languages/{$lang}.js")) {
                $this->template->JS->add("app/modules/texyla/languages/{$lang}.js");
            }
            else {
                $this->template->JS->add("app/modules/texyla/languages/sk.js");
            }
            
            $this->template->JS->add("app/modules/texyla/plugins/keys/keys.js");
            $this->template->JS->add("app/modules/texyla/plugins/window/window.js");
            $this->template->JS->add("app/modules/texyla/plugins/resizableTextarea/resizableTextarea.js");
            $this->template->JS->add("app/modules/texyla/plugins/img/img.js");
            $this->template->JS->add("app/modules/texyla/plugins/table/table.js");
            $this->template->JS->add("app/modules/texyla/plugins/link/link.js");
            $this->template->JS->add("app/modules/texyla/plugins/emoticon/emoticon.js");
            $this->template->JS->add("app/modules/texyla/plugins/symbol/symbol.js");
            $this->template->JS->add("app/modules/texyla/plugins/files/files.js");
            $this->template->JS->add("app/modules/texyla/plugins/color/color.js");
            $this->template->JS->add("app/modules/texyla/plugins/textTransform/textTransform.js");
            $this->template->JS->add("app/modules/texyla/plugins/youtube/youtube.js");
            
            ob_start();
            include dirname(__FILE__). '/script.inc';
            $sHtml = ob_get_clean();
        }

        return $sHtml;

    }
}
