document.addEventListener('DOMContentLoaded', function() {

// Get references to the necessary HTML elements
var emojiInput = document.getElementById('bericht');
var emojiButton = document.getElementById('emoji-button');
var emojiPopup = document.getElementById('emoji-popup');

// Function to toggle the visibility of the emoji popup
function toggleEmojiPopup() {
  emojiPopup.style.display = emojiPopup.style.display === 'none' ? 'block' : 'none';
}

// Event listener to show/hide the emoji popup when the button is clicked
emojiButton.addEventListener('click', toggleEmojiPopup);

// Event listener to insert the selected emoji into the input field
emojiPopup.addEventListener('click', function(event) {
  if (event.target.matches('.emoji')) {
    insertEmoji(event.target.innerText);
  }
});

// Close the emoji popup when the user clicks outside of it
document.addEventListener('click', function(event) {
  if (!emojiPopup.contains(event.target) && event.target !== emojiButton) {
    emojiPopup.style.display = 'none';
  }
});

// Function to generate emoji elements and populate the popup
function generateEmojiElements() {
  const emojis = ['😊', '🔥', '❤️', '🎉']; // Replace with your desired emojis
  let emojiContent = '';

  emojis.forEach((emoji) => {
    emojiContent += `<span class="emoji">${emoji}</span>`;
  });

  emojiPopup.innerHTML = emojiContent;
}

// Function to insert the selected emoji at the cursor position
function insertEmoji(selectedEmoji) {
  const cursorPosition = emojiInput.selectionStart;

  const currentValue = emojiInput.value;
  const newValue =
    currentValue.substring(0, cursorPosition) +
    selectedEmoji +
    currentValue.substring(cursorPosition);

  emojiInput.value = newValue;

  const newCursorPosition = cursorPosition + selectedEmoji.length;
  emojiInput.setSelectionRange(newCursorPosition, newCursorPosition);
}

// Call the function to generate the emoji elements
generateEmojiElements();

var lastUpdated; // Huidige laatste bijgewerkte timestamp of ID
var previousData = null;
var init = true; 

function fetchData() {

  var xhr = new XMLHttpRequest();
  date = new Date();
  date = date.getFullYear() + '-' +
  ('00' + (date.getMonth()+1)).slice(-2) + '-' +
  ('00' + date.getDate()).slice(-2) + ' ' + 
  ('00' + date.getHours()).slice(-2) + ':' + 
  ('00' + date.getMinutes()).slice(-2) + ':' + 
  ('00' + date.getSeconds()).slice(-2);
  lastUpdated=date;

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var responseData = xhr.responseText;

      if (responseData !== previousData) {
      appendDataToHtml(JSON.parse(xhr.responseText));
      var objDiv = document.getElementById("chatbox");
      objDiv.scrollTop = objDiv.scrollHeight;
      previousData = responseData;
      }
    }
  };

  if(init==true) {
    init = false;
  }
  xhr.open("GET", "chatbox.php?init="+init+"&lastUpdated=" + lastUpdated, true);
  xhr.send();
}

// Define a flag to track whether the function has executed
var dataAppended = false;

function appendDataToHtml(json) {
  if (dataAppended) {
    return; // Exit the function if data has already been appended
  }
  
  console.log(json);
  var el = document.getElementById('radius');
  var template = document.getElementById('mijnTemplate').innerHTML;

  json.data.forEach(row => {
    console.log(row);
    // userid, username, message, timestamp
    var div = document.createElement('div');
    tmp = template;
    tmp = tmp.replace('FLD_IMG',row.profileimage);
    tmp = tmp.replace('FLD_NAAM',row.username);
    tmp = tmp.replace('FLD_BERICHT',row.message);
    tmp = tmp.replace('FLD_TIMESTAMP',row.timestamp);
    tmp = tmp.replace('FLD_POSTID',row.postid);
    tmp = tmp.replace('FLD_LIKES',row.likes);

    // JavaScript code to replace the 'src' attribute with the actual image URL
    var imageURL = row.profileimage; // Replace this with the actual image URL
    
    // Get the 'img' element by its ID
    var profileImage = document.getElementById('profileimagetable');
    
    // Set the 'src' attribute of the 'img' element to the imageURL
    profileImage.src = imageURL;
    
    div.innerHTML = tmp;
    el.append(div);
  });
  
  // Set the flag to indicate that data has been appended
  dataAppended = true;
}

setInterval(fetchData, 1000);

fetchData();

});