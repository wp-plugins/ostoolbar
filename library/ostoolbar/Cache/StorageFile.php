<?php
/**
 * @version        $Id: file.php 20228 2011-01-10 00:52:54Z eddieajau $
 * @package        Joomla.Framework
 * @subpackage     Cache
 * @copyright      Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Ostoolbar\Cache;

defined('ABSPATH') or die();

/**
 * File cache storage handler
 *
 * @package        Joomla.Framework
 * @subpackage     Cache
 * @since          1.5
 */

/**
 * Class StorageFile
 *
 * Taken from Joomla 1.5 (See above)
 *
 * @TODO: Use an updated caching system
 */
class StorageFile
{
    protected $root = null;

    protected $cachePath = null;

    protected $lifetime = null;

    public function __construct()
    {
        $this->cachePath = __DIR__ . '/files';
        if (!is_dir($this->cachePath)) {
            @mkdir($this->cachePath, 0755);
        }
    }

    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
    }

    /**
     * Get cached data from a file by id and group
     *
     * @param    string  $id        The cache data id
     * @param    string  $group     The cache data group
     * @param    boolean $checkTime True to verify cache time expiration threshold
     *
     * @return    mixed    Boolean false on failure or a cached data string
     * @since    1.5
     */
    public function get($id, $group, $checkTime = true)
    {
        $data = false;

        $path = $this->getFilePath($id, $group);

        if ($checkTime == false || ($checkTime == true && $this->checkExpire($id, $group) === true)) {
            if (file_exists($path)) {
                $data = file_get_contents($path);
                if ($data) {
                    // Remove the initial die() statement
                    $data = str_replace('<?php die("Access Denied"); ?>#x#', '', $data);
                }
            }

            return $data;
        } else {
            return false;
        }
    }

    /**
     * Store the data to a file by id and group
     *
     * @param    string $id    The cache data id
     * @param    string $group The cache data group
     * @param    string $data  The data to store in cache
     *
     * @return    boolean    True on success, false otherwise
     * @since    1.5
     */
    public function store($id, $group, $data)
    {
        $written = false;
        $path    = $this->getFilePath($id, $group);
        $die     = '<?php die("Access Denied"); ?>#x#';

        // Prepend a die string
        $data = $die . $data;

        $fileOpen = @fopen($path, "wb");

        if ($fileOpen) {
            $len = strlen($data);
            @fwrite($fileOpen, $data, $len);
            $written = true;
        }

        // Data integrity check
        if ($written && ($data == file_get_contents($path))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove a cached data file by id and group
     *
     * @param    string $id    The cache data id
     * @param    string $group The cache data group
     *
     * @return    boolean    True on success, false otherwise
     * @since    1.5
     */
    public function remove($id, $group)
    {
        $path = $this->getFilePath($id, $group);
        if (!@unlink($path)) {
            return false;
        }

        return true;
    }

    /**
     * Clean cache for a group given a mode.
     *
     * group mode        : cleans all cache in the group
     * notgroup mode     : cleans all cache not in the group
     *
     * @param    string $group The cache data group
     * @param    string $mode  The mode for cleaning cache [group|notgroup]
     *
     * @return    boolean    True on success, false otherwise
     * @since    1.5
     */
    public function clean($group, $mode = null)
    {
        $return = true;
        $folder = $group;

        if (trim($folder) == '') {
            $mode = 'notgroup';
        }

        switch ($mode) {
            case 'notgroup':
                $folders = $this->folders($this->root);
                for ($i = 0, $n = count($folders); $i < $n; $i++) {
                    if ($folders[$i] != $folder) {
                        $return |= $this->deleteFolder($this->root . '/' . $folders[$i]);
                    }
                }
                break;
            case 'group':
            default:
                if (is_dir($this->root . '/' . $folder)) {
                    $return = $this->deleteFolder($this->root . '/' . $folder);
                }
                break;
        }

        return $return;
    }

    /**
     * Garbage collect expired cache data
     *
     * @return    boolean    True on success, false otherwise.
     * @since    1.5
     */
    public function gc()
    {
        $result = true;
        // files older than lifeTime get deleted from cache
        $files = $this->filesInFolder($this->root, '', true, true);
        foreach ($files as $file) {
            $time = @filemtime($file);
            if (($time + $this->lifetime) < time() || empty($time)) {
                $result |= @unlink($file);
            }
        }

        return $result;
    }

    /**
     * Test to see if the cache storage is available.
     *
     * @return    boolean    True on success, false otherwise.
     * @since    1.5
     */
    public function test()
    {
        return is_writable($this->cachePath);
    }

    /**
     * Check to make sure cache is still valid, if not, delete it.
     *
     * @param    string $id    Cache key to expire.
     * @param    string $group The cache data group.
     *
     * @since    1.6
     *
     * @return bool
     */
    protected function checkExpire($id, $group)
    {
        $path = $this->getFilePath($id, $group);

        // check prune period
        if (file_exists($path)) {
            $time = @filemtime($path);
            if (($time + $this->lifetime) < time() || empty($time)) {
                @unlink($path);

                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Get a cache file path from an id/group pair
     *
     * @param    string $id    The cache data id
     * @param    string $group The cache data group
     *
     * @return    string    The cache file path
     * @since    1.5
     */
    protected function getFilePath($id, $group)
    {
        $name = $this->getCacheId($id, $group);
        $dir  = $this->cachePath;

        // If the folder doesn't exist try to create it
        if (!is_dir($dir)) {
            @mkdir($dir);
        }

        // Make sure the folder exists
        if (!is_dir($dir)) {
            return false;
        }

        return $dir . '/' . $name . '.php';
    }

    protected function getCacheId($id, $group)
    {
        $name = md5(SECURE_AUTH_KEY . '-' . $id);

        return $name;
    }

    /**
     * Quickly delete a folder of files
     *
     * @param string $path The path to the folder to delete.
     *
     * @return boolean True on success.
     * @since 1.6
     */
    protected function deleteFolder($path)
    {
        // Sanity check
        if (!$path || !is_dir($path) || empty($this->root)) {
            // @TODO: Implement error messaging?
            return false;
        }

        $path = $this->cleanPath($path);

        // Check to make sure path is inside cache folder, we do not want to delete Joomla root!
        $pos = strpos($path, $this->cleanPath($this->root));

        if ($pos === false || $pos > 0) {
            // @TODO: Implement error messaging?
            return false;
        }

        // Remove all the files in folder if they exist; disable all filtering
        $files = $this->filesInFolder($path, '.', false, true, array(), array());

        if (!empty($files) && !is_array($files)) {
            if (@unlink($files) !== true) {
                return false;
            }
        } else {
            if (!empty($files) && is_array($files)) {
                foreach ($files as $file) {
                    $file = $this->cleanPath($file);

                    // In case of restricted permissions we zap it one way or the other
                    // as long as the owner is either the webserver or the ftp
                    if (@unlink($file)) {
                        // Do nothing
                    } else {
                        // @TODO: Implement error messaging?
                        return false;
                    }
                }
            }
        }

        // Remove sub-folders of folder; disable all filtering
        $folders = $this->folders($path, '.', false, true, array(), array());

        foreach ($folders as $folder) {
            if (is_link($folder)) {
                // Don't descend into linked directories, just delete the link.
                if (@unlink($folder) !== true) {
                    return false;
                }
            } elseif ($this->deleteFolder($folder) !== true) {
                return false;
            }
        }

        // In case of restricted permissions we zap it one way or the other
        // as long as the owner is either the webserver or the ftp
        if (@rmdir($path)) {
            $ret = true;
        } else {
            // @TODO: Implement error messaging?
            $ret = false;
        }

        return $ret;
    }

    /**
     * Function to strip additional / or \ in a path name
     *
     * @param string $path The path to clean
     *
     * @return    string    The cleaned path
     * @since    1.6
     */
    protected function cleanPath($path)
    {
        $path = trim($path);

        if (empty($path)) {
            $path = $this->root;
        }

        return $path;
    }

    /**
     * Utility function to quickly read the files in a folder.
     *
     * @param string  $path          The path of the folder to read.
     * @param string  $filter        A filter for file names.
     * @param mixed   $recurse       True to recursively search into sub-folders, or an
     *                               integer to specify the maximum depth.
     * @param boolean $fullPath      True to return the full path to the file.
     * @param array   $exclude       Array with names of folders which should not be shown in
     *                               the result.
     * @param array   $excludeFilter Array with names of files which should not be shown in
     *                               the result.
     *
     * @return    array    Files in the given folder.
     * @since 1.6
     */
    protected function filesInFolder(
        $path,
        $filter = '.',
        $recurse = false,
        $fullPath = false,
        $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX'),
        $excludeFilter = array('^\..*', '.*~')
    ) {
        // Initialise variables.
        $arr = array();

        // Check to make sure the path valid and clean
        $path = $this->cleanPath($path);

        // Is the path a folder?
        if (!is_dir($path)) {
            // @TODO: Implement error messaging?
            return false;
        }

        // read the source directory
        $handle = opendir($path);
        if (count($excludeFilter)) {
            $excludeFilter = '/(' . implode('|', $excludeFilter) . ')/';
        } else {
            $excludeFilter = '';
        }
        while (($file = readdir($handle)) !== false) {
            if (($file != '.')
                && ($file != '..')
                && (!in_array($file, $exclude))
                && (!$excludeFilter || !preg_match($excludeFilter, $file))
            ) {
                $dir   = $path . '/' . $file;
                $isDir = is_dir($dir);
                if ($isDir) {
                    if ($recurse) {
                        if (is_integer($recurse)) {
                            $arr2 = $this->filesInFolder($dir, $filter, $recurse - 1, $fullPath);
                        } else {
                            $arr2 = $this->filesInFolder($dir, $filter, $recurse, $fullPath);
                        }

                        $arr = array_merge($arr, $arr2);
                    }
                } else {
                    if (preg_match('/$filter/', $file)) {
                        if ($fullPath) {
                            $arr[] = $path . '/' . $file;
                        } else {
                            $arr[] = $file;
                        }
                    }
                }
            }
        }
        closedir($handle);

        return $arr;
    }

    /**
     * Utility function to read the folders in a folder.
     *
     * @param string  $path          The path of the folder to read.
     * @param string  $filter        A filter for folder names.
     * @param mixed   $recurse       True to recursively search into sub-folders, or an
     *                               integer to specify the maximum depth.
     * @param boolean $fullPath      True to return the full path to the folders.
     * @param array   $exclude       Array with names of folders which should not be shown in
     *                               the result.
     * @param array   $excludeFilter Array with regular expressions matching folders which
     *                               should not be shown in the result.
     *
     * @return    array    Folders in the given folder.
     * @since 1.6
     */
    protected function folders(
        $path,
        $filter = '.',
        $recurse = false,
        $fullPath = false,
        $exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX'),
        $excludeFilter = array('^\..*')
    ) {
        // Initialise variables.
        $arr = array();

        // Check to make sure the path valid and clean
        $path = $this->cleanPath($path);

        // Is the path a folder?
        if (!is_dir($path)) {
            // @TODO: Implement error messaging?
            return false;
        }

        // read the source directory
        $handle = opendir($path);

        if (count($excludeFilter)) {
            $excludeFilterString = '/(' . implode('|', $excludeFilter) . ')/';
        } else {
            $excludeFilterString = '';
        }
        while (($file = readdir($handle)) !== false) {
            if (($file != '.')
                && ($file != '..')
                && (!in_array($file, $exclude))
                && (empty($excludeFilterString) || !preg_match($excludeFilterString, $file))
            ) {
                $dir   = $path . '/' . $file;
                $isDir = is_dir($dir);
                if ($isDir) {
                    // Removes filtered directories
                    if (preg_match("/$filter/", $file)) {
                        if ($fullPath) {
                            $arr[] = $dir;
                        } else {
                            $arr[] = $file;
                        }
                    }
                    if ($recurse) {
                        if (is_integer($recurse)) {
                            $arr2 = $this->folders($dir, $filter, $recurse - 1, $fullPath, $exclude, $excludeFilter);
                        } else {
                            $arr2 = $this->folders($dir, $filter, $recurse, $fullPath, $exclude, $excludeFilter);
                        }

                        $arr = array_merge($arr, $arr2);
                    }
                }
            }
        }
        closedir($handle);

        return $arr;
    }
}
