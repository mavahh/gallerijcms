<?php
/**
 * Plugin Name: Gallerij CMS
 * Description: Beheer realisaties met titels, links en hover-overlay via een shortcode. Inclusief responsive grid voor mobiel,
 * tablet en desktop.
 * Version: 5.3
 * Author: Jouw Naam
 */

define('GALLERIJCMS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GALLERIJCMS_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once GALLERIJCMS_PLUGIN_DIR . 'admin/admin-page.php';
require_once GALLERIJCMS_PLUGIN_DIR . 'public/shortcode-output.php';
require_once GALLERIJCMS_PLUGIN_DIR . 'public/single-realisatie.php';

/* Assets enkel laden als shortcode aanwezig is */
function gallerijcms_maybe_enqueue_assets($posts) {
    if (empty($posts)) return $posts;
    foreach ($posts as $post) {
      if (has_shortcode($post->post_content, 'gallerijcms')) {
        wp_enqueue_style('gallerijcms-frontend', GALLERIJCMS_PLUGIN_URL . 'css/style.css', [], '5.3');
        wp_enqueue_script('gallerijcms-search', GALLERIJCMS_PLUGIN_URL . 'js/search.js', [], '5.1', true);
        break;
      }
    }
  return $posts;
}
add_filter('the_posts', 'gallerijcms_maybe_enqueue_assets');

function gallerijcms_enqueue_single_assets() {
  if (!get_query_var('gallerijcms_slug')) return;
  wp_enqueue_style('gallerijcms-frontend', GALLERIJCMS_PLUGIN_URL . 'css/style.css', [], '5.3');
}
add_action('wp_enqueue_scripts', 'gallerijcms_enqueue_single_assets');

function gallerijcms_enqueue_admin_assets($hook) {
  if (strpos($hook, 'gallerijcms') === false) return;
  wp_enqueue_media();
    wp_enqueue_script('gallerijcms-admin', GALLERIJCMS_PLUGIN_URL . 'js/admin.js', ['jquery'], '5.1', true);
}
add_action('admin_enqueue_scripts', 'gallerijcms_enqueue_admin_assets');

function gallerijcms_rewrite_rules() {
  add_rewrite_rule('^realisaties/([^/]+)/?$', 'index.php?gallerijcms_slug=$matches[1]', 'top');
}
add_action('init', 'gallerijcms_rewrite_rules');

function gallerijcms_register_query_vars($vars) {
  $vars[] = 'gallerijcms_slug';
  return $vars;
}
add_filter('query_vars', 'gallerijcms_register_query_vars');

function gallerijcms_load_template($template) {
  $slug = get_query_var('gallerijcms_slug');
  if (!$slug) return $template;

  $item = gallerijcms_find_item_by_slug($slug);
  if (!$item) {
    return get_query_template('404');
  }

  $GLOBALS['gallerijcms_current_item'] = $item;
  return GALLERIJCMS_PLUGIN_DIR . 'public/realisatie-template.php';
}
add_filter('template_include', 'gallerijcms_load_template');

function gallerijcms_flush_rewrites() {
  gallerijcms_rewrite_rules();
  flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'gallerijcms_flush_rewrites');
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
