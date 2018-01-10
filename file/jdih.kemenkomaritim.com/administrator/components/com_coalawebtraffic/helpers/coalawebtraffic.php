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

/**
 *  CoalaWeb Traffic component helper.
 */
class CoalawebtrafficHelper
{

    /**
     * Configure the Linkbar.
     *
     * @param string $vName The name of the active view.
     *
     * @return void
     */
    public static function addSubmenu($vName = 'controlpanel') 
    {
        // Load version.php
        $version_php = JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/version.php';
        if (!defined('COM_CWTRAFFIC_VERSION') && JFile::exists($version_php)) {
            include_once $version_php;
        }

        JHtmlSidebar::addEntry(
            JText::_('COM_CWTRAFFIC_TITLE_CPANEL'), 'index.php?option=com_coalawebtraffic&view=controlpanel', $vName == 'controlpanel'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_CWTRAFFIC_TITLE_VISITORS'), 'index.php?option=com_coalawebtraffic&view=visitors', $vName == 'visitors'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_CWTRAFFIC_TITLE_IPCATS'), 'index.php?option=com_categories&extension=com_coalawebtraffic', $vName == 'categories'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_CWTRAFFIC_TITLE_KNOWNIPS'), 'index.php?option=com_coalawebtraffic&view=knownips', $vName == 'knownips'
        );
        JHtmlSidebar::addEntry(
            JText::_('COM_CWTRAFFIC_TITLE_GEO'), 'index.php?option=com_coalawebtraffic&view=geoupload', $vName == 'geoupload'
        );
        if (COM_CWTRAFFIC_PRO == 1){
            JHtmlSidebar::addEntry(
                JText::_('COM_CWTRAFFIC_TITLE_CHARTS'), 'index.php?option=com_coalawebtraffic&view=charts', $vName == 'charts'
            );
        }

        JHtmlSidebar::addEntry(
            JText::_('COM_CWTRAFFIC_TITLE_MANAGE'), 'index.php?option=com_coalawebtraffic&view=manage', $vName == 'manage'
        );
        
    }

}
