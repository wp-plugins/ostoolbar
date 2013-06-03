<?php

class OST_Controller
{
	

	public function action_tutorials($isFrontend = false)
	{
		if ($id = (int)$_GET['id'])
		{
			$this->action_tutorial($id);
			return;
		}
		
		if ($help = (int)$_GET['help'])
		{
			$this->action_tutorial($help, true);
			return;
		}
		
		$model = OST_Factory::getInstance('OSTModel_Tutorials');
		$tutorials = $model->getList();
		
		$videos = preg_split("/,/", get_option('videos'), -1, PREG_SPLIT_NO_EMPTY);
		?>
		<div class="wrap">
			<h2><img src="<?php echo plugins_url('/ostoolbar/assets/images/icon-tutorials.png');?>" align="absmiddle" /> Tutorials</h2>
			<?php
			$api_key = get_option('api_key');
			if (OST_RequestHelper::$isTrial)
			{
				if ($api_key)
					echo "<div class='error'>Your API key is invalid. Please enter an API key in the <a href='options-general.php?page=options-ostoolbar'>OSToolbar settings</a>.</div>";
				else
					echo "<div class='error'>You are using OSToolbar Free. Visit <a href='http://OSToolbar.com'>OSToolbar.com</a> to get the Pro version with more videos, more features and to remove all advertising.</div>";
			}
			?>
			<table class="widefat">
				<thead>
					<tr>
						<th><?php _e('Name'); ?></th>
						<th><?php _e('Category');?></th>
					</tr>
				</thead>
				<tbody>
					<?php for ($i=0; $i<count($tutorials); $i++) : ?>
                    	<?php
						if (is_array($videos) && count($videos) && !in_array( $tutorials[$i]->id, $videos))
						{
							continue;
						}
						?>
						<tr>
							<td><a href="<?php echo ($isFrontend ?  "index.php?page_id=".$_GET["page_id"]."&id=".$tutorials[$i]->id : $tutorials[$i]->link);?>">
								<?php echo $tutorials[$i]->title; ?></a>
							</td>
							<td><?php echo $tutorials[$i]->ostcat_name; ?></td>
						</tr>
					<?php endfor; ?>
				</tbody>
			</table>
			
		</div>
		<?php
	}
	
	public function action_tutorial($id, $helparticle=false)
	{
		if ($helparticle)
		{
			$model = OST_Factory::getInstance('OSTModel_Help');
			$model->setState('id', $id);
			$tutorial = $model->getData();
		}
		else
		{
			$model = OST_Factory::getInstance('OSTModel_Tutorial');
			$model->setState('id', $id);
			$tutorial = $model->getData();
		}
		?>
		<div class="wrap">
			<?php if (in_array($tutorial->jversion, array("wp_trial"))):?>
                <iframe src="http://www.ostraining.com/services/adv/adv1.html" width="734px" height="80px" style="overflow:visible"></iframe>
            <?php endif;?>
        
			<h2><?php echo $tutorial->title?></h2>
			<?php echo $tutorial->introtext.$tutorial->fulltext; ?>
		</div>
		<?php
	}
	
	public function action_help()
	{
		$model = OST_Factory::getInstance('OSTModel_HelpPage');
		$help = $model->getData();
		?>
		<div class="wrap">
			<h2><?php echo $help->title;?></h2>
			<?php echo $help->introtext;?>
		</div>
		<?php
	}
	
	
	public function action_configuration()
	{
		wp_deregister_script( 'jquery_ui' );
		wp_register_script( 'jquery_ui', plugins_url('assets/js/jquery-ui-1.10.3.custom.min.js', __FILE__));
		wp_enqueue_script( 'jquery_ui' );

		wp_deregister_style( 'jquery_ui_css' );
		wp_register_style( 'jquery_ui_css', plugins_url('assets/css/ui-lightness/jquery-ui-1.8.6.custom.css', __FILE__));
		wp_enqueue_style( 'jquery_ui_css' );
		
		
		?>
		<div class="wrap">
			<h2>OSToolbar Configuration</h2>
			<form method="post" action="options.php"> 
				<?php settings_fields(OST_Configuration::SETTINGS_GROUP); ?>
				<?PHP do_settings_sections(OST_Configuration::SETTINGS_PAGE); ?>
				<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>
		</div>
		<?php
	}
	
}