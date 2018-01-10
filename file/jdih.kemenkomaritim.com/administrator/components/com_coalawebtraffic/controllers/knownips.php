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

// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');

use Joomla\Utilities\ArrayHelper;


/**
 * Coalawebtraffic list controller class.
 *
 * @package    Joomla.Administrator
 * @subpackage com_coalawebtraffic
 */
class CoalawebtrafficControllerKnownips extends JControllerAdmin
{

    /**
     * @var        string    The prefix to use with controller messages.
     * @since    1.6
     */
    protected $text_prefix = 'COM_CWTRAFFIC';

    /**
     * Constructor.
     *
     * @param array An optional associative array of configuration settings.
     * @see   JController
     */
    public function __construct($config = array()) 
    {
        parent::__construct($config);

        $this->registerTask('count_unpublish', 'count_publish');
    }

    /**
     * Proxy for getModel
     * 
     * @param type $name
     * @param type $prefix
     * @param type $config
     * 
     * @return JModel
     */
    public function getModel($name = 'Knownip', $prefix = 'CoalawebtrafficModel', $config = array('ignore_request' => true)) 
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Turn on or off count for a particular known ip
     */
    public function count_publish() 
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $ids = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $values = array('count_publish' => 1, 'count_unpublish' => 0);
        $task = $this->getTask();
        $value = ArrayHelper::getValue($values, $task, 0, 'int');

        if (empty($ids)) {
            throw new Exception(JText::_('COM_CWTRAFFIC_NO_ITEM_SELECTED'), 500);
        } else {
            // Get the model.
            $model = $this->getModel();

            // Change the state of the records.
            if (!$model->count($ids, $value)) {
                throw new Exception($model->getError(), 500);
            } else {
                if ($value == 1) {
                    $ntext = 'COM_CWTRAFFIC_N_COUNT';
                } else {
                    $ntext = 'COM_CWTRAFFIC_N_UNCOUNT';
                }
                $this->setMessage(JText::plural($ntext, count($ids)));
            }
        }

        $this->setRedirect('index.php?option=com_coalawebtraffic&view=knownips');
    }

}
