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
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_coalawebtraffic&task=knownips.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'knownipList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&view=knownips'); ?>" method="post" name="adminForm" id="adminForm">
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

            <?php
            // Search tools bar
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
            ?>
            <?php if (empty($this->items)) : ?>
                <?php if (isset($this->nocategory)) : ?>
                    <div class="alert alert-no-items">
                        <?php echo $this->nocategory ?>
                    </div>
                <?php endif ?>
                <div class="alert alert-no-items">
                    <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                </div>
            <?php else : ?>
                <table class="table table-striped" id="knownipList">
                    <thead>
                        <tr>
                            <th width="1%">
                                <?php echo JHtml::_('grid.checkall'); ?>
                            </th>
                            <th width="1%" class="nowrap center hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                            </th>
                            <th width="30%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                            </th>
                            <th width="20%" class="nowrap">
                                <?php echo JHtml::_('searchtools.sort', 'COM_CWTRAFFIC_VISITOR_IP', 'a.ip', $listDirn, $listOrder); ?>
                            </th>
                            <th width="20%" class="nowrap">
                                <?php echo JHtml::_('searchtools.sort', 'COM_CWTRAFFIC_TITLE_BOTNAME', 'a.botname', $listDirn, $listOrder); ?>
                            </th>
                            <th width="3%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                            </th>                             
                            <th width="3%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('searchtools.sort', 'COM_CWTRAFFIC_TITLE_COUNT', 'a.count', $listDirn, $listOrder); ?>
                            </th>
                            <th width="15%" class="nowrap hidden-phone">
                                <?php echo JHtml::_('searchtools.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
                            </th>
                            <th width="1%" class="nowrap">
                                <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        foreach ($this->items as $i => $item) :
                            $canEdit = $user->authorise('core.edit', 'com_coalawebcontact.cwcontact_customfield.' . $item->id);
                            $canCheckin = $user->authorise('core.manage', 'com_checkin');
                            $canEditOwn = $user->authorise('core.edit.own', 'com_coalawebcontact.cwcontact_customfield.' . $item->id);
                            $canChange = $user->authorise('core.edit.state', 'com_coalawebcontact.cwcontact_customfield.' . $item->id) && $canCheckin;
                            $ordering  = ($listOrder == 'ordering');
                            ?>
                            <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">
                                <td class="center hidden-phone">
                                    <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                                </td>
                                
                                                            <!-- item ordering -->
                            <td class="order nowrap center hidden-phone">
                                <?php
                                if ($canChange) :
                                    $disableClassName = '';
                                    $disabledLabel = '';
                                    if (!$saveOrder) :
                                        $disabledLabel = JText::_('JORDERINGDISABLED');
                                        $disableClassName = 'inactive tip-top';
                                    endif;
                                    ?>
                                    <span class="sortable-handler hasTooltip <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>">
                                        <i class="icon-menu"></i>
                                    </span>
                                    <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
    <?php else : ?>
                                    <span class="sortable-handler inactive" >
                                        <i class="icon-menu"></i>
                                    </span>
    <?php endif; ?>
                            </td>
                            
                            <!-- item main field -->
                            <td class="nowrap has-context">
                                <div class="pull-left">
                                    <?php if ($canEdit || $canEditOwn) : ?>
                                        <a href="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&task=knownip.edit&id=' . (int) $item->id); ?>">
                                        <?php echo $item->title; ?></a>						
                                    <?php else : ?>
                                        <?php echo $item->title; ?>								
                                    <?php endif; ?>
                                    <?php
                                        $limit = (300 - 3); // limits description to 300 the minus 3 is for the ...
                                        $description = JString::substr(strip_tags($item->description), 0, $limit)."...";
                                    ?>
                                    <span class="small break-word">
                                        <?php echo JText::sprintf('COM_CWTRAFFIC_VISITOR_DESCRIPTION', $this->escape($description)); ?>
                                    </span>
                                </div>
                                <div class="pull-left">
                                    <?php
                                    // Create dropdown items
                                    JHtml::_('dropdown.edit', $item->id, 'knownip.');
                                    if (!isset($this->items[0]->published) || $this->state->get('filter.published') == -2) :
                                        JHtml::_('dropdown.addCustomItem', JText::_('JTOOLBAR_DELETE'), 'javascript:void(0)', "onclick=\"contextAction('cb$i', 'knownips.delete')\"");
                                    endif;
                                    JHtml::_('dropdown.divider');

                                    // render dropdown list
                                    echo JHtml::_('dropdown.render');
                                    ?>
                                </div>
                            </td>
                                <td>
                                    <?php echo $this->escape($item->ip); ?>
                                </td>

                                <td>
                                    <?php echo $this->escape($item->botname); ?>
                                </td>
                           <td class="center">
                                <div class="btn-group">
                                    <?php echo JHtml::_('jgrid.published', $item->state, $i, 'knownips.', $canChange); ?>
                                    <?php
                                    // Create dropdown items
                                    $action = $archived ? 'unarchive' : 'archive';
                                    JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'knownips');

                                    $action = $trashed ? 'untrash' : 'trash';
                                    JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'knownips');

                                    // Render dropdown list
                                    echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
                                    ?>
                                </div>
                            </td>
                                <td class="center">
                                    <?php echo JHtml::_('knownip.counted', $item->count, $i, $canChange); ?>
                                </td>
                                <td>
                                    <?php echo $this->escape($item->category_title); ?>
                                </td>

                                <td class="center hidden-phone">
                                    <?php echo (int) $item->id; ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <?php echo $this->pagination->getListFooter(); ?>

            <?php //Load the batch processing form.  ?>
            <?php if ($user->authorise('core.create', 'com_coalawebtraffic') && $user->authorise('core.edit', 'com_coalawebtraffic') && $user->authorise('core.edit.state', 'com_coalawebtraffic')) : ?>
                <?php echo $this->loadTemplate('batch'); ?>
            <?php endif; ?>

            <div>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo JHtml::_('form.token'); ?>
            </div>
        </form>
    </div>
