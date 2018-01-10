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

jimport('joomla.application.component.modeladmin');

/**
 * Coalawebtraffic model.
 *
 * @package    Joomla.Administrator
 * @subpackage com_coalawebtraffic
 */
class CoalawebtrafficModelKnownip extends JModelAdmin
{

    
    public $typeAlias = 'com_coalawebtraffic.knownip';
    
    /**
     * @var        string    The prefix to use with controller messages.
     * @since    1.6
     */
    protected $text_prefix = 'COM_CWTRAFFIC';

    /**
     * Method to test whether a record can be deleted.
     *
     * @param object    A record object.
     * 
     * @return boolean    True if allowed to delete the record. Defaults to the permission set in the component.
     */
    protected function canDelete($record) 
    {
        if (!empty($record->id)) {
            if ($record->state != -2) {
                return;
            }
            $user = JFactory::getUser();

            if ($record->catid) {
                return $user->authorise('core.delete', 'com_coalawebtraffic.category.' . (int) $record->catid);
            } else {
                return $user->authorise('core.delete', 'com_coalawebtraffic');
            }
        }
    }

    /**
     * Method to test whether a record can have its state changed.
     *
     * @param object    A record object.
     * 
     * @return boolean    True if allowed to change the state of the record. Defaults to the permission set in the component.
     */
    protected function canEditState($record) 
    {
        $user = JFactory::getUser();

        if (!empty($record->catid)) {
            return $user->authorise('core.edit.state', 'com_coalawebtraffic.category.' . (int) $record->catid);
        } else {
            return $user->authorise('core.edit.state', 'com_coalawebtraffic');
        }
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
    public function getTable($type = 'Knownip', $prefix = 'CoalawebtrafficTable', $config = array()) 
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param array   $data     An optional array of data for the form to interogate.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     * 
     * @return JForm    A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true) 
    {
        // Initialise variables.
        $app = JFactory::getApplication();

        // Get the form.
        $form = $this->loadForm('com_coalawebtraffic.knownip', 'knownip', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        // Determine correct permissions to check.
        if ($this->getState('knownip.id')) {
            // Existing record. Can only edit in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit');
        } else {
            // New record. Can only create in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.create');
        }

        // Modify the form based on access controls.
        if (!$this->canEditState((object) $data)) {
            // Disable fields for display.
            $form->setFieldAttribute('ordering', 'disabled', 'true');
            $form->setFieldAttribute('state', 'disabled', 'true');
            $form->setFieldAttribute('count', 'disabled', 'true');
            $form->setFieldAttribute('publish_up', 'disabled', 'true');
            $form->setFieldAttribute('publish_down', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute('ordering', 'filter', 'unset');
            $form->setFieldAttribute('state', 'filter', 'unset');
            $form->setFieldAttribute('count', 'filter', 'unset');
            $form->setFieldAttribute('publish_up', 'filter', 'unset');
            $form->setFieldAttribute('publish_down', 'filter', 'unset');
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return mixed    The data for the form.
     */
    protected function loadFormData() 
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_coalawebtraffic.edit.knownip.data', array());

        if (empty($data)) {
            $data = $this->getItem();

            // Prime some default values.
            if ($this->getState('knownip.id') == 0) {
                $app = JFactory::getApplication();
                if (version_compare(JVERSION, '3.0', '>')) {
                    $data->set('catid', $app->input->get('catid', $app->getUserState('com_coalawebtraffic.knownips.filter.category_id'), 'int'));
                } else {
                    $data->set('catid', JFactory::getApplication()->input->get('catid', $app->getUserState('com_coalawebtraffic.knownips.filter.category_id')));
                }
            }
        }

        return $data;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     * 
     * @param type $table
     * 
     * @return void
     */
    protected function prepareTable($table) 
    {
        $date = JFactory::getDate();
        $user = JFactory::getUser();

        $table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
        $table->alias = JApplication::stringURLSafe($table->alias);

        if (empty($table->alias)) {
            $table->alias = JApplication::stringURLSafe($table->title);
        }

        if (empty($table->id)) {
            // Set the values
            // Set ordering to the last item if not set
            if (empty($table->ordering)) {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                    ->select('MAX(ordering)')
                    ->from($db->qn('#__cwtraffic_knownips'));

                $db->setQuery($query);
                $max = $db->loadResult();

                $table->ordering = $max + 1;
            } else {
                // Set the values
                $table->modified = $date->toSql();
                $table->modified_by = $user->get('id');
            }
        }
        
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param object    A record object.
     * 
     * @return array    An array of conditions to add to add to ordering queries.
     */
    protected function getReorderConditions($table) 
    {
        $condition = array();
        $condition[] = 'catid = ' . (int) $table->catid;
        return $condition;
    }

    
    /**
     * Method to save the form data.
     *
     * @param array $data The form data.
     *
     * @return boolean  True on success.
     */
    public function save($data) 
    {
        $app = JFactory::getApplication();

        // Alter the title for save as copy
        if ($app->input->get('task') == 'save2copy') {
            list($name, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
            $data['title'] = $name;
            $data['alias'] = $alias;
            $data['state'] = 0;
        }

        return parent::save($data);
    }
    
        /**
     * Method to change the title & alias.
     *
     * @param integer $category_id The id of the parent.
     * @param string  $alias       The alias.
     * @param string  $name        The title.
     *
     * @return array  Contains the modified title and alias.
     */
    protected function generateNewTitle($category_id, $alias, $name) 
    {
        // Alter the title & alias
        $table = $this->getTable();

        while ($table->load(array('alias' => $alias, 'catid' => $category_id))) {
            if ($name == $table->title) {
                $name = JString::increment($name);
            }

            $alias = JString::increment($alias, 'dash');
        }

        return array($name, $alias);
    }
    
    /**
     * Method to decide if the IP should be counted.
     *
     * @param array   &$pks  The ids of the items to count.
     * @param integer $value The value of the count state
     *
     * @return boolean  True on success.
     */
    function count(&$pks, $value = 1) 
    {
        // Initialise variables.
        $user = JFactory::getUser();
        $table = $this->getTable();
        $pks = (array) $pks;

        // Access checks.
        foreach ($pks as $i => $pk) {
            if ($table->load($pk)) {
                if (!$this->canEditState($table)) {
                    // Prune items that you can't change.
                    unset($pks[$i]);
                    throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 403);
                }
            }
        }

        // Attempt to change the state of the records.
        if (!$table->count($pks, $value, $user->get('id'))) {
            $this->setError($table->getError());
            return false;
        }

        return true;
    }

}
