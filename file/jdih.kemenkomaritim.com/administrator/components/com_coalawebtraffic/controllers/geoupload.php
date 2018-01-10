<?php

/**
 * @package             Joomla
 * @subpackage          com_coalawebtraffic
 * @author              Steven Palmer
 * @author url          http://coalaweb.com
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /files/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * CoalaWeb Traffic is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');

// Try to increase all relevant settings to prevent timeouts on upload/unzip
ini_set('memory_limit', '512M');
ini_set('error_reporting', 0);
ini_set('upload_max_filesize', '32M');
ini_set('post_max_size', '32M');
@set_time_limit(3600);
                
// Load version.php
$version_php = JPATH_COMPONENT_ADMINISTRATOR . '/version.php';
if (!defined('COM_CWTRAFFIC_VERSION') && JFile::exists($version_php)) {
    include_once $version_php;
}

/**
 * Geoupload controller class.
 *
 * @package    Joomla.Administrator
 * @subpackage com_coalawebtraffic
 */
class CoalawebtrafficControllerGeoupload extends JControllerLegacy
{
    
    /**
     * @var        string    The prefix to use with controller messages.
     * @since    1.6
     */
    protected $text_prefix = 'COM_CWTRAFFIC';

    /**
     * Constructor.
     *
     * @param array An optional associative array of configuration settings.
     * @see   JController
     */
    public function __construct() 
    {
        $tmpdir = $this->getTempFolder();
        
        if (COM_CWTRAFFIC_PRO == 1) {
            $this->dbName = 'GeoLite2-City.mmdb';
            $this->pathUpload = $tmpdir . '/com_coalawebtraffic/'. $this->dbName . '.gz';
            $this->pathUnzip = JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/assets/geoip/v2/' . $this->dbName;
        } else {
            $this->dbName = 'GeoLiteCity.dat';
            $this->pathUpload = $tmpdir . '/com_coalawebtraffic/'. $this->dbName . '.gz';
            $this->pathUnzip = JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/assets/geoip/' . $this->dbName;
        }

        parent::__construct();
    }

    /**
     * Upload the GeoLiteCity dat file
     * 
     * @return boolean
     */
    public function geoinstall() {
        $app = JFactory::getApplication();

        //If the file didn't get uploded correctly return an error
        if ($this->upload() === false) {
            $app->redirect('index.php?option=com_coalawebtraffic&view=geoupload', JText::_('COM_CWTRAFFIC_UPLOAD_FAILED'));
        }
        
        //If the file didn't unzip correctly delete the zipped file and return an error
        if ($this->unzip() === false) {
            JFile::delete($this->pathUpload);
            $app->redirect('index.php?option=com_coalawebtraffic&view=geoupload', JText::_('COM_CWTRAFFIC_UNZIP_FAILED'));
        }
        
        //All good then delete the zipped file and return successful
        JFile::delete($this->pathUpload);
        $app->redirect('index.php?option=com_coalawebtraffic&view=geoupload', JText::_('COM_CWTRAFFIC_INSTALL_SUCCESSFUL'));
    }
    
    /**
     * Upload the GeoLiteCity dat file
     * 
     * @return boolean
     */
    public function geoupdate() {
        $app = JFactory::getApplication();

        //If the file didn't get uploded correctly return an error
        if ($this->upload() === false) {
            $app->redirect('index.php?option=com_coalawebtraffic&view=controlpanel', JText::_('COM_CWTRAFFIC_UPLOAD_FAILED'));
        }
        
        //If the file didn't unzip correctly return an error
        if ($this->unzip() === false) {
            JFile::delete($this->pathUpload);
            $app->redirect('index.php?option=com_coalawebtraffic&view=controlpanel', JText::_('COM_CWTRAFFIC_UNZIP_FAILED'));
        }
        
        JFile::delete($this->pathUpload);
        $app->redirect('index.php?option=com_coalawebtraffic&view=controlpanel', JText::_('COM_CWTRAFFIC_UPDATE_SUCCESSFUL'));
    }
    
     /**
     * Upload the GeoLiteCity dat file
     * 
     * @return boolean
     */
    private function upload() {
        $source = 'http://geolite.maxmind.com/download/geoip/database/' . $this->dbName . '.gz';

        $package = JHttpFactory::getHttp()->get($source, null, 30);

        if (!$package || $package->code != 200 || empty($package->body)) {
            return false;
        }
        
        JFile::write($this->pathUpload, $package->body);
        
        return true;
    }

    /**
     * Unzip the GeoLiteCity dat file
     * 
     * @return string
     */
    private function unzip() {
        
        if (!$file = gzopen($this->pathUpload , 'rb')) {
            return false;
        }

        if (!$out_file = fopen($this->pathUnzip, 'wb')) {
            return false;
        }

        $buffer_size = 4096; // read 4kb at a time

        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            if (!fwrite($out_file, gzread($file, $buffer_size))) {
                return false;
            }
        }

        fclose($out_file);
        gzclose($file);

       return true;
    }
    
    /**
     * Reads (and checks) the temp Joomla folder
     *
     * @return string
     */
    private function getTempFolder() {
        $jreg = JFactory::getConfig();
        $tmpdir = $jreg->get('tmp_path');

        if (realpath($tmpdir) == '/tmp') {
            $tmpdir = JPATH_SITE . '/tmp';
        } elseif (!JFolder::exists($tmpdir)) {
            $tmpdir = JPATH_SITE . '/tmp';
        }

        return $tmpdir;
    }

}
