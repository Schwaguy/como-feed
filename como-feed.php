<?php

/*
Plugin Name: Como Feed Embed
Plugin URI: http://www.comocreative.com/
Version: 1.0.8
Author: Como Creative LLC
Description: Plugin to display WordPress News Feed or IR Feed 
Shortcode example: Usage: [como-feed template='' feed-type=wpposts/irfeed id='' id='' limit='' category='' excerpt=true/false length='' link='' link-title='' clientid='']
Custom templates can be created in your theme in a folder named "como-feed" 
*/

defined('ABSPATH') or die('No Hackers!');

/* Include plugin updater. */
require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/updater.php' );

// Usage: [como-feed template='' feed-type=wpposts/irfeed id='' id='' limit='' category='' excerpt=true/false length='' link='' link-title='' clientid='']
class feed_widget_shortcode {
	//static $add_script;
	//static $add_style;
	static function init() {
		add_shortcode('como-feed', array(__CLASS__, 'handle_shortcode'));
	}
	static function handle_shortcode($atts) {
		//self::$add_style = false;
		//self::$add_script = false;
		
		if (!is_admin()) {

			$comoFeed = ''; 
			$feedType = (isset($atts['feed-type']) ? $atts['feed-type'] : 'wpposts');
			$limit = (isset($atts['limit']) ? $atts['limit'] : -1);
			$category = (isset($atts['category']) ? $atts['category'] : 'uncategorized');
			$id = (isset($atts['id']) ? $atts['id'] : '');
			$feedURL = (isset($atts['feedURL']) ? $atts['feedURL'] : ((isset($atts['feedurl'])) ? $atts['feedurl'] : ''));
			$class = (isset($atts['class']) ? $atts['class'] : 'investor-feed-list');
			$excerpt = (isset($atts['excerpt']) ? $atts['excerpt'] : false);
			$length = (isset($atts['length']) ? $atts['length'] : 150);
			$link = (isset($atts['link']) ? $atts['link'] : '');
			$linkTitle = (isset($atts['link-title']) ? $atts['link-title'] : 'More News');
			$clientid = (isset($atts['clientid']) ? $atts['clientid'] : '');
			$template = (isset($atts['template']) ? $atts['template'] : '');

			if ($feedType == 'irfeed') {
				if (!empty($clientid)) {
					$uri = 'https://clientapi.gcs-web.com/data/'. $clientid .'/news?size='. $limit;
					$irFeed = file_get_contents($uri);
					$irFeed = json_decode($irFeed);
					$cmsDomain = $irFeed->{'meta'}->{'cmsDomain'};
					$posts = $irFeed->{'data'};
					if (!empty($template)) {
						$temp = (is_child_theme() ? get_stylesheet_directory() : get_template_directory() ) . '/como-feed/'. $template .'.php';
						if (file_exists($temp)) {
							include($temp);
						} else {
							include(plugin_dir_path( __FILE__ ) .'templates/'. $template .'.php');
						}
					} else {
						include(plugin_dir_path( __FILE__ ) .'templates/default-rest.php');
					}
				} else {
					$comoFeed = '<strong>You Must Specity a Client ID</strong>';
				}
				//  wp_reset_query();
			} elseif ($feedType == 'irfeed-xml') {
				if (!empty($feedURL)) {
					//$irFeedArray = xml2array($feedURL, $get_attributes=1, $priority='tag');
					if (!empty($template)) {
						$temp = (is_child_theme() ? get_stylesheet_directory() : get_template_directory() ) . '/como-feed/'. $template .'.php';
						if (file_exists($temp)) {
							include($temp);
						} else {
							include(plugin_dir_path( __FILE__ ) .'templates/'. $template .'.php');
						}
					} else {
						include(plugin_dir_path( __FILE__ ) .'templates/default-xml.php');
					}
				} else {
					$comoFeed = '<strong>You Must Specity a Feed URL</strong>';
				}
				//wp_reset_query();
			} else {
				$args = array(
					'post_type'				=>'post',
					'category_name'			=> $category,
					'post_status'			=> 'publish',
					'posts_per_page'		=> $limit,
					'orderby'				=> 'date',
					'order'					=> 'DESC'
				);
				query_posts($args);
				if (have_posts()) {
					if (!empty($template)) {
						$temp = (is_child_theme() ? get_stylesheet_directory() : get_template_directory() ) . '/como-feed/'. $template .'.php';
						if (file_exists($temp)) {
							include($temp);
						} else {
							include(plugin_dir_path( __FILE__ ) .'templates/'. $template .'.php');
						}
					} else {
						include(plugin_dir_path( __FILE__ ) .'templates/default.php');
					}
				}
				wp_reset_query();
			}
			return $comoFeed;
		}
	}
}
feed_widget_shortcode::init();

/********* TinyMCE Button Add-On ***********/
add_action( 'after_setup_theme', 'comofeed_button_setup' );
if (!function_exists('comofeed_button_setup')) {
    function comofeed_button_setup() {
        add_action( 'init', 'comofeed_button' );
    }
}
if ( ! function_exists( 'comofeed_button' ) ) {
    function comofeed_button() {
        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
            return;
        }
        if ( get_user_option( 'rich_editing' ) !== 'true' ) {
            return;
        }
        add_filter( 'mce_external_plugins', 'comofeed_add_buttons' );
        add_filter( 'mce_buttons', 'comofeed_register_buttons' );
    }
}
if ( ! function_exists( 'comofeed_add_buttons' ) ) {
    function comofeed_add_buttons( $plugin_array ) {
        $plugin_array['comoFeedButton'] = plugin_dir_url( __FILE__ ) .'js/tinymce_feed_button.js';
        return $plugin_array;
    }
}
if ( ! function_exists( 'comofeed_register_buttons' ) ) {
    function comofeed_register_buttons( $buttons ) {
        array_push( $buttons, 'comoFeedButton' );
        return $buttons;
    }
}

add_action ( 'after_wp_tiny_mce', 'comofeed_tinymce_extra_vars' );
if ( !function_exists( 'comofeed_tinymce_extra_vars' ) ) {
	function comofeed_tinymce_extra_vars() { 
		// Get Templates
		$feedTemplates[] = array('value'=>'default','text'=>'Default');
		$feedTemplates[] = array('value'=>'default-rest','text'=>'Default REST Feed');
		$templateDir = (is_child_theme() ? get_stylesheet_directory() : get_template_directory() ) . '/como-feed/';
		if ($handle = opendir($templateDir)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$feedTemplates[] = array('value'=>basename($entry, '.php'),'text'=>basename($entry, '.php'));
				}
			}
			closedir($handle);
		}
		$feedTemplates = json_encode($feedTemplates);
		
		// Get Post Categories
		$cats = get_categories(array(
			'orderby' => 'name',
    		'order'   => 'ASC'
		));

		$catArr = array();	
		if (count($cats) > 0) {
			foreach ($cats as $cat) {
				$catArr[] = array('value'=>$cat->slug,'text'=>$cat->name);
			}
			$catArr = json_encode($catArr);
		}
		?>
		<script type="text/javascript">
			var tinyMCE_feed = <?php echo json_encode(
				array(
					'button_name' => esc_html__('Embed Feed', 'comofeed'),
					'button_title' => esc_html__('Embed Feed', 'comofeed'),
					'feed_template_select_options' => $feedTemplates,
					'feed_category_select_options' => $catArr
				)
			);
			?>;
		</script><?php
	} 
}

// Feed Widget
// Register and load the widget
function comofeed_load_widget() {
    register_widget( 'como_feed_widget' );
}
add_action( 'widgets_init', 'comofeed_load_widget' );
 
// Creating the widget 
class como_feed_widget extends WP_Widget {
	function __construct() {
		parent::__construct('como_feed_widget',	__('News Feed Widget', 'como-feed'),array( 'description' => __( 'Displays a Feed Widget', 'como-feed' ), ) 
		);
	}
 
	// Creating widget front-end
	public function widget( $args, $instance ) {
		extract($args, EXTR_SKIP);
    	
		$comoFeed = ''; 
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		$feedType = (isset($instance['feedType']) ? $instance['feedType'] : 'wpposts');
		$feedURL = (isset($instance['feedURL']) ? $instance['feedURL'] : '');
		$limit = (isset($instance['limit']) ? $instance['limit'] : -1);
		$category = (isset($instance['category']) ? $instance['category'] : 'uncategorized');
		$id = (isset($instance['feedID']) ? $instance['feedID'] : '');
		$class = (isset($instance['feedClass']) ? $instance['feedClass'] : 'investor-feed-list');
		$excerpt = (isset($instance['excerpt']) ? $instance['excerpt'] : false);
		$length = (isset($instance['length']) ? $instance['length'] : 150);
		$link = (isset($instance['link']) ? $instance['link'] : '');
		$linkTitle = (isset($instance['linkTitle']) ? $instance['linkTitle'] : 'More News');
		$clientid = (isset($instance['clientid']) ? $instance['clientid'] : '');
		$template = (isset($instance['template']) ? $instance['template'] : '');
		
		if ($feedType == 'irfeed') {
			if (!empty($clientid)) {
				$uri = 'https://clientapi.gcs-web.com/data/'. $clientid .'/news?size='. $limit;
				$irFeed = file_get_contents($uri);
				$irFeed = json_decode($irFeed);
				
				$cmsDomain = $irFeed->{'meta'}->{'cmsDomain'};
				$posts = $irFeed->{'data'};
				if (!empty($template)) {
					$temp = (is_child_theme() ? get_stylesheet_directory() : get_template_directory() ) . '/como-feed/'. $template .'.php';
					if (file_exists($temp)) {
						include($temp);
					} else {
						include(plugin_dir_path( __FILE__ ) .'templates/'. $template .'.php');
					}
				} else {
					include(plugin_dir_path( __FILE__ ) .'templates/default-rest.php');
				}
			} else {
				$comoFeed = '<strong>You Must Specity a Client ID</strong>';
			}
			//wp_reset_query();
		} elseif ($feedType == 'irfeed-xml') {
			if (!empty($feedURL)) {
				//$irFeedArray = xml2array($feedURL, $get_attributes=1, $priority='tag');
				if (!empty($template)) {
					$temp = (is_child_theme() ? get_stylesheet_directory() : get_template_directory() ) . '/como-feed/'. $template .'.php';
					if (file_exists($temp)) {
						include($temp);
					} else {
						include(plugin_dir_path( __FILE__ ) .'templates/'. $template .'.php');
					}
				} else {
					include(plugin_dir_path( __FILE__ ) .'templates/default-xml.php');
				}
			} else {
				$comoFeed = '<strong>You Must Specity a Feed URL</strong>';
			}
			wp_reset_query();
		} else {
			$args = array(
				'post_type'				=>'post',
				'category_name'			=> $category,
				'post_status'			=> 'publish',
				'posts_per_page'		=> $limit,
				'orderby'				=> 'date',
				'order'					=> 'DESC'
			);
			query_posts($args);
			if (have_posts()) {
				if (!empty($template)) {
					$temp = (is_child_theme() ? get_stylesheet_directory() : get_template_directory() ) . '/como-feed/'. $template .'.php';
					if (file_exists($temp)) {
						include($temp);
					} else {
						include(plugin_dir_path( __FILE__ ) .'templates/'. $template .'.php');
					}
				} else {
					include(plugin_dir_path( __FILE__ ) .'templates/default.php');
				}
			}
			wp_reset_query();
		}
		
		// Before widget code, if any
    	echo (isset($before_widget) ? $before_widget : '');
   
    	// PART 2: The title and the text output
    	if (!empty($title)) {
      		echo $before_title . $title . $after_title;
		}
    	if (!empty($comoFeed)) {
      		echo $comoFeed;
		}
   
    	// After widget code, if any  
    	echo (isset($after_widget) ? $after_widget : '');
	}

	// Widget Backend 
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
     	$title = ((isset($instance['title'])) ? $instance['title'] : '');
     	$feedType = ((isset($instance['feedType'])) ? $instance['feedType'] : '');
		$feedURL = ((isset($instance['feedURL'])) ? $instance['feedURL'] : '');
		$template = ((isset($instance['template'])) ? $instance['template'] : '');
		$feedID = ((isset($instance['feedID'])) ? $instance['feedID'] : '');
		$feedClass = ((isset($instance['feedClass'])) ? $instance['feedClass'] : '');
		$limit = ((isset($instance['limit'])) ? $instance['limit'] : '');
		$category = ((isset($instance['category'])) ? $instance['category'] : '');
		$excerpt = ((isset($instance['excerpt'])) ? $instance['excerpt'] : '');
		$length = ((isset($instance['length'])) ? $instance['length'] : '');
		$link = ((isset($instance['link'])) ? $instance['link'] : '');
		$linkTitle = ((isset($instance['linkTitle'])) ? $instance['linkTitle'] : '');
		$clientid = ((isset($instance['clientid'])) ? $instance['clientid'] : '');
		?>

		<!-- Widget Title field -->
		<p><label for="<?=$this->get_field_id('title')?>">Title: <input class="widefat" id="<?=$this->get_field_id('title')?>" name="<?=$this->get_field_name('title')?>" type="text" value="<?=$title?>" /></label></p>
      	
     	<!-- Widget Feed Type field -->
		<p><label for="<?=$this->get_field_id('feedType')?>">Type: <select class="widefat" id="<?=$this->get_field_id('feedType')?>" name="<?=$this->get_field_name('feedType')?>">
			<option value="wpposts" <?=(($feedType == 'wpposts') ? 'selected="selected"' : '')?>>WordPress Posts</option>
			<option value="irfeed" <?=(($feedType == 'irfeed') ? 'selected="selected"' : '')?>>IR Feed - Nasdaq/Cision REST</option>
			<option value="irfeed-xml" <?=(($feedType == 'irfeed-xml') ? 'selected="selected"' : '')?>>IR Feed - XML</option>
		</select></label></p>

		<!-- Feed Link field -->
		<p><label for="<?=$this->get_field_id('feedURL')?>">Feed URL: <input class="widefat" id="<?=$this->get_field_id('feedURL')?>" name="<?=$this->get_field_name('feedURL')?>" type="text" value="<?=$feedURL?>" /></label></p>

		<!-- Feed Template -->
		<p><label for="<?=$this->get_field_id('template')?>">Template: <select class="widefat" id="<?=$this->get_field_id('template')?>" name="<?=$this->get_field_name('template')?>">
			<?php
				$feedTemplates[] = array('value'=>'default','text'=>'Default');
				$feedTemplates[] = array('value'=>'default-rest','text'=>'Default REST Feed');
				$feedTemplates[] = array('value'=>'default-xml','text'=>'Default XML Feed');
				$templateDir = (is_child_theme() ? get_stylesheet_directory() : get_template_directory() ) . '/como-feed/';
				if ($handle = opendir($templateDir)) {
					while (false !== ($entry = readdir($handle))) {
						if ($entry != "." && $entry != "..") {
							$feedTemplates[] = array('value'=>basename($entry, '.php'),'text'=>basename($entry, '.php'));
						}
					}
					closedir($handle);
				}
				foreach ($feedTemplates as $temp) {
					?><option value="<?=$temp['value']?>" <?=(($template == $temp['value']) ? 'selected="selected"' : '')?>><?=$temp['text']?></option><?php
				}
			?>
		</select></label></p>	

		<!-- Feed ID field -->
		<p><label for="<?=$this->get_field_id('feedID')?>">Feed ID: <input class="widefat" id="<?=$this->get_field_id('feedID')?>" name="<?=$this->get_field_name('feedID')?>" type="text" value="<?=$feedID?>" /></label></p>

		<!-- Feed Class field -->
		<p><label for="<?=$this->get_field_id('feedClass')?>">Feed Class: <input class="widefat" id="<?=$this->get_field_id('feedClass')?>" name="<?=$this->get_field_name('feedClass')?>" type="text" value="<?=$feedClass?>" /></label></p>

		<!-- Feed Limit field -->
		<p><label for="<?=$this->get_field_id('limit')?>">Display Number: <input class="widefat" id="<?=$this->get_field_id('limit')?>" name="<?=$this->get_field_name('limit')?>" type="number" min="-1" max="100" step="1" value="<?=$limit?>" /></label><div class"como-note">Home many posts should be displayed</div></p>

		<!-- Feed Category -->
		<p><label for="<?=$this->get_field_id('category')?>">Category: <select class="widefat" id="<?=$this->get_field_id('category')?>" name="<?=$this->get_field_name('category')?>">
			<?php
				// Get Post Categories
				$cats = get_categories(array(
					'orderby' => 'name',
					'order'   => 'ASC'
				));
				$catArr = array();	
				if (count($cats) > 0) {
					foreach ($cats as $cat) {
						?><option value="<?=$cat->slug?>" <?=(($category == $cat->slug) ? 'selected="selected"' : '')?>><?=$cat->name?></option><?php
						$catArr[] = array('value'=>$cat->slug,'text'=>$cat->name);
					}
				}
			?>
		</select></label></p>

		<!-- Feed Excerpt field -->
		<p><label for="<?=$this->get_field_id('excerpt')?>">Show Excerpt: <input type="hidden" name="<?=$this->get_field_name('excerpt')?>" value="no" /><input type="checkbox" name="<?=$this->get_field_name('excerpt')?>" id="<?=$this->get_field_name('excerpt')?>" value="yes" <?php if (isset($excerpt)) checked($excerpt, 'yes'); ?> /></label></p>

		<!-- Excerpt Length field -->
		<p><label for="<?=$this->get_field_id('length')?>">Excerpt Length: <input class="widefat" id="<?=$this->get_field_id('length')?>" name="<?=$this->get_field_name('length')?>" type="number" min="0" max="600" step="1" value="<?=$length?>" /></label><div class"como-note">Length of excerpt</div></p>

		<!-- Feed Press Release Page field -->
		<p><label for="<?=$this->get_field_id('link')?>">Press Release Page Link: <input class="widefat" id="<?=$this->get_field_id('link')?>" name="<?=$this->get_field_name('link')?>" type="text" value="<?=$link?>" /></label></p>

		<!-- Feed Link Title field -->
		<p><label for="<?=$this->get_field_id('linkTitle')?>">Link Title: <input class="widefat" id="<?=$this->get_field_id('linkTitle')?>" name="<?=$this->get_field_name('linkTitle')?>" type="text" value="<?=$linkTitle?>" /></label></p>

		<!-- Client ID Field -->
		<p><label for="<?=$this->get_field_id('clientid')?>">Client ID: <input class="widefat" id="<?=$this->get_field_id('clientid'); ?>" name="<?=$this->get_field_name('clientid'); ?>" type="text" value="<?=$clientid?>" /></label><div class"como-note">Full client ID from IR provider</div></p>
      	
     	<?php 
	}
     
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
    	$instance['title'] = $new_instance['title'];
    	$instance['feedType'] = $new_instance['feedType'];
		$instance['template'] = $new_instance['template'];
		$instance['feedID'] = $new_instance['feedID'];
		$instance['feedURL'] = $new_instance['feedURL'];
		$instance['feedClass'] = $new_instance['feedClass'];
		$instance['limit'] = $new_instance['limit'];
		$instance['category'] = $new_instance['category'];
		$instance['excerpt'] = $new_instance['excerpt'];
		$instance['length'] = $new_instance['length'];
		$instance['link'] = $new_instance['link'];
		$instance['linkTitle'] = $new_instance['linkTitle'];
		$instance['clientid'] = $new_instance['clientid'];
    	return $instance;
	}
} // Class como_feed_widget ends here

if (!function_exists('xml2array')) {
	function xml2array($url, $get_attributes = 1, $priority = 'tag') {
		$contents = "";
		if (!function_exists('xml_parser_create')) {
			return array ();
		}
		$parser = xml_parser_create('');
		if (!($fp = @ fopen($url, 'rb'))) {
			return array ();
		}
		while (!feof($fp)) {
			$contents .= fread($fp, 8192);
		}
		fclose($fp);
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($contents), $xml_values);
		xml_parser_free($parser);
		if (!$xml_values)
			return; //Hmm...
		$xml_array = array ();
		$parents = array ();
		$opened_tags = array ();
		$arr = array ();
		$current = & $xml_array;
		$repeated_tag_index = array ();
		foreach ($xml_values as $data) {
			unset ($attributes, $value);
			extract($data);
			$result = array ();
			$attributes_data = array ();
			if (isset ($value)) {
				if ($priority == 'tag')
					$result = $value;
				else
					$result['value'] = $value;
			}
			if (isset ($attributes) and $get_attributes) {
				foreach ($attributes as $attr => $val) {
					if ($priority == 'tag')
						$attributes_data[$attr] = $val;
					else
						$result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
				}
			}
			if ($type == "open") {
				$parent[$level -1] = & $current;
				if (!is_array($current) or (!in_array($tag, array_keys($current)))) {
					$current[$tag] = $result;
					if ($attributes_data)
						$current[$tag . '_attr'] = $attributes_data;
					$repeated_tag_index[$tag . '_' . $level] = 1;
					$current = & $current[$tag];
				} else {
					if (isset ($current[$tag][0])) {
						$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
						$repeated_tag_index[$tag . '_' . $level]++;
					} else {
						$current[$tag] = array (
							$current[$tag],
							$result
						);
						$repeated_tag_index[$tag . '_' . $level] = 2;
						if (isset ($current[$tag . '_attr'])) {
							$current[$tag]['0_attr'] = $current[$tag . '_attr'];
							unset ($current[$tag . '_attr']);
						}
					}
					$last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
					$current = & $current[$tag][$last_item_index];
				}
			}
			elseif ($type == "complete") {
				if (!isset ($current[$tag])) {
					$current[$tag] = $result;
					$repeated_tag_index[$tag . '_' . $level] = 1;
					if ($priority == 'tag' and $attributes_data)
						$current[$tag . '_attr'] = $attributes_data;
				} else {
					if (isset ($current[$tag][0]) and is_array($current[$tag])) {
						$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
						if ($priority == 'tag' and $get_attributes and $attributes_data) {
							$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
						}
						$repeated_tag_index[$tag . '_' . $level]++;
					} else {
						$current[$tag] = array (
							$current[$tag],
							$result
						);
						$repeated_tag_index[$tag . '_' . $level] = 1;
						if ($priority == 'tag' and $get_attributes) {
							if (isset ($current[$tag . '_attr'])) {
								$current[$tag]['0_attr'] = $current[$tag . '_attr'];
								unset ($current[$tag . '_attr']);
							}
							if ($attributes_data) {
								$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
							}
						}
						$repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
					}
				}
			} elseif ($type == 'close') {
				$current = & $parent[$level -1];
			}
		}
		return ($xml_array);
	}
}