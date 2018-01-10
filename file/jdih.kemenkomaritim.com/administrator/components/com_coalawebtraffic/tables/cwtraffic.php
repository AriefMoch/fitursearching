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

// import Joomla table library
jimport('joomla.database.table');

/**
 *  Cwtraffic Table class
 * 
 * @package    Joomla.Administrator
 * @subpackage com_coalawebtraffic
 */
class CoalawebtrafficTableCwtraffic extends JTable
{

    /**
     * Constructor
     *
     * @param object Database connector object
     */
    public function __construct(&$db) 
    {
        parent::__construct('#__cwtraffic', 'id', $db);
    }

    /**
     * Overloaded bind function
     *
     * @param array           named array
     * 
     * @return null|string     null is operation was satisfactory, otherwise returns an error
     */
    public function bind($array, $ignore = '') 
    {
        if (isset($array['params']) && is_array($array['params'])) {
            // Convert the params field to a string.
            $parameter = new JRegistry;
            $parameter->loadArray($array['params']);
            $array['params'] = (string) $parameter;
        }
        return parent::bind($array, $ignore);
    }

   
}
