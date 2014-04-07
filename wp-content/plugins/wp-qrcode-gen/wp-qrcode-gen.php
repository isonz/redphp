<?php
/*
Plugin Name: WP QRCode Widget
Plugin URI: http://www.appinstore.com/2013/07/wordpress-qr-code-widgetr-generate.html
Description:  a simple widget that displays the downloadable QR Code of the current location in website.
Version: 1.0
Author: Jatinder Pal Singh
Author URI: http://www.appinstore.com
License: GPLv2
*/


add_action( 'widgets_init', 'jps_create_widgets' );

function jps_create_widgets() {
	register_widget( 'wp_qrcode_gen' );
	}
	
class wp_qrcode_gen extends WP_Widget {
	function __construct () {
		parent::__construct( 'wp_qrcode', 'QR Code Generator', array ('description' => 'Displays the downloadable QR Code Image' ) );
	}
	
	function widget( $args, $instance ) {
	    extract( $args );
        $title = apply_filters('widget_title', $instance['title'] ); 
		echo $before_widget;    
		if ( $title )  
			echo $before_title . $title . $after_title;  
		
		$url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		?>
		<!-- 
		<a href="<?php echo plugins_url()."/wp-qrcode-gen/"; ?>saveas.php?website=<?php bloginfo('name');?>&image_path=http://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo $url; ?>" target="_blank">
		-->
		<img src="http://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo $url; ?>" title="<?php echo $url; ?>" alt="QR: <?php echo $url; ?>" />
		<!-- 
		</a>
		-->
		<br /><sub>扫描二维码打开本页地址</sub>
		<?php
		echo $after_widget;
	}
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance; 
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance; 
	}
	function form($instance)
	{
		$defaults = array( 'title' => __('QR Code', 'example'));  
		$instance = wp_parse_args( (array) $instance, $defaults );
?>
<p>  
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'example'); ?></label>  
    <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />  
</p>  
<?php
	}
}
/*EOF*/