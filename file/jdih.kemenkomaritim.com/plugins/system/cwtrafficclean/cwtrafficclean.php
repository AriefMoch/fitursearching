<?php

defined('_JEXEC') or die('Restricted access');
/**
 * @package             Joomla
 * @subpackage          CW Traffic Clean Plugin
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
jimport('joomla.log.log');

class plgSystemCwtrafficClean extends JPlugin {

    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);

        // load the CoalaWeb Traffic language file
        $lang = JFactory::getLanguage();
        if ($lang->getTag() != 'en-GB') {
            // Loads English language file as fallback (for undefined stuff in other language file)
            $lang->load('plg_system_cwtrafficclean', JPATH_ADMINISTRATOR, 'en-GB');
        }
        $lang->load('plg_system_cwtrafficclean', JPATH_ADMINISTRATOR, null, 1);

        //Load the component language strings
        if ($lang->getTag() != 'en-GB') {
            $lang->load('com_coalawebtraffic', JPATH_ADMINISTRATOR, 'en-GB');
        }
        $lang->load('com_coalawebtraffic', JPATH_ADMINISTRATOR, null, 1);
    }

    public function onAfterRoute() {
        $app = JFactory::getApplication();
        $comParams = JComponentHelper::getParams('com_coalawebtraffic');
        $dbClean = $comParams->get('db_clean');
        $duplicateClean = $comParams->get('duplicate_clean');
        $dbKeep = $comParams->get('db_keep', '1');
        $storeRaw = $comParams->get('store_raw', '1');

        if ($app->isSite()) {

            if ($comParams->get('log_sql')) {
                //Start our log file code
                JLog::addLogger(array('text_file' => 'com_coalawebtraffic_sql.log.php'), JLog::ERROR, 'com_coalawebtraffic_sql');
            }

            $db = JFactory::getDbo();

            //Let check the lock time
            $locktime = $comParams->get('locktime', 60) * 60;
            if ($locktime > 0 && $duplicateClean) {
                //Lets get rid of duplicates with in 10 seconds of each other (Probably Bots!)
                //We will do it in 50 record chunks to keep the queries fast.compar
                $query = $db->getQuery(true);
                if($storeRaw){
                    $query
                        ->select('id, LEFT( tm, 9 ) , ip, count(*)')
                        ->from($db->qn('#__cwtraffic'))
                        ->group('LEFT( tm, 9 ) , ip')
                        ->having('count(*) > 1');
                } else {
                $query
                    ->select('id, LEFT( tm, 9 ) , HEX(iphash), count(*)')
                    ->from($db->qn('#__cwtraffic'))
                    ->group('LEFT( tm, 9 ) , HEX(iphash)')
                    ->having('count(*) > 1');
                }
                
                $db->setQuery($query, 0, 50);
                              
                try {
                    $dups = $db->loadObjectList();
                } catch (Exception $e) {
                    $dups = '';
                    if ($comParams->get('log_sql')) {
                        //Log error
                        $msg = JText::sprintf('COM_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                        JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
                    }
                }

                //If we have some lets delete them and only leave 1
                if ($dups) {
                    foreach ($dups as $row) {
                        $query = $db->getQuery(true);
                        $query->from($db->qn('#__cwtraffic'));
                        $query->delete();
                        $query->where('id = ' . $db->q($row->id));
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
            }

            if ($dbClean) {

                //Prepare Total table
                $query = $db->getQuery(true);
                $query->select('TCOUNT');
                $query->from($db->qn('#__cwtraffic_total'));
                $db->setQuery($query);
                
                try {
                    $currenttotal = $db->loadResult();
                } catch (Exception $e) {
                    $currenttotal = '';
                    if ($comParams->get('log_sql')) {
                        //Log error
                        $msg = JText::sprintf('COM_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                        JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
                    }
                }

                if (empty($currenttotal)) {
                    $query = $db->getQuery(true);

                    $columns = array('tcount');
                    $values = array(0);

                    $query
                            ->insert($db->qn('#__cwtraffic_total'))
                            ->columns($db->qn($columns))
                            ->values(implode(',', $values));

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

                $config = JFactory::getConfig();
                $siteOffset = $config->get('offset');
                date_default_timezone_set($siteOffset);

                $month = date('m');
                $year = date('Y');

                //Calculate the start of the month
                $monthstart = mktime(0, 0, 0, $month, 1, $year);

                switch ($dbKeep) {
                    case 1:
                        $cleanstart = strtotime("-1 week", $monthstart);
                        break;
                    case 2:
                        $cleanstart = strtotime("-3 months", $monthstart);
                        break;
                    case 3:
                        $cleanstart = strtotime("-6 months", $monthstart);
                        break;
                    case 4:
                        $cleanstart = strtotime("-12 months", $monthstart);
                        break;
                    default:
                        $cleanstart = strtotime("-1 week", $monthstart);
                        break;
                }

                $query = $db->getQuery(true);
                $query->select('count(*)');
                $query->from($db->qn('#__cwtraffic'));
                $query->where('tm < ' . $db->q($cleanstart));
                $db->setQuery($query);
         
                try {
                    $oldrows = $db->loadResult();
                } catch (Exception $e) {
                    $oldrows = '';
                    if ($comParams->get('log_sql')) {
                        //Log error
                        $msg = JText::sprintf('COM_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                        JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
                    }
                }

                if (!empty($oldrows)) {
                    $query = $db->getQuery(true);
                    $query->update($db->qn('#__cwtraffic_total'));
                    $query->set('tcount = tcount +' . $db->q($oldrows));
                    $db->setQuery($query);
                    
                    try {
                        $db->execute();
                    } catch (Exception $e) {
                        if ($comParams->get('log_sql')) {
                            //Log error
                            $msg = JText::sprintf('COM_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                            JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
                        }
                        //return before deleting
                        return false;
                    }

                    $query = $db->getQuery(true);
                    $query->from($db->qn('#__cwtraffic'));
                    $query->delete();
                    $query->where('tm < ' . $db->q($cleanstart));
                    $db->setQuery($query);
                    
                    try {
                        $db->execute();
                    } catch (Exception $e) {
                        if ($comParams->get('log_sql')) {
                            //Log error
                            $msg = JText::sprintf('COM_CWTRAFFIC_DATABASE_ERROR', $e->getMessage());
                            JLog::add($msg, JLog::ERROR, 'com_coalawebtraffic_sql');
                        }
                        return false;
                    }
                }

                return;
            }
        }
        return;
    }

}
