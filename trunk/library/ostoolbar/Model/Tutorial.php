<?php
/**
 * @package    OSToolbar-WP
 * @contact    www.alledia.com, support@alledia.com
 * @copyright  2015 Alledia.com, All rights reserved
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
namespace Ostoolbar\Model;

use Ostoolbar\Factory;
use Ostoolbar\Model;
use Ostoolbar\Request;

class Tutorial extends Model
{
    protected $data = null;

    public function getData()
    {
        $id = $this->getState('id');

        /** @var Tutorials $model */
        $model = Factory::getModel('Tutorials');

        $tutorials = $model->getList();
        if (is_array($tutorials)) {
            foreach ($tutorials as $tutorial) {
                if ($tutorial->id == $id) {
                    $tutorial->introtext = Request::filter($tutorial->introtext);
                    $tutorial->fulltext  = Request::filter($tutorial->fulltext);

                    $this->data = $tutorial;
                    break;
                }
            }
        }

        return $this->data;
    }
}
