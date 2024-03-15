<?php
// save_settings.php

$temporaryFolder = sys_get_temp_dir();

session_start();

// Retrieve the submitted form data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$trimmedpassword = trim($password);
$hashedPassword = hash("sha512", $trimmedpassword); // Revert back to SHA-512 hashing
$image_tmp = $_FILES['profileimage']['tmp_name'] ?? '';
$image = $_FILES['profileimage']['name'] ?? '';



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


if (containsBadWords($name)) {
    header("Location: settings.php?error=bad_words");
    //exit;
}

if (containsBadWords($password)) {
    header("Location: settings.php?error=bad_words");
    //exit;
}

// Check if username or password is empty
if (empty($name) || empty($password)) {
    header("Location: settings.php?error=empty_password_or_name");
    //exit;
}

if (strlen($name) < 6 || strlen($password) < 6) {
    header("Location: settings.php?error=password_or_name_too_short");
    //exit;
}
// Assuming you have a MySQL database, here's an example:

include 'header.php';

// 1. Establish a database connection using MySQLi
$conn = new mysqli($servernamesql, $usernamesql, $passwordsql, $databasesql);

$twoFactorCode = isset($_SESSION['secretkey']) ? $_SESSION['secretkey'] : '';

    // Check if 2FA is empty or not already set
if (!empty($twoFactorCode)) {
    $stmtCheck2FA = $conn->prepare("SELECT `2FA` FROM users WHERE userid = ?");
    $stmtCheck2FA->bind_param("i", $_SESSION['userid']);
    $stmtCheck2FA->execute();
    $stmtCheck2FA->bind_result($existing2FA);
    $stmtCheck2FA->fetch();
    $stmtCheck2FA->close();

    if ($existing2FA !== null) {
        $errors[] = "Two-Factor Authentication code is already set.";
		
		header("location: settings.php");
    } else {
        $stmtTwoFactor = $conn->prepare("UPDATE users SET `2FA` = ? WHERE userid = ?");
        $stmtTwoFactor->bind_param("si", $twoFactorCode, $_SESSION['userid']);

        $updateTwoFactor = $stmtTwoFactor->execute();

        if (!$updateTwoFactor) {
            $errors[] = "Error updating Two-Factor Authentication code.";
        }

        $stmtTwoFactor->close();
        $stmtTwoFactor->execute();
		
		header("location: settings.php");
    }
}

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute the update query
if (!empty($name)) {
    $stmtUsername = $conn->prepare("UPDATE users SET username = ? WHERE userid = ?");
    $stmtUsername->bind_param("si", $name, $_SESSION['userid']);

    $updateUsername = $stmtUsername->execute();

    if (!$updateUsername) {
        $errors[] = "Error updating username.";
    }

    $stmtUsername->close();
}

if (!empty($password)) {
    $stmtPassword = $conn->prepare("UPDATE users SET password = ? WHERE userid = ?");
    $stmtPassword->bind_param("si", $hashedPassword, $_SESSION['userid']);

    $updatePassword = $stmtPassword->execute();

    if (!$updatePassword) {
        $errors[] = "Error updating password.";
    }

    $stmtPassword->close();
}

// ...


// File upload handling
$upload_directory = $_SERVER['DOCUMENT_ROOT'] . '/profile_images/';
$directoryinroot = './profile_images/' . basename($image);
$destination_path = $upload_directory . basename($image);

if ($_FILES['profileimage']['error'] === UPLOAD_ERR_OK) {
    // Perform additional checks for file type and size if necessary
    // For example, check if the file is an image and within an acceptable size range

    if (move_uploaded_file($image_tmp, $directoryinroot)) {
        // Update the profile image path in the database
        $stmtProfileImage = $conn->prepare("UPDATE users SET profileimage = ? WHERE userid = ?");
        $stmtProfileImage->bind_param("si", $directoryinroot, $_SESSION['userid']);

        if (!empty($image)) {
            $updateProfileImage = $stmtProfileImage->execute();

            if ($updateProfileImage) {
                echo "Upload successful.";
                $_SESSION['profileimage'] = $directoryinroot;
            } else {
                $errors[] = "Error updating profile image.";
            }
        }

        $stmtProfileImage->close();
    } else {
        $errors[] = "Error uploading file.";
    }
} else {
    // Handle file upload errors
    switch ($_FILES['profileimage']['error']) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $errors[] = "The uploaded file exceeds the maximum file size.";
            break;
        case UPLOAD_ERR_PARTIAL:
            $errors[] = "The uploaded file was only partially uploaded.";
            break;
        case UPLOAD_ERR_NO_FILE:
            // No file was uploaded, which is okay if it's not required
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
        case UPLOAD_ERR_CANT_WRITE:
        case UPLOAD_ERR_EXTENSION:
            $errors[] = "File upload error. Please try again later.";
            break;
        default:
            $errors[] = "Unknown file upload error.";
            break;
    }
}

$conn->close();

// If there are errors, display them and redirect back to the settings page
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo htmlspecialchars($error) . "<br>"; // Sanitize error message
    }
    echo '<a href="settings.php">Go back</a>';
    exit;
}

echo "Settings saved successfully!";

// Redirect back to the settings page after a delay
?>
