<?php

class OST_Configuration
{
	
	const SETTINGS_PAGE 	= 'ostoolbar_settings';
	const SETTINGS_SECTION 	= 'ostoolbar_settings_section';
	const SETTINGS_GROUP 	= 'ostoolbar_settings_group';
	
	public function init_settings()
	{
		add_settings_section(self::SETTINGS_SECTION, 'Plugin Settings', array($this, 'section_out'), self::SETTINGS_PAGE);
		add_settings_field('api_key', __('API Key', 'ostoolbar'), array($this, 'api_key_field'), self::SETTINGS_PAGE, self::SETTINGS_SECTION);
		register_setting(self::SETTINGS_GROUP, 'api_key');
		
		add_settings_field('videos', __('Choose and rearrange videos', 'ostoolbar'), array($this, 'video_field'), self::SETTINGS_PAGE, self::SETTINGS_SECTION);
		add_settings_field('toolbar_text', __('OSToolbar menu text', 'ostoolbar'), array($this, 'toolbar_text_field'), self::SETTINGS_PAGE, self::SETTINGS_SECTION);
		//add_settings_field('toolbar_icon', __('OSToolbar Icon', 'ostoolbar'), array($this, 'toolbar_icon_field'), self::SETTINGS_PAGE, self::SETTINGS_SECTION);

		add_settings_field('toolbar_permission', __('Choose which users can see videos', 'ostoolbar'), array($this, 'toolbar_permission_field'), self::SETTINGS_PAGE, self::SETTINGS_SECTION);

		register_setting(self::SETTINGS_GROUP, 'videos');
		register_setting(self::SETTINGS_GROUP, 'toolbar_text');
		register_setting(self::SETTINGS_GROUP, 'toolbar_icon');
		register_setting(self::SETTINGS_GROUP, 'toolbar_permission');
	}

	public function section_out()
	{
		$response = OST_RequestHelper::makeRequest(array('resource' => 'checkapi'));
		if ($response->hasError())
		{
			echo('<iframe src="http://www.ostraining.com/services/adv/adv1.html" width="734px" height="80px"></iframe>');

		}
		echo '<p>'.__('Configure the OSToolbar plugin').'</p>';
	}

	public function api_key_field()
	{
		echo "<input type='text' size='55' name='api_key' value='".get_option('api_key')."' /> ".__('Enter your API Key from <a href="http://OSTraining.com">OSTraining.com</a>', 'ostoolbar');
	}

	public function toolbar_permission_field()
	{
		$response = OST_RequestHelper::makeRequest(array('resource' => 'checkapi'));
		if ($response->hasError())
		{
			echo(__("Please enter an API key to use this feature."));
			return;

		}
		
		$text = get_option('toolbar_permission');
		if ($text == "")
			$text = '{"editor":0,"contributor":0,"author":0,"subscriber":0}';
		$permission = json_decode($text, true);
		?>
        	<script>
			function array2json(arr) {
				var parts = [];
				var is_list = (Object.prototype.toString.apply(arr) === '[object Array]');
			
				for(var key in arr) {
					var value = arr[key];
					if(typeof value == "object") { //Custom handling for arrays
						if(is_list) parts.push(array2json(value)); /* :RECURSION: */
						else parts[key] = array2json(value); /* :RECURSION: */
					} else {
						var str = "";
						if(!is_list) str = '"' + key + '":';
			
						//Custom handling for multiple data types
						if(typeof value == "number") str += value; //Numbers
						else if(value === false) str += 'false'; //The booleans
						else if(value === true) str += 'true';
						else str += '"' + value + '"'; //All other things
						// :TODO: Is there any more datatype we should be in the lookout for? (Functions?)
			
						parts.push(str);
					}
				}
				var json = parts.join(",");
				
				if(is_list) return '[' + json + ']';//Return numerical JSON
				return '{' + json + '}';//Return associative JSON
			}			
			function UpdatePermission(name)
			{
				if (document.getElementById("toolbar_permission").value)
					var test = eval('('+document.getElementById("toolbar_permission").value+')');
				else
					var test = {};
				test[name] = document.getElementById("chk_"+name).checked ? 1 : 0;
				document.getElementById("toolbar_permission").value = array2json(test);
				
			}
			</script>
        	<table border="0" cellpadding="0" cellspacing="0">
            	<tr>
                	<td>Administrator</td>
                    <td><input type="checkbox" disabled="disabled" checked="checked" id="chk_administrator" name="administrator" /></td>
                </tr>
            	<tr>
                	<td>Editors</td>
                    <td><input type="checkbox" onclick="UpdatePermission('editor')" <?php if ($permission['editor']) echo('checked="checked"');?> id="chk_editor" name="editor" /></td>
                </tr>
            	<tr>
                	<td>Authors</td>
                    <td><input type="checkbox" onclick="UpdatePermission('author')" <?php if ($permission['author']) echo('checked="checked"');?> id="chk_author" name="author" /></td>
                </tr>
            	<tr>
                	<td>Contributors</td>
                    <td><input type="checkbox" onclick="UpdatePermission('contributor')" <?php if ($permission['contributor']) echo('checked="checked"');?> id="chk_contributor" name="contributor" /></td>
                </tr>
            	<tr>
                	<td>Subscribers</td>
                    <td><input type="checkbox" onclick="UpdatePermission('subscriber')" <?php if ($permission['subscriber']) echo('checked="checked"');?> id="chk_subscriber" name="subscriber" /></td>
                </tr>
            </table>
            <input type="hidden" name="toolbar_permission" id="toolbar_permission" value='<?php echo $text;?>' />
        <?php
	}
	
	
	public function video_field()
	{
		$data	= array('resource' => 'articles');
		$response = OST_RequestHelper::makeRequest($data);
		if ($response->hasError())
		{
			echo(__("Please enter an API key to use this feature."));
			return;
		}
		$list	= $response->getBody();
	
		for($i=0; $i<count($list); $i++) :
			$list[$i]->link = 'admin.php?page=ostoolbar&id='.$list[$i]->id;
		endfor;
		
		$videos = preg_split("/,/", get_option('videos'), -1, PREG_SPLIT_NO_EMPTY);
		


		?>
        	<script>
				jQuery(function() {
					jQuery('#sortable1, #sortable2').sortable({
						connectWith: '.connectedSortable'
					}).disableSelection();
					
					function updateSortableField() {
						var selected = jQuery('#sortable2').sortable('toArray');
						var string	= selected.join(',');
						jQuery('#videos').val(string);
					}
					
					jQuery('#sortable2').bind('sortupdate', function(event, ui) {
						updateSortableField();
					});
					
					updateSortableField();
				});
			</script>
            <style>
				#sortable1, #sortable2 {
					width:295px;
					float:left;
					list-style:none;
					padding:0;
					margin:0;
					padding:3px;
					border:1px solid #dedede;
					height:300px;
					overflow-y:scroll;
				}
				
				#sortable1 {
					margin-right:15px;
				}
				
				#sortable1 li, #sortable2 li {
					padding:5px;
					margin-top:1px;
					cursor:pointer;
				}
				.clearfix
				{
					clear:both;
				}
			</style>
            <div class='sortable_holder'>
                <ul id="sortable1" class="connectedSortable">
                	<?php foreach ($list as $item):?>
                    	<?php 
						if ((!$videos || !is_array($videos)) || (count($videos) && in_array($item->id, $videos)))
							continue;
						?>
                        <li class="ui-state-default" id="<?php echo($item->id);?>"><?php echo($item->title);?></li>
                    <?php endforeach;?>
                </ul>
            	<div style="float:left; width:50px; margin-right:20px;"><?php echo(__("Drag and drop the videos to choose which ones will show to users"));?></div>
                <?php
				if (count($videos))
				{
					$temp = array();
					foreach ($videos as $item)
					{
						foreach ($list as $row)
						{
							if ($row->id == $item)
							{
								$temp[] = $row;
								break;
							}
						}
					}
					$list = $temp;
				}
				?>
                <ul id="sortable2" class="connectedSortable">
                	<?php foreach ($list as $item):?>
                    	<?php 
						if ($videos && is_array($videos) && !in_array($item->id, $videos))
							continue;
						?>
                        <li class="ui-state-highlight" id="<?php echo($item->id);?>"><?php echo($item->title);?></li>
                    <?php endforeach;?>
                </ul>
                <div class="clearfix"></div>
            </div>
	        <input type='hidden' size='55' name='videos' id="videos" value='<?php echo get_option('videos');?>' />
        <?php
	}

	public function toolbar_text_field()
	{
		$response = OST_RequestHelper::makeRequest(array('resource' => 'checkapi'));
		if ($response->hasError())
		{
			echo(__("Please enter an API key to use this feature."));
			return;

		}
		echo "<input type='text' size='55' name='toolbar_text' value='".get_option('toolbar_text')."' /> ".__('The text seen in the toolbar link', 'ostoolbar');
	}
	public function toolbar_icon_field()
	{
		$dir = WP_PLUGIN_DIR ."/ostoolbar/assets/images";
		$result = scandir($dir);		
		?>
        	<select name='toolbar_icon'>-->
            	<?php foreach ($result as $file):
					$ext = split("\.", $file);
					$mayext = strtolower($ext[count($ext) - 1]);
					if (!in_array($mayext, array("jpg", "gif", "png")))
						continue;
				?>
                	<option value="<?php echo($file);?>"><?php echo($file);?></option>
                <?php endforeach;?>
            </select>
        <?php
		echo __('The icon seen in the toolbar link. It is a file name in assets\images folder', 'ostoolbar');
		//echo "<input type='text' size='55' name='toolbar_icon' value='".get_option('toolbar_icon')."' /> ".__('The icon seen in the toolbar link. It is a file name in assets\images folder', 'ostoolbar');
	}
}