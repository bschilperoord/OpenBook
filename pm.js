var conversationId = null;

// Declare the conversationId as a global variable
$(document).ready(function() {
	
  let intervalId;

  loadConversations();

  $('#message-thread').hide();

  // Load message thread for a conversation when clicked
  $(document).on('click', '.conversation', function() {
    var recipient = $('#recipient').val();

    $('#message-thread').toggle();
    clickedValue = $(this).data('conversation-id');

    conversationId = $(this).data('conversation-id');
    $('#conversation-id').val(conversationId);

    var recipientUsernameinput = $(this).text();
    $('#recipient').val(recipientUsernameinput);

    $('#message-thread').empty();

    function startInterval(conversationId, recipientUsernameinput) {
    clearInterval(intervalId);

    intervalId = setInterval(function() {
      loadMessageThread(conversationId, recipientUsernameinput);
    }, 1000);
    }

    startInterval(conversationId, recipientUsernameinput);
  });

  // Declare variables outside the block
  var clickedValue;
  var conversationStarted = false;

  $(document).on('submit', '#compose-form', function(e) {
    e.preventDefault();
    var recipient = $('#recipient').val();
    var messageContent = $('#message-content').val();

    if (conversationId === "") {
      conversationId = 1;
      clickedValue = "1";
      conversationStarted = true;
    } else {
      conversationId = $('#conversation-id').val();
    }

    if (clickedValue !== null) {
      startConversation(recipient, messageContent);
      sendMessage(conversationId, recipient, messageContent);
    } else {
      if (!conversationStarted) {
        console.log("No conversation element has been clicked.");
        conversationStarted = false;
      }
    }
  });

  // Function to load conversations
  function loadConversations() {
    $.ajax({
      url: 'getconversations.php',
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          var conversations = response.conversations;
          displayConversations(conversations);
          
          // Extract username from the GET parameter 'user'
          var urlParams = new URLSearchParams(window.location.search);
          var targetUsername = urlParams.get('user');

          // Find the .conversation element with the matching username
          var targetConversation = $('.conversation').filter(function() {
            return $(this).text().trim() === targetUsername;
          });

          // Trigger click on the matched .conversation element
          if (targetConversation.length > 0) {
            targetConversation.click();
          }

        } else {
          displayNoConversationsMessage();
        }
      },
      error: function(xhr, status, error) {
        console.error(error);
        displayErrorMessage();
      }
    });
  }

  // Function to load message thread for a conversation
  function loadMessageThread(conversationId, recipient) {
    $.ajax({
      url: 'getmessages.php',
      type: 'GET',
      dataType: 'json',
      data: { conversationId: conversationId, recipient: recipient },
      success: function(response) {
        if (response.success) {
          var messages = response.messages;
          var currentUserId = response.currentUserId;
          
          displayMessageThread(messages, currentUserId);
        } else {
          //displayNoMessagesMessage();
        }
      },
      error: function(xhr, status, error) {
        console.error(error);
        displayErrorMessage();
      }
    });
  }

  // Function to send a message
  function sendMessage(conversationId, recipient, messageContent) {
    $.ajax({
      url: 'sendmessage.php',
      type: 'POST',
      dataType: 'json',
      data: { conversationId: conversationId, recipient: recipient, messageContent: messageContent },
      success: function(response) {
        if (response.success) {
          displaySuccessMessage();
        } else {
          displayErrorMessage();
        }
      },
      error: function(xhr, status, error) {
        console.error(error);
        displayErrorMessage();
      }
    });
  }

  // Example function to display conversations
  function displayConversations(conversations) {
    // Clear the conversation list
    $('#conversation-list').empty();

    // Loop through conversations and display each one
    conversations.forEach(function(conversation) {
      conversationId = conversation.conversation_id;
      var recipientUsername = conversation.other_username;

      // Create and append a conversation item to the list
      var listItem = $('<li>')
        .text(recipientUsername)
        .data('conversation-id', conversationId)
        .addClass('conversation');
      $('#conversation-list').append(listItem);
    });
  }

  // Example function to display message thread
  function displayMessageThread(messages) {
    // Clear the message thread
    $('#message-thread').empty();

    // Assuming you have a way to identify the current user's ID
    var currentUserId = 'currentUserId'; // You should replace this with the actual current user's ID

    messages.forEach(function (message) {
        var sender = message.sender_username;
        var receiver = message.receiver_username;
        var content = message.content;

        // Determine if the message is sent by the current user
        var isCurrentUserSender = message.sender_id === currentUserId;

        // If the message is sent by the current user, adjust the display
        if (isCurrentUserSender) {
            receiver = message.receiver_username;
        } else {
            // If the message is received by the current user, adjust the display
            sender = message.sender_username;
        }

        // Create the div with the message content
        var messageItem = $('<div>')
            .addClass('message')
            .append($('<span>').text(sender + ': '))
            .append($('<span>').text(content))
            .append($('<span>').text(' naar: ' + receiver));

        // Append the message item to the message thread
        $('#message-thread').append(messageItem);
    });

    // Create the close button and append it to the last message
    var closeButton = $('<img>', {
        src: 'exitbutton.png',
        class: 'close-conversation'
    }).prependTo('#message-thread .message:first');
}
  
  $(document).on('click', '.close-conversation', function() {
    clearInterval(intervalId);
    $('#message-thread').toggle();
  });

  // Example function to display success message
  function displaySuccessMessage() {
    alert('Message sent successfully.');
    //location.reload();
  }

  // Example function to display error message
  function displayErrorMessage() {
    //alert('An error occurred. Please try again.');
  }

  // Example function to handle the case when no conversations are available
  function displayNoConversationsMessage() {
    $('#conversation-list').html('<li>No conversations found.</li>');
    $('#compose-form').show();
  }

  // Example function to handle the case when no messages are available
  function displayNoMessagesMessage() {
    $('#message-thread').html('<div>No messages found.</div>');
  }

  function startConversation(recipient, messageContent){
    $.ajax({
      url: 'startconversation.php',
      type: 'POST',
      dataType: 'json',
      data: { recipient: recipient, messageContent: messageContent },
      success: function(response) {
        if (response.success) {
          var recipient = $('#recipient').val();
          var messageContent = $('#message-content').val();

            alert("Conversation has been started, page will refresh.");

          location.reload();
        } else {
          displayErrorMessage();
        }
      },
      error: function(xhr, status, error) {
        console.error(error);
        displayErrorMessage();
      }
    });
  }
});