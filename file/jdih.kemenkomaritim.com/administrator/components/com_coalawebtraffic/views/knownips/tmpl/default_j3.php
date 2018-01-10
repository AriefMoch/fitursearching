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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$canOrder = $user->authorise('core.edit.state', 'com_coalawebtraffic.category');
$saveOrder = $listOrder == 'a.ordering';
//$params = (isset($this->state->params)) ? $this->state->params : new JObject();

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_coalawebtraffic&task=knownips.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'knownipList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>

<script type="text/javascript">
    Joomla.orderTable = function() {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&view=knownips'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
        <?php else : ?>
            <div id="j-main-container">
            <?php endif; ?>
            <div id="filter-bar" class="btn-toolbar">
                <div class="filter-search btn-group pull-left">
                    <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_CWTRAFFIC_SEARCH_KNOWNIPS_DESC'); ?></label>
                    <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_CWTRAFFIC_SEARCH_KNOWNIPS_DESC'); ?>" />
                </div>
                <div class="btn-group pull-left">
                    <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                    <button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value = '';
                                        this.form.submit();"><i class="icon-remove"></i></button>
                </div>
                <div class="btn-group pull-right hidden-phone">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                    <?php echo $this->pagination->getLimitBox(); ?>
                </div>
                <div class="btn-group pull-right hidden-phone">
                    <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
                    <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                        <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
                        <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
                        <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
                    </select>
                </div>
                <div class="btn-group pull-right">
                    <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
                    <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                        <option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
                        <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
                    </select>
                </div>
            </div>
            <div class="clearfix"> </div>
            <?php if (empty($this->items)) : ?>
                <div class="alert alert-no-items">
                    <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                </div>
            <?php else : ?>
                <table class="table table-striped" id="knownipList">
                    <thead>
                        <tr>
                            <th width="1%" class="nowrap hidden-phone">
                                <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                            </th>
                            <th width="30%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                            </th>

                            <th width="20%" class="nowrap">
                                <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_VISITOR_IP', 'a.ip', $listDirn, $listOrder); ?>
                            </th>
                            <th width="20%" class="nowrap">
                                <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_TITLE_BOTNAME', 'a.ip', $listDirn, $listOrder); ?>
                            </th>
                            <th width="3%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                            </th>                             
                            <th width="3%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_TITLE_COUNT', 'a.count', $listDirn, $listOrder); ?>
                            </th>
                            <th width="15%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
                            </th>
                            <th width="5%" class="nowrap center hidden-phone">
                                <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                            </th>
                            <th width="1%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="9">
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
                            <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">
                                <td class="center hidden-phone">
                                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                </td>
                                <td class="has-context">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'knownips.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php if ($canEdit) : ?>
                                    <?php
                                        $limit = (300 - 3); // limits description to 300 the minus 3 is for the ...
                                        $description = mb_substr(strip_tags($item->description), 0, $limit)."...";
                                    ?>

                                    <div class="pull-left">
                                        <a href="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&task=knownip.edit&id=' . (int) $item->id); ?>">
                                            <?php echo $this->escape($item->title); ?>
                                        </a>
                                        <div class="small">
                                            <?php echo JText::sprintf('COM_CWTRAFFIC_VISITOR_DESCRIPTION', $this->escape($description));?>
                                        </div>
                                    </div>

                                    <?php else : ?>
                                        <?php echo $this->escape($item->title); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $this->escape($item->ip); ?>
                                </td>

                                <td>
                                    <?php echo $this->escape($item->botname); ?>
                                </td>
                                <td class="center">
                                    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'knownips.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
                                </td>
                                <td class="center">
                                    <?php echo JHtml::_('jgrid.published', $item->count, $i, 'knownips.count_', $canChange); ?>
                                </td>
                                <td>
                                    <?php echo $this->escape($item->category_title); ?>
                                </td>
                                <td class="order nowrap center hidden-phone">
                                    <?php
                                    $iconClass = '';
                                    if (!$canChange) {
                                        $iconClass = ' inactive';
                                    } elseif (!$saveOrder) {
                                        $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                                    }
                                    ?>
                                    <span class="sortable-handler<?php echo $iconClass ?>">
                                        <i class="icon-menu"></i>
                                    </span>
                                    <?php if ($canChange && $saveOrder) : ?>
                                        <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
                                <?php endif; ?>
                                </td>

                                <td class="center hidden-phone">
                                    <?php echo (int) $item->id; ?>
                                </td>
                            </tr>
                                <?php endforeach; ?>
                    </tbody>
                </table>
                    <?php endif; ?>

            <?php //Load the batch processing form.  ?>
            <?php if ($user->authorise('core.create', 'com_coalawebtraffic') && $user->authorise('core.edit', 'com_coalawebtraffic') && $user->authorise('core.edit.state', 'com_coalawebtraffic')) : ?>
                <?php echo $this->loadTemplate('batch_j3'); ?>
            <?php endif; ?>


            <div>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                <?php echo JHtml::_('form.token'); ?>
            </div>
            </form>
