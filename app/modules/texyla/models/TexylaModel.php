<?php
class TexylaModel
{
  // Properties
  
  // Constructor
  public function __construct()
  {
  
  }
  
  // Methods

  public function find($id)
  {
    $row = db::fetch("SELECT * FROM table_name WHERE id=$id");
	return $row;
  }
  
  public function findAll()
  {
    $rows = db::fetchAll("SELECT * FROM texyla ORDER BY textarea");
	return $rows;
  }
  
  
  public function save($post)
  {
    foreach($post as $key => $val){
		$$key = $val;
	}
	    if(!isset($admin_allow)) $admin_allow = 0;
		if(!isset($admin_bottomLeftToolbarEdit)) $admin_bottomLeftToolbarEdit = 0;
		if(!isset($admin_bottomLeftToolbarPreview)) $admin_bottomLeftToolbarPreview = 0;
		if(!isset($admin_bottomLeftToolbarHtmlPreview)) $admin_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($admin_tabs)) $admin_tabs = 0;
		if(!isset($admin_headers)) $admin_headers = 0;
		if(!isset($admin_font_style)) $admin_font_style = 0;
		if(!isset($admin_text_align)) $admin_text_align = 0;
		if(!isset($admin_lists)) $admin_lists = 0;
		if(!isset($admin_link)) $admin_link = 0;
		if(!isset($admin_img)) $admin_img = 0;
		if(!isset($admin_table)) $admin_table = 0;
		if(!isset($admin_emoticon)) $admin_emoticon = 0;
		if(!isset($admin_symbol)) $admin_symbol = 0;
		if(!isset($admin_color)) $admin_color = 0;
		if(!isset($admin_textTransform)) $admin_textTransform = 0;
		if(!isset($admin_blocks)) $admin_blocks = 0;
		if(!isset($admin_codes)) $admin_codes = 0;
		if(!isset($admin_others)) $admin_others = 0;
		
		if(!isset($editor_allow)) $editor_allow = 0;
		if(!isset($editor_bottomLeftToolbarEdit)) $editor_bottomLeftToolbarEdit = 0;
		if(!isset($editor_bottomLeftToolbarPreview)) $editor_bottomLeftToolbarPreview = 0;
		if(!isset($editor_bottomLeftToolbarHtmlPreview)) $editor_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($editor_tabs)) $editor_tabs = 0;
		if(!isset($editor_headers)) $editor_headers = 0;
		if(!isset($editor_font_style)) $editor_font_style = 0;
		if(!isset($editor_text_align)) $editor_text_align = 0;
		if(!isset($editor_lists)) $editor_lists = 0;
		if(!isset($editor_link)) $editor_link = 0;
		if(!isset($editor_img)) $editor_img = 0;
		if(!isset($editor_table)) $editor_table = 0;
		if(!isset($editor_emoticon)) $editor_emoticon = 0;
		if(!isset($editor_symbol)) $editor_symbol = 0;
		if(!isset($editor_color)) $editor_color = 0;
		if(!isset($editor_textTransform)) $editor_textTransform = 0;
		if(!isset($editor_blocks)) $editor_blocks = 0;
		if(!isset($editor_codes)) $editor_codes = 0;
		if(!isset($editor_others)) $editor_others = 0;
		
		if(!isset($visitor_allow)) $visitor_allow = 0;
		if(!isset($visitor_bottomLeftToolbarEdit)) $visitor_bottomLeftToolbarEdit = 0;
		if(!isset($visitor_bottomLeftToolbarPreview)) $visitor_bottomLeftToolbarPreview = 0;
		if(!isset($visitor_bottomLeftToolbarHtmlPreview)) $visitor_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($visitor_tabs)) $visitor_tabs = 0;
		if(!isset($visitor_headers)) $visitor_headers = 0;
		if(!isset($visitor_font_style)) $visitor_font_style = 0;
		if(!isset($visitor_text_align)) $visitor_text_align = 0;
		if(!isset($visitor_lists)) $visitor_lists = 0;
		if(!isset($visitor_link)) $visitor_link = 0;
		if(!isset($visitor_img)) $visitor_img = 0;
		if(!isset($visitor_table)) $visitor_table = 0;
		if(!isset($visitor_emoticon)) $visitor_emoticon = 0;
		if(!isset($visitor_symbol)) $visitor_symbol = 0;
		if(!isset($visitor_color)) $visitor_color = 0;
		if(!isset($visitor_textTransform)) $visitor_textTransform = 0;
		if(!isset($visitor_blocks)) $visitor_blocks = 0;
		if(!isset($visitor_codes)) $visitor_codes = 0;
		if(!isset($visitor_others)) $visitor_others = 0;
		
		if(!isset($user_allow)) $user_allow = 0;
		if(!isset($user_bottomLeftToolbarEdit)) $user_bottomLeftToolbarEdit = 0;
		if(!isset($user_bottomLeftToolbarPreview)) $user_bottomLeftToolbarPreview = 0;
		if(!isset($user_bottomLeftToolbarHtmlPreview)) $user_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($user_tabs)) $user_tabs = 0;
		if(!isset($user_headers)) $user_headers = 0;
		if(!isset($user_font_style)) $user_font_style = 0;
		if(!isset($user_text_align)) $user_text_align = 0;
		if(!isset($user_lists)) $user_lists = 0;
		if(!isset($user_link)) $user_link = 0;
		if(!isset($user_img)) $user_img = 0;
		if(!isset($user_table)) $user_table = 0;
		if(!isset($user_emoticon)) $user_emoticon = 0;
		if(!isset($user_symbol)) $user_symbol = 0;
		if(!isset($user_color)) $user_color = 0;
		if(!isset($user_textTransform)) $user_textTransform = 0;
		if(!isset($user_blocks)) $user_blocks = 0;
		if(!isset($user_codes)) $user_codes = 0;
		if(!isset($user_others)) $user_others = 0;
		
	// Get new ID
	$result0 = db::exec("UPDATE texyla SET `textarea`='$textarea', `description`='$description' WHERE id='$id'");
	$result1 = db::exec("UPDATE texyla_settings SET `texyCfg`='$admin_texyCfg', `allow`='$admin_allow', `bottomLeftToolbarEdit`='$admin_bottomLeftToolbarEdit',
						`bottomLeftToolbarPreview`='$admin_bottomLeftToolbarPreview', `bottomLeftToolbarHtmlPreview`='$admin_bottomLeftToolbarHtmlPreview',
						`buttonType`='$admin_buttonType', `tabs`='$admin_tabs', `headers`='$admin_headers', `font_style`='$admin_font_style',
						`text_align`='$admin_text_align', `lists`='$admin_lists', `link`='$admin_link', `img`='$admin_img', `table`='$admin_table',
						`emoticon`='$admin_emoticon', `symbol`='$admin_symbol', `color`='$admin_color', `textTransform`='$admin_textTransform',
						`blocks`='$admin_blocks', `codes`='$admin_codes', `others`='$admin_others' WHERE `role`='admin' AND `texyla_id`='$id'");
	$result2 = db::exec("UPDATE texyla_settings SET `texyCfg`='$editor_texyCfg', `allow`='$editor_allow', `bottomLeftToolbarEdit`='$editor_bottomLeftToolbarEdit',
						`bottomLeftToolbarPreview`='$editor_bottomLeftToolbarPreview', `bottomLeftToolbarHtmlPreview`='$editor_bottomLeftToolbarHtmlPreview',
						`buttonType`='$editor_buttonType', `tabs`='$editor_tabs', `headers`='$editor_headers', `font_style`='$editor_font_style',
						`text_align`='$editor_text_align', `lists`='$editor_lists', `link`='$editor_link', `img`='$editor_img', `table`='$editor_table',
						`emoticon`='$editor_emoticon', `symbol`='$editor_symbol', `color`='$editor_color', `textTransform`='$editor_textTransform',
						`blocks`='$editor_blocks', `codes`='$editor_codes', `others`='$editor_others' WHERE `role`='editor' AND `texyla_id` = '$id'");
	$result3 = db::exec("UPDATE texyla_settings SET `texyCfg`='$user_texyCfg', `allow`='$user_allow', `bottomLeftToolbarEdit`='$user_bottomLeftToolbarEdit',
						`bottomLeftToolbarPreview`='$user_bottomLeftToolbarPreview', `bottomLeftToolbarHtmlPreview`='$user_bottomLeftToolbarHtmlPreview',
						`buttonType`='$user_buttonType', `tabs`='$user_tabs', `headers`='$user_headers', `font_style`='$user_font_style',
						`text_align`='$user_text_align', `lists`='$user_lists', `link`='$user_link', `img`='$user_img', `table`='$user_table',
						`emoticon`='$user_emoticon', `symbol`='$user_symbol', `color`='$user_color', `textTransform`='$user_textTransform',
						`blocks`='$user_blocks', `codes`='$user_codes', `others`='$user_others' WHERE `role`='user' AND `texyla_id` = '$id'");
	$result4 = db::exec("UPDATE texyla_settings SET `texyCfg`='$visitor_texyCfg', `allow`='$visitor_allow', `bottomLeftToolbarEdit`='$visitor_bottomLeftToolbarEdit',
						`bottomLeftToolbarPreview`='$visitor_bottomLeftToolbarPreview', `bottomLeftToolbarHtmlPreview`='$visitor_bottomLeftToolbarHtmlPreview',
						`buttonType`='$visitor_buttonType', `tabs`='$visitor_tabs', `headers`='$visitor_headers', `font_style`='$visitor_font_style',
						`text_align`='$visitor_text_align', `lists`='$visitor_lists', `link`='$visitor_link', `img`='$visitor_img', `table`='$visitor_table',
						`emoticon`='$visitor_emoticon', `symbol`='$visitor_symbol', `color`='$visitor_color', `textTransform`='$visitor_textTransform',
						`blocks`='$visitor_blocks', `codes`='$visitor_codes', `others`='$visitor_others' WHERE `role`='visitor' AND `texyla_id` = '$id'");
	
	if($result0 && $result1 && $result2 && $result3 && $result4){
		return true;
	}
	else{
		return false;
	}
  }
  
  public function saveNew($post)
  {
	foreach($post as $key => $val){
		$$key = $val;
	}
	    if(!isset($admin_allow)) $admin_allow = 0;
		if(!isset($admin_bottomLeftToolbarEdit)) $admin_bottomLeftToolbarEdit = 0;
		if(!isset($admin_bottomLeftToolbarPreview)) $admin_bottomLeftToolbarPreview = 0;
		if(!isset($admin_bottomLeftToolbarHtmlPreview)) $admin_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($admin_tabs)) $admin_tabs = 0;
		if(!isset($admin_headers)) $admin_headers = 0;
		if(!isset($admin_font_style)) $admin_font_style = 0;
		if(!isset($admin_text_align)) $admin_text_align = 0;
		if(!isset($admin_lists)) $admin_lists = 0;
		if(!isset($admin_link)) $admin_link = 0;
		if(!isset($admin_img)) $admin_img = 0;
		if(!isset($admin_table)) $admin_table = 0;
		if(!isset($admin_emoticon)) $admin_emoticon = 0;
		if(!isset($admin_symbol)) $admin_symbol = 0;
		if(!isset($admin_color)) $admin_color = 0;
		if(!isset($admin_textTransform)) $admin_textTransform = 0;
		if(!isset($admin_blocks)) $admin_blocks = 0;
		if(!isset($admin_codes)) $admin_codes = 0;
		if(!isset($admin_others)) $admin_others = 0;
		
		if(!isset($editor_allow)) $editor_allow = 0;
		if(!isset($editor_bottomLeftToolbarEdit)) $editor_bottomLeftToolbarEdit = 0;
		if(!isset($editor_bottomLeftToolbarPreview)) $editor_bottomLeftToolbarPreview = 0;
		if(!isset($editor_bottomLeftToolbarHtmlPreview)) $editor_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($editor_tabs)) $editor_tabs = 0;
		if(!isset($editor_headers)) $editor_headers = 0;
		if(!isset($editor_font_style)) $editor_font_style = 0;
		if(!isset($editor_text_align)) $editor_text_align = 0;
		if(!isset($editor_lists)) $editor_lists = 0;
		if(!isset($editor_link)) $editor_link = 0;
		if(!isset($editor_img)) $editor_img = 0;
		if(!isset($editor_table)) $editor_table = 0;
		if(!isset($editor_emoticon)) $editor_emoticon = 0;
		if(!isset($editor_symbol)) $editor_symbol = 0;
		if(!isset($editor_color)) $editor_color = 0;
		if(!isset($editor_textTransform)) $editor_textTransform = 0;
		if(!isset($editor_blocks)) $editor_blocks = 0;
		if(!isset($editor_codes)) $editor_codes = 0;
		if(!isset($editor_others)) $editor_others = 0;
		
		if(!isset($visitor_allow)) $visitor_allow = 0;
		if(!isset($visitor_bottomLeftToolbarEdit)) $visitor_bottomLeftToolbarEdit = 0;
		if(!isset($visitor_bottomLeftToolbarPreview)) $visitor_bottomLeftToolbarPreview = 0;
		if(!isset($visitor_bottomLeftToolbarHtmlPreview)) $visitor_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($visitor_tabs)) $visitor_tabs = 0;
		if(!isset($visitor_headers)) $visitor_headers = 0;
		if(!isset($visitor_font_style)) $visitor_font_style = 0;
		if(!isset($visitor_text_align)) $visitor_text_align = 0;
		if(!isset($visitor_lists)) $visitor_lists = 0;
		if(!isset($visitor_link)) $visitor_link = 0;
		if(!isset($visitor_img)) $visitor_img = 0;
		if(!isset($visitor_table)) $visitor_table = 0;
		if(!isset($visitor_emoticon)) $visitor_emoticon = 0;
		if(!isset($visitor_symbol)) $visitor_symbol = 0;
		if(!isset($visitor_color)) $visitor_color = 0;
		if(!isset($visitor_textTransform)) $visitor_textTransform = 0;
		if(!isset($visitor_blocks)) $visitor_blocks = 0;
		if(!isset($visitor_codes)) $visitor_codes = 0;
		if(!isset($visitor_others)) $visitor_others = 0;
		
		if(!isset($user_allow)) $user_allow = 0;
		if(!isset($user_bottomLeftToolbarEdit)) $user_bottomLeftToolbarEdit = 0;
		if(!isset($user_bottomLeftToolbarPreview)) $user_bottomLeftToolbarPreview = 0;
		if(!isset($user_bottomLeftToolbarHtmlPreview)) $user_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($user_tabs)) $user_tabs = 0;
		if(!isset($user_headers)) $user_headers = 0;
		if(!isset($user_font_style)) $user_font_style = 0;
		if(!isset($user_text_align)) $user_text_align = 0;
		if(!isset($user_lists)) $user_lists = 0;
		if(!isset($user_link)) $user_link = 0;
		if(!isset($user_img)) $user_img = 0;
		if(!isset($user_table)) $user_table = 0;
		if(!isset($user_emoticon)) $user_emoticon = 0;
		if(!isset($user_symbol)) $user_symbol = 0;
		if(!isset($user_color)) $user_color = 0;
		if(!isset($user_textTransform)) $user_textTransform = 0;
		if(!isset($user_blocks)) $user_blocks = 0;
		if(!isset($user_codes)) $user_codes = 0;
		if(!isset($user_others)) $user_others = 0;
		
	// Get new ID
	$row = db::fetch("SELECT MAX(id) AS id FROM texyla");
	$newid = $row['id']+1;
	
	$result0 = db::exec("INSERT INTO texyla (`id`, `textarea`, `description`) VALUES ('$newid', '$textarea', '$description')");
	$result1 = db::exec("INSERT INTO texyla_settings (`texyla_id`, `role`, `texyCfg`, `allow`, `bottomLeftToolbarEdit`, `bottomLeftToolbarPreview`, `bottomLeftToolbarHtmlPreview`, `buttonType`,
	                    `tabs`, `headers`, `font_style`, `text_align`, `lists`, `link`, `img`, `table`, `emoticon`, `symbol`, `color`, `textTransform`, `blocks`,
						`codes`, `others`)
						VALUES ('$newid', 'admin', '$admin_texyCfg', '$admin_allow', '$admin_bottomLeftToolbarEdit', '$admin_bottomLeftToolbarPreview', '$admin_bottomLeftToolbarHtmlPreview', '$admin_buttonType',
						'$admin_tabs', '$admin_headers', '$admin_font_style', '$admin_text_align', '$admin_lists', '$admin_link', '$admin_img', '$admin_table', '$admin_emoticon',
						'$admin_symbol', '$admin_color', '$admin_textTransform', '$admin_blocks', '$admin_codes', '$admin_others')");
	$result2 = db::exec("INSERT INTO texyla_settings (`texyla_id`, `role`, `texyCfg`, `allow`, `bottomLeftToolbarEdit`, `bottomLeftToolbarPreview`, `bottomLeftToolbarHtmlPreview`, `buttonType`,
	                    `tabs`, `headers`, `font_style`, `text_align`, `lists`, `link`, `img`, `table`, `emoticon`, `symbol`, `color`, `textTransform`, `blocks`,
						`codes`, `others`)
						VALUES ('$newid', 'editor', '$editor_texyCfg', '$editor_allow', '$editor_bottomLeftToolbarEdit', '$editor_bottomLeftToolbarPreview', '$editor_bottomLeftToolbarHtmlPreview', '$editor_buttonType',
						'$editor_tabs', '$editor_headers', '$editor_font_style', '$editor_text_align', '$editor_lists', '$editor_link', '$editor_img', '$editor_table', '$editor_emoticon',
						'$editor_symbol', '$editor_color', '$editor_textTransform', '$editor_blocks', '$editor_codes', '$editor_others')");
	$result3 = db::exec("INSERT INTO texyla_settings (`texyla_id`, `role`, `texyCfg`, `allow`, `bottomLeftToolbarEdit`, `bottomLeftToolbarPreview`, `bottomLeftToolbarHtmlPreview`, `buttonType`,
	                    `tabs`, `headers`, `font_style`, `text_align`, `lists`, `link`, `img`, `table`, `emoticon`, `symbol`, `color`, `textTransform`, `blocks`,
						`codes`, `others`)
						VALUES ('$newid', 'user', '$user_texyCfg', '$user_allow', '$user_bottomLeftToolbarEdit', '$user_bottomLeftToolbarPreview', '$user_bottomLeftToolbarHtmlPreview', '$user_buttonType',
						'$user_tabs', '$user_headers', '$user_font_style', '$user_text_align', '$user_lists', '$user_link', '$user_img', '$user_table', '$user_emoticon',
						'$user_symbol', '$user_color', '$user_textTransform', '$user_blocks', '$user_codes', '$user_others')");
	$result4 = db::exec("INSERT INTO texyla_settings (`texyla_id`, `role`, `texyCfg`, `allow`, `bottomLeftToolbarEdit`, `bottomLeftToolbarPreview`, `bottomLeftToolbarHtmlPreview`, `buttonType`,
	                    `tabs`, `headers`, `font_style`, `text_align`, `lists`, `link`, `img`, `table`, `emoticon`, `symbol`, `color`, `textTransform`, `blocks`,
						`codes`, `others`)
						VALUES ('$newid', 'visitor', '$visitor_texyCfg', '$visitor_allow', '$visitor_bottomLeftToolbarEdit', '$visitor_bottomLeftToolbarPreview', '$visitor_bottomLeftToolbarHtmlPreview', '$visitor_buttonType',
						'$visitor_tabs', '$visitor_headers', '$visitor_font_style', '$visitor_text_align', '$visitor_lists', '$visitor_link', '$visitor_img', '$visitor_table', '$visitor_emoticon',
						'$visitor_symbol', '$visitor_color', '$visitor_textTransform', '$visitor_blocks', '$visitor_codes', '$visitor_others')");
	
	if($result0 && $result1 && $result2 && $result3 && $result4){
		return true;
	}
	else{
		return false;
	}
  }
  
  public function delete($id)
  {
    $result1 = db::exec("DELETE FROM texyla WHERE id=$id");
	$result2 = db::exec("DELETE FROM texyla_settings WHERE texyla_id=$id");
	
	if($result1 && $result2) return true;
	
	return false;
  }
  
  public function getValues($id)
  {
    $row = db::fetch("SELECT * FROM texyla WHERE id=$id");
	if(!$row) redirect('texyla/admin', TEXYLA_ERR_NO_ID);
	
	$vals = db::fetch("SELECT * FROM texyla_settings WHERE texyla_id=$id AND role='admin'");
	foreach($vals as $key => $val){
		if($key == 'texila_id'){
		  //do nothing
		}
		elseif($key == 'role'){
		  //do nothing
		}
		else{
			$row['admin_'.$key] = $val;
		}
		
	}
	
	$vals = db::fetch("SELECT * FROM texyla_settings WHERE texyla_id=$id AND role='editor'");
	foreach($vals as $key => $val){
		if($key == 'texila_id'){
		  //do nothing
		}
		elseif($key == 'role'){
		  //do nothing
		}
		else{
			$row['editor_'.$key] = $val;
		}
		
	}
	
	$vals = db::fetch("SELECT * FROM texyla_settings WHERE texyla_id=$id AND role='user'");
	foreach($vals as $key => $val){
		if($key == 'texila_id'){
		  //do nothing
		}
		elseif($key == 'role'){
		  //do nothing
		}
		else{
			$row['user_'.$key] = $val;
		}
	}
	
	$vals = db::fetch("SELECT * FROM texyla_settings WHERE texyla_id=$id AND role='visitor'");
	foreach($vals as $key => $val){
		if($key == 'texila_id'){
		  //do nothing
		}
		elseif($key == 'role'){
		  //do nothing
		}
		else{
			$row['visitor_'.$key] = $val;
		}
	}
	
	return $row;
	
  }

}