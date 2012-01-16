<?php
/**
 * Project Lite Page
 * System controller: Permisions
 * file: Permisions.php
 *
 * 
 */
class Permisions extends BaseAdmin
{

  // Properties
  
  // Constructor
  public function __construct()
  {
  
    parent::__construct();

  
  }
  
  /******************************************* ACTIONS */
  function actionSave()
  {
  
    $obj = new AdmPermisionsModel;
	$result = $obj->save($_POST);
	if($result){
	  redirect('admin/permisions', 'Oprávnenia boli uložené');
	}
	else{
	  Application::setError('Oprávnenia sa nepodarilo uložiť');
	  $this->template->setView('default');
	}
  
  }
  
    
  /******************************************* RENDERERS */
  function renderDefault()
  {

        $this->template->title = PERMISSIONS_ADMIN_TITLE;
	if(Application::$logged['role'] == 'admin'){
	  $this->template->content = $this['listForm'];
	}
	else{
	  $this->template->content = PERMISSIONS_ADMIN_TITLE_NO_PERMISSIONS;
	}
  
  }
  
  /********************************************* FACTORIES */
  public function createControlListForm()
  {
  
    $obj = new AdmPermisionsModel;
	
    $rows = $obj->findAll();
	
	$form = new Form('permision-list', 'frm-permisions', BASEPATH.'/admin/permisions/save');
	
	foreach($rows as $row){
	  $form->addCheckbox($row['id'].'__editor_view', 1, '', '', $row['editor_view'], true);
	  $form->addCheckbox($row['id'].'__editor_add', 1, '', '', $row['editor_add'], true);
	  $form->addCheckbox($row['id'].'__editor_edit', 1, '', '', $row['editor_edit'], true);
	  $form->addCheckbox($row['id'].'__editor_delete', 1, '', '', $row['editor_delete'], true);
	  $form->addCheckbox($row['id'].'__user_view', 1, '', '', $row['user_view'], true);
	  $form->addCheckbox($row['id'].'__user_add', 1, '', '', $row['user_add'], true);
	  $form->addCheckbox($row['id'].'__user_edit', 1, '', '', $row['user_edit'], true);
	  $form->addCheckbox($row['id'].'__user_delete', 1, '', '', $row['user_delete'], true);
	  $form->addCheckbox($row['id'].'__visitor_view', 1, '', '', $row['visitor_view'], true);
	  $form->addCheckbox($row['id'].'__visitor_add', 1, '', '', $row['visitor_add'], true);
	  $form->addCheckbox($row['id'].'__visitor_edit', 1, '', '', $row['visitor_edit'], true);
	  $form->addCheckbox($row['id'].'__visitor_delete', 1, '', '', $row['visitor_delete'], true);
	}
	
	$form->addSubmit('save', 'Uložiť');
	
	$output = $form->start();
	
	
	
	$output .= '<table class="admin-table" cellspacing="0" style="width:100%">';
	$output .= '<thead>
				<tr>
	              <td rowspan="2">Oprávnenia pre:</td>
				  <td colspan="4" style="text-align:center">Editor</td>
				  <td colspan="4" style="text-align:center">Užívateľ</td>
				  <td colspan="4" style="text-align:center">Návštevník</td>
				</tr>
				<tr>
	              <td style="height:40px;width:40px;padding:0"><img src="'.BASEPATH.'/images/icon-view.jpg" alt="Prezeranie" title="Prezeranie" /></td>
				  <td style="height:40px;width:40px;padding:0"><img src="'.BASEPATH.'/images/icon-add.jpg" alt="Pridávanie" title="Pridávanie" /></td>
				  <td style="height:40px;width:40px;padding:0"><img src="'.BASEPATH.'/images/icon-edit.jpg" alt="Úprava title="Úprava"/></td>
				  <td style="height:40px;width:40px;padding:0"><img src="'.BASEPATH.'/images/icon-delete.jpg" alt="Výmaz" title="Výmaz" /></td>
				  <td style="height:40px;width:40px;padding:0"><img src="'.BASEPATH.'/images/icon-view.jpg" alt="Prezeranie" title="Prezeranie" /></td>
				  <td style="height:40px;width:40px;padding:0"><img src="'.BASEPATH.'/images/icon-add.jpg" alt="Pridávanie" title="Pridávanie" /></td>
				  <td style="height:40px;width:40px;padding:0"><img src="'.BASEPATH.'/images/icon-edit.jpg" alt="Úprava" title="Úprava" /></td>
				  <td style="height:40px;width:40px;padding:0"><img src="'.BASEPATH.'/images/icon-delete.jpg" alt="Výmaz" title="Výmaz"/></td>
				  <td style="height:40px;width:40px;padding:0"><img src="'.BASEPATH.'/images/icon-view.jpg" alt="Prezeranie" title="Prezeranie" /></td>
				  <td style="height:40px;width:40px;padding:0"><img src="'.BASEPATH.'/images/icon-add.jpg" alt="Pridávanie" title="Pridávanie" /></td>
				  <td style="height:40px;width:40px;padding:0"><img src="'.BASEPATH.'/images/icon-edit.jpg" alt="Úprava" title="Úprava" /></td>
				  <td style="height:40px;width:40px;padding:0"><img src="'.BASEPATH.'/images/icon-delete.jpg" alt="Výmaz" title="Výmaz" /></td>
				</tr>
				</thead>';
	$output .= '<tbody>';
	foreach($rows as $row){
	  $output .= '<tr>
	               <td>'.$row['content'].'</td>
				   <td>'.$form->renderSingle($row['id'].'__editor_view').'</td>
				   <td>'.$form->renderSingle($row['id'].'__editor_add').'</td>
				   <td>'.$form->renderSingle($row['id'].'__editor_edit').'</td>
				   <td>'.$form->renderSingle($row['id'].'__editor_delete').'</td>
				   <td>'.$form->renderSingle($row['id'].'__user_view').'</td>
				   <td>'.$form->renderSingle($row['id'].'__user_add').'</td>
				   <td>'.$form->renderSingle($row['id'].'__user_edit').'</td>
				   <td>'.$form->renderSingle($row['id'].'__user_delete').'</td>
				   <td>'.$form->renderSingle($row['id'].'__visitor_view').'</td>
				   <td>'.$form->renderSingle($row['id'].'__visitor_add').'</td>
				   <td>'.$form->renderSingle($row['id'].'__visitor_edit').'</td>
				   <td>'.$form->renderSingle($row['id'].'__visitor_delete').'</td>
				 </tr>';
	}
	$output .= '</tbody>';
	$output .= '</table>
	           <br/>';
	$output .= $form->renderSingle('save');
	$output .= $form->end();
					
	return $output;
  
  }

}