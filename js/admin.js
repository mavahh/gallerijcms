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
});
