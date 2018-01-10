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

jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_coalawebtraffic')) {
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
}

$lang = JFactory::getLanguage();
if ($lang->getTag() != 'en-GB') {
    $lang->load('com_coalawebtraffic', JPATH_ADMINISTRATOR, 'en-GB');
}
$lang->load('com_coalawebtraffic', JPATH_ADMINISTRATOR, null, 1);

//Our helpers
JLoader::register('CoalawebtrafficHelper', dirname(__FILE__) . '/helpers/coalawebtraffic.php');
JLoader::register('CoalawebtrafficHelperLocation', dirname(__FILE__) . '/helpers/location.php');

//Lets check if our classes exist and if not display a nice graceful message
if (
    !class_exists('CoalawebtrafficHelper') ||
    !class_exists('CoalawebtrafficHelperLocation')){

    JFactory::getApplication()->enqueueMessage(JText::_('COM_CWTRAFFIC_MSG_MISSING'), 'error');
    return;
}

// Lets get some style
JFactory::getDocument()->addStyleSheet("../media/coalawebtraffic/components/traffic/css/com-traffic-base.css");

// Check count plugin
if (JPluginHelper::isEnabled('system', 'cwtrafficcount', true) == false) {
    echo JText::_('COM_CWTRAFFIC_NOCOUNTPLUGIN_GENERAL_MESSAGE');
}
// Check clean plugin
if (JPluginHelper::isEnabled('system', 'cwtrafficclean', true) == false) {
    echo JText::_('COM_CWTRAFFIC_NOCLEANPLUGIN_GENERAL_MESSAGE');
}

// Lets make sure CoalaWeb Gears is loaded
$cwgp = JPluginHelper::getPlugin('system', 'cwgears');
if (!isset($cwgp->name)) {
    JFactory::getApplication()->set('_messageQueue', '');
    $msg = JText::_('COM_CWTRAFFIC_NOGEARSPLUGIN_GENERAL_MESSAGE');
    JFactory::getApplication()->enqueueMessage($msg, 'notice');
}

// Load version.php
$version_php = JPATH_COMPONENT_ADMINISTRATOR . '/version.php';
if (!defined('COM_CWTRAFFIC_VERSION') && JFile::exists($version_php)) {
    include_once $version_php;
}
$comParams = JComponentHelper::getParams('com_coalawebtraffic');
// Update location info for our visitors
if ($comParams->get('store_location', 1)) {
    if (COM_CWTRAFFIC_PRO == 1) {
        if (CoalawebtrafficHelperLocation::geodatExist('geoip/v2', TRUE)) {
            CoalawebtrafficHelperLocation::location_updatev2();
        }
    } else {
        if (CoalawebtrafficHelperLocation::geodatExist('geoip', FALSE)) {
            CoalawebtrafficHelperLocation::location_update();
        }
    }
}

// Include dependancies
$controller = JControllerLegacy::getInstance('Coalawebtraffic');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>
<div class="cw-powerby-back">
    <span class="cw-powerby-back">
        <?php echo JTEXT::_('COM_CWTRAFFIC_POWEREDBY_MSG'); ?> <a href="http://www.coalaweb.com" target="_blank" title="CoalaWeb">CoalaWeb</a> <?php
        echo JTEXT::_('COM_CWTRAFFIC_POWEREDBY_VERSION');
        if (COM_CWTRAFFIC_PRO == 1) {
            echo COM_CWTRAFFIC_VERSION . ' ' . JTEXT::_('COM_CWTRAFFIC_POWEREDBY_PRO');
        } else {
            echo COM_CWTRAFFIC_VERSION;
        }
        ?>
    </span>
</div>
