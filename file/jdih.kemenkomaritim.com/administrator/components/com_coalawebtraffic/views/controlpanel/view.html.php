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
jimport('joomla.filesystem.file');

// Helpers
JLoader::register('CoalawebtrafficHelper', JPATH_COMPONENT . '/helpers/coalawebtraffic.php');
JLoader::register('CoalawebtrafficHelperLocation', JPATH_COMPONENT . '/helpers/location.php');

$latestVersion = JPATH_SITE . '/plugins/system/cwgears/helpers/latestversion.php';
if (JFile::exists($latestVersion) && !class_exists('CwGearsLatestversion')) {
    JLoader::register('CwGearsLatestversion', $latestVersion);
}

/**
 * View class for control panel
 */
class CoalawebtrafficViewControlpanel extends JViewLegacy
{

    /**
     * Display the view
     *
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return void
     */
    function display($tpl = null) 
    {
        $model = $this->getModel();
        
        //Get stats
        $countries = $model->getCountries();
        $cities = $model->getCities();
        $stats = $model->getStats();
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }

        CoalawebtrafficHelper::addSubmenu('controlpanel');

        // Is this the Pro release
        $isPro = (COM_CWTRAFFIC_PRO == 1);
        $this->isPro = $isPro;
        
        // The curent version and release date
        $version = (COM_CWTRAFFIC_VERSION);
        $this->version = $version;
        
        //Release date
        $releaseDate = (COM_CWTRAFFIC_DATE);
        $this->release_date = $releaseDate;
        
        //Need a download ID?
        $needsDlid = $model->needsDownloadID();
        $this->needsdlid = $needsDlid;

        // Stats summary
        $this->countries = $countries;
        $this->cities = $cities;
        $this->stats = $stats;
 
        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this->addToolbar();
        }
        
        // What geo location to use
        $geodb = $isPro ? 'geoip/v2' : 'geoip';
        if (CoalawebtrafficHelperLocation::geodatExist($geodb, $isPro )) {
            $mymod = CoalawebtrafficHelperLocation::geodatMod($geodb, $isPro );
            $this->geoExist = true;
        } else {
            $this->geoExist  = false;
        }

        // Lets check the current version against the latest
        $type = $isPro ? 'pro' : 'core';
        if (class_exists('CwGearsLatestversion')) {
            $this->current = CwGearsLatestversion::getCurrent('cw-traffic-'. $type, $version );
        } else {
            $this->current = [
                'remote' => JText::_('COM_CWTRAFFIC_NO_FILE'),
                'update' => ''
            ]; 
        }
        
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

        // Is this the Pro release
        $isPro = (COM_CWTRAFFIC_PRO == 1);
        
        // What geo db location to use
        $geodb = $isPro ? 'geoip/v2' : 'geoip';
        
        if (!CoalawebtrafficHelperLocation::geodatExist($geodb, $isPro )) {
            echo JText::_('COM_CWTRAFFIC_NOGEO_CPANEL_MESSAGE');
        }

        $title = $isPro ? JText::_('COM_CWTRAFFIC_TITLE_PRO') : JText::_('COM_CWTRAFFIC_TITLE_CORE');
        JToolBarHelper::title($title . ' [ ' . JText::_('COM_CWTRAFFIC_TITLE_CPANEL') . ' ]', 'cogs');
        
        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_coalawebtraffic');
        }

        $help_url = 'http://coalaweb.com/support/documentation/item/coalaweb-traffic-guide';
        JToolBarHelper::help('COM_CWTRAFFIC_TITLE_HELP', false, $help_url);

    }

}
