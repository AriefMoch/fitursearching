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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$canOrder  = $user->authorise('core.edit.state', 'com_banners.category');
$archived  = $this->state->get('filter.state') == 2 ? true : false;
$trashed   = $this->state->get('filter.state') == -2 ? true : false;
$params    = (isset($this->state->params)) ? $this->state->params : new JObject;
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
<form action="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&view=visitors'); ?>" method="post" id="adminForm" name="adminForm">
            <?php
            // Search tools bar
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
            ?>
            <?php if (empty($this->items)) : ?>
                <div class="alert alert-no-items">
                    <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                </div>
            <?php else : ?>
                <table class="table table-striped" id="knownipList">
        <thead>
            <tr>
                <th width="1%" class="hidden-phone">
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                </th>			
                <th width="15%" class="nowrap center" >
                    <?php echo JHtml::_('searchtools.sort', 'COM_CWTRAFFIC_VISITOR_IP', 'a.ip', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap center hidden-phone" width="5%">
                    <?php echo JHtml::_('searchtools.sort', 'COM_CWTRAFFIC_VISITOR_BROWSER', 'a.browser', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap center hidden-phone" width="4%">
                    <?php echo JText::_('COM_CWTRAFFIC_BROWSER_VERSION'); ?>
                </th>
                <th class="nowrap center hidden-phone" width="5%">
                    <?php echo JHtml::_('searchtools.sort', 'COM_CWTRAFFIC_PLATFORM', 'a.platform', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap center hidden-phone" width="5%">
                    <?php echo JText::_('COM_CWTRAFFIC_VISITOR_REFERER'); ?>
                </th>
                <th class="nowrap center" width="5%">
                    <?php echo JHtml::_('searchtools.sort', 'COM_CWTRAFFIC_VISITOR_DATE', 'a.tm', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap center hidden-phone" width="5%">
                    <?php echo JText::_('COM_CWTRAFFIC_VISITOR_TIME'); ?>
                </th>
                <th width="20%" class="nowrap hidden-phone">
                    <?php echo JText::_('COM_CWTRAFFIC_IP_OWNER'); ?>
                </th>
                <th width="25%">
                    <?php echo JHTML::_('searchtools.sort', JText::_('COM_CWTRAFFIC_HEADER_LOCATION'), 'a.country_name', $listDirn, $listOrder); ?>
                </th>
                <th width="5%" class="nowrap center">
                    <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder) ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center hidden-phone">
                        <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>
                    <?php if ($params->get('store_raw', 1) && $item->ip != '0.0.0.0') : ?>
                        <td class="center">
                            <span class="editlinktip hasTip" title="<?php echo JText::_($item->ip); ?>">
                                <a href="http://ip-lookup.net/index.php?ip=<?php echo $item->ip ?>" target="_blank">
                                    <i class="icon-search icon-white"></i>
                                </a>
                            </span>
                            <?php echo $this->escape($item->ip); ?>
                        </td>
                    <?php else : ?>
                        <td class="center">
                            <?php echo JText::_('COM_CWTRAFFIC_STORERAW_MSG'); ?>
                        </td>
                    <?php endif ?>
                        
                    <?php if ($item->browser) : ?>
                        <td class="nowrap center hidden-phone">
                            <?php if ($item->browser === 'COM_CWTRAFFIC_IS_ROBOT') : ?>
                                <?php echo JText::_($item->browser); ?>
                            <?php else : ?>
                                <?php echo ucfirst($this->escape($item->browser)); ?>
                            <?php endif ?>
                        </td>
                    <?php else : ?>
                        <td class="nowrap center hidden-phone">
                            <?php echo JText::_('COM_CWTRAFFIC_UNKNOWN'); ?> 
                        </td>
                    <?php endif ?>
                        
                    <?php if ($item->bversion) : ?>
                        <td class="nowrap center hidden-phone">
                            <?php if ($item->bversion === 'COM_CWTRAFFIC_LOCATION_UNKNOWN') : ?>
                                <?php echo JText::_($item->bversion); ?>
                            <?php else : ?>
                                <?php echo ucfirst($this->escape($item->bversion)); ?>
                            <?php endif ?>
                        </td>
                    <?php else : ?>
                        <td class="nowrap center hidden-phone">
                            <?php echo JText::_('COM_CWTRAFFIC_UNKNOWN'); ?> 
                        </td>
                    <?php endif ?>
                    
                    <?php if ($item->platform) : ?>
                        <td class="nowrap center hidden-phone">
                            <?php if ($item->platform === 'COM_CWTRAFFIC_LOCATION_UNKNOWN') : ?>
                                <?php echo JText::_($item->bversion); ?>
                            <?php else : ?>
                                <?php echo ucfirst($this->escape($item->platform)); ?>
                            <?php endif ?>
                        </td>
                    <?php else : ?>
                        <td class="nowrap center hidden-phone">
                            <?php echo JText::_('COM_CWTRAFFIC_UNKNOWN'); ?> 
                        </td>
                    <?php endif ?>
                    
                    <td class="nowrap center hidden-phone">
                        <?php if ($item->referer === JText::_('COM_CWTRAFFIC_UNKNOWN')) : ?>
                            <?php echo JText::_('COM_CWTRAFFIC_UNKNOWN'); ?> 
                        <?php else : ?>
                            <a href="<?php echo $this->escape($item->referer); ?>" target="_blank"><?php echo JText::_('COM_CWTRAFFIC_VISITOR_REFERER') ?></a>
                        <?php endif ?>
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
                    <?php if ($knownip) : ?>
                        <td class="nowrap hidden-phone">
                            <?php echo $knownip ?> 
                        </td>
                    <?php else : ?>
                        <td class="nowrap hidden-phone">
                            <?php echo JText::_('COM_CWTRAFFIC_UNKNOWN'); ?> 
                        </td>
                    <?php endif ?>
                    <td>
                        <?php
                        if (empty($item->country_code)) {
                            echo JText::_('COM_CWTRAFFIC_LOCATION_UNKNOWN');
                        } else {
                            echo JHTML::_('image', 'media/coalawebtraffic/components/traffic/flags/' . $item->country_code . '.png', $item->country_code);
                            echo " " . $item->country_name;
                            if (empty($item->city)) {
                                echo ", " . JText::_('COM_CWTRAFFIC_LOCATION_UNKNOWN');
                            } else {
                                echo ", " . $item->city;
                            }
                        }
                        ?>
                    </td>
                    <td class="center">
                        <?php echo $this->escape($item->id); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
            <?php endif; ?>
            <?php echo $this->pagination->getListFooter(); ?>

    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
</div>
