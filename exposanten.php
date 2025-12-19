<?php
/**
 * Plugin Name: Exposanten
 * Description: Beheerbare galerij met shortcode en uniforme vierkante tegels. Mobiel 2 kolommen, tablet 3, desktop 5.
 * Version: 4.2
 * Author: Jouw Naam
 */
if (!defined('ABSPATH')) exit;

define('EXPOSANTEN_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EXPOSANTEN_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once EXPOSANTEN_PLUGIN_DIR . 'admin/admin-page.php';
require_once EXPOSANTEN_PLUGIN_DIR . 'public/shortcode-output.php';

/* Assets enkel laden als shortcode aanwezig is */
function exposanten_maybe_enqueue_assets($posts) {
  if (empty($posts)) return $posts;
  foreach ($posts as $post) {
    if (has_shortcode($post->post_content, 'exposanten')) {
      wp_enqueue_style('exposanten-frontend', EXPOSANTEN_PLUGIN_URL . 'css/style.css', [], '4.2');
      wp_enqueue_script('exposanten-search', EXPOSANTEN_PLUGIN_URL . 'js/search.js', [], '4.2', true);
      break;
    }
  }
  return $posts;
}
add_filter('the_posts', 'exposanten_maybe_enqueue_assets');

function exposanten_enqueue_admin_assets($hook) {
  if (strpos($hook, 'page_exposanten') === false) return;
  wp_enqueue_media();
  wp_enqueue_script('exposanten-admin', EXPOSANTEN_PLUGIN_URL . 'js/admin.js', ['jquery'], '4.2', true);
}
add_action('admin_enqueue_scripts', 'exposanten_enqueue_admin_assets');
