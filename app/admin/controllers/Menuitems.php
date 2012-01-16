<?php
/**
 * Project Lite Page
 * System controller: Menu items
 * file: Menuitems.php
 *
 * 
 */
class Menuitems extends BaseAdmin
{

  // Properties
  
  // Constructor
  public function __construct()
  {
    $this->perm_mach_name = 'menu_items';
    parent::__construct();

  
  }
  
  /******************************************* ACTIONS */
  function actionOrderitems($id)
  {
  
    $obj = new AdmMenusModel;
	
	$result = $obj->orderItems($_POST);
	
	if($result){
	  redirect('admin/menuitems/edititems/'.$id, 'Zoradenie bolo uložené.');
	}
	else{
	  $this->template->setView('edititems');
	}
  
  }
  
  function actionSave($id)
  {
    if(Form::$isvalid){
		$obj = new AdmMenusModel;
		
		$result = $obj->saveMenuItem($_POST, $id);
		
		if($result){
		  redirect('admin/menuitems/edititems/'.$_POST['menu_id'], 'Položka bola uložená.');
		}
		else{
		  Application::$id = $_POST['menu_id'];
		  Application::setError('Uloženie sa nepodarilo');
		  $this->template->setView('edititems');
		}
	}
	else{
		Application::$id = $id;
		$this->template->setView('edit');
	}
  }
  
  function actionSaveNew()
  {
    if(Form::$isvalid){
		$obj = new AdmMenusModel;
		
		$result = $obj->saveNewMenuItem($_POST);
		
		if($result){
		  redirect('admin/menuitems/edititems/'.$_POST['id'], 'Položka bola uložená.');
		}
		else{
		  Application::$id = $_POST['id'];
		  Application::setError('Uloženie sa nepodarilo');
		  $this->template->setView('edititems');
		}
	}
	else{
		Application::$id = $_POST['id'];
		$this->template->setView('add');
	}
  }
  
  function actionConfirmDelete()
  {
    if(!Application::$delete) redirect('error/show/1');
	$id = $_POST['id'];
	$menu_id = $_POST['menu_id'];
	$child_of = $_POST['child_of'];
    
	$obj = new AdmMenusModel;
	
	$result = $obj->deleteMenuItem($id, $child_of);
	
	if($result){
	  redirect('admin/menuitems/edititems/'.$menu_id, 'Položka bola vymazaná.');
	}
	else{
	  Application::$id = $menu_id;
	  Application::setError('Vymazanie sa nepodarilo');
	  $this->template->setView('edititems');
	}
  }
  
  function actionGet($mach_name)
  {
    $obj = new AdmMenusModel;
	
	Application::$id = $obj->getMenuId($mach_name);
	
	$this->template->setView('edititems');
  
  }
  
  /******************************************* RENDERERS */
  function renderDefault()
  {
	
	
  
  }
  
  function renderEdititems($id)
  {
    if(!Application::$view) redirect('error/show/1');
	$this->template->title = MENUITEMS_ADMIN_TITLE;
	$this->template->menu_id = $id;
	$this->template->form = $this->createListItemForm($id);
  }
  
  function renderEdit($id)
  {
    if(!Application::$edit) redirect('error/show/1');
	$this->template->title = MENUITEMS_ADMIN_EDIT_TITLE;
	$this->template->form = $this->createEditItemForm($id);
  }
  
  function renderAdd($id)
  {
    if(!Application::$add) redirect('error/show/1');
	$this->template->setView('edit');
	$this->template->title = MENUITEMS_ADMIN_ADD_TITLE;
	$this->template->form = $this->createAddItemForm($id);
  }
  
  function renderDelete($id)
  {
    if(!Application::$delete) redirect('error/show/1');
	$this->template->setView('edit');
	$this->template->title = MENUITEMS_ADMIN_DELETE_TITLE;
	$this->template->form = $this->createDeleteItemForm($id);
  }
  
  /********************************************* FACTORIES */
    
  private function createEditItemForm($id)
  {
  
    $obj = new AdmMenusModel;
	
	$values = $obj->findItem($id);
	$menu_id = $values['menu_id'];
	$menus = $obj->getMenuStructure($menu_id);
	
	if($_POST){
	  $values = $_POST;
	  $values['expanded'] = (isset($_POST['expanded']) ? $_POST['expanded'] : 0);
	  $values['allowed'] = (isset($_POST['allowed']) ? $_POST['allowed'] : 0);
	}
	
	foreach($values as $key => $val){
	  $$key = $val;
	}
	
	$prior_arr = $this->createPriorityArray();
	$sel_arr = array(0 => 'Hlavná položka');
	$sel_arr += $this->createSelectArray($menus);
	
	$form = new Form('edit-menu-item', 'frm-menu-item', Application::link('admin/menuitems/save/'.$id) );
	
    $form->addHidden('menu_id', $menu_id);
	$form->addText('title', 'Názov položky', $title);
		$form->addRule('title', Form::FILLED, 'Zadajte názov položky');
	$form->addText('name', 'Meno položky', $name);
		$form->addDescription('name', 'Meno položky je vhodné zadať ak si potrebuje v scripte titulok podstránky natiahnuť z tohoto políčka. Titulok v menu može byť kratší a titulok na stránke môže byť dlhší. Ak necháte prázdne, automaticky sa vyplní obsahom políčka Názov položky.');
	$form->addText('path', 'Cesta k podstránke', $path);
		$form->addDescription('path', 'Ak ešte nemáte cestu môžete zadať &lt;front>, &lt;none>, &lt;basepath> alebo URL adresu');
		$form->addRule('path', Form::FILLED, 'Zadajte cestu k podstránke');
		$form->addRule('path', Form::REGEX, "/^[http:\/\/]*[\w+\.]*[a-z0-9\<\>\.\/_-]+$/i", 'Cesta k podstránke: Povolené sú písmená číslice a znaky - _ < >, alebo URL adresy');
	$form->addCheckbox('allowed', 1, 'Zobraziť', '', $allowed, true);
	$form->addCheckbox('expanded', 1, 'Rozbaliť', '', $expanded);
	$form->addSelect('child_of', $sel_arr, 'Nadradená položka', $child_of);
	$form->addSelect('priority', $prior_arr, 'Priorita', $priority);
	$form->emptyLine();
	$form->addSubmit('save', 'Uložiť');
	
	return $form->render();
  
  }
  
  private function createDeleteItemForm($id)
  {
  
    $obj = new AdmMenusModel;
	
	$values = $obj->findItem($id);
	$menu_id = $values['menu_id'];
	$child_of = $values['child_of'];
	$menus = $obj->getMenuStructure($menu_id);
	
	$form = new Form('delete-menu-item', 'frm-menu-item', Application::link('admin/menuitems/confirmDelete/'));
	$form->insertContent('Chystáte sa vymazať položku menu. Ak ste si istý, stlačte tlačidlo Potvrdiť.');
	
    $form->addHidden('id', $id);
	$form->addHidden('menu_id', $menu_id);
	$form->addHidden('child_of', $child_of);
	$form->emptyLine();
	$form->addSubmit('confirm', 'Potvrdiť');
	
	return $form->render();
  
  }
  
  private function createAddItemForm($id)
  {
  
    $obj = new AdmMenusModel;
	
	$title =''; $name=''; $path=''; $allowed=1; $expanded=0; $child_of=0; $priority=0;
	$menu_id = $id;
	$menus = $obj->getMenuStructure($menu_id);
	
	if($_POST){
	  $values = $_POST;
	  foreach($values as $key => $val){
	    $$key = $val;
	  }
	}
	
	$prior_arr = $this->createPriorityArray();
	$sel_arr = array(0 => 'Hlavná položka');
	if($menus){
	  $sel_arr += $this->createSelectArray($menus);
	}
	
	$form = new Form('edit-menu-item', 'frm-menu-item', Application::link('admin/menuitems/saveNew/'));
	
    $form->addHidden('id',$id);
	$form->addHidden('menu_id', $menu_id);
	$form->addText('title', 'Názov položky', $title);
		$form->addRule('title', Form::FILLED, 'Zadajte názov položky');
	$form->addText('name', 'Meno položky', $name);
		$form->addDescription('name', 'Meno položky je vhodné zadať ak si potrebuje v scripte titulok podstránky natiahnuť z tohoto políčka. Titulok v menu može byť kratší a titulok na stránke môže byť dlhší. Ak necháte prázdne, automaticky sa vyplní obsahom políčka Názov položky.');
	$form->addText('path', 'Cesta k podstránke', $path);
		$form->addRule('path', Form::FILLED, 'Zadajte cestu k podstránke');
		$form->addRule('path', Form::REGEX, "/^[http:\/\/]*[\w+\.]*[a-z0-9\<\>\.\/_-]+$/i", 'Cesta k podstránke: Povolené sú písmená číslice a znaky - _ < >, alebo celé url adresy');
		$form->addDescription('path', 'Ak potrebujete, aby odkaz smeroval na uvodnú stránku zadajte <b>&lt;front></b>. Ak z nejakého dôvodu nechcete zadať cestu, pretože ju napríklad chcete zadať neskôr, zadajte <b>&lt;none></b>. Ak potrebujete zadať hlavnú adresu tak použite <b>&lt;basepath></b>');
	$form->addCheckbox('allowed', 1, 'Zobraziť', '', $allowed, true);
	$form->addCheckbox('expanded', 1, 'Rozbaliť', '', $expanded);
	$form->addSelect('child_of', $sel_arr, 'Nadradená položka', $child_of);
	$form->addSelect('priority', $prior_arr, 'Priorita', $priority);
	$form->emptyLine();
	$form->addSubmit('save', 'Uložiť');
	
	return $form->render();
  
  }
  
  private function createListItemForm($id)
  {
    
	$obj = new AdmMenusModel;
	
	$menu = $obj->find($id);
	
	$select_array = array(0 => '&lt Hlavná položka >');
	
	$rows = $obj->getMenuStructure($id);
	
	if($rows){	
	  $select_array += $this->createSelectArray($rows);
	  $tbl_array = $this->createTableArray($rows);
	  $pr_arr = $this->createPriorityArray();
	}
	else{
	  $tbl_array = false;
	}
	
	if($tbl_array){
		$form = new Form('menu-items-order', 'frm-menu-tems', Application::link('admin/menuitems/orderitems/'.$id) );
		$ids = '';
		foreach($tbl_array as $row){
		  $ids .= $row['id'].';';
		  $form->addSelect('priority__'.$row['id'], $pr_arr, '',$row['priority']);
		  $form->addCheckbox('allowed__'.$row['id'], 1, '', null, $row['allowed']);
		  $form->addCheckbox('expanded__'.$row['id'], 1, '', null, $row['expanded']);
		  $form->addSelect('child_of__'.$row['id'], $select_array, '', $row['child_of']);
		}
		$form->addHidden('ids', $ids);
		$form->addSubmit('save', 'Uložiť');
		
		$output = $form->start();
		$output .= $form->renderSingle('ids');
		$output .= '<table class="admin-table adm-menu-itmes" cellspacing="0">';
		$output .= '
					 <thead>
					 <tr>
					   <td>'.TITLE.'</td>
					   <td>'.PRIORITY.'</td>
					   <td>'.SHOW.'</td>
					   <td>'.EXPAND.'</td>
					   <td>'.PARENT_ITEM.'</td>
					   <td colspan="2" style="text-align:center">'.ACTIONS.'</td>
					 </tr>
					 </thead>
					 <tbody>
		  ';

		foreach($tbl_array as $row){
			
			$output .= '<tr style="background-color:#E9E9E9">';
			$output .= '<td>'.$row['padding'].$row['title'].'</td>';
			$output .= '<td>'.$form->renderSingle('priority__'.$row['id']).'</td>';
			$output .= '<td>'.$form->renderSingle('allowed__'.$row['id']).'</td>';
			$output .= '<td>'.$form->renderSingle('expanded__'.$row['id']).'</td>';
			$output .= '<td>'.$form->renderSingle('child_of__'.$row['id']).'</td>';
			if(Application::$edit){
			  $output .= '<td><a class="edit" href="'.Application::link('admin/menuitems/edit/'.$row['id']).'"><img src="'.BASEPATH.'/images/icon-edit.jpg" alt="Úprava položky menu" title="Úprava položky menu" /></a></td>';
			}
			else{
			  $output .= '<td><span style="color:silver"><img src="'.BASEPATH.'/images/icon-edit-gr.jpg" alt="Úprava položky menu" title="Úprava položky menu" /></span></td>';
			}
			if(Application::$delete){
			  $output .= '<td><a href="'.Application::link('admin/menuitems/delete/'.$row['id']).'"><img src="'.BASEPATH.'/images/icon-delete.jpg" alt="Odstrániť" title="Odstrániť" /></a></td>';
			}
			else{
			  $output .= '<td><span style="color:silver"><img src="'.BASEPATH.'/images/icon-delete-gr.jpg" alt="Odstrániť" title="Odstrániť" /></span></td>';
			}
			$output .= '</tr>';
		}
		  
		$output .= '</tbody>
		           </table><br />';
		if(Application::$edit){
		  $output .= $form->renderSingle('save');
		}
		$output .= $form->end();
		  
		return $output;
	}
	else{
		return 'Pridajte položky do menu.';
	}
  
  }
    
  private function createSelectArray($values, $subline='')
  {
    $subline = $subline.'--'; 
    foreach($values as $val){
	  $out[$val['row']['id']] = '< '.$subline.' '.$val['row']['title'].' >';
	  if(isset($val['submenu'])) $out += $this->createSelectArray($val['submenu'], $subline);
	}
	
	return $out;
  
  }
  
  private function createTableArray($values, $padding='')
  {
  
    $padding = $padding.'+ ';
	foreach($values as $val){
	  $val['row']['padding'] = $padding;
	  $out[$val['row']['id']] = $val['row'];
	  if(isset($val['submenu'])) $out += $this->createTableArray($val['submenu'], $padding);
	}
	
	return $out;
  
  }
  
  private function createPriorityArray()
  {
  
    for($i=-10; $i<=30; $i++){
	  $out[$i] = $i;
	}
		
	return $out;
  
  }

}