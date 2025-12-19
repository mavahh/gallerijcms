<?php
/**
 * Plugin Name: Gallerij CMS
 * Description: Beheer realisaties met titels, links en hover-overlay via een shortcode. Inclusief responsive grid voor mobiel, tablet en desktop.
 * Version: 5.1
 * Author: Jouw Naam
 */
if (!defined('ABSPATH')) exit;

define('GALLERIJCMS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GALLERIJCMS_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once GALLERIJCMS_PLUGIN_DIR . 'admin/admin-page.php';
require_once GALLERIJCMS_PLUGIN_DIR . 'public/shortcode-output.php';

/* Assets enkel laden als shortcode aanwezig is */
function gallerijcms_maybe_enqueue_assets($posts) {
    if (empty($posts)) return $posts;
    foreach ($posts as $post) {
      if (has_shortcode($post->post_content, 'gallerijcms')) {
        wp_enqueue_style('gallerijcms-frontend', GALLERIJCMS_PLUGIN_URL . 'css/style.css', [], '5.1');
        wp_enqueue_script('gallerijcms-search', GALLERIJCMS_PLUGIN_URL . 'js/search.js', [], '5.1', true);
        break;
      }
    }
  return $posts;
}
add_filter('the_posts', 'gallerijcms_maybe_enqueue_assets');

function gallerijcms_enqueue_admin_assets($hook) {
  if (strpos($hook, 'gallerijcms') === false) return;
  wp_enqueue_media();
    wp_enqueue_script('gallerijcms-admin', GALLERIJCMS_PLUGIN_URL . 'js/admin.js', ['jquery'], '5.1', true);
}
add_action('admin_enqueue_scripts', 'gallerijcms_enqueue_admin_assets');
