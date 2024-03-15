function onImageClick() {

  var a = (function() {
    var e = null,
        c = null,
        h = null,
        d = function() {
            e = document.createElement("canvas");
            e.style.display = "none";
            var j = document.getElementsByTagName("body")[0];
            j.appendChild(e);
            c = e.getContext("2d")
        },
        b = function(k) {
            var j = document.getElementById(k);
            e.width = j.width;
            e.height = j.height;
            h = null;
            c.drawImage(j, 0, 0)
        },
        g = function(j) {
            e.width = j.width;
            e.height = j.height;
            h = null;
            c.drawImage(j, 0, 0)
        },
        i = function() {
            var l = c.getImageData(0, 0, e.width, e.height),
                m = l.data;
            var j = new Worker("worker.nude.js"),
                k = [m, e.width, e.height];
            j.postMessage(k);
            j.onmessage = function(n) {
                f(n.data)
            }
        },
        f = function(j) {
            if (h) {
                h(j);
            } else {
                if (j) {
                    console.log("The picture contains nudity");
                }
            }
        };
    return {
        init: function() {
            d();
            if (!!!window.Worker) {
                document.write(unescape("%3Cscript src='noworker.nude.js' type='text/javascript'%3E%3C/script%3E"));
            }
        },
        load: function(j) {
            if (typeof(j) == "string") {
                b(j);
            } else {
                g(j);
            }
        },
        scan: function(j) {
            if (arguments.length > 0 && typeof(arguments[0]) == "function") {
                h = j;
            }
            i();
        }
    };
})();

if (!window.nude) {
    window.nude = a;
}

a.init();

  // Your code for handling the image click event goes here
  // For example, you can load the image and start the nudity detection process:
  var imageElement = document.getElementById("profanitycheck");
  return new Promise(function(resolve, reject) {
    // ... Your existing code for nudity detection ...

    a.load(imageElement);
    a.scan(function(result) {
      if (result) {
        console.log("The picture contains nudity.");
        reject(new Error("Nudity and profanity detected."));
      } else {
        console.log("The picture is safe.");
        resolve();
      }
    });
  });
}

function changeBackground() {
    var selectedImage = document.getElementById("image-input").files[0];
    var reader = new FileReader();
    
    reader.onload = function(e) {
      var editableDiv = document.getElementById("editable-content");
      editableDiv.style.backgroundImage = "url('" + e.target.result + "')";
      var profanitycheck = document.getElementById('profanitycheck');
      profanitycheck.src = e.target.result;
    }
    
    reader.readAsDataURL(selectedImage);
  }

async function saveContent() {

    try {
      await onImageClick('profanitycheck');
    } catch (error) {
      console.error(error.message);
      alert("Profanity or invalid content detected. Exiting.")
      return; // Exit the saveContent function if an error is caught during nudity detection
    }

    var editableDiv = document.getElementById("editable-content");
    var content = editableDiv.innerHTML;
    var selectedImage = document.getElementById("image-input").files[0];
    
    var formData = new FormData();
    formData.append("content", content);
    formData.append("image", selectedImage);
    
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "saveblogpost.php", true);
    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        var response = xhr.responseText;
        alert(response);
      }
    };
    
    xhr.send(formData);
}