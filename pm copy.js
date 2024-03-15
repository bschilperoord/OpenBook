$(document).ready(function() {
    // Load conversations
    loadConversations();

    // Load message thread for a conversation when clicked
    $('#conversation-list').on('click', '.conversation', function() {
      var conversationId = $(this).data('conversation-id');
      loadMessageThread(conversationId);
    });
  
    // Submit the compose form to send a message
    $('#compose-form').submit(function(e) {
      e.preventDefault();
      var recipient = $('#recipient').val();
      var message = $('#message-content').val();
      sendMessage(recipient, message);
    });
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
  function loadMessageThread(conversationId) {
    $.ajax({
      url: 'getmessages.php',
      type: 'GET',
      dataType: 'json',
      data: { conversationId: conversationId },
      success: function(response) {
        if (response.success) {
          var messages = response.messages;
          displayMessageThread(messages);
        } else {
          displayNoMessagesMessage();
        }
      },
      error: function(xhr, status, error) {
        console.error(error);
        displayErrorMessage();
      }
    });
  }
  
  // Function to send a message
  function sendMessage(conversationId, recipientId, messageContent) {
    $.ajax({
      url: 'sendmessage.php',
      type: 'POST',
      dataType: 'json',
      data: { conversationId: conversationId, recipientId: recipientId, messageContent: messageContent },
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
      var conversationId = conversation.conversation_id;
      var recipientUsername = conversation.recipient_username;
  
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

    // Loop through messages and display each one
    messages.forEach(function(message) {
      var sender = message.sender_username; // Fix: Use 'sender_username' instead of 'sender'
      var content = message.content;

      // Create and append a message item to the thread
      var messageItem = $('<div>')
        .addClass('message')
        .append($('<span>').text(sender + ': '))
        .append($('<span>').text(content));
      $('#message-thread').append(messageItem);
    });
  }
  
  // Example function to display success message
  function displaySuccessMessage() {
    alert('Message sent successfully.');
  }
  
  // Example function to display error message
  function displayErrorMessage() {
    alert('An error occurred. Please try again.');
  }
  
  // Example function to handle the case when no conversations are available
  function displayNoConversationsMessage() {
    $('#conversation-list').html('<li>No conversations found.</li>');
  }
  
  // Example function to handle the case when no messages are available
  function displayNoMessagesMessage() {
    $('#message-thread').html('<div>No messages found.</div>');
  }