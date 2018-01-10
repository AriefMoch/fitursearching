<?php
defined('_JEXEC') or die('Restricted access');
/**
 * @package             Joomla
 * @subpackage          CoalaWeb Traffic Module
 * @author              Steven Palmer
 * @author url          http://coalaweb.com
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /files/en-GB.license.txt
 * @copyright           Copyright (c) 2017 Steven Palmer All rights reserved.
 *
 * The CoalaWeb traffic module was inspired by VCNT Thanks to Viktor Vogel {@link http://joomla-extensions.kubik-rubik.de/}
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
?>

<div class="<?php echo $moduleclass_sfx ?>">
    <div class="cw-mod-traffic-<?php echo $cssWidth; ?>" id="cw-traffic-<?php echo $uniqueId ?>">
        <div class="cwt-hor">
            <ul class="cwt-hor-items">
                <?php $numbers = $s_today + $s_yesterday + $s_week + $s_month + $s_all - 1; ?>
                <?php if ($sDigital && $horDigital) : ?>
                    <li>
                        <div class="cwt-digi-counter">
                            <?php echo $digitalCounter; ?>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if ($sHorText) : ?>
                    <li>
                        <?php echo $hor_text ?>
                    </li>
                <?php endif; ?>
                <?php if ($sDigital && !$horDigital) : ?>
                    <li>
                        <div class="cwt-digi-counter">
                            <?php echo $digitalCounter; ?>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if ($s_today) : ?>
                    <li>
                        <?php echo $today . ' ' . $today_visitors ?>
                    </li>
                <?php endif; ?>
                <?php if ($numbers AND $s_today) : ?>
                    <li>
                        <?php echo $separator ?>
                        <?php $numbers-- ?>
                    </li>
                <?php endif; ?>
                <?php if ($s_yesterday) : ?>
                    <li>
                        <?php echo $yesterday . ' ' . $yesterday_visitors ?>
                    </li>
                <?php endif; ?>
                <?php if ($numbers AND $s_yesterday) : ?>
                    <li>
                        <?php echo $separator ?>
                        <?php $numbers-- ?>
                    </li>
                <?php endif; ?>
                <?php if ($s_week) : ?>
                    <li>
                        <?php echo $x_week . ' ' . $week_visitors ?>
                    </li>
                <?php endif; ?>
                <?php if ($numbers AND $s_week) : ?>
                    <li>
                        <?php echo $separator ?>
                        <?php $numbers-- ?>
                    </li>
                <?php endif; ?>
                <?php if ($s_month) : ?>
                    <li>
                        <?php echo $x_month . ' ' . $month_visitors ?>
                    </li>
                <?php endif; ?>
                <?php if ($numbers AND $s_month) : ?>
                    <li>
                        <?php echo $separator ?>
                        <?php $numbers-- ?>
                    </li>
                <?php endif; ?>	
                <?php if ($s_all) : ?>
                    <li>
                        <?php echo $all . ' ' . $all_visitors ?>
                    </li>
                <?php endif; ?>
            </ul>
            <div style='clear:both'></div>
        </div>
    </div>
</div>
