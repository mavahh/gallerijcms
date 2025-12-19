<?php
if (!defined('ABSPATH')) exit;

function exposanten_shortcode($atts) {
    $atts = shortcode_atts([
        'id' => '0',
        'columns_desktop' => '5',
        'columns_tablet'  => '3',
        'columns_mobile'  => '2',
        'gap'    => '5px',
        'center' => 'true',
        'search' => 'true',
        // Optioneel: binnenmarge rond logo's
        'inner_padding' => '10%'
    ], $atts, 'exposanten');

    $id = (int)$atts['id'];
    $galleries = get_option('exposanten_galleries', []);
    if (!is_array($galleries) || !isset($galleries[$id])) return '';

    $items = isset($galleries[$id]['items']) && is_array($galleries[$id]['items']) ? $galleries[$id]['items'] : [];

    ob_start(); ?>

    <?php if ($atts['search'] === 'true'): ?>
    <div class="exposanten-search">
        <input type="text" placeholder="<?php echo esc_attr__('Zoek bedrijf...', 'exposanten'); ?>" onkeyup="filterExposanten(this)">
    </div>
    <?php endif; ?>

    <div class="exposanten-grid"
         style="--cols-desktop: <?php echo (int)$atts['columns_desktop']; ?>;
                --cols-tablet: <?php echo (int)$atts['columns_tablet']; ?>;
                --cols-mobile: <?php echo (int)$atts['columns_mobile']; ?>;
                --gap: <?php echo esc_attr($atts['gap']); ?>;
                --justify: <?php echo ($atts['center'] === 'true') ? 'center' : 'start'; ?>;
                --inner-padding: <?php echo esc_attr($atts['inner_padding']); ?>;">
        <?php foreach ($items as $item):
            $img = isset($item['img']) ? $item['img'] : '';
            $name = isset($item['name']) ? $item['name'] : '';
            $link = isset($item['link']) ? $item['link'] : '#';
            if (!$img) continue;
        ?>
            <a href="<?php echo esc_url($link); ?>" target="_blank" class="exposanten-item" data-name="<?php echo esc_attr(strtolower($name)); ?>">
                <span class="exposanten-thumb">
                    <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($name); ?>" />
                </span>
            </a>
        <?php endforeach; ?>
    </div>

    <?php return ob_get_clean();
}
add_shortcode('exposanten', 'exposanten_shortcode');
