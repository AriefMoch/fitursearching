<?php
/**
* @version $Id: mod_jdownloads_last_updated.php v3.2
* @package mod_jdownloads_last_updated
* @copyright (C) 2014 Arno Betz
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author Arno Betz http://www.jDownloads.com
*
*
* This modul shows you the last updated downloads from the jDownloads component. 

*/

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once JPATH_SITE . '/components/com_jdownloads/helpers/route.php';

JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jdownloads/models', 'jdownloadsModel');

class modJdownloadsLastUpdatedHelper
{
	static function getList($params)
	{
        $db = JFactory::getDbo();

        // get config value
        $db->setQuery("SELECT setting_value FROM #__jdownloads_config WHERE setting_name = 'days.is.file.updated'");
        $days = $db->loadResult();
        if (!$days) $days = 15;

        // set value for query
        $until_day = mktime(0,0,0,date("m"), date("d")-$days, date("Y"));
        $until = date('Y-m-d H:m:s', $until_day);
        
        // Get an instance of the generic downloads model
        $model = JModelLegacy::getInstance ('downloads', 'jdownloadsModel', array('ignore_request' => true));

        // Set application parameters in model
        $app = JFactory::getApplication();
        $appParams = $app->getParams('com_jdownloads');
        $model->setState('params', $appParams);        
        
        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', (int) $params->get('sum_view', 5));
        $model->setState('filter.published', 1);
        
        // Set the filter for 'updated' field
        $model->setState('filter.updated', 1);
        
        // Set the filter for date range
        $model->setState('filter.date_filtering', 'range');
        $model->setState('filter.date_field', 'a.modified_date');
        $model->setState('filter.start_date_range', $until);
        $model->setState('filter.end_date_range', date('Y-m-d H:m:s', strtotime('now')));
        
        // Access filter
        $access = !JComponentHelper::getParams('com_jdownloads')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $model->setState('filter.access', $access);

        // Category filter
        $catid = $params->get('cat_id', array()); 
        if (empty($catid)){
            $model->setState('filter.category_id', '');
        } else {
            $model->setState('filter.category_id', $catid);
        }    

        // User filter
        $userId = JFactory::getUser()->get('id');

        // Filter by language
        $model->setState('filter.language', $app->getLanguageFilter());

        // Set sort ordering
        $ordering = 'a.modified_date';
        $dir = 'DESC';

        $model->setState('list.ordering', $ordering);
        $model->setState('list.direction', $dir);

        $items = $model->getItems();

        foreach ($items as &$item)
        {
            $item->slug = $item->file_id . ':' . $item->file_alias;
            $item->catslug = $item->cat_id . ':' . $item->category_alias;

            if ($access || in_array($item->access, $authorised))
            {
                // We know that user has the privilege to view the download
                $item->link = '-';
            } else {
                $item->link = JRoute::_('index.php?option=com_users&view=login');
            }
        }
        return $items;        
	}
    
    /**
    * remove the language tag from a given text and return only the text
    *    
    * @param string     $msg
    */
    public static function getOnlyLanguageSubstring($msg)
    {
        // Get the current locale language tag
        $lang       = JFactory::getLanguage();
        $lang_key   = $lang->getTag();        
        
        // remove the language tag from the text
        $startpos = strpos($msg, '{'.$lang_key.'}') +  strlen( $lang_key) + 2 ;
        $endpos   = strpos($msg, '{/'.$lang_key.'}') ;
        
        if ($startpos !== false && $endpos !== false){
            return substr($msg, $startpos, ($endpos - $startpos ));
        } else {    
            return $msg;
        }    
    }     
    
}	
?>