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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$canOrder = $user->authorise('core.edit.state', 'com_coalawebtraffic.category');
$saveOrder = $listOrder == 'a.ordering';
$params = (isset($this->state->params)) ? $this->state->params : new JObject();
?>

<form action="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&view=knownips'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CWTRAFFIC_SEARCH_IN_TITLE'); ?>" />
            <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value = '';
                                this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-select fltrt">

            <select name="filter_state" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true); ?>
            </select>

            <select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY'); ?></option>
                <?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_coalawebtraffic'), 'value', 'text', $this->state->get('filter.category_id')); ?>
            </select>

        </div>
    </fieldset>
    <div class="clr"> </div>

    <table class="adminlist">
        <thead>
            <tr>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                </th>
                <th width="25%">
                    <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                </th>

                <th width="10%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_VISITOR_IP', 'a.ip', $listDirn, $listOrder); ?>
                </th>
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_TITLE_BOTNAME', 'a.ip', $listDirn, $listOrder); ?>
                </th>
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                </th>                             
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_TITLE_COUNT', 'a.count', $listDirn, $listOrder); ?>
                </th>
                <th width="10%">
                    <?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
                </th>
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
                    <?php if ($canOrder && $saveOrder) : ?>
                        <?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'knownips.saveorder'); ?>
                    <?php endif; ?>
                </th>
                <th width="1%" class="nowrap">
                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="11">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
            <?php
            foreach ($this->items as $i => $item) :
                $ordering = ($listOrder == 'a.ordering');
                $item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_coalawebtraffic&task=edit&type=other&cid[]=' . $item->catid);
                $canCreate = $user->authorise('core.create', 'com_coalawebtraffic.category.' . $item->catid);
                $canEdit = $user->authorise('core.edit', 'com_coalawebtraffic.category.' . $item->catid);
                $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
                $canChange = $user->authorise('core.edit.state', 'com_coalawebtraffic.category.' . $item->catid) && $canCheckin;
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>
                    <td>
                        <?php
                            $limit = (300 - 3); // limits description to 300 the minus 3 is for the ...
                            $description = mb_substr(strip_tags($item->description), 0, $limit)."...";
                        ?>
                        <?php if ($item->checked_out) : ?>
                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'knownips.', $canCheckin); ?>
                        <?php endif; ?>
                        <?php if ($canEdit) : ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&task=knownip.edit&id=' . (int) $item->id); ?>">
                                <?php echo $this->escape($item->title); ?></a>
                        <?php else : ?>
                            <?php echo $this->escape($item->title); ?>
                        <?php endif; ?>
                        <p class="smallsub">
                            <?php echo JText::sprintf('COM_CWTRAFFIC_VISITOR_DESCRIPTION', $this->escape($description)); ?></p>
                    </td>
                    <td class="center">
                        <?php echo $this->escape($item->ip); ?>
                    </td>

                    <td class="center">
                        <?php echo $this->escape($item->botname); ?>
                    </td>
                    <td class="center">
                        <?php echo JHtml::_('jgrid.published', $item->state, $i, 'knownips.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
                    </td>
                    <td class="center">
                        <?php echo JHtml::_('jgrid.published', $item->count, $i, 'knownips.count_', $canChange); ?>
                    </td>
                    <td class="center">
                        <?php echo $this->escape($item->category_title); ?>
                    </td>
                    <td class="order">
                        <?php if ($canChange) : ?>
                            <?php if ($saveOrder) : ?>
                                <?php if ($listDirn == 'asc') : ?>
                                    <span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i - 1]->catid), 'knownips.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i + 1]->catid), 'knownips.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                <?php elseif ($listDirn == 'desc') : ?>
                                    <span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i - 1]->catid), 'knownips.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i + 1]->catid), 'knownips.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
                            <input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled ?> class="text-area-order" />
                        <?php else : ?>
                            <?php echo $item->ordering; ?>
                        <?php endif; ?>
                    </td>

                    <td class="center">
                        <?php echo (int) $item->id; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php //Load the batch processing form. ?>
    <?php if ($user->authorize('core.create', 'com_coalawebtraffic') && $user->authorize('core.edit', 'com_coalawebtraffic') && $user->authorize('core.edit.state', 'com_coalawebtraffic')) : ?>
        <?php echo $this->loadTemplate('batch_j25'); ?>
    <?php endif; ?>

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
