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
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'knownip.cancel' || document.formvalidator.isValid(document.id('knownip-form'))) {
<?php echo $this->form->getField('description')->save(); ?>
            Joomla.submitform(task, document.getElementById('knownip-form'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_coalawebtraffic&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="knownip-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo empty($this->item->id) ? JText::_('COM_CWTRAFFIC_NEW_IP') : JText::sprintf('COM_CWTRAFFIC_EDIT_IP', $this->item->id); ?></legend>
            <ul class="adminformlist">
                <li>
                    <?php echo $this->form->getLabel('title'); ?>
                    <?php echo $this->form->getInput('title'); ?>
                </li>

                <li>
                    <?php echo $this->form->getLabel('alias'); ?>
                    <?php echo $this->form->getInput('alias'); ?>
                </li>


                <li>
                    <?php echo $this->form->getLabel('catid'); ?>
                    <?php echo $this->form->getInput('catid'); ?>
                </li>

                <li>
                    <?php echo $this->form->getLabel('state'); ?>
                    <?php echo $this->form->getInput('state'); ?>
                </li>

                <li>
                    <?php echo $this->form->getLabel('ordering'); ?>
                    <?php echo $this->form->getInput('ordering'); ?>
                </li>

                <li>
                    <?php echo $this->form->getLabel('id'); ?>
                    <?php echo $this->form->getInput('id'); ?>
                </li>
            </ul>

            <div>
                <?php echo $this->form->getLabel('description'); ?>
                <div class="clr"></div>
                <?php echo $this->form->getInput('description'); ?>
            </div>
        </fieldset>
    </div>

    <div class="width-40 fltrt">
        <?php echo JHtml::_('sliders.start', 'knownip-sliders-' . $this->item->id, array('useCookie' => 1)); ?>

        <?php echo JHtml::_('sliders.panel', JText::_('COM_CWTRAFFIC_SLIDER_TITLE_GENERAL'), 'ip-details'); ?>

        <fieldset class="panelform">
            <span class="cw-message">
            <p class="info"><?php echo JText::_('COM_CWTRAFFIC_IP_OPTION_MSG'); ?></p>
            </span>
            <ul class="adminformlist">

                <li>
                    <?php echo $this->form->getLabel('ip'); ?>
                    <?php echo $this->form->getInput('ip'); ?>
                </li>

                <li>
                    <?php echo $this->form->getLabel('botname'); ?>
                    <?php echo $this->form->getInput('botname'); ?>
                </li>
                <li>
                    <?php echo $this->form->getLabel('count'); ?>
                    <?php echo $this->form->getInput('count'); ?>
                </li>
                

            </ul>
        </fieldset>
        
        <?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_PUBLISHING'), 'publishing-details'); ?>

        <fieldset class="panelform">
            <ul class="adminformlist">
                <li>
                    <?php echo $this->form->getLabel('created_by'); ?>
                    <?php echo $this->form->getInput('created_by'); ?>
                </li>

                <li>
                    <?php echo $this->form->getLabel('created_by_alias'); ?>
                    <?php echo $this->form->getInput('created_by_alias'); ?>
                </li>

                <li>
                    <?php echo $this->form->getLabel('created'); ?>
                    <?php echo $this->form->getInput('created'); ?>
                </li>

                <?php if ($this->item->modified_by) : ?>
                    <li><?php echo $this->form->getLabel('modified_by'); ?>
                        <?php echo $this->form->getInput('modified_by'); ?></li>

                    <li><?php echo $this->form->getLabel('modified'); ?>
                        <?php echo $this->form->getInput('modified'); ?></li>
                    <?php endif; ?>

            </ul>
        </fieldset>

        <?php echo JHtml::_('sliders.end'); ?>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
    <div class="clr"></div>
</form>
