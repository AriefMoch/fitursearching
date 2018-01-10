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
 * View class for Known IPs
 */
class CoalawebtrafficViewKnownips extends JViewLegacy
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
        $this->items = $this->getModel()->getItems();
        $this->state = $this->getModel()->getState();
        $this->pagination = $this->getModel()->getPagination();
        $this->filterForm = $this->getModel()->getFilterForm();
        $this->activeFilters = $this->getModel()->getActiveFilters();

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }

        CoalawebtrafficHelper::addSubmenu('knownips');

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
        $user = JFactory::getUser();
        
        // Since we don't track these assets at the item level, use the category id.
        $canDo = JHelperContent::getActions('com_coalawebtraffic', 'category', $this->state->get('filter.category_id'));

        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Link', 'wrench', 'COM_CWTRAFFIC_TITLE_CPANEL', 'index.php?option=com_coalawebtraffic');

        $title = COM_CWTRAFFIC_PRO == 1 ? JText::_('COM_CWTRAFFIC_TITLE_PRO') : JText::_('COM_CWTRAFFIC_TITLE_CORE');
        JToolBarHelper::title($title . ' [ ' . JText::_('COM_CWTRAFFIC_TITLE_KNOWNIPS') . ' ]', 'eye');  

        if (count($user->getAuthorisedCategories('com_coalawebtraffic', 'core.create')) > 0) {
            JToolBarHelper::addNew('knownip.add');
        } else {
            $this->nocategory = JText::_('COM_CWTRAFFIC_MSG_NOCATEGORY');
        }

        if ($canDo->get('core.edit')) {
            JToolBarHelper::editList('knownip.edit');
        }

        if ($canDo->get('core.edit.state')) {
            JToolBarHelper::publish('knownips.publish', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::unpublish('knownips.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            JToolBarHelper::archiveList('knownips.archive');
            JToolBarHelper::checkin('knownips.checkin');
        }

        if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
            JToolBarHelper::deleteList('', 'knownips.delete', 'JTOOLBAR_EMPTY_TRASH');
        } elseif ($canDo->get('core.edit.state')) {
            JToolBarHelper::trash('knownips.trash');
        }

        // Add a batch button
        if ($user->authorise('core.create', 'com_coalawebtraffic') 
                && $user->authorise('core.edit', 'com_coalawebtraffic') 
                && $user->authorise('core.edit.state', 'com_coalawebtraffic')) {
            
            JHtml::_('bootstrap.modal', 'collapseModal');
            $title = JText::_('JTOOLBAR_BATCH');

            // Instantiate a new JLayoutFile instance and render the batch button
            $layout = new JLayoutFile('joomla.toolbar.batch');

            $dhtml = $layout->render(array('title' => $title));
            $bar->appendButton('Custom', $dhtml, 'batch');
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
            'a.state' => JText::_('JSTATUS'),
            'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
            'a.title' => JText::_('JGRID_HEADING_TITLE'),
            'a.id' => JText::_('JGRID_HEADING_ID'),
            'a.count' => JText::_('JGRID_HEADING_ID'),
        );
    }

}
