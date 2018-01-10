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

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

use Joomla\Utilities\ArrayHelper;

/**
 * Knownip controller class.
 *
 * @package    Joomla.Administrator
 * @subpackage com_coalawebtraffic
 */
class CoalawebtrafficControllerKnownip extends JControllerForm
{

    /**
     * @var        string    The prefix to use with controller messages.
     * @since    1.6
     */
    protected $text_prefix = 'COM_CWTRAFFIC';

    /**
     * Method override to check if you can add a new record.
     *
     * @param array $data An array of input data.
     *
     * @return boolean
     *
     * @since 1.6
     */
    protected function allowAdd($data = array()) 
    {
        // Initialise variables.
        $user = JFactory::getUser();
        $categoryId = ArrayHelper::getValue($data, 'catid', JFactory::getApplication()->input->get('filter_category_id'), 'int');
        $allow = null;

        if ($categoryId) {
            // If the category has been passed in the URL check it.
            $allow = $user->authorise('core.create', $this->option . '.category.' . $categoryId);
        }

        if ($allow === null) {
            // In the absense of better information, revert to the component permissions.
            return parent::allowAdd($data);
        } else {
            return $allow;
        }
    }

    /**
     * Method to check if you can add a new record.
     *
     * @param array  $data An array of input data.
     * @param string $key  The name of the key for the primary key.
     *
     * @return boolean
     * @since  1.6
     */
    protected function allowEdit($data = array(), $key = 'id') 
    {
        // Initialise variables.
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
        $categoryId = 0;

        if ($recordId) {
            $categoryId = (int) $this->getModel()->getItem($recordId)->catid;
        }

        if ($categoryId) {
            // The category has been set. Check the category permissions.
            return JFactory::getUser()->authorise('core.edit', $this->option . '.category.' . $categoryId);
        } else {
            // Since there is no asset tracking, revert to the component permissions.
            return parent::allowEdit($data, $key);
        }
    }

    /**
     * Method to run batch operations.
     *
     * @param object $model The model.
     *
     * @return boolean     True if successful, false otherwise and internal error is set.
     *
     * @since 1.7
     */
    public function batch($model = null) 
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Set the model
        $model = $this->getModel('Knownip', '', array());

        // Preset the redirect
        $this->setRedirect(JRoute::_('index.php?option=com_coalawebtraffic&view=knownips' . $this->getRedirectToListAppend(), false));

        return parent::batch($model);
    }

}
