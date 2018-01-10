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

jimport('joomla.filesystem');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');

/**
 * Manage controller class.
 *
 * @package    Joomla.Administrator
 * @subpackage com_coalawebtraffic
 */
class CoalawebtrafficControllerManage extends JControllerLegacy {

    /**
     * @var string The prefix to use with controller messages.
     */
    protected $text_prefix = 'COM_CWTRAFFIC';

    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JControllerLegacy
     * @since   1.6
     */
    public function __construct($config = array()) {
        parent::__construct($config);
    }

    /**
     * Proxy for getModel
     * 
     * @param type $name
     * @param type $prefix
     * 
     * @return JModel
     */
    public function getModel($name = 'Manage', $prefix = 'CoalawebtrafficModel', $config = array('ignore_request' => true)) {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Messages and redirect for purge of Traffic data
     */
    function purge() {
        // First check our token to stop any Cross Site Request Forgeries
        JSession::checkToken('get') or die('Invalid Token');

        $model = $this->getModel();
        $msgType = '';

        if (!$model->purge()) {
            $msg = JText::_('COM_CWTRAFFIC_PURGE_ERROR_MSG');
            $msgType = 'error';
        } else {
            $msg = JText::_('COM_CWTRAFFIC_PURGE_SUCCESS_MSG');
        }
        $this->setRedirect('index.php?option=com_coalawebtraffic&view=manage', $msg, $msgType);
    }

}
