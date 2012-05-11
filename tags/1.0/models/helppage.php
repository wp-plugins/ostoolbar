<?php 

class OSTModel_HelpPage extends OST_Model {

	protected $option 		= null;
	protected $view			= null;
	protected $context		= null;
	protected $pagination 	= null;
	
	protected $list			= null;
	protected $total		= null;
	
  	public function __construct() {
   
  	}

	public function getData() {
		$data = OST_Cache::callback($this, '_fetchList', array(), null, true);
		return $data;
	}
		
	public function _fetchList() {
		
		$data	= array('resource' => 'help');

		$response = OST_RequestHelper::makeRequest($data);
		
		if ($response->hasError()) :
			$this->setError(__('OSToolbar Error').':  '.$response->getErrorMsg().' ('.__('Code').' '.$response->getErrorCode().')');
			return false;
		endif;
		
		$list	= $response->getBody();
		
		return $list;
	}

}
