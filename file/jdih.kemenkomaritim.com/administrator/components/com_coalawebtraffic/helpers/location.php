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
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 *  CoalaWeb Traffic component helper.
 */
class CoalawebtrafficHelperLocation
{

    /**
     * Update Visitor info to include country and city
     * 
     * @return void
     */
    public static function location_update() 
    {

        $datLocation = JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/assets/geoip/GeoLiteCity.dat';

        $db = JFactory::getDbo();

        if (file_exists($datLocation)) {
            if (filesize($datLocation) != 0) {
                if (!function_exists('GeoIP_record_by_addr')) {
                    include JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/assets/geoip/geoipcity.inc';
                }

                // Open GEO database
                $gi = geoip_open($datLocation, GEOIP_STANDARD);

                $query = $db->getQuery(true);
                
                $query->select('id, ip');
                $query->from($db->quoteName('#__cwtraffic'));
                $query->where('country_code = "" OR country_code is null');
                $db->setQuery($query);

                foreach ($db->loadObjectList() as $row) {

                    // Get location info based on IP V4
                    // If we don't have any data skip
                    if (!$record = geoip_record_by_addr($gi, $row->ip)) {
                        continue;
                    }

                    // Assign info to variables to be recorded
                    $country_code = strtolower($record->country_code);
                    $country_name = $record->country_name;
                    $city = isset($record->city) ? $record->city : null;

                    if ($country_code != '' && $country_name != '') {

                        $query = $db->getQuery(true);
                        $query->update('#__cwtraffic');
                        $query->set('country_code = ' . $db->quote($country_code));
                        $query->set('country_name = ' . $db->quote($country_name));
                        $query->set('city = ' . $db->quote($city));
                        $query->where('id = ' . $row->id);
                        $db->setQuery($query);
                        
                        try {
                            $db->execute();
                        } catch (Exception $exc) {
                            // Nothing
                        }
                    }
                }

                // Close GEO database
                geoip_close($gi);

                $query = $db->getQuery(true);
                $query->update('#__cwtraffic');
                $query->set('city = NULL');
                $query->where('city = ""');
                $db->setQuery($query);
                
                try {
                    $db->execute();
                } catch (Exception $exc) {
                    // Nothing
                }
            }
        }
        return;
    }
    
    
    /**
     * Update Visitor info to include country and city
     * 
     * @param type $ip
     * 
     * @return void
     */
    public static function whoisonlineUpdate($ip) 
    {

        $datLocation = JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/assets/geoip/GeoLiteCity.dat';

        if (file_exists($datLocation)) {
            if (filesize($datLocation) != 0) {

                if (!function_exists('GeoIP_record_by_addr')) {
                    include JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/assets/geoip/geoipcity.inc';
                }

                // Open GEO database
                $gi = geoip_open($datLocation, GEOIP_STANDARD);

                // Get location info based on IP V4
                // If we don't have any data, abort
                if (!$record = geoip_record_by_addr($gi, $ip)) {
                    return false;
                }

                // Assign info to variables to be recorded
                $country_code = strtolower($record->country_code);
                $country_name = $record->country_name;
                $city = isset($record->city) ? $record->city : NULL;

                // Close GEO database
                geoip_close($gi);
            
            }

            return array(
                'country_code' => $country_code,
                'country_name' => $country_name,
                'city' => $city
            );
        }
        return;
    }
    
    /**
     * Checks if GeoLiteCity.dat file exists
     *
     * @param  $geo
     * @return bool
     */
    public static function geodatExist($geo, $v) 
    {
        self::renamedb();
                
        if ($v) {
            $path = JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/assets/' . $geo;
            if (JFolder::files($path, 'GeoLite2-City.mmdb', false)) {
                return true;
            }
        } else {
            //Let check if GeoIp already exsit on the server.
            if (function_exists('geoip_record_by_name')) {
                return true;
            }
            //If not lets check ours.
            $path = JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/assets/' . $geo;
            if (JFolder::files($path, 'GeoLiteCity.dat', false)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns modified date for file
     *
     * @param  $geo
     * @return $mod
     */
    public static function geodatMod($geo, $v) 
    {
        if ($v) {
            $path = JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/assets/' . $geo;
            $mod = date("d m Y", filemtime($path . '/GeoLite2-City.mmdb'));
        
        } else {
            //Let check if GeoIp already exsit on the server and reflect that in the date.
            if (function_exists('geoip_record_by_name')) {
                return date("F d Y", filemtime(geoip_db_filename(GEOIP_COUNTRY_EDITION)));
            }
            //If not lets update the date on ours.   
            $path = JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/assets/' . $geo;
            $mod = date("d m Y", filemtime($path . '/GeoLiteCity.dat'));
        }

        return $mod;
    }
    
        /**
     * Function to rename old lowercase GEO DB files
     */
    private static function renamedb() {

        //Rename Core GEO DB
        $path = JPATH_ADMINISTRATOR . '/components/com_coalawebtraffic/assets/geoip';
        $old = 'geolitecity.dat';
        $new = 'GeoLiteCity.dat';

        if (JFolder::files($path, $old, false)) {
            try {
                rename($path . '/' . $old, $path . '/' . $new);
            } catch (Exception $exc) {
                // Nothing
            }
        }
    }

}