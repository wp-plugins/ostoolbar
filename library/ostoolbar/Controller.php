<?php
/**
 * @package    OSToolbar-WP
 * @contact    www.alledia.com, support@alledia.com
 * @copyright  2015 Alledia.com, All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
namespace Ostoolbar;

defined('ABSPATH') or die();

class Controller
{
    public static function actionTutorials($isFrontend = false)
    {
        if ($id = Factory::getSanitize()->getInt('id')) {
            static::actionTutorial($id);

            return;
        }

        /** @var Model\Tutorials $model */
        $model     = Factory::getModel('Tutorials');
        $tutorials = $model->getList();

        $videos = preg_split('/,/', get_option('ostoolbar_videos'), -1, PREG_SPLIT_NO_EMPTY);
        ?>
        <div class="wrap">
            <h2>
                <img
                    src="<?php echo plugins_url('/ostoolbar/assets/images/icon-48-tutorials.png'); ?>"
                    align="absmiddle"/>
                Tutorials
            </h2>
            <?php
            $apikey = get_option('ostoolbar_apikey');
            if (Request::$isTrial) {
                if ($apikey) {
                    echo '<div class="error">'
                        . 'Your API key is invalid. Please enter an API key in'
                        . ' <a href="options-general.php?page=options-ostoolbar">OSToolbar settings</a>.'
                        . '</div>';
                }
            }
            ?>
            <table class="widefat">
                <thead>
                <tr>
                    <th><?php _e('Name'); ?></th>
                    <th><?php _e('Category'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($tutorials as $i => $tutorial) :
                    if (!is_array($videos) || !count($videos) || in_array($tutorial->id, $videos)) :
                        if ($isFrontend) {
                            $pageId = Factory::getSanitize()->getKey('page_id');
                            $link = "index.php?page_id={$pageId}&id={$tutorial->id}";
                        } else {
                            $link = "admin.php?page=ostoolbar&id={$tutorial->id}";
                        }
                    ?>
                    <tr>
                        <td>
                            <a href="<?php echo $link; ?>">
                                <?php echo $tutorial->title; ?></a>
                        </td>
                        <td><?php echo $tutorial->ostcat_name; ?></td>
                    </tr>
                <?php
                    endif;
                endforeach;
                ?>
                </tbody>
            </table>
        </div>
    <?php
    }

    public static function actionTutorial($id)
    {
        /** @var Model\Tutorial $model */
        $model = Factory::getModel('Tutorial');
        $model->setState('id', $id);
        $tutorial = $model->getData();
        ?>
        <div class="wrap">
            <h2>
                <img
                    src="<?php echo plugins_url('/ostoolbar/assets/images/icon-48-tutorials.png'); ?>"
                    align="absmiddle"/>
                <?php echo $tutorial->title ?>
            </h2>

            <?php echo $tutorial->introtext . $tutorial->fulltext; ?>
        </div>
    <?php
    }

    public function actionConfiguration()
    {
        ?>
        <div class="wrap">
            <h2>OSToolbar Configuration</h2>

            <form method="post" action="options.php">
                <?php settings_fields(Configuration::SETTINGS_GROUP); ?>
                <?php do_settings_sections(Configuration::SETTINGS_PAGE); ?>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
                </p>
            </form>
        </div>
    <?php
    }
}
