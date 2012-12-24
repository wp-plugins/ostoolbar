<?php 

class OSTModel_Help extends OST_Model {

	public function listen()
	{
		$uri = $_SERVER['REQUEST_URI'];
		$split = explode("/", $uri);
		$last = count($split)-1;
		$admin_link = $split[$last];
		$helparticles = $this->getList();
		
		if ($msg = $this->getError())
		{
			if (strpos($msg, 'API Key Not Found') !== false)
			{
				$msg .= ". Fix this <a href='options-general.php?page=options-ostoolbar'>here</a>.";
			}
			echo $msg;
			return false;
		}
		
		if ($article = $this->search($admin_link, $helparticles))
		{
			$link = 'admin.php?page=ostoolbar&help='.$article->id;;
			echo '<a href="javascript:ostoolbar_popup(\''.$link.'\', \''.$article->title.'\');">'.$article->title.'</a>';
		}
		
	}

	private function search($admin_link, $helparticles)
	{
		$admin_uri = $this->parseURI($admin_link);
		
		for($i=0; $i<count($helparticles); $i++)
		{
			$h = $helparticles[$i];
			$parsed = $this->parseURI($h->url);
			if ($h->url_exact)
			{
				if ($parsed['hash'] == $admin_uri['hash'])
				{
					return $h;
				}
			}
			elseif ($parsed['page'] == $admin_uri['page']) {
				if (!$parsed['vars'])
				{
					return $h;
				}
				// Compare keys
				$admin_keys = array_keys($admin_uri['vars']);
				$parsed_keys = array_keys($parsed['vars']);
				
				$intersect = array_intersect($parsed_keys, $admin_keys);
				if (count($intersect) == count($parsed_keys))
				{
					$compare = array();
					foreach($intersect as $index)
					{
						$compare[$index] = $admin_uri['vars'][$index];
					}
					ksort($compare);
					
					if (md5(serialize($compare)) == md5(serialize($parsed['vars'])))
					{
						return $h;
					}
				}
				
			}
		}
		
		return false;
		
	}
	
	private function parseURI($uri)
	{
		list($page, $query) = explode("?", $uri);
		$vars = array();
		if ($query)
		{
			parse_str($query, $vars);
		}
		
		ksort($vars);
		
		$hash = $page;
		if ($vars)
		{
			$hash .= md5(serialize($vars));
		}
		
		return compact('page', 'vars', 'hash');
	}
	
	public function getData()
	{
		$data = $this->getList();
		for($i=0; $i<count($data); $i++)
		{
			$d = $data[$i];
			if ($d->id == $this->getState('id'))
			{
				$d->introtext = OST_RequestHelper::filter($d->introtext);
				$d->fulltext  = OST_RequestHelper::filter($d->fulltext);
				return $d;
			}
		}
		return null;
	}
	
	public function getList() {
		$data = OST_Cache::callback($this, '_fetchList', array(), null, true);
		return $data;
	}
		
	public function _fetchList() {
		
		$data	= array('resource' => 'helparticles');

		$response = OST_RequestHelper::makeRequest($data);
		
		if ($response->hasError()) :
			$this->setError(__('OSToolbar Error').':  '.$response->getErrorMsg().' ('.__('Code').' '.$response->getErrorCode().')');
			return false;
		endif;
		
		$list	= $response->getBody();
	
		for($i=0; $i<count($list); $i++) :
			$list[$i]->link = 'admin.php?page=ostoolbar&id='.$list[$i]->id;
		endfor;
		
		return $list;
	}
	
}
