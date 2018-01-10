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

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller for coalawebtraffic component
 */
class CoalawebtrafficController extends JControllerLegacy
{

    protected $default_view = 'Controlpanel';

    /**
     * Method to display a view.
     *
     * @param boolean
     * @param array
     *
     * @return JController
     */
    public function display($cachable = false, $urlparams = false) 
    {
        include_once JPATH_COMPONENT . '/helpers/coalawebtraffic.php';
        
        $view = JFactory::getApplication()->input->get('view', 'Controlpanel');
        $layout = JFactory::getApplication()->input->get('layout', 'default');
        $id = JFactory::getApplication()->input->get('id');

        if ($view == 'knownip' && $layout == 'edit' && !$this->checkEditId('com_coalawebtraffic.edit.knownip', $id)) {

            // Somehow the person just went to the form - we don't allow that.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_coalawebtraffic&view=knownips', false));

            return false;
        }

        parent::display();
        return $this;
    }

}
