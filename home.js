$(function() {
    $(document).on('click', '.like-btn-chatbox', function(event) {
        event.preventDefault(); // Prevent any default behavior of the link
        
        var $likeBtn = $(this);
        var postId = $likeBtn.data('post-id');
        var $likeCount = $likeBtn.siblings('.like-count');
        
        $likeCount.attr('data-post-id', postId);

        $.ajax({
            url: 'likes.php',
            type: 'POST',
            data: { post_id: postId, type: 'chatbox' },
            dataType: 'json', // Specify that you expect JSON response
            success: function(response) {
                try {
                    // Parse the JSON response directly using response variable
                    const responseObject = response;
        
                    // Update the likecount directly, no need for replace
                    $likeCount.text(responseObject.likecount);
        
                    console.log(responseObject.likecount); // Output: 126
        
                } catch (error) {
                    console.error('Error parsing response:', error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
            }
        });
    });

        var dragItem = document.getElementById('winretro');
        var active = false;
        var currentX = 0;
        var currentY = 0;
        var initialX = 0;
        var initialY = 0;
        var xOffset = 0;
        var yOffset = 0;
    
        function setTranslate(xPos, yPos) {
            dragItem.style.transform = "translate3d(" + xPos + "px, " + yPos + "px, 0)";
        }
    
        function dragStart(e) {
            initialX = e.clientX - xOffset;
            initialY = e.clientY - yOffset;
            if (e.target === dragItem) {
                active = true;
            }
        }
    
        function dragEnd() {
            initialX = currentX;
            initialY = currentY;
            active = false;
            localStorage.setItem('dragItemX', initialX);
            localStorage.setItem('dragItemY', initialY);
        }
    
        function drag(e) {
            if (active) {
                e.preventDefault();
    
                currentX = e.clientX - initialX;
                currentY = e.clientY - initialY;
                xOffset = currentX;
                yOffset = currentY;
    
                setTranslate(currentX, currentY);
            }
        }
    
        function restorePosition() {
            var xPos = parseInt(localStorage.getItem('dragItemX'), 10);
            var yPos = parseInt(localStorage.getItem('dragItemY'), 10);
            if (!isNaN(xPos) && !isNaN(yPos)) {
                xOffset = xPos;
                yOffset = yPos;
                setTranslate(xPos, yPos);
            }
        }
    
        dragItem.addEventListener("mousedown", dragStart, false);
        document.addEventListener("mouseup", dragEnd, false);
        document.addEventListener("mousemove", drag, false);
        restorePosition();

    $(document).on('click', '.like-btn-blogposts', function(event) {
        event.preventDefault(); // Prevent any default behavior of the link
        
        var $likeBtn = $(this);
        var postId = $likeBtn.data('post-id');
        var $likeCount = $likeBtn.siblings('.like-count');
        
        $likeCount.attr('data-post-id', postId);

        $.ajax({
            url: 'likes.php',
            type: 'POST',
            data: { post_id: postId, type: 'blogposts' },
            dataType: 'json', // Specify that you expect JSON response
            success: function(response) {
                try {
                    // Parse the JSON response directly using response variable
                    const responseObject = response;
        
                    // Update the likecount directly, no need for replace
                    $likeCount.text(responseObject.likecount);
        
                    console.log(responseObject.likecount); // Output: 126
        
                } catch (error) {
                    console.error('Error parsing response:', error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {

// Flag to keep track of whether weather info is already shown
let isWeatherInfoShown = sessionStorage.getItem('isWeatherInfoShown2') === 'true';

// Flag to keep track of whether the RSS feed is already shown
let isRSSFeedShown = sessionStorage.getItem('isRSSFeedShown2') === 'true';

// Function to show weather info and RSS feed
function showWeatherInfoAndRSSFeed() {
    console.log("showWeatherInfoAndRSSFeed");

    // Check if weather info and RSS feed are not already shown
        if (!isWeatherInfoShown) {
            // Simulate displaying weather info (replace with your actual logic)
            console.log("Displaying weather info");

            // Perform the fetch operation for weather (replace with your actual fetch logic)
            fetch('widgets.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'settingwidget=weather, 1'
            })
            .then(response => response.text())
            .then(data => {
                //console.log(data); // Display server response
            })
            .catch(error => {
                //console.error('Error:', error);
            });

            // Set the flag in sessionStorage to indicate that weather info is now shown
            isWeatherInfoShown = true;
            sessionStorage.setItem('isWeatherInfoShown2', true);
        } else {
            console.log("Weather info already shown");
        }

        if (!isRSSFeedShown) {
            // Simulate displaying RSS feed (replace with your actual logic)
            console.log("Displaying RSS feed");

            // Perform the fetch operation for RSS feed (replace with your actual fetch logic)
            fetch('widgets.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'settingwidget=rss, 1'
            })
            .then(response => response.text())
            .then(data => {
                //console.log(data); // Display server response
            })
            .catch(error => {
                //console.error('Error:', error);
            });

            // Set the flag in sessionStorage to indicate that the RSS feed is now shown
            isRSSFeedShown = true;
            sessionStorage.setItem('isRSSFeedShown2', true);
        } else {
            console.log("RSS feed already shown");
        }
    }

    // Function to hide weather info
    function hideWeatherInfo() {
        console.log("hideWeatherInfo");
        var div = document.querySelector(".visible");
        div.classList.remove("visible");
        div.classList.add("hidden");
        fetch('widgets.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'settingwidget=weather, 0'
        })
        .then(response => response.text())
        .then(data => {
            //console.log(data); // Display server response
        })
        .catch(error => {
            //console.error('Error:', error);
        });
    }

    // Flag to keep track of whether weather info is already shown
    let isWeatherInfoShown2 = sessionStorage.getItem('isWeatherInfoShown2') === 'true';

    // Function to show weather info
    function showWeatherInfo() {
        console.log("showWeatherInfo");

        // Check if weather info is not already shown
        if (!isWeatherInfoShown2) {
            var div = document.querySelector(".visible");

            fetch('widgets.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'settingwidget=weather, 1'
            })
            .then(response => response.text())
            .then(data => {
                //console.log(data); // Display server response
            })
            .catch(error => {
                //console.error('Error:', error);
            });

            // Set the flag in sessionStorage to indicate that weather info is now shown
            isWeatherInfoShown2 = true;
            sessionStorage.setItem('isWeatherInfoShown2', true);
        } else {
            console.log("Weather info already shown");
        }
    }

    // Your API Key and city for weather data
    const apiKey = 'e931d419cfca8b945144291c1c03ff47';
    const city = 'New York';

    // Fetch weather data
    fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}`)
        .then(response => response.json())
        .then(data => {
            // Check if data.weather exists and contains at least one element
            if (data.weather && data.weather.length > 0) {
                const weatherDescription = data.weather[0].description;
                const temperature = Math.round(data.main.temp - 273.15); // Convert temperature from Kelvin to Celsius
                const humidity = data.main.humidity;

                // Create a string with the weather information
                const weatherDataString = `Weather: ${weatherDescription}<br>Temperature: ${temperature}°C<br>Humidity: ${humidity}%`;

                // Update the weatherInfo element's content with the weather information
                const weatherInfoElements = document.querySelectorAll(".visible");
                weatherInfoElements.forEach(element => {
                    element.innerHTML = `<div id='exit-button-weatherdiv'><img id='exit-button-weather' src='exitbutton.png'></div><br><img id='openweathermap' src='openweathermap.png'>${city}<br>${weatherDataString}`;
                    element.querySelector('#exit-button-weather').addEventListener("click", hideWeatherInfo);
                });
            } else {
                // If weather data is not available, display an error message
                const errorMessage = 'Weather data not available for this city.';
                const weatherInfoElements = document.querySelectorAll(".visible");
                weatherInfoElements.forEach(element => {
                    element.innerHTML = errorMessage;
                });
            }
        })
        .catch(error => {
            console.error('Error fetching weather data:', error);
        });

    // Function to display the RSS feed
    function displayRSSFeed(url, containerId) {
        console.log("displayRSSFeed");
        fetch(url)
            .then(response => response.text())
            .then(data => {
                const parser = new DOMParser();
                const xmlDoc = parser.parseFromString(data, 'text/xml');
                const items = xmlDoc.querySelectorAll('item');
                let html = '';

                items.forEach(item => {
                    const title = item.querySelector('title').textContent;
                    const link = item.querySelector('link').textContent;
                    html += `<a href="${link}">${title}</a><br><br>`;
                });

                // Update the RSS feed container with the feed data
                const rssFeedElements = document.querySelectorAll(".visible2");
                rssFeedElements.forEach(element => {
                    element.innerHTML = `<div id="exit-button-rssdiv"><img id="exit-button-rss" src="exitbutton.png"></div><br><img id="nunllogo" src="Symbol-New-York-Times.png">${html}`;
                    element.querySelector('#exit-button-rss').addEventListener("click", hideRSSFeed);
                });
            })
            .catch(error => console.error('Error fetching RSS feed:', error));
    }

    // Function to hide RSS feed
    function hideRSSFeed() {
        console.log("hideRSSFeed");

        var div = document.querySelector(".visible2");
        div.classList.remove("visible2");
        div.classList.add("hidden2");
        fetch('widgets.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'settingwidget=rss, 0'
        })
        .then(response => response.text())
        .then(data => {
            //console.log(data); // Display server response
        })
        .catch(error => {
            //console.error('Error:', error);
        });
    }

    // Flag to keep track of whether the RSS feed is already shown
    let isRSSFeedShown2 = sessionStorage.getItem('isRSSFeedShown2') === 'true';

    // Function to show RSS feed
    function showRSSFeed() {
        console.log("showRSSFeed");
        
        displayRSSFeed('rss-proxy.php', 'visible2');

        // Check if RSS feed is not already shown
        if (!isRSSFeedShown2) {
            console.log("Displaying RSS feed");

            fetch('widgets.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-umrlencoded'
                },
                body: 'settingwidget=rss, 1'
            })
            .then(response => response.text())
            .then(data => {
                console.log("Fetch response:", data); // Display server response
            })
            .catch(error => {
                console.error('Error:', error);
            });

            // Set the flag in sessionStorage to indicate that the RSS feed is now shown
            isRSSFeedShown = true;
            sessionStorage.setItem('isRSSFeedShown2', true);
        } else {
            console.log("RSS feed already shown");
        }
    }

    const draggableDiv = document.getElementById('sidebar');
    const optionsSelect = document.getElementById('options');
    const optionsForm = document.getElementById('optionsForm');
    let offsetX, offsetY, isDragging = false;
    let x = 0, y = 0; // Initialize coordinates

    draggableDiv.addEventListener('mousedown', (e) => {
        console.log("draggableDiv mousedown");
        if (!e.target.matches('#exit-button-weather, #exit-button-rss') && !optionsSelect.contains(e.target)) {
            isDragging = true;
            offsetX = e.clientX - draggableDiv.getBoundingClientRect().left;
            offsetY = e.clientY - draggableDiv.getBoundingClientRect().top;
            e.preventDefault();
        }
    });

    document.addEventListener('mousemove', (e) => {
        if (isDragging) {
            x = e.clientX - offsetX;
            y = e.clientY - offsetY;
            draggableDiv.style.left = `${x}px`;
            draggableDiv.style.top = `${y}px`;
        }
    });

    document.addEventListener('mouseup', () => {
        console.log("document mouseup");
        if (isDragging) {
            isDragging = false;
            sendDataToDatabase(x, y); // Send updated coordinates
        }
    });

    optionsForm.addEventListener('submit', (e) => {
        e.preventDefault();
        if (!isDragging) {
            // Allow form submission if not dragging
            // Do not preventDefault here
            const selectedValue = optionsSelect.value;

            if (selectedValue === "rss") {
                // Handle RSS option selection
                console.log("RSS option selected");

                fetch('widgets.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'settingwidget=rss, 1'
                })
                .then(response => response.text())
                .then(data => {

                location.reload();
                    //console.log(data); // Display server response
                })
                .catch(error => {
                    //console.error('Error:', error);
                });
            } else if (selectedValue === "weather") {
                // Handle Weather option selection
                console.log("Weather option selected");

                fetch('widgets.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'settingwidget=weather, 1'
                })
                .then(response => response.text())
                .then(data => {

                location.reload();
                    //console.log(data); // Display server response
                })
                .catch(error => {
                    //console.error('Error:', error);
                });
            }
        }
    });

    // Function to send coordinates to PHP script
    function sendDataToDatabase(x, y) {
        console.log("sendDataToDatabase");
        fetch('savecoordinates.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `x=${x}&y=${y}`
        })
        .then(response => response.text())
        .then(data => {
            //console.log(data); // Display server response
        })
        .catch(error => {
            //console.error('Error:', error);
        });
    }

    // Show weather info and RSS feed initially
    showWeatherInfo();
    showRSSFeed();
    showWeatherInfoAndRSSFeed();
});