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
jimport('joomla.application.component.modeladmin');

/**
 * Coalawebtraffic model.
 *
 * @package    Joomla.Administrator
 * @subpackage com_coalawebtraffic
 */
class CoalawebtrafficModelVisitor extends JModelAdmin
{
    
    /**
     * @var        string    The prefix to use with controller messages.
     */
    protected $text_prefix = 'COM_CWTRAFFIC';

    /**
     * Method override to check if you can edit an existing record.
     *
     * @param array  $data An array of input data.
     * @param string $key  The name of the key for the primary key.
     *
     * @return boolean
     */
    protected function allowEdit($data = array(), $key = 'id') 
    {
        // Check specific edit permission then general edit permission.
        return JFactory::getUser()->authorise('core.edit', 'com_coalawebtraffic.message.' . ((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param type    The table type to instantiate
     * @param string    A prefix for the table class name. Optional.
     * @param array    Configuration array for model. Optional.
     * 
     * @return JTable    A database object
     */
    public function getTable($type = 'Cwtraffic', $prefix = 'CoalawebtrafficTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param array   $data     Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     * 
     * @return mixed    A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true) 
    {
        // Get the form.
        $form = $this->loadForm('com_coalawebtraffic.visitor', 'visitor', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    /**
     * Method to get the script that have to be included on the form
     *
     * @return string    Script files
     */
    public function getScript() 
    {
        return 'administrator/components/com_coalawebtraffic/models/forms/visitor.js';
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return mixed    The data for the form.
     */
    protected function loadFormData() 
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_coalawebtraffic.edit.visitor.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

}
