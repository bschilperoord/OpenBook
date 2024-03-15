<?php
include 'header.php';

// Establish the database connection
$conn = new mysqli("$servernamesql", "$usernamesql", "$passwordsql", "$databasesql");

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the sender ID, recipient username, and message content from the request
$senderId = $_SESSION['userid']; // Assuming you have the sender ID stored in the session
$recipientUsername = $_POST['recipient'] ?? '';
$conversationId = $_POST['conversationId'] ?? '';
$messageContent = $_POST['messageContent'] ?? '';

// Validate and sanitize the recipient username, conversation ID, and message content
$recipientUsername = trim($recipientUsername);
$recipientUsername = htmlspecialchars($recipientUsername, ENT_QUOTES, 'UTF-8');

$conversationId = trim($conversationId);
$conversationId = filter_var($conversationId, FILTER_VALIDATE_INT);

$messageContent = trim($messageContent);
$messageContent = htmlspecialchars($messageContent, ENT_QUOTES, 'UTF-8');

function containsBadWords($input) {
    // Define an array of bad words
    $badWords = array(
        // Add more bad words to the list as needed
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

if(containsBadWords($messageContent)){
    $response = array(
        'success' => false
    );
    exit;
}

if (!empty($recipientUsername) && !empty($messageContent)) {
    // Prepare and execute the query to get the recipient ID based on the recipient username
    $query = "SELECT userid FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $recipientUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the recipient exists
    if ($result->num_rows > 0) {
    
        // Fetch the recipient ID
        $row = $result->fetch_assoc();
        $recipientId = $row['userid'];

       // Query to get the maximum conversation_id
        $query2 = "SELECT MAX(conversation_id) AS max_conversation_id FROM conversations";

        $result2 = $conn->query($query2);

        if ($result2->num_rows > 0) {
            // Fetch the result row
            $row2 = $result2->fetch_assoc();
            $maxConversationId = $row2["max_conversation_id"];
            $maxConversationId = intval($maxConversationId);
            $maxConversationId = $maxConversationId + 1;
        } else {
            // No records found, start with conversation_id 1
            $maxConversationId = 1;
        }

        // Insert the message into the database
        $query3 = "INSERT INTO privatemessages (conversation_id, sender_id, recipient_id, content)
        VALUES (?, ?, ?, ?)";
        $stmt3 = $conn->prepare($query3);
        $stmt3->bind_param('iiis', $conversationId, $senderId, $recipientId, $messageContent);
        $stmt3->execute();

        // Check if the message was inserted successfully
        if ($stmt->affected_rows > 0) {
            $response = array(
                'success' => true
            );
        } else {
            $response = array(
                'success' => false
            );
        }

        // Close the database connection
        $stmt3->close();
        $conn->close();

    } else {
        // Recipient does not exist
        $response = array(
            'success' => false
        );
    }
} else {
    // Invalid input
    $response = array(
        'success' => false
    );
}

// Set the response header to JSON
header('Content-Type: application/json');

// Send the JSON response
echo json_encode($response);
?>