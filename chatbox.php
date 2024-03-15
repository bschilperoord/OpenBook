<?php

include 'header.php';

if (!isset($_SESSION["loggedinuser"])) {
    header("location: user.php");
    exit; // Stop further execution of the code
}

// Verbinding maken met de database
$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");

// Check if sending a message
if (isset($_GET['sendingmessage']) && $_GET['sendingmessage'] === 'true') {
  // Prepare the query to update the value in the database
  $stmt = $conn->prepare("INSERT INTO messages (userid, username, message, timestamp) VALUES (?, ?, ?, ?)");
  $stmt->bind_param('ssss', $_SESSION['userid'], $_SESSION['loggedinuser'], $_POST['bericht'], $_COOKIE['datecookie']);

  function containsBadWords($input) {
    // Define an array of bad words
    $badWords = array(
        "2g1c",
        "2 girls 1 cup",
        "acrotomophilia",
        "alabama hot pocket",
        "alaskan pipeline",
        "anal",
        "anilingus",
        "anus",
        "apeshit",
        "arsehole",
        "ass",
        "asshole",
        "assmunch",
        "auto erotic",
        "autoerotic",
        "babeland",
        "baby batter",
        "baby juice",
        "ball gag",
        "ball gravy",
        "ball kicking",
        "ball licking",
        "ball sack",
        "ball sucking",
        "bangbros",
        "bareback",
        "barely legal",
        "barenaked",
        "bastard",
        "bastardo",
        "bastinado",
        "bbw",
        "bdsm",
        "beaner",
        "beaners",
        "beaver cleaver",
        "beaver lips",
        "bestiality",
        "big black",
        "big breasts",
        "big knockers",
        "big tits",
        "bimbos",
        "birdlock",
        "bitch",
        "bitches",
        "black cock",
        "blonde action",
        "blonde on blonde action",
        "blowjob",
        "blow job",
        "blow your load",
        "blue waffle",
        "blumpkin",
        "bollocks",
        "bondage",
        "boner",
        "boob",
        "boobs",
        "booty call",
        "brown showers",
        "brunette action",
        "bukkake",
        "bulldyke",
        "bullet vibe",
        "bullshit",
        "bung hole",
        "bunghole",
        "busty",
        "butt",
        "buttcheeks",
        "butthole",
        "camel toe",
        "camgirl",
        "camslut",
        "camwhore",
        "carpet muncher",
        "carpetmuncher",
        "chocolate rosebuds",
        "circlejerk",
        "cleveland steamer",
        "clit",
        "clitoris",
        "clover clamps",
        "clusterfuck",
        "cock",
        "cocks",
        "coprolagnia",
        "coprophilia",
        "cornhole",
        "coon",
        "coons",
        "creampie",
        "cum",
        "cumming",
        "cunnilingus",
        "cunt",
        "darkie",
        "date rape",
        "daterape",
        "deep throat",
        "deepthroat",
        "dendrophilia",
        "dick",
        "dildo",
        "dingleberry",
        "dingleberries",
        "dirty pillows",
        "dirty sanchez",
        "doggie style",
        "doggiestyle",
        "doggy style",
        "doggystyle",
        "dog style",
        "dolcett",
        "domination",
        "dominatrix",
        "dommes",
        "donkey punch",
        "double dong",
        "double penetration",
        "dp action",
        "dry hump",
        "dvda",
        "eat my ass",
        "ecchi",
        "ejaculation",
        "erotic",
        "erotism",
        "escort",
        "eunuch",
        "faggot",
        "fecal",
        "felch",
        "fellatio",
        "feltch",
        "female squirting",
        "femdom",
        "figging",
        "fingerbang",
        "fingering",
        "fisting",
        "foot fetish",
        "footjob",
        "frotting",
        "fuck",
        "fuck buttons",
        "fuckin",
        "fucking",
        "fucktards",
        "fudge packer",
        "fudgepacker",
        "futanari",
        "gang bang",
        "gay sex",
        "genitals",
        "giant cock",
        "girl on",
        "girl on top",
        "girls gone wild",
        "goatcx",
        "goatse",
        "god damn",
        "gokkun",
        "golden shower",
        "goodpoop",
        "goo girl",
        "goregasm",
        "grope",
        "group sex",
        "g-spot",
        "guro",
        "hand job",
        "handjob",
        "hard core",
        "hardcore",
        "hentai",
        "homoerotic",
        "honkey",
        "hooker",
        "hot carl",
        "hot chick",
        "how to kill",
        "how to murder",
        "huge fat",
        "humping",
        "incest",
        "intercourse",
        "jack off",
        "jail bait",
        "jailbait",
        "jelly donut",
        "jerk off",
        "jigaboo",
        "jiggaboo",
        "jiggerboo",
        "jizz",
        "juggs",
        "kike",
        "kinbaku",
        "kinkster",
        "kinky",
        "knobbing",
        "leather restraint",
        "leather straight jacket",
        "lemon party",
        "lolita",
        "lovemaking",
        "make me come",
        "male squirting",
        "masturbate",
        "menage a trois",
        "milf",
        "missionary position",
        "motherfucker",
        "mound of venus",
        "mr hands",
        "muff diver",
        "muffdiving",
        "nambla",
        "nawashi",
        "negro",
        "neonazi",
        "nigga",
        "nigger",
        "nig nog",
        "nimphomania",
        "nipple",
        "nipples",
        "nsfw images",
        "nude",
        "nudity",
        "nympho",
        "nymphomania",
        "octopussy",
        "omorashi",
        "one cup two girls",
        "one guy one jar",
        "orgasm",
        "orgy",
        "paedophile",
        "paki",
        "panties",
        "panty",
        "pedobear",
        "pedophile",
        "pegging",
        "penis",
        "phone sex",
        "piece of shit",
        "pissing",
        "piss pig",
        "pisspig",
        "playboy",
        "pleasure chest",
        "pole smoker",
        "ponyplay",
        "poof",
        "poon",
        "poontang",
        "punany",
        "poop chute",
        "poopchute",
        "porn",
        "porno",
        "pornography",
        "prince albert piercing",
        "pthc",
        "pubes",
        "pussy",
        "queaf",
        "queef",
        "quim",
        "raghead",
        "raging boner",
        "rape",
        "raping",
        "rapist",
        "rectum",
        "reverse cowgirl",
        "rimjob",
        "rimming",
        "rosy palm",
        "rosy palm and her 5 sisters",
        "rusty trombone",
        "sadism",
        "santorum",
        "scat",
        "schlong",
        "scissoring",
        "semen",
        "sex",
        "sexo",
        "sexy",
        "shaved beaver",
        "shaved pussy",
        "shemale",
        "shibari",
        "shit",
        "shitblimp",
        "shitty",
        "shota",
        "shrimping",
        "skeet",
        "slanteye",
        "slut",
        "s&m",
        "smut",
        "snatch",
        "snowballing",
        "sodomize",
        "sodomy",
        "spic",
        "splooge",
        "splooge moose",
        "spooge",
        "spread legs",
        "spunk",
        "strap on",
        "strapon",
        "strappado",
        "strip club",
        "style doggy",
        "suck",
        "sucks",
        "suicide girls",
        "sultry women",
        "swastika",
        "swinger",
        "tainted love",
        "taste my",
        "tea bagging",
        "threesome",
        "throating",
        "tied up",
        "tight white",
        "tit",
        "tits",
        "titties",
        "titty",
        "tongue in a",
        "topless",
        "tosser",
        "towelhead",
        "tranny",
        "tribadism",
        "tub girl",
        "tubgirl",
        "tushy",
        "twat",
        "twink",
        "twinkie",
        "two girls one cup",
        "undressing",
        "upskirt",
        "urethra play",
        "urophilia",
        "vagina",
        "venus mound",
        "vibrator",
        "violet wand",
        "vorarephilia",
        "voyeur",
        "vulva",
        "wank",
        "wetback",
        "wet dream",
        "white power",
        "wrapping men",
        "wrinkled starfish",
        "xx",
        "xxx",
        "yaoi",
        "yellow showers",
        "yiffy",
        "zoophilia",
        "🖕",
        // Add more bad words to the list as needed
    );

    // Convert the input to lowercase for a case-insensitive comparison
    $inputLowercase = strtolower($input);

    // Check if any of the bad words are present in the input
    foreach ($badWords as $badWord) {
        if (strpos($inputLowercase, $badWord) !== false) {
            return true; // Bad word found
        }
    }

    return false; // No bad words found
}

if(containsBadWords($_POST['bericht'])){
    header("Location: index.php?error=bad_words");
    exit;
}

  // Execute the query
  if ($stmt->execute()) {
      echo "Value updated successfully in the database";
      header("location: index.php");
      exit;
  } else {
      echo "Error updating value in the database: " . $stmt->error;
  }

}

// Controleren op fouten bij het maken van de verbinding
if ($conn->connect_error) {
    die("Kan geen verbinding maken met de database: " . $conn->connect_error);
}

// Laatste bijgewerkte timestamp of ID ophalen (bijvoorbeeld uit een database)
$lastUpdated = $_COOKIE['datecookie'];

// Controleren of er nieuwe gegevens zijn sinds de opgegeven timestamp of ID7

// Validate and sanitize the 'init' parameter
$init = isset($_GET['init']) && $_GET['init'] === "true" ? true : false;

// Use prepared statements to prevent SQL injection
if ($init) {
    $sql = "SELECT
    u.profileimage,
    u.username,
    m.postid,
    m.message,
    m.timestamp,
    COALESCE(l.postid, 0) AS likes
FROM messages m
LEFT JOIN users u ON m.username = u.username
LEFT JOIN likes l ON l.itemid = m.postid AND l.type = 'chatbox'
WHERE m.timestamp >= (
    SELECT MIN(messages.timestamp)
    FROM messages
    ORDER BY messages.timestamp DESC
    LIMIT 1
);";
    
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT
    u.profileimage,
    u.username,
    m.postid,
    m.message,
    m.timestamp,
    COALESCE(l.postid, 0) AS likes
FROM messages m
LEFT JOIN users u ON m.username = u.username
LEFT JOIN likes l ON l.itemid = m.postid AND l.type = 'chatbox'
WHERE m.timestamp >= (
    SELECT MIN(messages.timestamp)
    FROM messages
    ORDER BY messages.timestamp DESC
    LIMIT 1
);";
    $stmt = $conn->prepare($sql);
}

// Check if the statement was successfully prepared
if ($stmt === false) {
    die("Error preparing the SQL query: " . $conn->error);
}

// Execute the prepared statement
if (!$stmt->execute()) {
    die("Error executing the SQL query: " . $stmt->error);
}

// Get the result set
$result = $stmt->get_result();

// Format the data as HTML fragment or JSON, depending on your needs
$resultset = [];

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$info = [
    "naam" => $setting["waarde"]
];

if ($dev === true) {
    $info["cookie"] = $_COOKIE['datecookie'];
    $info["sql"] = $sql;
}

$resultset = [
    "info" => $info,
    "data" => $data
];

echo json_encode($resultset);

// Close the prepared statement
$stmt->close();

// Close the database connection
$conn->close();
?>
