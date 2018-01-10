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

// Helpers
JLoader::register('CoalawebtrafficHelper', JPATH_COMPONENT . '/helpers/coalawebtraffic.php');
JLoader::register('CoalawebtrafficHelperLocation', JPATH_COMPONENT . '/helpers/location.php');

/**
 * View class for Vistitors
 */
class CoalawebtrafficViewVisitors extends JViewLegacy
{

    protected $items;
    protected $pagination;
    protected $state;
    
    /**
     * Display the view
     *
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return void
     */
    public function display($tpl = null) 
    {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->knownips = $this->get('Knownips');
        $this->pagination = $this->get('Pagination');
        $this->filterForm = $this->getModel()->getFilterForm();
        $this->activeFilters = $this->getModel()->getActiveFilters();

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }

        CoalawebtrafficHelper::addSubmenu('visitors');

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            $this->addToolbar();
            $this->sidebar = JHtmlSidebar::render();
        }

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     */
    protected function addToolbar() 
    {
        $canDo = JHelperContent::getActions('com_coalawebtraffic');

        $title = COM_CWTRAFFIC_PRO == 1 ? JText::_('COM_CWTRAFFIC_TITLE_PRO') : JText::_('COM_CWTRAFFIC_TITLE_CORE');
        JToolBarHelper::title($title. ' [ ' . JText::_('COM_CWTRAFFIC_TITLE_VISITORS') . ' ]', 'users');
                
        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Link', 'wrench', 'COM_CWTRAFFIC_TITLE_CPANEL', 'index.php?option=com_coalawebtraffic');

        if ($canDo->get('core.delete')) {
            JToolBarHelper::divider();
            JToolBarHelper::deleteList('', 'visitors.delete', 'JTOOLBAR_DELETE');
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_coalawebtraffic');
        }

        $help_url = 'http://coalaweb.com/support/documentation/item/coalaweb-traffic-guide';
        JToolBarHelper::help('COM_CWTRAFFIC_TITLE_HELP', false, $help_url);
    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return array  Array containing the field name to sort by as the key and display text as value
     */
    protected function getSortFields() 
    {
        return array(
            'a.ip' => JText::_('COM_CWTRAFFIC_VISITOR_IP'),
            'a.tm' => JText::_('COM_CWTRAFFIC_VISITOR_DATE'),
            'a.country_name' => JText::_('COM_CWTRAFFIC_HEADER_LOCATION'),
            'a.browser' => JText::_('COM_CWTRAFFIC_VISITOR_BROWSER'),
            'a.bversion' => JText::_('COM_CWTRAFFIC_BROWSER_VERSION'),
            'a.platform' => JText::_('COM_CWTRAFFIC_PLATFORM')
        );
    }

}
