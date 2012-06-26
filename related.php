<?php
/*
Plugin Name: Related
Plugin URI: https://github.com/matthiassiegel/Related
Description: A simple 'related posts' plugin that lets you select related posts manually instead of automatically generating the list.
Version: 1.1.1
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
			add_action('admin_menu', array(&$this, 'start'));
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
			add_action('admin_print_scripts', array(&$this, 'loadScripts'));
			
			// Load the CSS
			add_action('admin_print_styles', array(&$this, 'loadCSS'));
			
			// Adds a meta box for related posts to the edit screen of each post type in WordPress
			foreach (get_post_types() as $post_type) :
				add_meta_box($post_type . '-related-posts-box', 'Related posts', array(&$this, 'displayMetaBox'), $post_type, 'normal', 'high');
			endforeach;
		}


		// Load Javascript
		public function loadScripts() {
		
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('related-scripts', RELATED_URLPATH .'/scripts.js', false, RELATED_VERSION);
		}


		// Load CSS
		public function loadCSS() {
		
			wp_enqueue_style('related-css', RELATED_URLPATH .'/styles.css', false, RELATED_VERSION, 'all');
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
				</p>
				<p>
					Select related posts from the list. Drag selected ones to change order.
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