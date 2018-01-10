<?php
defined('_JEXEC') or die('Restricted Access');
/**
 * @package             Joomla
 * @subpackage          CoalaWeb Traffic Component
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
JHtml::_('jquery.framework');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');

$doc= JFactory::getDocument();
$doc->addScript(JURI::root(true) . '/media/coalawebtraffic/components/traffic/js/sweetalert.min.js');
$doc->addStyleSheet(JURI::root(true) . '/media/coalawebtraffic/components/traffic/css/sweetalert.css')
        
?>

<?php if (!empty($this->sidebar)) : ?>
    <!-- sidebar -->
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <!-- end sidebar -->
    <div id="j-main-container" class="span10">
<?php else : ?>
    <div id="j-main-container">
<?php endif; ?>

<div id="cpanel-v2" class="span8 well">
    <div class="row-fluid">
    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'tools')); ?>
        
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'tools', JText::_('COM_CWTRAFFIC_TITLE_TOOLS', true)); ?>
        
            <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
                <div class="icon">
                    <a class="red-dark purge-traffic" href="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&task=manage.purge&'. JSession::getFormToken() .'=1' ); ?>">
                        <img alt="<?php echo JText::_('COM_CWTRAFFIC_TITLE_PURGE'); ?>" src="<?php echo JURI::root() ?>/media/coalaweb/components/generic/images/icons/icon-48-cw-trash-v2.png" />
                        <span><?php echo JText::_('COM_CWTRAFFIC_TITLE_PURGE'); ?></span>
                    </a>
                </div>
            </div>

           <?php if ($this->isPro): ?>
                <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
                    <div class="icon">
                        <a class="orange-dark optimize" href="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&task=manage.optimize&'. JSession::getFormToken() .'=1' ); ?>">
                            <img alt="<?php echo JText::_('COM_CWTRAFFIC_TITLE_OPTIMIZE'); ?>" src="<?php echo JURI::root() ?>/media/coalaweb/components/generic/images/icons/icon-48-cw-speed-v2.png" />
                            <span><?php echo JText::_('COM_CWTRAFFIC_TITLE_OPTIMIZE'); ?></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

           <?php if ($this->isPro): ?>
                <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
                    <div class="icon">
                        <a class="aqua-dark addbots" href="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&task=manage.addRobots&'. JSession::getFormToken() .'=1' ); ?>">
                            <img alt="<?php echo JText::_('COM_CWTRAFFIC_TITLE_BOTS'); ?>" src="<?php echo JURI::root() ?>/media/coalaweb/components/generic/images/icons/icon-48-cw-upload-v2.png" />
                            <span><?php echo JText::_('COM_CWTRAFFIC_TITLE_BOTS'); ?></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        <?php if ($this->isPro): ?>
            <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
                <div class="icon">
                    <a class="green-dark" href="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&task=manage.geoRefresh&'. JSession::getFormToken() .'=1' ); ?>">
                        <img alt="<?php echo JText::_('COM_CWTRAFFIC_TITLE_GEO_REFRESH'); ?>" src="<?php echo JURI::root() ?>/media/coalaweb/components/generic/images/icons/icon-48-cw-upload-v2.png" />
                        <span><?php echo JText::_('COM_CWTRAFFIC_TITLE_GEO_REFRESH'); ?></span>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php echo JHtml::_('bootstrap.endTab'); ?>
        
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'reports', JText::_('COM_CWTRAFFIC_TITLE_REPORTS', true)); ?>
        
            <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
                <div class="icon">
                    <a class="aqua-light" href="index.php?option=com_coalawebtraffic&task=visitors.csvreportall">
                        <img alt="<?php echo JText::_('COM_CWTRAFFIC_TITLE_REPORT'); ?>" src="<?php echo JURI::root() ?>/media/coalaweb/components/generic/images/icons/icon-48-cw-download-v2.png" />
                        <span><?php echo JText::_('COM_CWTRAFFIC_TITLE_REPORT'); ?></span>
                    </a>
                </div>
            </div>

        <?php echo JHtml::_('bootstrap.endTab'); ?>
        
        <?php if ($this->isPro): ?>
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'backup', JText::_('COM_CWTRAFFIC_TITLE_BACKUP', true)); ?>
                <fieldset >
                    <legend ><?php echo JText::_('COM_CWTRAFFIC_TITLE_DB') ?></legend>
                    <form name="upload" method="post" enctype="multipart/form-data">
                        <span class="cw-message">
                            <p class="info">
                                <?php echo JText::_('COM_CWTRAFFIC_DB_BACKUP_MESSAGE'); ?>
                            </p>
                        </span>

                        <button type="submit" class="btn btn-info">
                            <span class="icon icon-download"></span>
                            <?php echo JText::_('COM_CWTRAFFIC_BACKUP_DB_BTN') ?>
                        </button>
                        <input type="hidden" name="option" value="com_coalawebtraffic" />
                        <input type="hidden" name="view" value="manage" />
                        <input type="hidden" name="task" value="manage.doBackup" />
                        <?php echo JHtml::_( 'form.token' ); ?>

                    </form>
                </fieldset>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php endif; ?>
       
        <?php if ($this->isPro): ?>
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'restore', JText::_('COM_CWTRAFFIC_TITLE_RESTORE', true)); ?>
                <fieldset >
                    <legend ><?php echo JText::_('COM_CWTRAFFIC_TITLE_DB') ?></legend>
                    <form id="upload" name="upload" method="post" enctype="multipart/form-data">

                        <span class="cw-message">
                            <p class="info">
                                <?php echo JText::_('COM_CWTRAFFIC_DB_RESTORE_MESSAGE'); ?>
                            </p>
                        </span>
                        
                        <span class="cw-message">
                            <p class="alert">
                                <?php echo JText::_('COM_CWTRAFFIC_DB_RESTORE_WARNING'); ?>
                            </p>
                        </span>

                        <input type="file" id="backup-file" name="file_upload" />
                        <button type="submit" class="btn btn-info upload">
                            <span class="icon icon-upload"></span>
                            <?php echo JText::_('COM_CWTRAFFIC_RESTORE_DB_BTN') ?>
                        </button>
                        <input type="hidden" name="option" value="com_coalawebtraffic" />
                        <input type="hidden" name="view" value="manage" />
                        <input type="hidden" name="task" value="manage.restoreBackup" />
                        <?php echo JHtml::_( 'form.token' ); ?>

                    </form>
                </fieldset>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php endif; ?>
        
    <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>
</div>
<div id="tabs" class="span4">
    <div class="row-fluid">

    <?php
    $options = array(
        'onActive' => 'function(title, description){
        description.setStyle("display", "block");
        title.addClass("open").removeClass("closed");
    }',
        'onBackground' => 'function(title, description){
        description.setStyle("display", "none");
        title.addClass("closed").removeClass("open");
    }',
        'startOffset' => 0, // 0 starts on the first tab, 1 starts the second, etc...
        'useCookie' => true, // this must not be a string. Don't use quotes.
        'startTransition' => 1,
    );
    ?>

    <?php echo JHtml::_('sliders.start', 'slider_group_id', $options); ?>

    <?php echo JHtml::_('sliders.panel', JText::_('COM_CWTRAFFIC_SLIDER_TITLE_SUPPORT'), 'slider_1_id'); ?>
    <div class="cw-slider well well-small">
        <?php echo JText::_('COM_CWTRAFFIC_SUPPORT_DESCRIPTION'); ?>
    </div>
        
    <?php if (!$this->isPro): ?>
        <?php echo JHtml::_('sliders.panel', JText::_('COM_CWTRAFFIC_SLIDER_TITLE_UPGRADE'), 'slider_2_id'); ?>
         <div class="cw-slider well well-small">
            <div class="cw-message-block">
                <div class="cw-message">
                    <p class="upgrade"><?php echo JText::_('COM_CWTRAFFIC_MSG_UPGRADE'); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
        
    <?php echo JHtml::_('sliders.end'); ?>
</div>
</div>
</div>
<script>
  jQuery.noConflict();
  
  jQuery('a.purge-traffic').on('click',function(e){
    e.preventDefault(); // Prevent the href from redirecting directly
    var linkURL = jQuery(this).attr("href");
    warnBeforePurge(linkURL);
  });
  
  jQuery('a.optimize').on('click',function(e){
    e.preventDefault(); // Prevent the href from redirecting directly
    var linkURL = jQuery(this).attr("href");
    warnBeforeOptimize(linkURL);
  });
  
  jQuery('a.addbots').on('click',function(e){
    e.preventDefault(); // Prevent the href from redirecting directly
    var linkURL = jQuery(this).attr("href");
    warnBeforeAddbots(linkURL);
  });
  
  jQuery('a.addbots').on('click',function(e){
    e.preventDefault(); // Prevent the href from redirecting directly
    var linkURL = jQuery(this).attr("href");
    warnBeforeAddbots(linkURL);
  });
  
  jQuery('.upload').on('click',function(e){
    e.preventDefault(); // Prevent form from being submitted
    var form = jQuery(this).parents('form');
    if (jQuery('#backup-file').val() == '') {
        warnEmptyRestore();
    } else {
        warnBeforeRestore(form);
    }
    
  });


    function warnEmptyRestore() {
        swal({
            title: "<?php echo JText::_('COM_CWTRAFFIC_FILE_MISSING_POPUP_TITLE'); ?>", 
            text: "<?php echo JText::_('COM_CWTRAFFIC_FILE_MISSING_POPUP_MSG'); ?>", 
            type: "warning",
        });
    }
    
    function warnBeforeRestore(form) {
        swal({
            title: "<?php echo JText::_('COM_CWTRAFFIC_RESTORE_POPUP_TITLE'); ?>", 
            text: "<?php echo JText::_('COM_CWTRAFFIC_RESTORE_POPUP_MSG'); ?>", 
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?php echo JText::_('COM_CWTRAFFIC_RESTORE_POPUP_BUTTON'); ?>",
            closeOnConfirm: false
        }, function(isConfirm){
            if (isConfirm) form.submit();
        });
    }

    function warnBeforePurge(linkURL) {
        swal({
          title: "<?php echo JText::_('COM_CWTRAFFIC_PURGE_POPUP_TITLE'); ?>", 
          text: "<?php echo JText::_('COM_CWTRAFFIC_PURGE_POPUP_MSG'); ?>", 
          type: "warning",
          html: true,
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
        }, function() {
          // Redirect the user
          window.location.href = linkURL;
        });
    }
  
 function warnBeforeOptimize(linkURL) {
    swal({
      title: "<?php echo JText::_('COM_CWTRAFFIC_OPTIMIZE_POPUP_TITLE'); ?>", 
      text: "<?php echo JText::_('COM_CWTRAFFIC_OPTIMIZE_POPUP_MSG'); ?>", 
      type: "warning",
      html: true,
      showCancelButton: true
    }, function() {
      // Redirect the user
      window.location.href = linkURL;
    });
  }
  
 function warnBeforeAddbots(linkURL) {
    swal({
      title: "<?php echo JText::_('COM_CWTRAFFIC_ADDBOTS_POPUP_TITLE'); ?>", 
      text: "<?php echo JText::_('COM_CWTRAFFIC_ADDBOTS_POPUP_MSG'); ?>", 
      type: "warning",
      html: true,
      showCancelButton: true
    }, function() {
      // Redirect the user
      window.location.href = linkURL;
    });
  }

  </script>

