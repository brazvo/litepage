<?php

class Flighttrack extends Controller
{  
  
  
	protected function beforeRender()
	{
		$this->template->bodyclass = 'flighttrack';
		$this->template->activeMembers = file_get_contents(baseUrl()."/api/whats-up");
		//$this->template->JS->add("http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAFMRzjghz_nxpCjfcQpSyARQ-gN5wIZlg3_7s6D1g3afl-3e9dhQrOpGb5Vssy11ZEEFLA68D3R5JHA");
		//$this->template->JS->add("http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAmN5hnum4bUTD4kTZS68TtRSQe0tQUAgfBkm39ZfO2VpXujQEzhQYdXC7hGTNVVas3I_Xr3ZbuB0Wgg");
	}
  
 
  
	function renderShow($id)
	{
		$iframe = Html::elem('iframe')->src('http://www.ivao.sk/flighttrack.php?cs='.$id);
		$iframe->width = 560;
		$iframe->height =800;
		$iframe->style = "border:none;";
		
		$this->template->title = "Flight Track";
		$this->template->content = $iframe;
	}
  
  
}