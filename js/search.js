function filterGallerijcms(input) {
  var query = (input.value || '').toLowerCase();
  document.querySelectorAll('.gallerijcms-item').forEach(function(el) {
    var name = el.dataset.name || '';
    el.style.display = name.indexOf(query) !== -1 ? '' : 'none';
  });
}
