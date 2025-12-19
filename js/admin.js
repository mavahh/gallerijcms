jQuery(document).ready(function($) {
  $(document).on('click', '.select-media', function(e) {
    e.preventDefault();
    var target = $(this).data('target');
    var custom_uploader = wp.media({
      title: 'Selecteer afbeelding',
      button: { text: 'Gebruik deze afbeelding' },
      multiple: false
    }).on('select', function() {
      var attachment = custom_uploader.state().get('selection').first().toJSON();
      $('#' + target).val(attachment.url);
    }).open();
  });

  $(document).on('click', '.toggle-edit', function(e) {
    e.preventDefault();
    var targetId = $(this).data('target');
    $('#' + targetId).toggle();
  });

  $(document).on('click', '.add-gallery-images', function(e) {
    e.preventDefault();
    var target = $(this).data('target');
    var textarea = $('.gallerijcms-gallery-list[data-target="' + target + '"]');

    var gallery_uploader = wp.media({
      title: 'Selecteer galerijfoto\'s',
      button: { text: 'Voeg toe' },
      multiple: true
    }).on('select', function() {
      var selection = gallery_uploader.state().get('selection').toJSON();
      var urls = selection.map(function(item) { return item.url; });
      var existing = textarea.val().trim();
      var combined = existing ? existing.split(/\n+/) : [];
      textarea.val(combined.concat(urls).join('\n'));
    }).open();
  });
});
