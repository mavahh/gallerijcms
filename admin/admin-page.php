<?php
if (!defined('ABSPATH')) exit;

function gallerijcms_menu_page() {
  add_menu_page(
    'Beheer realisaties',
    'Gallerij CMS',
    'manage_options',
    'gallerijcms',
    'gallerijcms_render_admin_page',
    'dashicons-format-gallery'
  );
}
add_action('admin_menu', 'gallerijcms_menu_page');

function gallerijcms_render_admin_page() {
  if (!current_user_can('manage_options')) return;
  $galleries = get_option('gallerijcms_galleries', []);
  if (!is_array($galleries)) $galleries = [];
  ?>
  <div class="wrap">
    <h1>Beheer realisaties</h1>
    <p>Plaats de shortcode van een galerij in een (Elementor) pagina om de realisaties automatisch te tonen en de lay-out daar te kunnen aanpassen.</p>

    <form method="post">
      <?php wp_nonce_field('gallerijcms_add_gallery','gallerijcms_nonce'); ?>
      <h2>Nieuwe galerij toevoegen</h2>
      <input type="text" name="new_gallery_name" placeholder="Naam van nieuwe galerij" required />
      <input type="submit" name="add_gallery" class="button button-primary" value="Toevoegen">
    </form>

    <hr>
    <?php foreach ($galleries as $gallery_id => $gallery):
      $gallery_name = isset($gallery['name']) ? $gallery['name'] : ('Galerij ' . intval($gallery_id));
      $items = isset($gallery['items']) && is_array($gallery['items']) ? $gallery['items'] : [];
    ?>
      <h2><?php echo esc_html($gallery_name); ?></h2>
      <p><strong>Shortcode voor deze galerij:</strong> <code>[gallerijcms id="<?php echo intval($gallery_id); ?>"]</code></p>

      <table class="widefat">
        <thead><tr><th>Afbeelding</th><th>Titel realisatie</th><th>Link</th><th>Actie</th></tr></thead>
        <tbody>
          <?php if (empty($items)): ?>
            <tr><td colspan="4">Nog geen items.</td></tr>
          <?php else: foreach ($items as $index => $item): ?>
            <tr>
              <td><?php if (!empty($item['img'])): ?><img src="<?php echo esc_url($item['img']); ?>" width="80"><?php endif; ?></td>
              <td><?php echo esc_html($item['name'] ?? ''); ?></td>
              <td><?php echo esc_url($item['link'] ?? ''); ?></td>
              <td>
                <form method="post" style="display:inline;">
                  <?php wp_nonce_field('gallerijcms_remove_item','gallerijcms_nonce'); ?>
                  <input type="hidden" name="remove_gallery" value="<?php echo intval($gallery_id); ?>">
                  <input type="hidden" name="remove_index" value="<?php echo intval($index); ?>">
                  <button class="button">Verwijder</button>
                </form>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>

      <form method="post" style="margin-top:10px;">
        <?php wp_nonce_field('gallerijcms_add_item','gallerijcms_nonce'); ?>
        <input type="hidden" name="gallery_id" value="<?php echo intval($gallery_id); ?>">
        <input type="hidden" name="add_image" value="1">
        <input type="hidden" id="gallery_<?php echo intval($gallery_id); ?>_image" name="image_url" />
        <input type="url" name="image_link" placeholder="Link naar realisatie (bijv. /realisaties/moderne-carport/)" required />
        <input type="text" name="image_name" placeholder="Titel van de realisatie" required />
        <button class="button select-media" data-target="gallery_<?php echo intval($gallery_id); ?>_image">Selecteer afbeelding</button>
        <input type="submit" class="button button-primary" value="Toevoegen">
      </form>
      <hr>
    <?php endforeach; ?>
  </div>
  <?php
}

add_action('admin_init', function () {
  if (!current_user_can('manage_options')) return;
  $galleries = get_option('gallerijcms_galleries', []);
  if (!is_array($galleries)) $galleries = [];

  if (isset($_POST['add_gallery']) && isset($_POST['gallerijcms_nonce']) && wp_verify_nonce($_POST['gallerijcms_nonce'], 'gallerijcms_add_gallery')) {
    $galleries[] = ['name' => sanitize_text_field($_POST['new_gallery_name']), 'items' => []];
    update_option('gallerijcms_galleries', $galleries);
  }

  if (isset($_POST['add_image']) && isset($_POST['gallerijcms_nonce']) && wp_verify_nonce($_POST['gallerijcms_nonce'], 'gallerijcms_add_item')) {
    $id = isset($_POST['gallery_id']) ? (int)$_POST['gallery_id'] : -1;
    if (isset($galleries[$id])) {
      $galleries[$id]['items'][] = [
        'img'  => esc_url_raw($_POST['image_url'] ?? ''),
        'link' => esc_url_raw($_POST['image_link'] ?? ''),
        'name' => sanitize_text_field($_POST['image_name'] ?? ''),
      ];
      update_option('gallerijcms_galleries', $galleries);
    }
  }

  if (isset($_POST['remove_gallery']) && isset($_POST['gallerijcms_nonce']) && wp_verify_nonce($_POST['gallerijcms_nonce'], 'gallerijcms_remove_item')) {
    $gid = (int)$_POST['remove_gallery'];
    $idx = (int)$_POST['remove_index'];
    if (isset($galleries[$gid]['items'][$idx])) {
      unset($galleries[$gid]['items'][$idx]);
      $galleries[$gid]['items'] = array_values($galleries[$gid]['items']);
      update_option('gallerijcms_galleries', $galleries);
    }
  }
});
