<?php

defined('_JEXEC') or die('Restricted access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Traffic Component
 * @author              Steven Palmer
 * @author url          http://coalaweb.com
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /files/en-GB.license.txt
 * @copyright           Copyright (c) 2015 Steven Palmer All rights reserved.
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
// import Joomla modelform library
jimport('joomla.application.component.modellist');
JLoader::register('CoalawebtrafficModelVisitors', JPATH_COMPONENT . '/models/visitors.php');

/**
 *  Model
 */
class CoalawebtrafficModelCSVReport extends CoalawebtrafficModelVisitors {

    /**
     * Method to set the state using the values from the visitors view
     */
    public function setModelState() {
        $this->context = 'com_coalawebtraffic.visitors';
        parent::populateState();
    }

    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select', 'a.id AS ID, DATE(FROM_UNIXTIME(tm)) AS Date, TIME(FROM_UNIXTIME(tm)) AS Time, a.ip as IP, a.browser as Browser, a.bversion as Version, a.platform as Platform, a.country_name AS Country')
        );
        // From the cwtraffic table
        $query->from($db->quoteName('#__cwtraffic') . ' AS a');

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where(
                        '(' . $db->quoteName('a.ip') . ' LIKE ' . $search .
                        ' OR DATE(FROM_UNIXTIME(tm)) LIKE ' . $search . ')'
                );
            }
        }

        // Add the list ordering clause.
        $query->order($db->escape($this->getState('list.ordering', 'a.tm')) . ' ' . $db->escape($this->getState('list.direction', 'desc')));


        return $query;
    }

}
