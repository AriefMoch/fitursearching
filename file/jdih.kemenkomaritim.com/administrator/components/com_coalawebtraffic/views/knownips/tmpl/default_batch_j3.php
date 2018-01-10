<?php
defined('_JEXEC') or die('Restricted Access');

/**
 * @package             Joomla
 * @subpackage          CoalaWeb Traffic Component
 * @author              Steven Palmer
 * @author url          http://coalaweb.com
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /files/en-GB.license.txt
 * @copyright           Copyright (c) 2015 Steven Palmer All rights reserved.
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

$published = $this->state->get('filter.state');
?>
<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
            <button type="button" role="presentation" class="close" data-dismiss="modal">&#215;</button>
            <h3><?php echo JText::_('COM_CWTRAFFIC_BATCH_OPTIONS'); ?></h3>
	</div>
	<div class="modal-body modal-batch">
            <p><?php echo JText::_('COM_CWTRAFFIC_BATCH_TIP'); ?></p>
            <div class="row-fluid">
                <?php if ($published >= 0) : ?>
                    <div class="control-group span6">
                        <div class="controls">
                                <?php echo JHtml::_('batch.item', 'com_coalawebtraffic'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
	</div>
	<div class="modal-footer">
            <button class="btn" type="button" onclick="document.id('batch-category-id').value=''" data-dismiss="modal">
                <?php echo JText::_('JCANCEL'); ?>
            </button>
            <button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('knownip.batch');">
                <?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
            </button>
	</div>
</div>
