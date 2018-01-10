<?php
defined('_JEXEC') or die('Restricted access');
/**
 * @package             Joomla
 * @subpackage          CoalaWeb Traffic Component
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
 * 
 */
jimport('joomla.application.component.helper');
jimport('joomla.log.log');

class mod_coalawebtrafficHelper {

    // Function - Counting Data
    function read(&$params) {
        $comParams = JComponentHelper::getParams('com_coalawebtraffic');
        $db = JFactory::getDbo();

        if ($comParams->get('log_sql')) {
            //Start our log file code
            JLog::addLogger(array('text_file' => 'com_coalawebtraffic_sql.log.php'), JLog::ERROR, 'com_coalawebtraffic_sql');
        }

        //Work out the time off set
        $config = JFactory::getConfig();

            $siteOffset = $config->get('offset');
            date_default_timezone_set($siteOffset);
        

        $day = date('d');
        $month = date('m');
        $year = date('Y');
        
        $daystart = mktime(0, 0, 0, $month, $day, $year);
        $monthstart = mktime(0, 0, 0, $month, 1, $year);
        $yesterdaystart = $daystart - (24 * 60 * 60);
        
        $weekDayStart = $comParams->get('week_start', 'mon');
        
        switch ($weekDayStart) {
            case 'sat':
                $weekstart = $daystart - ((date('N') + 1) * 24 * 60 * 60);
                break;
            case 'sun':
                $weekstart = $daystart - (date('N') * 24 * 60 * 60);
                break;
            case 'mon':
                $weekstart = $daystart - ((date('N') - 1) * 24 * 60 * 60);
                break;
            default:
                $weekstart = $daystart - ((date('N') - 1) * 24 * 60 * 60);
                break;
        }

        $preset = $comParams->get('preset', 0);

        //Count ongoing total
        $query = $db->getQuery(true);
        $query->select('TCOUNT');
        $query->from($db->quoteName('#__cwtraffic_total'));
        $db->setQuery($query);

        try {
            $tcount = $db->loadResult();
        } catch (Exception $e) {
            $tcount = '';
            if ($comParams->get('log_sql')) {
                //Log error
                $msg = JText::sprintf('MOD_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
            }
        }

        // Create base to count from
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from($db->quoteName('#__cwtraffic'));
        $db->setQuery($query);

        try {
            $all_visitors = $db->loadResult();
        } catch (Exception $e) {
            $all_visitors = '';
            if ($comParams->get('log_sql')) {
                //Log error
                $msg = JText::sprintf('MOD_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
            }
        }

        $all_visitors += $preset;
        $all_visitors += $tcount;

        //Todays Visitors
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from($db->quoteName('#__cwtraffic'));
        $query->where('tm > ' . $db->quote($daystart));
        $db->setQuery($query);

        try {
            $today_visitors = $db->loadResult();
        } catch (Exception $e) {
            $today_visitors = '';
            if ($comParams->get('log_sql')) {
                //Log error
                $msg = JText::sprintf('MOD_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
            }
        }

        //Yesterdays Visitors
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from($db->quoteName('#__cwtraffic'));
        $query->where('tm > ' . $db->quote($yesterdaystart));
        $query->where('tm < ' . $db->quote($daystart));
        $db->setQuery($query);

        try {
            $yesterday_visitors = $db->loadResult();
        } catch (Exception $e) {
            $yesterday_visitors = '';
            if ($comParams->get('log_sql')) {
                //Log error
                $msg = JText::sprintf('MOD_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
            }
        }

        //This Weeks Visitors
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from($db->quoteName('#__cwtraffic'));
        $query->where('tm >= ' . $db->quote($weekstart));
        $db->setQuery($query);

        try {
            $week_visitors = $db->loadResult();
        } catch (Exception $e) {
            $week_visitors = '';
            if ($comParams->get('log_sql')) {
                //Log error
                $msg = JText::sprintf('MOD_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
            }
        }

        //Months Visitors
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from($db->quoteName('#__cwtraffic'));
        $query->where('tm >= ' . $db->quote($monthstart));
        $db->setQuery($query);

        try {
            $month_visitors = $db->loadResult();
        } catch (Exception $e) {
            $month_visitors = '';
            if ($comParams->get('log_sql')) {
                //Log error
                $msg = JText::sprintf('MOD_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
            }
        }

        $ret = array($all_visitors, $today_visitors, $yesterday_visitors, $week_visitors, $month_visitors);
        return ($ret);
    }


    // Who is online
    static function getRealCount() {
        $comParams = JComponentHelper::getParams('com_coalawebtraffic');
        $db = JFactory::getDbo();

        if ($comParams->get('log_sql')) {
            //Start our log file code
            JLog::addLogger(array('text_file' => 'com_coalawebtraffic_sql.log.php'), JLog::ERROR, 'com_coalawebtraffic_sql');
        }

        //Check the Who is Online table for IPs
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from($db->quoteName('#__cwtraffic_whoisonline'));
        $db->setQuery($query);

        try {
            $result = $db->loadResult();
        } catch (Exception $e) {
            $result = '';
            if ($comParams->get('log_sql')) {
                //Log error
                $msg = JText::sprintf('MOD_CWTRAFFICSTATS_DATABASE_ERROR', $e->getMessage());
                JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
            }
        }

        return $result;
    }

    //Lets create the digital counter
    public static function getTotalImage($params, $totalNumber) {
        $digitTheme = $params->get('digit_theme');
        $digitNumber = $params->get('digit_number');
        
        $arrNumber = mod_coalawebtrafficHelper::getArrayNumber($digitNumber, $totalNumber);

        $html = '';
        if ($arrNumber) {
            foreach ($arrNumber as $number) {
                $html .= mod_coalawebtrafficHelper::getDigitImage($number, $digitTheme);
            }
        }

        return $html;
    }

    static function getArrayNumber($length, $number) {
        $strlen = strlen($number);

        $arr = array();
        $diff = $length - $strlen;

        while ($diff > 0) {
            array_push($arr, 0);
            $diff--;
        }

        $arrNumber = str_split($number);
        $arr = array_merge($arr, $arrNumber);

        return $arr;
    }

    static function getDigitImage($number, $type) {
        $html = '';
        $html .= '<img class="" src="' . JURI :: base(true) . '/media/coalawebtraffic/modules/traffic/digit-themes/' . $type . '/' . $number . '.png" alt="'. $number . '.png"/>';
        return $html;
    }

}
