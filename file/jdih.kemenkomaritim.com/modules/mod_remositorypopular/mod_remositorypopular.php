<?php

/**************************************************************
* This file is part of Remository
* Copyright (c) 2006-12 Martin Brampton
* Issued as open source under GNU/GPL
* For support and other information, visit http://remository.com
* To contact Martin Brampton, write to martin@remository.com
*
* Remository started life as the psx-dude script by psx-dude@psx-dude.net
* It was enhanced by Matt Smith up to version 2.10
* Since then development has been primarily by Martin Brampton,
* with contributions from other people gratefully accepted
*/

defined( '_VALID_MOS' ) OR defined ( '_JEXEC' ) OR die( 'Direct Access to this location is not allowed.' );

$cmsapi_addon_topdir = 'mambots/plugins/modules';
$cmsapi_addon_tops = explode('/', $cmsapi_addon_topdir);
$cmsapi_mydir = array_reverse(explode('/', str_replace('\\', '/', __FILE__)));
do $cmsapi_shifted = array_shift($cmsapi_mydir); while (!in_array($cmsapi_shifted, $cmsapi_addon_tops));
$cmsapi_absolute_path = implode('/', array_reverse($cmsapi_mydir));

require_once($cmsapi_absolute_path.'/components/com_remository/remository.interface.php');

$mod_remositorypopular = new mod_remositoryPopular();
$mod_remositorypopular->showFileList(null, $content, null, $params);