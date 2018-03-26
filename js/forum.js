$('.ortic-forum-edit, .ortic-forum-edit-cancel').on('click', function (event) {
  var parent = $(event.target).parent().parent();

  parent.find('.ortic-forum-message-edit, .ortic-forum-message-text, .ortic-forum-edit-cancel, .ortic-forum-edit').toggle();
});

$('.ortic-forum-delete').on('click', function (event) {
  event.preventDefault();
  if (confirm('Are you sure you want to delete this message?')) {
    window.location.href = $(this).attr('href');
  }
});