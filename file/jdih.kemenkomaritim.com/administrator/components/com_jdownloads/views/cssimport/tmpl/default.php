<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 *
 * @component jDownloads
 * @version 2.0  
 * @copyright (C) 2007 - 2011 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
 
defined('_JEXEC') or die('Restricted access');

global $jlistConfig, $jversion; 

if (version_compare( $jversion->RELEASE,  '3.4', 'ge' ) == FALSE ) {
    // is not 3.4 or newer - so we must use mootols
    JHTML::_('behavior.formvalidation'); 
} else {
    JHtml::_('behavior.formvalidator'); //Joomla >= 3.4 jQuery
} 
JHtml::_('behavior.tooltip');

?>
<script type="text/javascript">
    Joomla.submitbutton = function(pressbutton) {
        var form = document.getElementById('adminForm');

        // do field validation
        if (form.install_file.value == ""){
            alert("<?php echo JText::_('COM_JDOWNLOADS_CSS_IMPORT_NO_FILE', true); ?>");
        } else {
            form.submit();
        }
    }
</script>  

<form action="<?php echo JRoute::_('index.php?option=com_jdownloads');?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
   
    <?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>   
    
    <div>
        <fieldset style="background-color: #ffffff;" class="uploadform">
            <legend><?php echo JText::_('COM_JDOWNLOADS_CSS_IMPORT_LABEL'); ?></legend>
            <div><?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_INFO_TITLE'); ?></div>                              
            <div style="margin-left:15px; margin-top:8px;"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_INFO_1'); ?></div>
            <div style="margin-left:15px; margin-top:8px;"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_INFO_2'); ?></div>
            <div style="margin-left:15px; margin-top:8px; color: maroon;"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_EDIT_CSS_INFO_3'); ?></div>
            <div style="margin-top:15px;"><?php echo JText::_('COM_JDOWNLOADS_CSS_IMPORT_DESC'); ?></div>                              
            <input style="margin-top:15px;" class="input_box" id="install_file" name="install_file" type="file" size="60" />
            <input style="margin-top:15px; margin-left:15px;" class="button" type="button" value="<?php echo JText::_('COM_JDOWNLOADS_CSS_IMPORT_LABEL').'&nbsp; '; ?>" onclick="Joomla.submitbutton()" />
        </fieldset>
    </div>
  
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="option" value="com_jdownloads" />
    <input type="hidden" name="task" value="cssimport.import" />
    <input type="hidden" name="view" value="cssimport" />
    <input type="hidden" name="hidemainmenu" value="0" />
    
    <?php echo JHtml::_('form.token'); ?>
   </form>
