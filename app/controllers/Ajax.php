<?php
class Ajax extends CoreAPIController {
    
	//////////////////////////////// StartUp
    protected function startUp($params)
    {
		$this->params = $this->parseParams($params);
    }
	
	//////////////////////////////// actions
	protected function actionUser($params) {
		
	}
	
	/////////////////////////////// getters
	protected function getUser($params)
	{

	}
	
	protected function getJson($params)
	{
		header('Content-type: text/html; charset=utf-8');
		header('Content-type: application/json');
		$jsonString = base64_decode($params['id']);
		echo $jsonString;
		exit;
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

}
