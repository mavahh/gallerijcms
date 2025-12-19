<?php
if (!defined('ABSPATH')) exit;

$item = $GLOBALS['gallerijcms_current_item'] ?? null;
if (!$item) {
  $slug = get_query_var('gallerijcms_slug');
  $item = gallerijcms_find_item_by_slug($slug);
}

if (!$item) {
  status_header(404);
  nocache_headers();
  include get_query_template('404');
  exit;
}

get_header();
?>
<main class="gallerijcms-single-page">
  <?php echo gallerijcms_render_realisatie(['slug' => $item['slug'] ?? '']); ?>
</main>
<?php
get_footer();
exit;
