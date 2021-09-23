/*
  Project       : ActiveDNS
  Document      : dialogs.js
  Document type : Javascript file
  Created at    : 31.08.2012
  Author        : Eugene V Chernyshev <evc22rus@gmail.com>
*/
function bmAlert(title, message, callback, context) {
  var okButton = (typeof bmDialogLocale != 'undefined' ? bmDialogLocale.okButtonLabel : null) || 'OK';
  var template = '<div id="modal-alert" class="modal fade"><div class="modal-header"><a href="javascript:void(0)" class="close" data-dismiss="modal">&times;</a><h3>' + title + '</h3></div><div class="modal-body">' + message + '</div><div class="modal-footer"><a data-dismiss="modal" class="btn" href="javascript:void(0)">' + (okButton) + '</a></div></div>';
  var modal = $(template).appendTo('body');
  $(modal)
    .modal('show')
    .on('hidden', function() {
      $(modal).remove();
      if (typeof callback != 'undefined') {
        var func = $.proxy(callback, context);
        func();
      }
    });
}

function bmConfirm(title, content, callbackOk, callbackCancel) {
  var yesButton = (typeof bmDialogLocale != 'undefined' ? bmDialogLocale.yesButtonLabel : null) || 'Yes';
  var noButton = (typeof bmDialogLocale != 'undefined' ? bmDialogLocale.noButtonLabel : null) || 'No';
  var template = '<div id="modal-confirm" class="modal fade" style="display:none;"><div class="modal-header"><a href="javascript:void(0)" class="close" data-dismiss="modal">&times;</a><h3>' + title + '</h3></div><div class="modal-body form-horizontal">' + content + '</div><div class="modal-footer"><button class="btn btn-primary" type="button">' + yesButton + '</button><a data-dismiss="modal" class="btn" href="javascript:void(0)">' + noButton + '</a></div></div>';
  var modal = $(template).appendTo('body');
  $(modal)
    .modal('show')
    .on('shown', function() {
      $('button', modal).bind('click',callbackOk);
    })
    .on('hidden', function() {
      $(modal).remove();
      if (typeof callbackCancel != 'undefined') {
        callbackCancel();
      }
    });
  return modal;
}

function bmCreateRR(title, content, callback) {
  var addButton = (typeof bmDialogLocale != 'undefined' ? bmDialogLocale.addButtonLabel : null) || 'Add';
  var cancelButton = (typeof bmDialogLocale != 'undefined' ? bmDialogLocale.cancelButtonLabel : null) || 'Cancel';
  var template = '<div id="modal-create" class="modal fade" style="display:none;"><div class="modal-header"><a href="javascript:void(0)" class="close" data-dismiss="modal">&times;</a><h3>' + title + '</h3></div><div class="modal-body form-horizontal">' + content + '</div><div class="modal-footer"><button class="btn btn-primary" type="button">' + addButton + '</button><a data-dismiss="modal" class="btn" href="javascript:void(0)">' + cancelButton + '</a></div></div>';
  var modal = $(template).appendTo('body');
  $(modal)
    .modal('show')
    .on('shown', function() {
      $('button', modal).bind('click', callback);
    })
    .on('hidden', function() {
      $(modal).remove();
    });
  return modal;
}

function bmUpdateRR(title, content, callback) {
  var updateButton = (typeof bmDialogLocale != 'undefined' ? bmDialogLocale.updateButtonLabel : null) || 'Update';
  var cancelButton = (typeof bmDialogLocale != 'undefined' ? bmDialogLocale.cancelButtonLabel : null) || 'Cancel';
  var template = '<div id="modal-update" class="modal fade" style="display:none;"><div class="modal-header"><a href="javascript:void(0)" class="close" data-dismiss="modal">&times;</a><h3>' + title + '</h3></div><div class="modal-body form-horizontal">' + content + '</div><div class="modal-footer"><button class="btn btn-primary" type="button">' + updateButton + '</button><a data-dismiss="modal" class="btn" href="javascript:void(0)">' + cancelButton + '</a></div></div>';
  var modal = $(template).appendTo('body');
  $(modal)
    .modal('show')
    .on('shown', function() {
      $('button', modal).bind('click', callback);
    })
    .on('hidden', function() {
      $(modal).remove();
    });
  return modal;
}
