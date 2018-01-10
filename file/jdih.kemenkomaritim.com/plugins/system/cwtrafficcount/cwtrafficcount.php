<?php
defined('_JEXEC') or die('Restricted access');
/**
 * @package             Joomla
 * @subpackage          CW Traffic Count Plugin
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


class plgSystemCwtrafficCount extends JPlugin {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);

        // load the CoalaWeb Traffic language file
        $lang = JFactory::getLanguage();
        if ($lang->getTag() != 'en-GB') {
            // Loads English language file as fallback (for undefined stuff in other language file)
            $lang->load('plg_system_cwtrafficcount', JPATH_ADMINISTRATOR, 'en-GB');
        }
        $lang->load('plg_system_cwtrafficcount', JPATH_ADMINISTRATOR, null, 1);

        //Load the component language strings
        if ($lang->getTag() != 'en-GB') {
            $lang->load('com_coalawebtraffic', JPATH_ADMINISTRATOR, 'en-GB');
        }
        $lang->load('com_coalawebtraffic', JPATH_ADMINISTRATOR, null, 1);
    }

    public function onAfterRoute() {
        $app = JFactory::getApplication();
        $comParams = JComponentHelper::getParams('com_coalawebtraffic');

        if ($app->isSite()) {

            //Lets check if our classes exist and if not display a nice graceful message
            if (
                !class_exists('CoalawebtrafficHelperIptools') ||
                !class_exists('CoalawebtrafficHelperLocation') ||
                !class_exists('CoalawebtrafficHelperDetect')) {
                
                $app->enqueueMessage(JText::_('COM_CWTRAFFIC_MSG_MISSING'), 'error');
                return;
            }

            if ($comParams->get('log_sql')) {
                //Start our log file code
                JLog::addLogger(array('text_file' => 'plg_cwtraffic_count.log.php'), JLog::ALL, 'com_coalawebtraffic');
            }

            $siteOffset = JFactory::getApplication()->getCfg('offset');
            
            // Load version.php
            $version_php = JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/version.php';
            if (!defined('COM_CWTRAFFIC_VERSION') && JFile::exists($version_php)) {
                include_once $version_php;
            }

            //Let's get the visitors Browser and OS info
            $client = JFactory::getApplication()->client;
            if (!empty($client)) {
                if ($client->robot){
                    $this->browser = JText::_('COM_CWTRAFFIC_IS_ROBOT');
                    $this->bversion = JText::_('COM_CWTRAFFIC_LOCATION_UNKNOWN');
                    $platform = JText::_('COM_CWTRAFFIC_LOCATION_UNKNOWN');
                } else {
                    $this->browser = CoalawebtrafficHelperDetect::getName($client->browser);
                    $this->bversion = $client->browserVersion;
                    $platform = CoalawebtrafficHelperDetect::getName($client->platform);
                }
            } else {
                $this->browser = JText::_('COM_CWTRAFFIC_LOCATION_UNKNOWN');
                $this->bversion = JText::_('COM_CWTRAFFIC_LOCATION_UNKNOWN');
                $platform = JText::_('COM_CWTRAFFIC_LOCATION_UNKNOWN');
            }

            if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != "") {
                $referer = $_SERVER['HTTP_REFERER'];
            } else {
                $referer = JText::_('COM_CWTRAFFIC_UNKNOWN');
            }

            //Current date time
            $dtnow = JFactory::getDate('now', $siteOffset);
            $now = $dtnow->toUnix(true);

            //How long should an IP be locked for.
            $locktime = $comParams->get('locktime', 60) * 60;

            //Get the users IP
            $this->ip = CoalawebtrafficHelperIptools::getUserIp();

            //Start our database queries
            $db = JFactory::getDbo();

            //Check the Knownips table for IPs that are published and shouldn't be counted
            $query = $db->getQuery(true);
            $query->select('ip');
            $query->from($db->qn('#__cwtraffic_knownips'));
            $query->where('state = 1 AND count = 0');
            $db->setQuery($query);
            
            try {
                $ipTable = $db->loadColumn();
            } catch (Exception $e) {
                $ipTable = '';
                if ($comParams->get('log_sql')) {
                    //Log error
                    $msg = JText::sprintf('COM_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                    JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
                }
            }
            


            //Check the Known Ips returned from above against current user IP
            $iplock = 0;
            if (!empty($ipTable)) {
                if (class_exists('CoalawebtrafficHelperIptools')) {
                    $iplock = CoalawebtrafficHelperIptools::ipinList($this->ip, $ipTable);
                }
            } else {
                $iplock = 0;
            }

            //Check the Knownips table for Bots that are published and shouldn't be counted
            $query = $db->getQuery(true);
            $query->select('botname');
            $query->from($db->qn('#__cwtraffic_knownips'));
            $query->where('state = 1 AND count = 0');
            $db->setQuery($query);
            
            try {
                $botTable = $db->loadColumn();
            } catch (Exception $e) {
                $botTable = '';
                if ($comParams->get('log_sql')) {
                    //Log error
                    $msg = JText::sprintf('COM_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                    JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
                }
            }

            
            if ($client->userAgent) {
                $agent = $client->userAgent;
            } else {
                $agent = '';
            }

            $bot = 0;
            
            if (!empty($agent) && !empty($botTable)) {
                foreach ($botTable as $bot_value) {
                    if (!empty($bot_value)) {
                        if (stripos($agent, $bot_value) !== false) {
                            $bot = 1;
                            break;
                        }
                    }
                }
            }
            
            //Check for bot using JApplicationWebClient
            if (!empty($client) && $comParams->get('basic_bots', 0)) {
                if ($client->robot){
                    $bot = 1;
                }
            }
           

            //Check with Project Honeypot
            if ($comParams->get('honeypot')) {
                $hp = self::checkHoneypot($this->ip);
            } else {
                $hp = false;
            }

            $storeRaw = $comParams->get('store_raw', 1);
            $storeLocation = $comParams->get('store_location', 1);
            
            // Check if IP already exists and reload lock expired
            $query = $db->getQuery(true);
            $query->select('count(*)');
            $query->from($db->qn('#__cwtraffic'));
            $query->where('iphash = UNHEX(SHA1(' . $db->q($this->ip) . '))');
            $query->where('tm + ' . $db->q($locktime) . '>' . $db->q($now));
            $db->setQuery($query);
            
            try {
                $items = $db->loadResult();
            } catch (Exception $e) {
                $items = '';
                if ($comParams->get('log_sql')) {
                    //Log error
                    $msg = JText::sprintf('COM_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                    JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
                }
            }

            // If all is good lets count the visitor.
            if (empty($items) AND $bot == 0 AND $iplock == 0 AND $hp == false) {
                
                //Lets get some GEO info on our IP
                if ($storeLocation) {
                        if (CoalawebtrafficHelperLocation::geodatExist('geoip', FALSE)) {
                            $details = CoalawebtrafficHelperLocation::whoisonlineUpdate($this->ip);
                            $countryCode = $details['country_code'];
                            $countryName = $details['country_name'];
                            $city = $details['city'];
                            $location_latitude = NULL;
                            $location_longitude = NULL;
                            $location_time_zone = NULL;
                            $continent_code = NULL;
                        } else {
                            $countryCode = NULL;
                            $countryName = NULL;
                            $city = NULL;
                            $location_latitude = NULL;
                            $location_longitude = NULL;
                            $location_time_zone = NULL;
                            $continent_code = NULL;
                        }

                } else {
                    $countryCode = NULL;
                    $countryName = NULL;
                    $city = NULL;
                    $location_latitude = NULL;
                    $location_longitude = NULL;
                    $location_time_zone = NULL;
                    $continent_code = NULL;
                }

                $query = $db->getQuery(true);
                $query->insert($db->qn('#__cwtraffic'));

                if ($storeRaw){
                    $query->columns('tm, ip, iphash, browser, bversion, platform, referer, country_code, country_name, city, location_latitude, location_longitude, location_time_zone, continent_code');
                    $query->values($db->q($now) . ',' . $db->q($this->ip) . ', UNHEX(SHA1(' . $db->q($this->ip) . ')),' . $db->q($this->browser) . ',' . $db->q($this->bversion) . ',' . $db->q($platform) . ',' . $db->q($referer) . ',' . $db->q($countryCode) . ',' . $db->q($countryName) . ',' . $db->q($city) . ',' . $db->q($location_latitude) . ',' . $db->q($location_longitude) . ',' . $db->q($location_time_zone) . ',' . $db->q($continent_code));
                } else {
                    $query->columns('tm, iphash, browser, bversion, platform, referer, country_code, country_name, city, location_latitude, location_longitude, location_time_zone, continent_code');
                    $query->values($db->q($now) . ', UNHEX(SHA1(' . $db->q($this->ip) . ')),' . $db->q($this->browser) . ',' . $db->q($this->bversion) . ',' . $db->q($platform) . ',' . $db->q($referer) . ',' . $db->q($countryCode) . ',' . $db->q($countryName) . ',' . $db->q($city) . ',' . $db->q($location_latitude) . ',' . $db->q($location_longitude) . ',' . $db->q($location_time_zone) . ',' . $db->q($continent_code));
                }

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

            // Update location info for our visitors
            if($storeLocation){
                if (CoalawebtrafficHelperLocation::geodatExist('geoip', FALSE)) {
                    CoalawebtrafficHelperLocation::location_update();
                }
            }
            
            return $query;
        }
    }

    private function checkHoneypot($reqip) {
        $comParams = JComponentHelper::getParams('com_coalawebtraffic');
        $honeypotKey = $comParams->get('honeypot_key');
        $minThreat = $comParams->get('honeypot_min');
        $maxAge = $comParams->get('honeypot_max');
        $suspicious = $comParams->get('honeypot_sus');
        $searchEng = $comParams->get('honeypot_seng');
        $block = false;

        // Make sure we have an HTTP:BL  key set
        if (empty($honeypotKey)) {
            return;
        }

        // Get the IP address
        if ($reqip == '0.0.0.0') {
            return false;
        }

        if (strpos($reqip, '::') === 0) {
            $reqip = substr($reqip, strrpos($reqip, ':') + 1);
        }

        // No point continuing if we can't get an address, right?
        if (empty($reqip)) {
            return false;
        }

        // IPv6 addresses are not supported by HTTP:BL yet
        if (strpos($reqip, ":")) {
            return false;
        }

        $lookup = $honeypotKey . '.' . implode('.', array_reverse(explode ('.', $reqip ))) . '.dnsbl.httpbl.org';
        $result = explode( '.', gethostbyname($lookup));
        
        if (!empty($result)) {
            return false;
        }
        
        if ($result[0] != 127) {
            return; // Make sure it's a valid response
        }
        
        if (($suspicious && ($result[3] == 1)) || ($result[3] >= 2)) {
            $block = true; //Is supicious or block if requested next block anything with 2 or above. 
        }

        $block = $block && ($result[1] <= $maxAge); //Check age against extension options
        $block = $block && ($result[2] >= $minThreat); //Check max threat against extension options.

        if ($searchEng && $result[3] == 0) {
            $block = true; //If requested block search engine regardless of max age and min threat.
        }
        
        return $block;
    }

}
