<?php
/*
Plugin Name: WP Pineapple
Plugin URI: https://github.com/copify/wp-pineapple
Description: WP Pineapple adds a fruity looking anti-spam, anti-bot "are you human" test to your comment form.
Version: 1.1
Author: Rob McVey
Author URI: http://www.copify.com/
License: GPL2

Copyright 2012  Rob McVey  (email:rob@copify.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
class WpPineapple {

	protected $pluginName = 'WP Pineapple';
	
	protected $pluginFileName = 'WpPineapple';
	
	protected $pluginDir = 'wp-pineapple';

	protected $fruits = array('apple' , 'banana' , 'pineapple' , 'grapes');
	
	
	/**
	 * Handles the comment post data and checks we hase selected the pineapple
	 *
	 * @return mixed Returns the comment ready to save if fruit correct of dies..
	 * @author Rob Mcvey
	 **/
	public function WpPineappleHandleForm($comment) {
		
		if(!isset($_POST['wp_pineapple_fruit']) || empty($_POST['wp_pineapple_fruit'])) {
			wp_die(__("发表评论前需选对水果，请返回选择后，再发表评论！"));
		}
		
		// Hash of the fruit they selected
		$selectedFruitHash = $_POST['wp_pineapple_fruit'];

		// Correct value - we expect this to be chose for the comment to be NOT spam
		$correct_value = md5($this->WpPineappleGetSecretKey().$comment['comment_post_ID']);
				
		if($selectedFruitHash != $correct_value) {
			wp_set_comment_status($post_id, 'delete');
			wp_die(__("水果没选对，请返回重新选择！"));
			exit;
		} else {
			return $comment;
		}

	}
	
	
	/**
	 * Adds the fruity radios to the comment form.
	 * We assign a unique hash to each form input, each will can be decoded to their full name,
	 * and we use the IP as the base of the hash as this is highly unlikely to change from the
	 * comment form loading to the comment being submitted
	 *
	 * @return void Outputs HTML form inputs
	 * @author Rob Mcvey
	 **/
	public function WpPineappleRenderFruit($post_id) {

		// Decide which is our lucky fruit
		$random_fruit = array_rand($this->fruits);

		// Build an option for each 
		$options = '';
		
		// Correct value - we expect this to be chose for the comment to be NOT spam
		$correct_value = md5($this->WpPineappleGetSecretKey().$post_id);
		
		// Styles for fruity div
		$divStyles = 'cursor:pointer;height:50px;width:40px;display:inline-block;padding:2px;';
		$divStyles .= 'background-repeat:no-repeat;background-position:0 0;';
		
		// Styles for the radio input
		$inputStyles = 'display:none;';
		
		foreach($this->fruits as $k => $fruit) {
			
			// if it our lucky fruit the value is hash of secret key	
			if($k == $random_fruit) {
				$hash = $correct_value;
			} else {
				$hash = md5(uniqid());
			}
			
			// Path to image
			$img_url = plugins_url($this->pluginDir.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$fruit.'.png');
			
			// Add background image to div styles
			$divStyles .= 'background-image:url('.$img_url.');';

			// Build each radio input
			$options .= '<span class="wp_pineapple_div" style="'.$divStyles.'">' . "\n";
			$options .= '<input class="wp_pineapple_radio" style="'.$inputStyles.'" value="'.$hash.'" name="wp_pineapple_fruit" type="radio" />' . "\n";
			$options .= '</span>' . "\n";
			
		}
		
		// What's our form label? I.e which fruit do they neeed to pick to prove human brain
		$form_label = sprintf(__('发表评论前，请选对水果【Apple=苹果、Banana=香蕉、Pineapple=菠萝、Grapes=葡萄】 %s...') , ucfirst($this->fruits[$random_fruit]));
		
		// The form label
		echo $form_label;
		
		// The Div with everything
		echo '<div style="margin:20px 0;">' . "\n";
		echo $options;
		echo '</div>' . "\n";

	}
	
		
	/**
	 * Gets or sets a unique secret key for this install and adds to wp_options
	 *
	 * @return string $secretKey
	 * @author Rob Mcvey
	 **/
	public function WpPineappleGetSecretKey() {
		$secretKey = get_option('wp_pineapple_secret' , false);
		if(!$secretKey && add_option('wp_pineapple_secret', uniqid(), null, 'no')) {
			return get_option('wp_pineapple_secret' , false);
		}
		return $secretKey;
	}
	
	
	/**
	 * Add our JS to the doc head
	 *
	 * @return void
	 * @author Rob Mcvey
	 **/
	public function WpPineappleJs() {
		$js_url = plugins_url($this->pluginDir.DIRECTORY_SEPARATOR.'WpPineapple.js');
		wp_enqueue_script('wppineapple' , $js_url, array('jquery'));
		echo '<script type="text/javascript" src="'.$js_url.'" ></script>';
	}


}

// Initialise the WPPineapple class
$WpPineapple = new WpPineapple();

// Assign WpPineapple::WpPineappleHandleForm() to the `comment_post` hook
add_action('preprocess_comment', array($WpPineapple, 'WpPineappleHandleForm'));

// Assign WpPineapple::WpPineappleRenderFruit() to the `comment_form` hook
add_action('comment_form', array($WpPineapple, 'WpPineappleRenderFruit'));

// Modify the doc head to add css and jQuery
add_action('wp_print_scripts', array($WpPineapple, 'WpPineappleJs'));