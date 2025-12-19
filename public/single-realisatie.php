<?php
if (!defined('ABSPATH')) exit;

function gallerijcms_find_item_by_slug($slug) {
  $galleries = get_option('gallerijcms_galleries', []);
  if (!is_array($galleries)) return null;

  foreach ($galleries as $gallery) {
    if (!isset($gallery['items']) || !is_array($gallery['items'])) continue;
    foreach ($gallery['items'] as $item) {
      $candidate = isset($item['slug']) && $item['slug'] !== '' ? $item['slug'] : sanitize_title($item['name'] ?? '');
      if ($candidate === $slug) {
        return $item;
      }
    }
  }
  return null;
}

function gallerijcms_render_realisatie($attrs = []) {
  $attrs = shortcode_atts([
    'slug' => '',
  ], $attrs, 'gallerijcms_realisatie');

  $slug = $attrs['slug'];
  if (!$slug) {
    $slug = basename(parse_url(add_query_arg([]), PHP_URL_PATH));
  }

  if (!$slug) return '';

  $item = gallerijcms_find_item_by_slug($slug);
  if (!$item) return '';

  $name = $item['name'] ?? '';
  $description = $item['description'] ?? '';
  $image = $item['img'] ?? '';
  $gallery = isset($item['gallery']) && is_array($item['gallery']) ? $item['gallery'] : [];

  ob_start();
  ?>
  <div class="gallerijcms-single">
    <h1 class="gallerijcms-single__title"><?php echo esc_html($name); ?></h1>

    <?php if ($description): ?>
      <div class="gallerijcms-single__intro"><?php echo wpautop(esc_html($description)); ?></div>
    <?php endif; ?>

    <?php if ($image): ?>
      <div class="gallerijcms-single__hero">
        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($name); ?>" />
      </div>
    <?php endif; ?>

    <?php if (!empty($gallery)): ?>
      <div class="gallerijcms-single__gallery">
        <?php foreach ($gallery as $url): ?>
          <?php if (!$url) continue; ?>
          <figure class="gallerijcms-single__gallery-item">
            <img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($name); ?>" />
          </figure>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
  <?php
  return ob_get_clean();
}
add_shortcode('gallerijcms_realisatie', 'gallerijcms_render_realisatie');

?>
