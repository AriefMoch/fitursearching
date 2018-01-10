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
        <?php if ($sDigital) : ?>
            <div class="cwt-digi-counter">
                <?php echo $digitalCounter; ?>
            </div>
        <?php endif; ?>
        <?php if ($sIndividual) : ?>
            <div class="cwt-indi-counter-<?php echo $counterWidth; ?>">
                <?php if ($s_today) : ?>
                    <div class="cwt-icon">
                        <span class="cwt-stats-lt-<?php echo $select_theme ?>"><?php echo $today ?></span><span class="cw_stats_r0"><?php echo $today_visitors; ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($s_yesterday) : ?>
                    <div class="cwt-icon">
                        <span class="cwt-stats-ly-<?php echo $select_theme ?>"><?php echo $yesterday ?></span><span class="cw_stats_r0"><?php echo $yesterday_visitors; ?></span>
                    </div>
                <?php endif; ?>	
                <?php if ($s_week) : ?>
                    <div class="cwt-icon">
                        <span class="cwt-stats-lw-<?php echo $select_theme ?>"><?php echo $x_week ?></span><span class="cw_stats_r0"><?php echo $week_visitors; ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($s_month) : ?>
                    <div class="cwt-icon">
                        <span class="cwt-stats-lm-<?php echo $select_theme ?>"><?php echo $x_month ?></span><span class="cw_stats_r0"><?php echo $month_visitors; ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($s_all) : ?>
                    <div class="cwt-icon">
                        <span class="cwt-stats-la-<?php echo $select_theme ?>"><?php echo $all ?></span><span class="cw_stats_r0"><?php echo $all_visitors; ?></span>
                    </div>
                <?php endif; ?>
            </div>
        
            <?php if ($hline): ?>
                <hr/>
            <?php endif; ?>
            
        <?php endif; ?>

        <?php if ($sVisitorInfo) : ?>
        <!--Start Visitor Info -->
            <div class="cwt-vi">
                <?php if ($sTitleVisit) : ?>
                    <h<?php echo $tFormatVisit ?> class="<?php echo $tAlignVisit ?>">
                        <?php echo $titleVisitor ?>
                    </h<?php echo $tFormatVisit ?>>
                <?php endif; ?>

                <ul>
                    <?php if ($s_guestip) : ?>
                        <li>
                            <?php echo $guestip . ' <em>' . $ip ?></em>
                        </li>
                    <?php endif; ?>
                    <?php if ($sGuestBrowser) : ?>
                        <li>
                            <?php echo $guestBrowser . ' <em>' . $browser; ?></em>
                        </li>
                        <li>
                            <?php echo $guestBrowserV . ' <em>' . ' ' . $browserVersion; ?></em>
                        </li>
                    <?php endif; ?>
                    <?php if ($sGuestOs) : ?>
                        <li>
                            <?php echo $guestOs . ' <em>' . $platform; ?></em>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <?php if ($hlineVisitor) : ?>
                <hr/>
            <?php endif; ?>
        <!-- End Visitor Info -->
        <?php endif; ?>

        <?php if ($s_whoisonline) : ?>
        <!-- Start Who is Online --> 
            <div class="cwt-wio">
                <?php if ($display_title_who) : ?>
                    <h<?php echo $title_format_who ?> class="<?php echo $title_align_who ?>">
                        <?php echo $title_who; ?>
                    </h<?php echo $title_format_who ?>>
                <?php endif; ?>

                <div class="cwt-wio-inner">
                    <div class="cwt-wio-count"><?php echo $realCount; ?></div>
                    <div class="cwt-wio-label"><?php echo $subtitle_who; ?></div>
                </div>
            </div>
        
            <?php if ($hlineWho): ?>
                <hr/>
            <?php endif; ?>
                
        <!-- End Who is Online -->
        <?php endif; ?>

        <?php if ($s_dateTime) : ?>
            <div class="cwt-datetime">
        <?php echo $date ?>
        </div>
        <?php endif; ?>
    </div>
</div>
