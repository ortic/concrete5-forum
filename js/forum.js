$('.ortic-forum-edit, .ortic-forum-edit-cancel').on('click', function (event) {
  var parent = $(event.target).parent().parent();

  parent.find('.ortic-forum-message-edit, .ortic-forum-message-text, .ortic-forum-edit-cancel, .ortic-forum-edit').toggle();
});