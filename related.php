<?php
/*
Plugin Name: Related
Plugin URI: https://github.com/matthiassiegel/Related
Description: A simple 'related posts' plugin that lets you select related posts manually instead of automatically generating the list.
Version: 1.1.2
Author: Matthias Siegel
Author URI: https://github.com/matthiassiegel/Related


Copyright 2010-2012  Matthias Siegel  (email: matthias.siegel@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



if (!class_exists('Related')) :
	class Related {

		// Constructor
		public function __construct() {
			// Set some helpful constants
			$this->defineConstants();
						
			// Register hook to save the related posts when saving the post
			add_action('save_post', array(&$this, 'save'));

			// Start the plugin
			add_action('admin_init', array(&$this, 'start'));
		}
		

		// Defines a few static helper values we might need
		protected function defineConstants() {

			define('RELATED_VERSION', '1.1.1');
			define('RELATED_HOME', 'https://github.com/matthiassiegel/Related');
			define('RELATED_FILE', plugin_basename(dirname(__FILE__)));
			define('RELATED_ABSPATH', str_replace('\\', '/', WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__))));
			define('RELATED_URLPATH', WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)));
		}
				
		
		// Main function
		public function start() {
			// Load the scripts
			add_action('admin_enqueue_scripts', array(&$this, 'loadScripts'));
		
			// Load the CSS
			add_action('admin_print_styles', array(&$this, 'loadCSS'));
			
			add_action('wp_ajax_nopriv_rel_search', array(&$this,'related_search'));
			add_action('wp_ajax_rel_search', array(&$this,'related_search'));
			// Adds a meta box for related posts to the edit screen of each post type in WordPress
			foreach (get_post_types() as $post_type) :
				add_meta_box($post_type . '-related-posts-box', 'Related posts', array(&$this, 'displayMetaBox'), $post_type, 'normal', 'high');
			endforeach;
		}

		//load the ajax autocomplete function

		// Load Javascript
		public function loadScripts() {
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery_ui_autocomplete', RELATED_URLPATH.'/js/jquery-ui-1.8.21.custom.min.js',array('jquery'),'1.0');
			
			wp_register_script('related-scripts', RELATED_URLPATH .'/js/scripts.js',false,RELATED_VERSION);
			wp_enqueue_script('related-scripts');
			
			wp_localize_script( 'related-scripts', 'RelatedObj', array('url' => admin_url( 'admin-ajax.php' )));
		}


		// Load CSS
		public function loadCSS() {
			wp_enqueue_style('related-css', RELATED_URLPATH .'/css/styles.css', false, RELATED_VERSION, 'all');
			
			wp_register_style('jquery_autocomplete_css', RELATED_URLPATH.'/css/jquery-ui-1.8.21.custom.css');
			wp_enqueue_style('jquery_autocomplete_css');
		}


		// Save related posts when saving the post
		public function save($id) {
			
			global $wpdb;
			
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

			if (!isset($_POST['related-posts']) || empty($_POST['related-posts'])) :
				delete_post_meta($id, 'related_posts');
			else :
				update_post_meta($id, 'related_posts', $_POST['related-posts']);
			endif;			
		}
		
		// autocomplete search
		public function related_search() {
			$args = array(
				's' => $_REQUEST['term'],
				'orderby' => 'post_date',
   				'order' => 'DESC'
			);	
			$posts = get_posts($args);
			
			$search_dropdown = array();
			foreach($posts as $p){
				setup_postdata($p);
				$single_post = array();
				$single_post['label'] = $p->post_title;
				$single_post['ID'] = $p->ID;
				
				$search_dropdown[] = $single_post;
			}
			$response = $_GET['callback']."(".json_encode($search_dropdown).")";
			echo $response;
			exit;
		}


		// Creates the output on the post screen
		public function displayMetaBox() {
			
			global $post;
			
			$post_id = $post->ID;
			
			echo '<div id="related-posts">';
			
			
			// Get related posts if existing
			$related = get_post_meta($post_id, 'related_posts', true);

			if (!empty($related)) :
				foreach($related as $r) :
					$p = get_post($r);
					echo '
						<div class="related-post" id="related-post-' . $r . '">
							<input type="hidden" name="related-posts[]" value="' . $r . '">
							<span class="related-post-title">' . $p->post_title . ' (' . ucfirst(get_post_type($p->ID)) . ')</span>
							<a href="#">Delete</a>
						</div>';
				endforeach;
			endif;
			
			echo '
				</div>
				<p>
					<select id="related-posts-select" name="related-posts-select">
						<option value="0">Select</option>';
			
			$query = array(
				'nopaging' => true,
				'post__not_in' => array($post_id),
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'post_type' => 'any',
				'orderby' => 'title',
				'order' => 'ASC'
			);
			
			$p = new WP_Query($query);
			
			if ($p->have_posts()) :
				while ($p->have_posts()) :
					$p->the_post();
					echo '
						<option value="' . get_the_ID() . '">' . get_the_title() . ' (' . ucfirst(get_post_type(get_the_ID())) . ')</option>';
				endwhile;
			endif;
			
			wp_reset_query();
			wp_reset_postdata();
								
			echo '
					</select>
					<div id="rel_search_container" style="display: none">
						<input size="40" type="text" name="related_search" id="rel_search_id" />
					</div>
				</p>
				<p>
					Select related posts from the list. Drag selected ones to change order.
					<span style="float: right;"> Text search <input name="rel_text_search" id="rel_text_search" type="checkbox" /></span>
				</p>';
		}


		// The frontend function that is used to display the related post list
		public function show($id, $return = false) {

			global $wpdb;

			if (!empty($id) && is_numeric($id)) :
				$related = get_post_meta($id, 'related_posts', true);
				
				if (!empty($related)) :
					$rel = array();
					foreach ($related as $r) :
						$p = get_post($r);
						$rel[] = $p;
					endforeach;
					
					// If value should be returned as array, return it
					if ($return) :
						return $rel;
						
					// Otherwise return a formatted list
					else :
						$list = '<ul class="related-posts">';
						foreach ($rel as $r) :
							$list .= '<li><a href="' . get_permalink($r->ID) . '">' . $r->post_title . '</a></li>';
						endforeach;
						$list .= '</ul>';
						
						return $list;
					endif;
				else :
					return false;
				endif;
			else :
				return 'Invalid post ID specified';
			endif;
		}
	}
	
	
	
endif;



// Start the plugin

global $related;

$related = new Related();

?>