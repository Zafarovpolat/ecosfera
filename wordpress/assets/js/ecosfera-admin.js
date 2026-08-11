(function ($) {
  'use strict';

  $(document).on('click', '.ecosfera-repeat__add', function () {
    var $btn = $(this);
    var $box = $btn.closest('p').prevAll('.ecosfera-repeat').first();
    if (!$box.length) $box = $btn.parent().prevAll('.ecosfera-repeat').first();
    var name = $btn.data('name');
    var fields = String($btn.data('fields') || 'label').split(',');
    var flat = $btn.data('flat');
    var index = $box.children('.ecosfera-repeat__row').length;
    var $row = $('<div class="ecosfera-repeat__row"></div>');

    if (flat) {
      $row.append($('<input type="text" class="large-text">').attr('name', name + '[' + index + ']'));
    } else {
      fields.forEach(function (field) {
        $row.append(
          $('<input type="text" class="regular-text">').attr('name', name + '[' + index + '][' + field + ']')
        );
      });
    }

    $row.append('<button type="button" class="button ecosfera-repeat__remove">&times;</button>');
    $box.append($row);
  });

  $(document).on('click', '.ecosfera-repeat__remove', function () {
    $(this).closest('.ecosfera-repeat__row').remove();
  });

  $(document).on('click', '.ecosfera-media-multi', function (e) {
    e.preventDefault();
    var target = $($(this).data('target'));
    if (typeof wp === 'undefined' || !wp.media) return;

    var frame = wp.media({
      title: 'Галерея дома',
      multiple: true,
      library: { type: 'image' }
    });

    frame.on('select', function () {
      var ids = frame.state().get('selection').map(function (att) {
        return att.get('id');
      });
      var current = target.val() ? target.val().split(',') : [];
      target.val(current.concat(ids).join(','));
    });

    frame.open();
  });
})(jQuery);
