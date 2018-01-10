<?php
defined('_JEXEC') or die('Restricted access');
/**
 * @package             Joomla
 * @subpackage          CoalaWeb Traffic Module
 * @author              Steven Palmer
 * @author url          http://coalaweb.com
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /files/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * The CoalaWeb traffic module was inspired by VCNT Thanks to Viktor Vogel {@link http://joomla-extensions.kubik-rubik.de/}
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
require_once dirname(__FILE__) . '/helper.php';
jimport('joomla.html.html');
jimport('joomla.utilities.date');
jimport('joomla.filesystem.file');

//Helpers
$path = '/components/com_coalawebtraffic/helpers/';
JLoader::register('CoalawebtrafficHelperIptools', JPATH_ADMINISTRATOR . $path . 'iptools.php');
JLoader::register('CoalawebtrafficHelperDetect', JPATH_ADMINISTRATOR . $path . 'detect.php');

$app = JFactory::getApplication();
//Lets check if our classes exist and if not display a nice graceful message
if (
    !class_exists('CoalawebtrafficHelperIptools') ||
    !class_exists('CoalawebtrafficHelperDetect')) {

    $app->enqueueMessage(JText::_('COM_CWTRAFFIC_MSG_MISSING'), 'error');
    return;
}

//Load the module language strings
if ($lang->getTag() != 'en-GB') {
    $lang->load('mod_coalawebtraffic', JPATH_SITE, 'en-GB');
}
$lang->load('mod_coalawebtraffic', JPATH_SITE, null, 1);

//Load the component language strings
if ($lang->getTag() != 'en-GB') {
    $lang->load('com_coalawebtraffic', JPATH_ADMINISTRATOR, 'en-GB');
}
$lang->load('com_coalawebtraffic', JPATH_ADMINISTRATOR, null, 1);

//Digital Counter
$sDigital = $params->get('s_digital', 1);
//Individual Counters
$sIndividual = $params->get('s_individual', 1);
$today = $params->get('today', JText::_('MOD_CWTRAFFIC_TODAY'));
$yesterday = $params->get('yesterday', JText::_('MOD_CWTRAFFIC_YESTERDAY'));
$all = $params->get('all', JText::_('MOD_CWTRAFFIC_TOTAL'));
$preset = $params->get('preset', 0);
$x_month = $params->get('month', JText::_('MOD_CWTRAFFIC_MONTH'));
$x_week = $params->get('week', JText::_('MOD_CWTRAFFIC_WEEK'));
$s_today = $params->get('s_today', 1);
$s_yesterday = $params->get('s_yesterday', 1);
$s_all = $params->get('s_all', 1);
$s_week = $params->get('s_week', 1);
$s_month = $params->get('s_month', 1);
$horizontal = $params->get('horizontal');
$separator = $params->get('separator');
$horDigital = $params->get('hor_digital', 1);
$sHorText = $params->get('s_hor_text', 1);
$hor_text = $params->get('hor_text', JText::_('MOD_CWTRAFFIC_HORTEXT'));
$select_theme = $params->get('select_theme', 'users');
$hline = $params->get('hline', 1);
$cssWidth = $params->get('css_width', '100');
$counterWidth = $params->get('counter_width', '100');
$uniqueId = $module->id;

//Lets get the count from the helper file
$start = new mod_coalawebtrafficHelper;
list($all_visitors, $today_visitors, $yesterday_visitors, $week_visitors, $month_visitors) = $start->read($params);
$digitalCounter = mod_coalawebtrafficHelper::getTotalImage($params, $all_visitors);

//Lets get the visitors IP address
$ip = '';
$ipgrab = CoalawebtrafficHelperIptools::getUserIp();
if (!empty($ipgrab)) {
    $ip = $ipgrab;
} else {
    $ip = JText::_('MOD_CWTRAFFIC_UNKOWNIP');
}

//Let's get the visitors Browser and OS info. 
$client = JFactory::getApplication()->client;
if (!empty($client)) {
    $browser = CoalawebtrafficHelperDetect::getName($client->browser);
    $browserVersion = $client->browserVersion;
    $platform = CoalawebtrafficHelperDetect::getName($client->platform);
} else {
    $browser = JText::_('COM_CWTRAFFIC_LOCATION_UNKNOWN');
    $browserVersion = JText::_('COM_CWTRAFFIC_LOCATION_UNKNOWN');
    $platform = JText::_('COM_CWTRAFFIC_LOCATION_UNKNOWN');
}

//Get some style
$document = JFactory::getDocument();
if ($params->get('load_css', 1)){
    $document->addStyleSheet(JURI::base(true)  . '/media/coalawebtraffic/modules/traffic/css/cwt-base.css');
}
    
$document->addStyleSheet(JURI::base(true)  . '/media/coalawebtraffic/modules/traffic/counter-themes/' . $select_theme . '/css/cw-visitors.css');

//Visitor info
$sVisitorInfo = $params->get('s_visitor_info', 1);
$hlineVisitor = $params->get('hline_visitor', 1);
$tFormatVisit = $params->get('title_format_visitor', '3');
$tAlignVisit = $params->get('title_align_visitor', 'cwt-vi-title-ac');
$sTitleVisit = $params->get('display_title_visitor', 1);
$titleVisitor = $params->get('title_visitor', JTEXT::_('MOD_CWTRAFFIC_TITLE_VISITOR'));
$s_guestip = $params->get('s_guestip', 1);
$guestip = $params->get('guestip', JText::_('MOD_CWTRAFFIC_VISITOR_IP'));
$sGuestBrowser = $params->get('s_guest_browser', 1);
$guestBrowser = $params->get('guest_browser', JText::_('MOD_CWTRAFFIC_V_BROWSER'));
$guestBrowserV = $params->get('guest_browser_v', JText::_('MOD_CWTRAFFIC_V_BROWSER_VERSION'));
$sGuestOs = $params->get('s_guest_os', 1);
$guestOs = $params->get('guest_os', JText::_('MOD_CWTRAFFIC_V_OS'));


//Who is online
$title_who = $params->get('title_who', JTEXT::_('MOD_CWTRAFFIC_TITLE_WHO'));
$subtitle_who = $params->get('subtitle_who', JTEXT::_('MOD_CWTRAFFIC_SUBTITLE_WHO'));
$s_whoisonline = $params->get('s_whoisonline', 1);
$display_title_who = $params->get('display_title_who', 1);
$hlineWho = $params->get('hline_who', 1);
$title_align_who = $params->get('title_align_who', 'cwt-wio-title-ac');
$title_format_who = $params->get('title_format_who', '3');

$realCount = mod_coalawebtrafficHelper::getRealCount();

//Advanced Options
$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$dateTimeFormat = $params->get('dateTimeFormat', 'LC4');
$s_dateTime = $params->get('s_dateTime', 1);

//Set the date time format
switch ($dateTimeFormat) {
    case 'LC1':
        $date = JHtml::date($input = 'now', JText::_('DATE_FORMAT_LC1'), false);
        break;
    case 'LC2':
        $date = JHtml::date($input = 'now', JText::_('DATE_FORMAT_LC2'), false);
        break;
    case 'LC3':
        $date = JHtml::date($input = 'now', JText::_('DATE_FORMAT_LC3'), false);
        break;
    case 'LC4':
        $date = JHtml::date($input = 'now', JText::_('DATE_FORMAT_LC4'), false);
        break;
    case'JS1':
        $date = JHtml::date($input = 'now', JText::_('DATE_FORMAT_JS1'), false);
        break;
}

require JModuleHelper::getLayoutPath('mod_coalawebtraffic', $params->get('layout', 'default'));
