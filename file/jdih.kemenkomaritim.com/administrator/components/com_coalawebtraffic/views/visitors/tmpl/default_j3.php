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
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_knownips.category');
$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_knownips&task=knownips.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'knownipList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>


<form action="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&view=visitors'); ?>" method="post" id="adminForm" name="adminForm">
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
            <label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></label>
            <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_CWTRAFFIC_SEARCH_LABEL'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CWTRAFFIC_SEARCH_LABEL'); ?>" />
        </div>
        <div class="btn-group pull-left">
            <button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
            <button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value = '';
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

    <table class="table table-striped">

        <thead>
            <tr>
                <th class="nowrap center" width="3%">
                    <?php echo JText::_('COM_CWTRAFFIC_HEADER_ID'); ?>
                </th>
                <th width="3%"class="hidden-phone">
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                </th>			
                <th width="15%" class="nowrap center" >
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_VISITOR_IP', 'a.ip', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap center hidden-phone" width="8%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_VISITOR_BROWSER', 'a.browser', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap center hidden-phone" width="2%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_BROWSER_VERSION', 'a.bversion', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap center hidden-phone" width="5%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_PLATFORM', 'a.platform', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap center hidden-phone" width="5%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_VISITOR_REFERER', 'a.referer', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap center" width="5%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_VISITOR_DATE', 'a.tm', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap center hidden-phone" width="5%">
                    <?php echo JText::_('COM_CWTRAFFIC_VISITOR_TIME'); ?>
                </th>
                <th width="22%" class="nowrap hidden-phone">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_IP_OWNER', 'w.visitors', $listDirn, $listOrder); ?>
                </th>
                <th width="27%">
                    <?php echo JHTML::_('grid.sort', JText::_('COM_CWTRAFFIC_HEADER_LOCATION'), 'a.country_name', $listDirn, $listOrder); ?>
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
            <?php foreach ($this->items as $i => $item) : ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
                        <?php echo $this->escape($item->id); ?>
                    </td>
                    <td <td class="center hidden-phone">
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>
                    <td class="center">
                        <span class="editlinktip hasTip" title="<?php echo JText::_('COM_CWTRAFFIC_TT_IPINFO'); ?>">
                            <a href="http://ip-lookup.net/index.php?ip=<?php echo $item->ip ?>" target="_blank">
                                <i class="icon-search icon-white"></i>
                            </a>
                        </span>
                        <?php echo $this->escape($item->ip); ?>
                    </td>
                    <?php if ($item->browser) { ?>
                        <td class="nowrap center hidden-phone">
                            <?php echo $item->browser; ?> 
                        </td>
                    <?php } else { ?>
                        <td class="nowrap center hidden-phone">
                            <?php echo JText::_('COM_CWTRAFFIC_UNKNOWN'); ?> 
                        </td>
                    <?php } ?>
                    
                    <td class="nowrap center hidden-phone">
                        <?php echo $this->escape($item->bversion); ?>
                    </td>
                    
                    <td class="nowrap center hidden-phone">
                        <?php echo $this->escape($item->platform);?>
                    </td>
                    
                    <td class="nowrap center hidden-phone">
                        <a href="<?php echo $this->escape($item->referer); ?>" target="_blank"><?php echo JText::_('COM_CWTRAFFIC_VISITOR_REFERER') ?></a>
                    </td>
                    <td class="nowrap center">
                        <?php
                        $date = JHtml::date($item->tm, 'Y-m-d', false);
                        echo $date;
                        ?>
                    </td>
                    <td class="nowrap center hidden-phone">
                        <?php
                        $time = JHtml::date($item->tm, 'H:i', false);
                        echo $time;
                        ?>
                    </td>  
                    <?php $knownip = CoalawebtrafficHelperIptools::titleinList($item->ip, $this->knownips); ?>
                    <?php if ($knownip) { ?>
                        <td class="nowrap hidden-phone">
                            <?php echo $knownip ?> 
                        </td>
                    <?php } else { ?>
                        <td class="nowrap hidden-phone">
                            <?php echo JText::_('COM_CWTRAFFIC_UNKNOWN'); ?> 
                        </td>
                    <?php } ?>
                    <td>
                        <?php
                        if (empty($item->country_code)) {
                            echo JText::_('COM_CWTRAFFIC_LOCATION_UNKNOWN');
                        } else {
                            echo JHTML::_('image', 'media/coalawebtraffic/components/traffic/flags/' . $item->country_code . '.png', $item->country_code);
                            echo " " . $item->country_id;
                            if (empty($item->city)) {
                                echo ", " . JText::_('COM_CWTRAFFIC_LOCATION_UNKNOWN');
                            } else {
                                echo ", " . $item->city;
                            }
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
