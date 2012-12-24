<?php 

class OSTModel_Tutorial extends OST_Model {

	protected $data			= null;
	
	public function getData() {
		$id = $this->getState('id');
		$tmodel = OST_Factory::getInstance('OSTModel_Tutorials');
		$tutorials = $tmodel->getList();
		
		foreach(@$tutorials as $t)
		{
			if ($t->id == $id)
			{
				$this->data = $t;
				$this->data->introtext = OST_RequestHelper::filter($this->data->introtext);
				$this->data->fulltext  = OST_RequestHelper::filter($this->data->fulltext);
				break;
			}	
		}
		return $this->data;
	}

}
