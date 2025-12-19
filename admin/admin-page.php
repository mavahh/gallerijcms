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

    <p>Elke realisatie krijgt automatisch een eigen subpagina-link op basis van de titel (bijv. <code>/realisaties/titel-van-project/</code>). Pas de inhoud van die pagina in Elementor aan.</p>

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
        <thead><tr><th>Afbeelding</th><th>Titel realisatie</th><th>Subpagina</th><th>Actie</th></tr></thead>
        <tbody>
          <?php if (empty($items)): ?>
            <tr><td colspan="4">Nog geen items.</td></tr>
          <?php else: foreach ($items as $index => $item): ?>
            <tr>
              <td><?php if (!empty($item['img'])): ?><img src="<?php echo esc_url($item['img']); ?>" width="80"><?php endif; ?></td>
              <td><?php echo esc_html($item['name'] ?? ''); ?></td>
              <td>
                <?php
                  $slug = isset($item['slug']) ? $item['slug'] : sanitize_title($item['name'] ?? '');
                  $auto_link = home_url('/realisaties/' . $slug . '/');
                  $link = !empty($item['link']) ? $item['link'] : $auto_link;
                ?>
                <code><?php echo esc_url($link); ?></code>
              </td>
              <td>
                <button class="button toggle-edit" data-target="edit_<?php echo intval($gallery_id); ?>_<?php echo intval($index); ?>">Bewerk</button>
                <form method="post" style="display:inline;">
                  <?php wp_nonce_field('gallerijcms_remove_item','gallerijcms_nonce'); ?>
                  <input type="hidden" name="remove_gallery" value="<?php echo intval($gallery_id); ?>">
                  <input type="hidden" name="remove_index" value="<?php echo intval($index); ?>">
                  <button class="button">Verwijder</button>
                </form>
              </td>
            </tr>
            <tr id="edit_<?php echo intval($gallery_id); ?>_<?php echo intval($index); ?>" class="gallerijcms-edit-row" style="display:none;">
              <td colspan="4">
                <form method="post" class="gallerijcms-edit-form">
                  <?php wp_nonce_field('gallerijcms_update_item','gallerijcms_nonce'); ?>
                  <input type="hidden" name="update_gallery" value="<?php echo intval($gallery_id); ?>">
                  <input type="hidden" name="update_index" value="<?php echo intval($index); ?>">
                  <input type="hidden" name="update_item" value="1">
                  <p><strong>Gegevens realisatie</strong></p>
                  <p>
                    <label>Titel<br>
                      <input type="text" name="item_name" value="<?php echo esc_attr($item['name'] ?? ''); ?>" required>
                    </label>
                  </p>
                  <p>
                    <label>Omschrijving (kort)<br>
                      <textarea name="item_description" rows="3" placeholder="Korte intro voor de subpagina."><?php echo esc_textarea($item['description'] ?? ''); ?></textarea>
                    </label>
                  </p>
                  <p>
                    <label>Slug voor subpagina<br>
                      <input type="text" name="item_slug" value="<?php echo esc_attr($slug); ?>" placeholder="automatisch op basis van titel">
                      <small>Wordt gebruikt voor de link: <code><?php echo esc_url($auto_link); ?></code></small>
                    </label>
                  </p>
                  <p>
                    <label>Hoofdafbeelding<br>
                      <input type="hidden" id="edit_<?php echo intval($gallery_id); ?>_<?php echo intval($index); ?>_image" name="item_image" value="<?php echo esc_attr($item['img'] ?? ''); ?>">
                      <button class="button select-media" data-target="edit_<?php echo intval($gallery_id); ?>_<?php echo intval($index); ?>_image">Kies afbeelding</button>
                      <?php if (!empty($item['img'])): ?><br><img src="<?php echo esc_url($item['img']); ?>" width="120" style="margin-top:6px;" /><?php endif; ?>
                    </label>
                  </p>
                  <p>
                    <label>Galerijfoto's (1 per regel)<br>
                      <textarea name="item_gallery" rows="4" class="gallerijcms-gallery-list" data-target="gallerylist_<?php echo intval($gallery_id); ?>_<?php echo intval($index); ?>"><?php echo esc_textarea(isset($item['gallery']) && is_array($item['gallery']) ? implode("\n", $item['gallery']) : ''); ?></textarea>
                    </label>
                    <button class="button add-gallery-images" data-target="gallerylist_<?php echo intval($gallery_id); ?>_<?php echo intval($index); ?>">Voeg foto's uit mediabibliotheek toe</button>
                  </p>
                  <p><button type="submit" class="button button-primary">Opslaan</button></p>
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
        <input type="text" name="image_name" placeholder="Titel van de realisatie" required />
        <input type="url" name="image_link" placeholder="Optioneel: eigen link (anders /realisaties/titel/)" />
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
      $name = sanitize_text_field($_POST['image_name'] ?? '');
      $slug = sanitize_title($name);
      $auto_link = home_url('/realisaties/' . $slug . '/');
      $galleries[$id]['items'][] = [
        'img'  => esc_url_raw($_POST['image_url'] ?? ''),
        'link' => esc_url_raw($_POST['image_link'] ?? $auto_link),
        'name' => $name,
        'slug' => $slug,
        'description' => '',
        'gallery' => [],
      ];
      update_option('gallerijcms_galleries', $galleries);
    }
  }

  if (isset($_POST['update_item']) && isset($_POST['gallerijcms_nonce']) && wp_verify_nonce($_POST['gallerijcms_nonce'], 'gallerijcms_update_item')) {
    $gid = isset($_POST['update_gallery']) ? (int)$_POST['update_gallery'] : -1;
    $idx = isset($_POST['update_index']) ? (int)$_POST['update_index'] : -1;
    if (isset($galleries[$gid]['items'][$idx])) {
      $name = sanitize_text_field($_POST['item_name'] ?? '');
      $slug = sanitize_title($_POST['item_slug'] ?? $name);
      $gallery_list = isset($_POST['item_gallery']) ? explode("\n", $_POST['item_gallery']) : [];
      $gallery_clean = array_filter(array_map(function ($url) {
        return esc_url_raw(trim($url));
      }, $gallery_list));

      $galleries[$gid]['items'][$idx] = array_merge($galleries[$gid]['items'][$idx], [
        'name' => $name,
        'description' => sanitize_textarea_field($_POST['item_description'] ?? ''),
        'slug' => $slug,
        'img' => esc_url_raw($_POST['item_image'] ?? ''),
        'gallery' => array_values($gallery_clean),
      ]);

      if (empty($galleries[$gid]['items'][$idx]['link'])) {
        $galleries[$gid]['items'][$idx]['link'] = esc_url_raw(home_url('/realisaties/' . $slug . '/'));
      }

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
