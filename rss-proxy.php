<?php
$feedUrl = 'https://rss.nytimes.com/services/xml/rss/nyt/HomePage.xml'; // Replace with the actual RSS feed URL
$feedContent = file_get_contents($feedUrl);

header('Access-Control-Allow-Origin: *'); // Adjust this header based on your requirements
header('Content-Type: application/rss+xml');
echo $feedContent;
?>
