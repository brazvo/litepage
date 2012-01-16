<?php
class Api extends CoreAPIController {
    
	private $whatsup;
	
	//////////////////////////////// StartUp
    protected function startUp($params)
    {
		$this->params = $this->parseParams($params);
    }
	
	//////////////////////////////// actions
	protected function actionBlock($params) {
		
	}
	
	protected function actionContent($params)
	{
		
	}
	
	protected function actionPairs($params)
	{
		$obj = new ApiModel();
		
		$this->pairs = $obj->getPairsFromTable($params['id'], $params);
	}


	protected function actionWhatsUp($params)
	{
		$obj = new ApiModel();
		$this->whatsup = $obj->find();

	}
	
	protected function actionConvert()
	{
		$rows = db::fetchAll("SELECT `user` FROM `users`");
		foreach ($rows as $key => $value) {
			$users[] = $value['user'];
		}
		
		$connect = mysql_connect("localhost","nh828600","himawapjo");
		mysql_select_db("nh828600db", $connect);
		mysql_query("SET NAMES 'cp1250'", $connect);		
		$result = mysql_query("SELECT * FROM user", $connect);
		
		while ($row = mysql_fetch_array($result)) {
			if( !in_array($row['vid'], $users) ) {
				
				if( preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\s[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $row['datumreg']) ) {
					$regdate = date("Y-m-d H:i:s", strtotime($row['datumreg']));
				}
				else {
					$regdate = '0000-00-00 00:00:00';
				}
				
				db::exec("INSERT INTO `users` (`id`, `user`, `password`, `role`, `last_login`, `reg_date`, `session_id`)
					      VALUES (null, %v, %v, 'user', '0000-00-00 00:00:00', %v, 'XXX')",
						  $row['vid'], $row['heslo'], $regdate);
			}
		}
		
	}

		/////////////////////////////// getters
	protected function getBlock($params)
	{
		Block::get($params['id']);
	}
	
	protected function getContent($params)
	{
		$html = file_get_contents( baseUrl()."/content/show/{$params['id']}");
		$doc = new DOMDocument('1.0', 'UTF-8');
		$searchPage = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8"); 
		$doc->loadHTML($searchPage);
		// dirty fix
		foreach ($doc->childNodes as $item)
			if ($item->nodeType == XML_PI_NODE)
				$doc->removeChild($item); // remove hack
		$doc->encoding = 'UTF-8'; // insert proper
		
		$title = $doc->getElementById("content-title");
		if( isset($params['title']) && $params['title'] === 'false' ) {
			$title->parentNode->removeChild($title);
		}
		else {
			$h2 = $doc->createElement('h2');
			$h2->setAttribute('class', 'title');
			$h2->nodeValue = $title->nodeValue;
			$title->parentNode->replaceChild($h2, $title);
		}
		//echo $h2->nodeValue;
		
		$content = $doc->getElementById('content-body');
		echo Html::divBlock($this->innerHTML($content), 'content-block', 'content-block-'.$params['id']);
	}
	
	protected function getWhatsUp($params)
	{
		// ATC
		$airports = array(
			'LZBB' => '<span class="acc">Bratislava Radar </span>', 

			'LZIB' => 'Bratislava',
			'LZKZ' => 'Ko&scaron;ice',
			'LZTT' => 'Poprad',
			'LZPP' => 'Pie&scaron;&#357;any',
			'LZZI' => 'Žilina',
			'LZRU' => 'Ružomberok',
			'LZSE' => 'Senica',
			'LZLU' => 'Lučenec',
			'LZTN' => 'Trenčín',
			'LZSL' => 'Sliač',
			'LZPW' => 'Prešov',

		);
		$ctrlevel = array(
			1 => 'OBS',
			2 => 'S1',
			3 => 'S2',
			4 => 'S3',
			5 => 'C1',
			6 => 'C2',
			7 => 'C3',
			8 => '<span class="green">I1</span>',
			9 => '<span class="green">I2</span>',
			10 => '<span class="green">I3</span>',
			11 => '<span class="red">SUP</span>',
			12 => '<span class="red">ADM</span>'
		);
		
		$atcTitle = Html::elem('h3')->setCont('Riadiaci');
		if( empty($this->whatsup['atcs']) ) {
			$list = Html::elem('p')->setClass('noservice')->setCont('Žiadny ATC nie je online');
		}
		else {
			$list = Html::elem('table')->setClass('taffic-list-table');
			$list->cellspacing = 0;
			
			$atcTitle->add( " (".count($this->whatsup['atcs']).")" );
			$capicao = '';
			ksort($this->whatsup['atcs']);
			foreach ($this->whatsup['atcs'] as $controller) {
				if (substr_count($controller[0],'_') == 1) {
					list ($apicao, $position) = explode('_', $controller[0]);
				} else {
					list ($apicao, $position1, $position2) = explode('_', $controller[0]);
					$position = $position2 . ' (' . $position1 .')';
				}
				
				if ($apicao != $capicao) {
					$capicao = $apicao;
					$airPort = Html::elem('td')->setCont("$apicao - {$airports[$apicao]}");
					$airPort->colspan = 2;
					$list->add(Html::elem('tr')->setCont($airPort));
				}
				// realname
				$realname = ucwords($controller[2]);
        
				// Level
				$level = $ctrlevel[$controller[16]];
				
				$aPosDetail = Html::elem('a')->href( "http://network.ivao.aero/data/atcd.cgi?cs={$controller[0]}" )
					->setCont($position); $aPosDetail->rel = 'external';
				
				$aMemDetail = Html::elem('a')->href( "http://www.ivao.aero/members/person/details.asp?id={$controller[1]}" )
					->setCont($realname); $aMemDetail->rel = 'external';
				$tdPos = Html::elem('td')->setCont($aPosDetail);
				$tdPos->width = 60;
				$tdMem = Html::elem('td')->setCont($aMemDetail);
				$list->add(Html::elem('tr')->setCont($tdPos . $tdMem));
				//echo '<tr><td width="60"><a href="http://network.ivao.aero/data/atcd.cgi?cs='. $controller[0] .'" onClick="MM_openBrWindow(\'http://network.ivao.aero/data/atcd.cgi?cs='. $controller[0] .'\',\'\',\'scrollbars=yes,resizable=yes,width=700,height=600\');return false" target="_blank">&nbsp;&nbsp;' . $position . '</a></td><td><a href="http://www.ivao.aero/members/person/details.asp?id=' . $controller[1] . '" onClick="MM_openBrWindow(\'http://www.ivao.aero/members/person/details.asp?id=' . $controller[1] . '\',\'\',\'scrollbars=yes,resizable=yes,width=800,height=600\');return false" target="_blank" title="' . $realname . '">' . $controller[1] . '</a>&nbsp;(' . $level . ')</td></tr>';
			}
		}
		
		$atc = $atcTitle . $list;
		
		// pilots
		$pilotsTitle = Html::elem('h3')->setCont('Piloti');
		if( empty($this->whatsup['pilots']) ) {
			$list = Html::elem('p')->setClass('noservice')->setCont('Žiadna prevádzka v SVK');
		}
		else {
			$list = Html::elem('table')->setClass('taffic-list-table');
			$list->cellspacing = 0;
			$pilotsTitle->add( " (".count($this->whatsup['pilots']).")" );
			foreach ($this->whatsup['pilots'] as $pilot) {
				$realname = $pilot[2];
				if (substr($realname,-5,1) == ' ') {
					$realname = substr($realname,0,-5);
				}
				$ft= "flighttrack/show/$pilot[0]";
				
				$aFtDetail = Html::elem('a')->href( $ft )->title($realname)
					->setCont($pilot[0]);
				$tdFt = Html::elem('td')->setCont($aFtDetail);
				$tdFt->width = 50;
				$tdFr = Html::elem('td')->setCont(trim($pilot[11]) != '' ? trim($pilot[11]) : '&nbsp;');
				$tdFr->width = 30;
				$tdTo = Html::elem('td')->setCont( trim($pilot[13]) != '' ? "&gt;&nbsp;$pilot[13]" : 'On Ground' );
				$list->add(Html::elem('tr')->setCont($tdFt . $tdFr . $tdTo));
				//echo '<tr><td width="60"><a href="http://network.ivao.aero/data/atcd.cgi?cs='. $controller[0] .'" onClick="MM_openBrWindow(\'http://network.ivao.aero/data/atcd.cgi?cs='. $controller[0] .'\',\'\',\'scrollbars=yes,resizable=yes,width=700,height=600\');return false" target="_blank">&nbsp;&nbsp;' . $position . '</a></td><td><a href="http://www.ivao.aero/members/person/details.asp?id=' . $controller[1] . '" onClick="MM_openBrWindow(\'http://www.ivao.aero/members/person/details.asp?id=' . $controller[1] . '\',\'\',\'scrollbars=yes,resizable=yes,width=800,height=600\');return false" target="_blank" title="' . $realname . '">' . $controller[1] . '</a>&nbsp;(' . $level . ')</td></tr>';
			}
		}
		
		$pilots = $pilotsTitle . $list;
		
		// staff
		$staffTitle = Html::elem('h3')->setCont('Staff Online');
		if( empty($this->whatsup['staff']) ) {
			$list = Html::elem('p')->setClass('noservice')->setCont('Žiadny člen staffu');
		}
		else {
			$list = Html::elem('table')->setClass('taffic-list-table');
			$list->cellspacing = 0;
			$staffTitle->add( " (".count($this->whatsup['staff']).")" );
			foreach ($this->whatsup['staff'] as $staffmember) {
				$realname = $staffmember[2];
				
				$aStDetail = Html::elem('a')->href( "http://www.ivao.aero/staff/details.asp?id=$staffmember[0]" )->title($realname)
					->setCont($realname ." ($staffmember[0])");
				$aStDetail->rel = 'external';
				$tdSt = Html::elem('td')->setCont($aStDetail);
				$list->add(Html::elem('tr')->setCont($tdSt));
				//echo '<tr><td width="60"><a href="http://network.ivao.aero/data/atcd.cgi?cs='. $controller[0] .'" onClick="MM_openBrWindow(\'http://network.ivao.aero/data/atcd.cgi?cs='. $controller[0] .'\',\'\',\'scrollbars=yes,resizable=yes,width=700,height=600\');return false" target="_blank">&nbsp;&nbsp;' . $position . '</a></td><td><a href="http://www.ivao.aero/members/person/details.asp?id=' . $controller[1] . '" onClick="MM_openBrWindow(\'http://www.ivao.aero/members/person/details.asp?id=' . $controller[1] . '\',\'\',\'scrollbars=yes,resizable=yes,width=800,height=600\');return false" target="_blank" title="' . $realname . '">' . $controller[1] . '</a>&nbsp;(' . $level . ')</td></tr>';
			}
		}
		
		$staff = $staffTitle . $list;
		
		$sumar = sprintf("<b>Teraz je online:</b><br/> %d riadiacich<br/> %d pilotov", $this->whatsup['atcs_num'], $this->whatsup['pilots_num']);
		$sum = Html::elem('p')->setClass('summary')->setCont($sumar);
		$blockTitle = Html::elem('div')->setClass('menu-title')->setCont('Koho máme online?');
		$blockDiv = Html::elem('div')->setClass('online-wrapper');
		
		// result
		if($params['id'] === 'atc') {
			echo Html::divBlock( $atc, 'online-info' );
		}
		else if( $params['id'] === 'pilots') {
			echo Html::divBlock( $pilots, 'online-info' );
		}
		else if ($params['id'] === 'info'){
			echo Html::divBlock( $sum, 'online-info' );
		}
		else if ($params['id'] === 'staff'){
			echo Html::divBlock( $staff, 'online-info' );
		}
		else {
			echo Html::divBlock( $blockTitle . $blockDiv->setCont($pilots . $atc . $staff . $sum), 'online-info' );
		}
		
	}
	
	
	protected function getUser($params = NULL)
	{
		echo $params['id'];
		if($params) echo Application::$logged[$params['id']];
		else echo "undefined";
	}
	
	protected function getYear($params)
	{
		if($params['id'] == 'now') {
			echo date("Y");
		}
		else if($params['id'] == 'next') {
			echo date("Y")+1;
		}
		else if($params['id'] == 'prev') {
			echo date("Y")-1;
		}
	}

	protected function getDatetime($params)
	{
		if($params['id'] == 'now') {
			echo date("d.m.Y H:i");
		}
	}
	
	
	protected function getPairs()
	{
		foreach($this->pairs as $key => $val) {
			$tempArray[] = $key."=".$val;
		}
		if(isset($tempArray)) echo implode (';', $tempArray);
		else echo "undefined";
	}
	
	
	protected function getTourleg($params)
	{
		if($params['id'] == 'new') {
			echo db::fetchSingle("SELECT COALESCE(MAX(leg_number) ,0)+1 FROM tours_legs");
		}
	}


	protected function getConvert()
	{
		echo "SME HOTOVY"; exit;
	}
	
	protected function getTest($params)
	{
		$a = explode(',',$params['id']);
		var_dump($a);
	}
	
    
    //////////////////////////////// ShutDown
    protected function shutDown($params)
    {
        
    }
	
	//////////////////////////////// Helpers
	private function parseParams($params)
	{
		if( empty($params) )			return NULL;
		
		if( preg_match('/;/', $params) ) {
			
			$params = explode(';', $params);
			
		}
		
		else if( preg_match('/|/', $params) ) {
			
			$params = explode('|', $params);
			
		}
		
		if( is_array($params) ) {
			
			$ret['id'] = array_shift($params);
			
			// parse next params
			foreach( $params as $param ) {
				if ( preg_match('/:/', $param) ) {
					list($key, $value) = explode(':', $param);
					$ret[$key] = $value;
				}
				else {
					$ret[] = $param;
				}
			}
			
		}
		else {
			$ret['id'] = $params;
		}
		
		return $ret;
	}
	
	
	function innerHTML($node){
		$doc = new DOMDocument('', 'utf-8');
		foreach ($node->childNodes as $child)
		$doc->appendChild($doc->importNode($child, true));

		return $doc->saveHTML();
	}

}
