<?php

/**
 * Quickdown plugin for Remository 3.50+ 
 * License : http://www.gnu.org/copyleft/gpl.html ver 2
 * @ originally by Mamboaddons.com DEV, later Martin Brampton
 * @Copyright (C) 2004 - 2005 http://www.mamboaddons.com, 2006-9 Martin Brampton
 * martin@remository.com
 * http://remository.com
 * Special Thanks to wolfi from http://www.mamboport.de for preparing version
 * 1.1b for Mambo 4.5.1
 */

/**
 * Quick Down for Remository
 *
 * @package		Remository
 * @subpackage	Content
 * @since 		Remository 3.50+
 */

defined( '_VALID_MOS' ) OR defined ( '_JEXEC' ) OR die( 'Direct Access to this location is not allowed.' );

$cmsapi_addon_topdir = 'mambots/plugins/modules';
$cmsapi_addon_tops = explode('/', $cmsapi_addon_topdir);
$cmsapi_mydir = array_reverse(explode('/', str_replace('\\', '/', __FILE__)));
do $cmsapi_shifted = array_shift($cmsapi_mydir); while (!in_array($cmsapi_shifted, $cmsapi_addon_tops));
$cmsapi_absolute_path = implode('/', array_reverse($cmsapi_mydir));

require_once($cmsapi_absolute_path.'/components/com_remository/remository.interface.php');

// error_reporting(E_ALL);

if (defined('_JOOMLA_15PLUS')) {
	jimport( 'joomla.event.plugin' );
	jimport('joomla.html.parameter');
	
	if(version_compare(JVERSION,'1.6.0','<')){
		class plgContentQuickdown extends JPlugin  {
			protected $plugin_type = 'content';
		
			public function onPrepareContent ($article) {
				$worker = new remository_plugin_quickdown();
				return $worker->onPrepareContent ($this->params, $article);
			}
		}
	}
	else{
		jimport('joomla.plugin.plugin');
		class plgContentQuickdown extends JPlugin  {
			protected $plugin_type = 'content';
			
			/**
			 * @param	string	The context of the content being passed to the plugin.
			 * @param	object	The article object.  Note $article->text is also available
			 * @param	object	The article params
			 * @param	int		The 'page' number
			 */
			public function onContentPrepare($context, &$article, &$params, $page = 0) {
		
				$worker = new remository_plugin_quickdown();
				return $worker->onPrepareContent ($this->params, $article);
			}
		}
	}
}
else {
	global $_MAMBOTS;	
	/** Register search function inside Mambo's API */
	$_MAMBOTS->registerFunction( 'onPrepareContent', 'botQuickdown' );
	
	function botQuickdown ($published, $article) {
	 	$worker = new remository_plugin_quickdown();
		// Get parameters from Database
		$query = "SELECT params FROM #__mambots WHERE element = 'quickdown' AND folder = 'content'";
		$database = remositoryInterface::getInstance()->getDB();
		$database->setQuery($query);
		$paramstring = $database->loadResult();
		$params = new mosParameters($paramstring);
	 	return $worker->onPrepareContent ($params, $article, $published);
	}
}
 