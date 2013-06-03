<?php 

class OSTModel_Tutorials extends OST_Model {

	protected $option 		= null;
	protected $view			= null;
	protected $context		= null;
	protected $pagination 	= null;
	
	protected $list			= null;
	protected $total		= null;
	
  	public function __construct() {
    	//$this->populateState();    
  	}
	/*
	protected function populateState() {
		$app = JFactory::getApplication();
		//$search 			= $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
		//$this->setState('filter.search', $search);
		
		$filter_order		= $app->getUserStateFromRequest($this->context.'.filter.order', 'filter_order', 't.name', 'string');
		$this->setState('filter.order', $filter_order);
		
		$filter_order_Dir	= $app->getUserStateFromRequest($this->context.'.filter.order_dir', 'filter_order_Dir', 'ASC', 'string');
		$this->setState('filter.order_dir', $filter_order_Dir);
		
		$category			= $app->getUserStateFromRequest($this->context.'.category', 'category', null);
		$this->setState('category', $category);
  	}
	*/
	public function getList() {
		$data = OST_Cache::callback($this, '_fetchList', array(), null, true);

		$videos = preg_split("/,/", get_option('videos'), -1, PREG_SPLIT_NO_EMPTY);
		if (count($videos))
		{
			$temp = array();
			foreach ($videos as $item)
			{
				foreach ($data as $row)
				{
					if ($row->id == $item)
					{
						$temp[] = $row;
						break;
					}
				}
			}
			$data = $temp;
		}

		return $data;
	}
		
	public function _fetchList() {
		
		$data	= array('resource' => 'articles');

		$response = OST_RequestHelper::makeRequest($data);
		
		if ($response->hasError()) :
			//wp_die(__('OSToolbar Error').':  '.$response->getErrorMsg().' ('.__('Code').' '.$response->getErrorCode().')');
			wp_die(__('OSToolbar Error').': '.__('Please enter an API key in the Setting > OSToolbar.'));
			//$this->setError(__('OSToolbar Error').':  '.$response->getErrorMsg().' ('.__('Code').' '.$response->getErrorCode().')');
			return false;
		endif;
		
		$list	= $response->getBody();
	
		for($i=0; $i<count($list); $i++) :
			$list[$i]->link = 'admin.php?page=ostoolbar&id='.$list[$i]->id;
		endfor;
		
		return $list;
	}
/*
	public function getFilters($rows) {
		$filters = array();
		
		$cats = array();
		$options = array();
		foreach($rows as $row) :
			if ($row->ostcat_id && !in_array($row->ostcat_id, $cats)) :
				$cats[] = $row->ostcat_id;
				$options[] = JHTML::_('select.option', $row->ostcat_id, $row->ostcat_name);
			endif;
		endforeach;
		JArrayHelper::sortObjects($options, 'text');
		$options = array_merge(
					array(
						JHTML::_('select.option', '', 'All'),
						JHTML::_('select.option', 'none', '--')
					),
					$options
				);
		
		$attributes = "class='inputbox' onchange='document.adminForm.submit();'";
		
		$filters['category'] 	= JHTML::_('select.genericlist', $options, 'category', $attributes, 'value', 'text', $this->getState('category'));
		
		return $filters;
	}

	public function applyFilters($rows) {
		$filters = array();
		if ($this->getState('category', '') != '') :
			$category = $this->getState('category');
			$category = $category == 'none' ? null : $category;
			$filters[] = array('field' => 'ostcat_id', 'value' => $category);
		endif;
		
		if ($filters) :	
			$filtered = array();
			foreach($rows as $row) :
				$pass = true;
				foreach($filters as $f) :
					if ($row->{$f['field']} != $f['value']) :
						$pass = false;
					endif;
				endforeach;
				if ($pass) :
					$filtered[] = $row;
				endif;
			endforeach;
		else :
			$filtered = $rows;
		endif;
		
		return $filtered;
	}
*/
}
