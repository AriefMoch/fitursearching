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


class CoalawebtrafficControllerVisitors extends JControllerAdmin
{
    
    /**
     * Controller message language prefix
     * 
     * @var   string    The prefix to use with controller messages.
     * @since 1.6
     */
    protected $text_prefix = 'COM_CWTRAFFIC';

    /**
     * Proxy for getModel
     * 
     * @param type $name
     * @param type $prefix
     * @param type $config
     * 
     * @return JModel
     */
    public function getModel($name = 'Visitor', $prefix = 'CoalawebtrafficModel', $config = array('ignore_request' => true)) 
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    /**
     * Get the data together to be exported
     * 
     * @param type $prefix
     */
    public function csvReportAll($prefix = 'CoalawebtrafficModel') 
    {
        $model = $this->getModel('CSVReportAll', $prefix, array('ignore_request' => true));
        $data = $model->getItems();
        
        $this->exportReport($data);
    }

    /**
     * Export CSV report
     * 
     * @param type $data
     */
    protected function exportReport($data) {

       foreach ($data as &$item) {
            $item->Date = JHtml::date($item->Date, 'Y-m-d', false);
            $item->Time = JHtml::date($item->Time, 'H:i', false);
        }

        //Set Headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . 'visitors.csv');

        if ($fp = fopen('php://output', 'w')) {

            //Output the first row with column headings
            if ($data[0]) {
                fputcsv($fp, array_keys(ArrayHelper::fromObject($data[0])));
            }

            //Output the rows
            foreach ($data as $row) {
                fputcsv($fp, ArrayHelper::fromObject($row));
            }
            // Close file
            fclose($fp);
        }
        JFactory::getApplication()->close();
    }

}
