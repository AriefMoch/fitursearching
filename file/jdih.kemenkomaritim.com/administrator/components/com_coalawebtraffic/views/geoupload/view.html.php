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

jimport('joomla.application.component.view');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

// Helpers
JLoader::register('CoalawebtrafficHelper', JPATH_COMPONENT . '/helpers/coalawebtraffic.php');
JLoader::register('CoalawebtrafficHelperLocation', JPATH_COMPONENT . '/helpers/location.php');

/**
 * View class for Geo Upload
 */
class CoalawebtrafficViewGeoupload extends JViewLegacy
{
    /**
     * Display the view
     *
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return void
     */
    public function display($tpl = null) 
    {
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }
                
        // Load version.php
        $version_php = JPATH_COMPONENT_ADMINISTRATOR . '/version.php';
        if (!defined('COM_CWTRAFFIC_VERSION') && JFile::exists($version_php)) {
            include_once $version_php;
        }
        
        // Is this the Professional release?
        $isPro = (COM_CWTRAFFIC_PRO == 1);
        $this->isPro = $isPro;
        
        // What geo location to use
        $geodb = $isPro ? 'geoip/v2' : 'geoip';
        $dbName = $isPro ? JText::_('COM_CWTRAFFIC_GEO_PRO') : JText::_('COM_CWTRAFFIC_GEO_CORE');
        
        if (CoalawebtrafficHelperLocation::geodatExist($geodb, $isPro )) {
            $mymod = CoalawebtrafficHelperLocation::geodatMod($geodb, $isPro );
            $this->geoMessage = JText::sprintf('COM_CWTRAFFIC_YESGEO_UPLOAD_MESSAGE', $dbName, $mymod);
        } else {
            $this->geoMessage = JText::_('COM_CWTRAFFIC_NOGEO_UPLOAD_MESSAGE');
        }

        CoalawebtrafficHelper::addSubmenu('geoupload');

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this->addToolBar();
            $this->sidebar = JHtmlSidebar::render();
        }
        $this->jsOptions['url'] = JURI::base();
        
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     */
    protected function addToolbar() 
    {
        $canDo = JHelperContent::getActions('com_coalawebtraffic');

        $title = COM_CWTRAFFIC_PRO == 1 ? JText::_('COM_CWTRAFFIC_TITLE_PRO') : JText::_('COM_CWTRAFFIC_TITLE_CORE');
        JToolBarHelper::title($title . ' [ ' . JText::_('COM_CWTRAFFIC_TITLE_GEO') . ' ]', 'location');

        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Link', 'wrench', 'COM_CWTRAFFIC_TITLE_CPANEL', 'index.php?option=com_coalawebtraffic');

        if ($canDo->get('core.admin')) {
            if (COM_CWTRAFFIC_PRO == 1 && $this->filesExist('archivesv2')) {
                JToolBarHelper::custom('geoupload.unzip', 'box-add', 'box-add', 'Unzip', false);
            } elseif ($this->filesExist('archives')) {
                JToolBarHelper::custom('geoupload.unzip', 'box-add', 'box-add', 'Unzip', false);
            }
        }
        
        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_coalawebtraffic');
        }

        $help_url = 'http://coalaweb.com/support/documentation/item/coalaweb-traffic-guide';
        JToolBarHelper::help('COM_CWTRAFFIC_TITLE_HELP', false, $help_url);
    }

    /**
     * Checks if folder + files exist in the com_coalawebtraffic tmp path
     *
     * @param  $type
     * @return bool
     */
    private function filesExist($type) 
    {
        $path = JFactory::getConfig()->get('tmp_path') . '/com_coalawebtraffic/' . $type;
        if (JFolder::exists($path)) {
            if (JFolder::folders($path, '.', false) || JFolder::files($path, '.', false)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if Curl is installed
     *
     * @return boolean
     */
    function curlInstalled() 
    {
        if (in_array('curl', get_loaded_extensions())) {
            return true;
        } else {
            return false;
        }
    }

}
