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

document.addEventListener("DOMContentLoaded", async function () {

  document.getElementById("change2fa").addEventListener("click", function(event) {
    event.preventDefault(); // This prevents the default form submission behavior
    var qrcode = document.getElementById("qrcode");
    if (qrcode.style.display === "block") {
      qrcode.style.display = "none";
    } else {
      qrcode.style.display = "block";
    }
  });  

  // Add event listener to the file input
  const fileInput = document.getElementById("profile-image");
  fileInput.addEventListener("change", handleFileInputChange);

  // Function to handle file input change
  function handleFileInputChange(event) {
    const file = event.target.files[0];
    if (file) {
      // Create a FileReader to read the image file
      const reader = new FileReader();

      // When the FileReader finishes loading, set the image src
      reader.onload = function (e) {
        const imageElement = document.getElementById("profileimageeditor");
        imageElement.src = e.target.result;

        var profanitycheck = document.getElementById('profanitycheck');
        profanitycheck.src = e.target.result;
      };

      // Read the image file as a data URL
      reader.readAsDataURL(file);
    }
  }

  const form = document.getElementById('settingsform');
  const submitButton = document.getElementById('submitButton');

  submitButton.addEventListener('click', async function() {
    // Perform any additional validations or data processing here if needed
    try {
      await onImageClick('profanitycheck');
    } catch (error) {
      console.error(error.message);
      alert("Profanity detected. Exiting.")
      return; // Exit the saveContent function if an error is caught during nudity detection
    }

    form.dispatchEvent(new Event('submit'));
    });

    // Trigger the form's submit event programmatically
  // Optional: Handle the form submission event if you want to take further action
  form.addEventListener('submit', function(event) {
    // Prevent the default form submission behavior
    event.preventDefault();

    // Perform any final processing or Ajax submission if needed
    // For demonstration purposes, we will just log the form data
    const formData = new FormData(form);
    console.log('Form submitted with data:', Object.fromEntries(formData.entries()));
    });
  });