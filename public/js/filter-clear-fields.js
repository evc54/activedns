/*
  Project       : ActiveDNS
  Document      : filter-clear-fields.js
  Document type : Javascript file
  Created at    : 31.08.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
*/
function setClearFields()
{
  $('div.filter-container', 'tr.filters').bind('mouseover', function() {
    var self = $(this).find('input');
    if (self.val()) {
      var offset = self.offset();
      var clear = $('<div />')
        .addClass('clear-filter-field')
        .css('z-index', self.css('z-index') + 1)
        .css('display', 'none')
        .css('position', 'absolute')
        .html('<a href="javascript:void(0)"><s class="icon-remove-sign icon-black"></s></a>')
        .bind('click', function() {
          self.val('');
          $(this).fadeOut('fast');
          $(this).remove();
          self.change();
        })
        .insertAfter(self);
      clear.css('left', offset.left + self.width() - clear.width() + 5)
        .css('top', offset.top + self.height() / 2 - 4)
        .fadeIn('fast')
    }
  })
  .bind('mouseleave', function() {
    $('div.clear-filter-field')
      .fadeOut('fast')
      .remove();
  })
}
