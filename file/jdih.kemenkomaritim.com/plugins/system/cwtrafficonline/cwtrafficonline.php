<?php
defined('_JEXEC') or die('Restricted access');
/**
 * @package             Joomla
 * @subpackage          CW Traffic Who is Online Plugin
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

jimport('joomla.application.component.helper');
jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.log.log');

//Helpers
$path = '/components/com_coalawebtraffic/helpers/';
JLoader::register('CoalawebtrafficHelperIptools', JPATH_ADMINISTRATOR . $path . 'iptools.php');
JLoader::register('CoalawebtrafficHelperLocation', JPATH_ADMINISTRATOR . $path . 'location.php');
JLoader::register('CoalawebtrafficHelperDetect', JPATH_ADMINISTRATOR . $path . 'detect.php');

class plgSystemCwtrafficOnline extends JPlugin
{

    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        // load the CoalaWeb Traffic language file
        $lang = JFactory::getLanguage();
        if ($lang->getTag() != 'en-GB') {
            // Loads English language file as fallback (for undefined stuff in other language file)
            $lang->load('plg_system_cwtrafficonline', JPATH_ADMINISTRATOR, 'en-GB');
        }
        $lang->load('plg_system_cwtrafficonline', JPATH_ADMINISTRATOR, null, 1);

        //Load the component language strings
        if ($lang->getTag() != 'en-GB') {
            $lang->load('com_coalawebtraffic', JPATH_ADMINISTRATOR, 'en-GB');
        }
        $lang->load('com_coalawebtraffic', JPATH_ADMINISTRATOR, null, 1);
    }

    public function onAfterRoute()
    {
        $app = JFactory::getApplication();
        $client = JFactory::getApplication()->client;
        $comParams = JComponentHelper::getParams('com_coalawebtraffic');

        if ($client->robot || $app->getName() !== 'site') {
            return;
        }

        //Lets check if our classes exist and if not display a nice graceful message
        if (
            !class_exists('CoalawebtrafficHelperIptools') ||
            !class_exists('CoalawebtrafficHelperLocation') ||
            !class_exists('CoalawebtrafficHelperDetect')
        ) {

            $app->enqueueMessage(JText::_('COM_CWTRAFFIC_MSG_MISSING'), 'error');
            return;
        }

        if ($comParams->get('log_sql')) {
            //Start our log file code
            JLog::addLogger(array('text_file' => 'com_coalawebtraffic_sql.log.php'), JLog::ERROR, 'com_coalawebtraffic_sql');
        }

        // Load version.php
        $version_php = JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/version.php';
        if (!defined('COM_CWTRAFFIC_VERSION') && JFile::exists($version_php)) {
            include_once $version_php;
        }

        //Get the users IP
        $stringIp = CoalawebtrafficHelperIptools::getUserIp();
        $intIp = ip2long($stringIp);

        $db = JFactory::getDbo();

        //Check the Who is Online table for IPs
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from($db->qn('#__cwtraffic_whoisonline'));
        $query->where('ip = ' . $db->q($intIp));
        $db->setQuery($query);

        try {
            $inDB = $db->loadResult();
        } catch (Exception $e) {
            $inDB = '';
            if ($comParams->get('log_sql')) {
                //Log error
                $msg = JText::sprintf('COM_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
            }
        }

        if (!$inDB) {
            if (isset($_COOKIE['cwGeoData'])) {
                $cookie = $_COOKIE['cwGeoData'];
            }

            if (!empty($cookie)) {
                // A "geoData" cookie has been previously set by the script, so we will use it
                // Always escape any user input, including cookies:
                list($city, $countryName, $countryCode) = explode('|', strip_tags($cookie));
            } else {

                //Lets get some info on our IP
                if (COM_CWTRAFFIC_PRO == 1) {
                    if (CoalawebtrafficHelperLocation::geodatExist('geoip/v2', TRUE)) {
                        $details = CoalawebtrafficHelperLocation::geoInfo($stringIp);
                    }
                } else {
                    if (CoalawebtrafficHelperLocation::geodatExist('geoip', FALSE)) {
                        $details = CoalawebtrafficHelperLocation::whoisonlineUpdate($stringIp);
                    }
                }

                $city = !empty($details['city']) ? $details['city'] : "unknown city";
                $countryName = !empty($details['country_name']) ? $details['country_name'] : "unknown country";
                $countryCode = !empty($details['country_code']) ? $details['country_code'] : "xx";

                // Setting a cookie with the data, which is set to expire in a month:
                setcookie('cwGeoData', $city . '|' . $countryName . '|' . $countryCode, time() + 60 * 60 * 24 * 30, '/');
            }

            $query = $db->getQuery(true);
            $query->insert($db->qn('#__cwtraffic_whoisonline'));
            $query->columns('ip,country_name, country_code, city,  dt');
            $query->values($db->q($intIp) . ',' . $db->q($countryName) . ',' . $db->q($countryCode) . ',' . $db->q($city) . ',NOW()');
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (Exception $e) {
                if ($comParams->get('log_sql')) {
                    //Log error
                    $msg = JText::sprintf('COM_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                    JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
                }
            }
        } else {
            // If the visitor is already online, just update the dt value of the row:
            $query = $db->getQuery(true);
            $query->update($db->qn('#__cwtraffic_whoisonline'));
            $query->set('dt = NOW()');
            $query->where('ip = ' . $db->q($intIp));
            $db->setQuery($query);

            try {
                $db->execute();
            } catch (Exception $e) {
                if ($comParams->get('log_sql')) {
                    //Log error
                    $msg = JText::sprintf('COM_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                    JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
                }
            }
        }
        $query = $db->getQuery(true);
        $query->delete($db->qn('#__cwtraffic_whoisonline'));
        $query->where("dt < SUBTIME(NOW(),'0 0:10:0')");
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (Exception $e) {
            if ($comParams->get('log_sql')) {
                //Log error
                $msg = JText::sprintf('COM_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
            }
        }
    }
}
