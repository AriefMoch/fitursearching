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

// import Joomla modelform library
jimport('joomla.application.component.modellist');

// import helper so we can use the component options
jimport('joomla.application.component.helper');

JLoader::register('CoalawebtrafficModelVisitors', JPATH_COMPONENT . '/models/visitors.php');

/**
 * Methods supporting the export of CSV report
 *
 * @package    Joomla.Administrator
 * @subpackage com_coalawebtraffic
 */
class CoalawebtrafficModelCSVReportAll extends CoalawebtrafficModelVisitors
{

    /**
     * Get data with the help of the visitors model
     * 
     * @return query
     */
    protected function getListQuery() 
    {
        $storeRaw = JComponentHelper::getParams('com_coalawebtraffic')->get('store_raw', 1);
        
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        if ($storeRaw) {
            $query->select('a.id AS ID, a.tm AS Date, a.tm AS Time, a.ip as IP, a.browser as Browser, a.bversion as Version, a.platform as Platform, a.country_name AS Country, a.city AS City');
        } else {
            $query->select('a.id AS ID, a.tm AS Date, a.tm AS Time, a.browser as Browser, a.bversion as Version, a.platform as Platform, a.country_name AS Country, a.city AS City');
        }

        // From the cwtraffic table
        $query->from($db->qn('#__cwtraffic') . ' AS a');

        return $query;
    }

}
