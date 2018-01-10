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

/**
 * Methods supporting a list of knownip records.
 *
 * @package    Joomla.Administrator
 * @subpackage com_coalawebtraffic
 */
class CoalawebtrafficModelKnownips extends JModelList
{

    /**
     * Constructor.
     *
     * @param array    An optional associative array of configuration settings.
     * @see   JController
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'alias', 'a.alias',
                'description', 'a.description',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'catid', 'a.catid', 'category_title',
                'state', 'a.state',
                'count', 'a.count',
                'created', 'a.created',
                'created_by', 'a.created_by',
                'ordering', 'a.ordering',
                'publish_up', 'a.publish_up',
                'publish_down', 'a.publish_down',
                'botname', 'a.botname',
                'ip', 'a.ip',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param string $ordering  An optional ordering field.
     * @param string $direction An optional direction (asc|desc).
     *
     * @return void
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Adjust the context to support modal layouts.
        if ($layout = $app->input->get('layout')) {
                $this->context .= '.' . $layout;
        }

        // Set filter state for search
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
                
                // Set filter state for title
                $title = $this->getUserStateFromRequest($this->context . '.filter.title', 'filter_title', '');
                $this->setState('filter.title', $title);

        $published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $published);

        $categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', '');
        $this->setState('filter.category_id', $categoryId);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_coalawebtraffic');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.title', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param string $id A prefix for the store id.
         * 
     * @return string        A store id.
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');
                $id.= ':' . $this->getState('filter.count');
        $id.= ':' . $this->getState('filter.category_id');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return JDatabaseQuery
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db        = $this->getDbo();
        $query    = $db->getQuery(true);
        $user    = JFactory::getUser();

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.alias, a.description, a.checked_out, a.checked_out_time, a.catid,' .
                'a.state, a.ordering, a.ip, a.botname, a.count as count,'.
                'a.publish_up, a.publish_down'
            )
        );
        $query->from($db->qn('#__cwtraffic_knownips').' AS a');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the categories.
        $query->select('c.title AS category_title');
        $query->join('LEFT', '#__categories AS c ON c.id = a.catid');

        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = '.(int) $published);
        } elseif ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }
                
                // Filter by count state
        $count = $this->getState('filter.count');
        if (is_numeric($count)) {
            $query->where('a.count= '.(int) $count);
        } elseif ($count === '') {
            $query->where('(a.count IN (0, 1))');
        }

        // Filter by category.
        $categoryId = $this->getState('filter.category_id');
        if (is_numeric($categoryId)) {
            $query->where('a.catid = '.(int) $categoryId);
        }

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = '.(int) substr($search, 3));
            } else {
                $search = $db->Quote('%'.$db->escape($search, true).'%');
                $query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.' OR a.ip LIKE '.$search.' OR a.botname LIKE '.$search.')');
            }
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
            $orderCol = 'c.title ' . $orderDirn . ', a.ordering';
        }
        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }

}
