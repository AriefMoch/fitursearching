<?php
/**
 * @copyright    Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * @package jDownloads
 * @version 3.2  
 * @copyright (C) 2007 - 2017 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class
 *
 */
class JFormFieldjdCategorySelectEditFE extends JFormFieldList
{
	/**
	 * A flexible category list that respects access controls.
	 *
	 * @var		string
	 */
	protected $type = 'jdCategorySelectEditFE';

	/**
     * Method to get a list of categories that respects access controls and can be used for category assignment in edit screens.
	 *
	 * @return	array	The field option objects.
	 */
	protected function getOptions()
	{
        // Include helpers
        require_once JPATH_SITE.'/administrator/components/com_jdownloads/helpers/jdownloadshelper.php';

        $jd_config = JDownloadsHelper::buildjlistConfig();         
        
        // Initialise variables.
		$options = array();
        $user = JFactory::getUser();
        $levels = $user->getAuthorisedViewLevels();
        if ($levels){
            $levels = array_unique($levels);
        }
        $user_levels = implode(',',$levels);
                
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text, a.level');
		$query->from('#__jdownloads_categories AS a');
        $query->join('LEFT', '`#__jdownloads_categories` AS b ON a.lft > b.lft AND a.rgt < b.rgt');

		//$query->where('a.access IN ('.$db->quote($user_levels).')');
        $query->where('a.published IN (0,1)');
		$query->group('a.id');
		$query->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
        
        // Check for a database error.
        if ($db->getErrorNum()) {
            JError::raiseWarning(500, $db->getErrorMsg());
        }

        // add an empty array item in the first position 
        $empty_cat_object = new stdClass();
        $empty_cat_object->value = null;
        $empty_cat_object->text = JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_SELECT_CATEGORY');
        $empty_cat_object->level = 0;
        $empty_array[0] = $empty_cat_object; 
        $options = array_merge($empty_array, $options);
        
        // Pad the option text with spaces using depth level as a multiplier.
        for ($i = 0, $n = count($options); $i < $n; $i++)
        {
            // Translate ROOT
            if ($i == 1 && $options[$i]->level == 0) {
                if ($jd_config['show.header.catlist.uncategorised'] == 1){
                    $options[$i]->text = JText::_('COM_JDOWNLOADS_SELECT_UNCATEGORISED');
                } else {
                   unset($options[1]);
                   $i++;
                   //$n--;
                }    
            }
            
            if ($options[$i]->level > 1){
                $options[$i]->text = str_repeat('- ',($options[$i]->level -1)).$options[$i]->text;            
            }
        }

        if (empty($id)) {
            // New item, only have to check core.create.
            foreach ($options as $i => $option)
            {
                if ($option->value > 0){
                    // Special handling for the uncategorisied option (value = 1)
                    // Use here the components settings
                    if ($option->value == 1){
                        // Unset the option if the user isn't authorised for it.
                        if (!$user->authorise('core.create', 'com_jdownloads')) {
                            unset($options[$i]);
                        }
                    } else {        
                        // Unset the option if the user isn't authorised for it.
                        if (!$user->authorise('core.create', 'com_jdownloads.category.'.$option->value)) {
                            unset($options[$i]);
                        }
                    }    
                }    
            }
        }
        else {
            // Existing item is a bit more complex. Need to account for core.edit and core.edit.own.
            foreach ($options as $i => $option)
            {
                // Special handling for the uncategorisied option (value = 1)
                // Use here the components settings
                if ($option->value == 1){
                    if (!$user->authorise('core.edit', 'com_jdownloads')) {
                        // As a backup, check core.edit.own
                        if (!$user->authorise('core.edit.own', 'com_jdownloads')) {
                            // No core.edit nor core.edit.own - bounce this one
                            unset($options[$i]);
                        }                
                    }
                } else {        
                
                    // Unset the option if the user isn't authorised for it.
                    if (!$user->authorise('core.edit', 'com_jdownloads.category.'.$option->value)) {
                        // As a backup, check core.edit.own
                        if (!$user->authorise('core.edit.own', 'com_jdownloads.category.'.$option->value)) {
                            // No core.edit nor core.edit.own - bounce this one
                            unset($options[$i]);
                        }
                        else {
                            // TODO I've got a funny feeling we need to check core.create here.
                            // Maybe you can only get the list of categories you are allowed to create in?
                            // Need to think about that. If so, this is the place to do the check.
                        }
                    }
                }    
            }
        }
       
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}