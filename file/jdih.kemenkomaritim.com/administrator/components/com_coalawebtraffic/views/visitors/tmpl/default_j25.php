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
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&view=visitors'); ?>" method="post" id="adminForm" name="adminForm">

    <fieldset id="filter-bar">
        <div class="filter-search fltlft">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CWTRAFFIC_SEARCH_LABEL'); ?>" />
            <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
            <button type="button"  onclick="document.id('filter_search').value = '';
                    this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        
    </fieldset>
    <div class="clr"> </div>

    <table class="adminlist">

        <thead>
            <tr>
                <th width="3%">
                    <?php echo JText::_('COM_CWTRAFFIC_HEADER_ID'); ?>
                </th>
                <th width="3%">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
                </th>			
                <th width="15%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_VISITOR_IP', 'a.ip', $listDirn, $listOrder); ?>
                </th>
                <th width="8%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_VISITOR_BROWSER', 'a.browser', $listDirn, $listOrder); ?>
                </th>
                <th width="2%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_BROWSER_VERSION', 'a.bversion', $listDirn, $listOrder); ?>
                </th>
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_PLATFORM', 'a.platform', $listDirn, $listOrder); ?>
                </th>
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_VISITOR_REFERER', 'a.referer', $listDirn, $listOrder); ?>
                </th>
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_VISITOR_DATE', 'a.tm', $listDirn, $listOrder); ?>
                </th>
                <th width="5%">
                    <?php echo JText::_('COM_CWTRAFFIC_VISITOR_TIME'); ?>
                </th>
                <th width="22%">
                    <?php echo JHtml::_('grid.sort', 'COM_CWTRAFFIC_IP_OWNER', 'w.title', $listDirn, $listOrder); ?>
                </th>
                <th width="27%">
                    <?php echo JHTML::_('grid.sort', JText::_('COM_CWTRAFFIC_HEADER_LOCATION'), 'a.country_name', $listDirn, $listOrder); ?>
                </th>		
            </tr>
        </thead>

        <tfoot>
            <tr>
                <td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
        </tfoot>

        <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
                        <?php echo $this->escape($item->id); ?>
                    </td>
                    <td class="center">
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>
                    <td class="center">
                        <a class="cw-info-icon" target="_blank" title="<?php echo JText::_('COM_CWTRAFFIC_TT_IPINFO'); ?>" href="http://ip-lookup.net/index.php?ip=<?php echo $item->ip ?>"></a>
                        <?php echo $this->escape($item->ip); ?>
                    </td>
                    
                    <?php if ($item->browser) { ?>
                        <td class="center">
                            <?php echo $item->browser; ?> 
                        </td>
                    <?php } else { ?>
                        <td class="cw_ipownerunknown"><?php echo JText::_('COM_CWTRAFFIC_UNKNOWN'); ?> </td>
                    <?php } ?>
                    
                    <td class="center">
                        <?php echo $this->escape($item->bversion); ?>
                    </td>
                    
                    <td class="center">
                        <?php echo $this->escape($item->platform);?>
                    </td>
                    
                    <td class="center">
                        <a href="<?php echo $this->escape($item->referer); ?>" target="_blank"><?php echo JText::_('COM_CWTRAFFIC_VISITOR_REFERER') ?></a>
                    </td>
                    
                    <td class="center">
                        <?php
                        $date = JHtml::date($item->tm, 'Y-m-d', false);
                        echo $date;
                        ?>
                    </td>
                    <td class="center">
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