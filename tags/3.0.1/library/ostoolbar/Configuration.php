<?php
/**
 * @package    OSToolbar-WP
 * @contact    www.alledia.com, support@alledia.com
 * @copyright  2015 Alledia.com, All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
namespace Ostoolbar;

use Ostoolbar\Factory;

defined('ABSPATH') or die();

class Configuration
{
    const SETTINGS_PAGE    = 'ostoolbar_settings';
    const SETTINGS_SECTION = 'ostoolbar_settings_section';
    const SETTINGS_GROUP   = 'ostoolbar_settings_group';

    /**
     * Setup the configuration page
     */
    public function initSettings()
    {
        $app = Factory::getApplication();

        add_action('admin_enqueue_scripts', array($app, 'enqueueScripts'));

        // Section heading and description
        add_settings_section(
            static::SETTINGS_SECTION,
            'Plugin Settings',
            function () {
            },
            static::SETTINGS_PAGE
        );

        // API Key field
        add_settings_field(
            'ostoolbar_apikey',
            __('API Key', 'ostoolbar'),
            array(
                $this,
                'apikeyField'
            ),
            static::SETTINGS_PAGE,
            static::SETTINGS_SECTION
        );
        register_setting(static::SETTINGS_GROUP, 'ostoolbar_apikey');

        // Video selection and ordering
        add_settings_field(
            'ostoolbar_videos',
            __('Choose and rearrange videos', 'ostoolbar'),
            array(
                $this,
                'videoField'
            ),
            static::SETTINGS_PAGE,
            static::SETTINGS_SECTION
        );
        register_setting(static::SETTINGS_GROUP, 'ostoolbar_videos');

        // Toolbar permissions
        add_settings_field(
            'ostoolbar_permissions',
            __('Choose which users can see videos', 'ostoolbar'),
            array(
                $this,
                'toolbarPermissionField'
            ),
            static::SETTINGS_PAGE,
            static::SETTINGS_SECTION
        );
        register_setting(static::SETTINGS_GROUP, 'ostoolbar_permissions');
    }

    /**
     * Display API Key field
     */
    public function apikeyField()
    {
        $apiKey = get_option('ostoolbar_apikey');

        $text = '<input type="text" size="55" name="ostoolbar_apikey"'
            . ' value="' . $apiKey . '" />';
        if ($apiKey == '') {
            $text .= __(
                'Enter your API Key from <a href="http://OSTraining.com" target="_blank">OSTraining.com</a>',
                'ostoolbar'
            );
        }
        echo $text;
    }

    /**
     * Toolbar Permissions
     */
    public function toolbarPermissionField()
    {
        /** @var \WP_Roles $wp_roles */
        global $wp_roles;

        $allRoles = $wp_roles->roles;
        $old      = json_decode(get_option('ostoolbar_permissions'), true) ?: array();

        $permissions = array();
        foreach ($allRoles as $key => $role) {
            $optSet  = (isset($old[$key]) && $old[$key]) || $key == 'administrator';
            $roleSet = (isset($role['capabilities'][$key]) && $role['capabilities'][$key]);

            $permissions[$key] = array(
                'name'    => $role['name'],
                'allowed' => $optSet || $roleSet
            );
        }
        ?>
        <table border="0" cellpadding="0" cellspacing="0">
            <?php
            foreach ($permissions as $key => $permission) :
                ?>
                <tr>
                    <td><?php echo $permission['name']; ?></td>
                    <td>
                        <input
                            type="checkbox"
                            <?php echo $key == 'administrator' ? 'disabled' : ''; ?>
                            <?php echo $permission['allowed'] ? 'checked="checked"' : ''; ?>
                            id="<?php echo 'chk_' . $key; ?>"
                            class="role_permission"
                            name="<?php echo $key; ?>"/>
                    </td>
                </tr>
            <?php
            endforeach;
            ?>
        </table>
        <input
            type="hidden"
            name="ostoolbar_permissions"
            id="ostoolbar_permissions"
            value='<?php echo json_encode($old); ?>'/>
    <?php
    }

    /**
     * Video selection/ordering field
     */
    public function videoField()
    {
        $data     = array('resource' => 'articles');
        $response = Request::makeRequest($data);
        if ($response->hasError()) {
            echo(__('Please enter an API key to use this feature.'));

            return;
        }
        $list = $response->getBody();

        for ($i = 0; $i < count($list); $i++) {
            $list[$i]->link = 'admin.php?page=ostoolbar&id=' . $list[$i]->id;
        }

        $videos = preg_split("/,/", get_option('ostoolbar_videos'), -1, PREG_SPLIT_NO_EMPTY);
        ?>
        <div class='sortable-holder'>
            <div class="sortable-header">Videos not shown to users</div>
            <div class="sortable-header">Videos shown to users</div>
            <div style="clear:both"></div>
            <ul id="sortable1" class="connectedSortable">
                <?php
                foreach ($list as $item) :
                    if ((!$videos || !is_array($videos))
                        || (count($videos) && in_array($item->id, $videos))
                    ) {
                        continue;
                    }
                    ?>
                    <li class="ui-state-default" id="<?php echo($item->id); ?>"><?php echo($item->title); ?></li>
                <?php
                endforeach;
                ?>
            </ul>
            <div class="sortable-divider">
                <?php echo(__("Drag and drop the videos to choose which ones will show to users")); ?>
            </div>
            <?php
            if (count($videos)) {
                $temp = array();
                foreach ($videos as $item) {
                    foreach ($list as $row) {
                        if ($row->id == $item) {
                            $temp[] = $row;
                            break;
                        }
                    }
                }
                $list = $temp;
            }
            ?>
            <ul id="sortable2" class="connectedSortable">
                <?php
                foreach ($list as $item) :
                    if ($videos && is_array($videos) && !in_array($item->id, $videos)) {
                        continue;
                    }
                    ?>
                    <li class="ui-state-highlight" id="<?php echo($item->id); ?>"><?php echo($item->title); ?></li>
                <?php
                endforeach;
                ?>
            </ul>
            <div class="clearfix"></div>
        </div>
        <input
            type='hidden'
            size='55'
            name='ostoolbar_videos'
            id="ostoolbar_videos"
            value='<?php echo get_option('ostoolbar_videos'); ?>'/>
    <?php
    }
}
