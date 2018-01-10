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

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT . '/tables');

jimport('joomla.installer.helper');

/**
 * Methods supporting a control panel
 *
 * @package    Joomla.Administrator
 * @subpackage com_coalawebtraffic
 */
class CoalawebtrafficModelManage extends JModelLegacy {
    
    /**
     * Delete (Purge) all the Traffic data from its associated tables and reset index
     * 
     * @return boolean
     */
    public function purge() {
        $deleted = 0;
        $result = true;
        
        $tables = array('#__cwtraffic', '#__cwtraffic_total', '#__cwtraffic_whoisonline');

        $db = $this->getDBO();
        
        while (count($tables)) {
            $table = array_shift($tables);

                // The table needs repair
                $db->setQuery('TRUNCATE TABLE ' . $db->qn($table));
                try {
                    $db->execute();
                    $deleted++;
                } catch (Exception $exc) {
                    $result = false;
                }
        }

        return $result;
    }


}