<?php

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Home Widgets Template
 *
 *
 * @file           sidebar-home.php
 * @package        Responsive
 * @author         Emil Uzelac
 * @copyright      2003 - 2014 CyberChimps
 * @license        license.txt
 * @version        Release: 1.0
 * @filesource     wp-content/themes/responsive/sidebar-home.php
 * @link           http://codex.wordpress.org/Theme_Development#Widgets_.28sidebar.php.29
 * @since          available since Release 1.0
 */
?>
<?php responsive_widgets_before(); // above widgets container hook ?>
	<div id="widgets" class="home-widgets">
		<div id="home_widget_1" class="grid col-300">
			<?php responsive_widgets(); // above widgets hook ?>

			<?php if( !dynamic_sidebar( 'home-widget-1' ) ) : ?>
				<div class="widget-wrapper">

					<div class="widget-title-home"><h3>
					本站支持所有浏览设备
					</h3></div>
					<div class="textwidget">
						<img class="aligncenter" src="/wp-content/themes/responsive/core/images/featured-image.png" alt="支持所有设备">
					</div>
				</div><!-- end of .widget-wrapper -->
			<?php endif; //end of home-widget-1 ?>

			<?php responsive_widgets_end(); // responsive after widgets hook ?>
		</div>
		<!-- end of .col-300 -->
		<div id="home_widget_2" class="grid col-300">
			<?php responsive_widgets(); // responsive above widgets hook ?>

			<?php if( !dynamic_sidebar( 'home-widget-2' ) ) : ?>
				<div class="widget-wrapper"><div class="widget-title-home"><h3>
				您的位置信息
				</h3></div>
				<div class="textwidget" style="padding-bottom: 17px;">
					IP:<?php echo $ip = getIP(); ?><br>
					<?php echo  str_replace(".", '', preg_replace("[\d]", '', mb_convert_encoding(file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?ip=$ip"), 'utf-8', 'gbk')));?>
					<br><br>
					<iframe name="weather_inc" src="http://i.tianqi.com/index.php?c=code&id=7" width="225" height="90" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe>
					<br><br>
				</div>
			</div><!-- end of .widget-wrapper -->
			<?php endif; //end of home-widget-2 ?>

			<?php responsive_widgets_end(); // after widgets hook ?>
		</div>
		<!-- end of .col-300 -->

		<div id="home_widget_3" class="grid col-300 fit">
			<?php responsive_widgets(); // above widgets hook ?>

			<?php if( !dynamic_sidebar( 'home-widget-3' ) ) : ?>
				<div class="widget-wrapper"><div class="widget-title-home"><h3>
				<?php _e( 'Home Widget 3', 'responsive' ); ?>
				</h3></div>
					<div class="textwidget">
					<?php _e( 'This is your third home widget box. To edit please go to Appearance > Widgets and choose 8th widget from the top in area 8 called Home Widget 3. Title is also manageable from widgets as well.', 'responsive' ); ?>
					</div>
				</div><!-- end of .widget-wrapper -->
			<?php endif; //end of home-widget-3 ?>

			<?php responsive_widgets_end(); // after widgets hook ?>
		</div>
		<!-- end of .col-300 fit -->
	</div><!-- end of #widgets -->
<?php responsive_widgets_after(); // after widgets container hook ?>