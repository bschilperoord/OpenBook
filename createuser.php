<?php
session_start();
include 'header.php';
require_once 'vendor/autoload.php';
use RobThree\Auth\TwoFactorAuth;

// Function to safely hash the password
function hashPassword($password) {
    return hash('sha512', $password);
}
s
// Validate and sanitize user input
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $twofactorauthcode = trim($_POST['2facode']);

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
    
    if (containsBadWords($username)) {
        header("Location: user.php?error=bad_words");
        exit;
    }

    if (containsBadWords($password)) {
        header("Location: user.php?error=bad_words");
        exit;
    }

    // Check if username or password is empty
    if (empty($username) || empty($password)) {
        header("Location: user.php?error=empty_fields");
        exit;
    }

    if (strlen($username) < 6 || strlen($password) < 6) {
        header("Location: user.php?error=incorrect_username_or_password_length");
        exit;
    }

    // Create a connection
    $conn = new mysqli($servernamesql, $usernamesql, $passwordsql, $databasesql);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve the user data from the database
    $queryForId = "SELECT userid, password, 2FA, is_confirmed, confirmation_token FROM users WHERE username = ?";
    $stmt = $conn->prepare($queryForId);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists in the database
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userId = $row['userid'];
        $dbPassword = $row['password'];
        $db2fa = $row['2FA'];
        $isconfirmed = $row['is_confirmed'];
        $confirmationtoken = $row['confirmation_token'];

        // Verify password
        $hashedpw = hashPassword($password);
        if ($hashedpw !== $dbPassword) {
            // Password doesn't match, handle the error or redirect accordingly
            header("Location: user.php?error=invalid_password");
            exit;
        }
        else
        {
            // Verify Two-Factor Authentication code if a valid 2FA value is present
            if (!empty($db2fa)) {
                $tfa = new TwoFactorAuth('OpenBook'); // Replace 'OpenBook' with your application name
                $isValidOTP = $tfa->verifyCode($db2fa, $twofactorauthcode);

                echo $db2fa;
                echo "<br>";
                echo $twofactorauthcode;
                echo "<br>";

                if ($isValidOTP) {
                    if($isconfirmed == 1 && $confirmationtoken != ''){
                    // The one-time password is valid
                    echo "Valid one-time password!";
                    $_SESSION["loggedinuser"] = $username;
                    $_SESSION["userid"] = $userId;
                    header("Location: index.php");
                    }
                    else
                    {
                    header("Location: user.php");
                    }
                } else {
                    // The one-time password is invalid
                    echo "Invalid one-time password!";
                    header("Location: user.php?error=invalid_2fa_code");
                }
            } else {
                // Handle the case where 2FA is not set for the user
                // For example, you can assume 2FA failed and redirect to a different page
                if($isconfirmed == 1 && $confirmationtoken != ''){
                $_SESSION["loggedinuser"] = $username;
                $_SESSION["userid"] = $userId;
                header("Location: index.php");
                }
                else
                {
                header("Location: user.php");
                }
            }
        }
    } else {

        $token = md5(uniqid(rand(), true)); // Generate a unique token
        // User does not exist, insert the new user row
        $hashedpw = hashPassword($password);
        $insertuserrow = "INSERT IGNORE INTO users (username, password, 2FA, profileimage, confirmation_token) VALUES (?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($insertuserrow);
        $defaultProfileImage = 'profile.png';
        $default2FA = null; // Or set it as an empty string ''
        $stmt2->bind_param('sssss', $username, $hashedpw, $default2FA, $defaultProfileImage, $token);
        if (!$stmt2->execute()) {
            die("Error inserting user: " . $stmt2->error);
        }
        else
        {
            $_SESSION["justcreatedauser"] = true;
            $subject = "Confirm Your Email";
            $message = "Click the following link to confirm your email: https://openlifebook.org/confirm.php?token=$token";
            $headers = "From: basschilperoord@openlifebook.org";

            if (mail($email, $subject, $message, $headers)) {
                echo "Registration successful. Please check your email to confirm your account.";
                header("Location: index.php"); // Redirect to another page
            } else {
                echo "Email sending failed. Please try again later.";
                header("Location: user.php"); 
            }
        }
        $userId = $stmt2->insert_id;
    }
	
	// Check if $_SESSION['justcreatedauser'] is set
	if (isset($_SESSION['justcreatedauser'])) {
		// Data to insert
			$data = [
				["name" => "sidebarcoordinates", "value" => "500, 200", "userid" => $_SESSION['userid']],
				["name" => "widget", "value" => "weather, 1", "userid" => $_SESSION['userid']],
				["name" => "widget", "value" => "rss, 1", "userid" => $_SESSION['userid']]
			];

		// Prepare and execute SQL insert statements
		foreach ($data as $item) {
			$name = $item['name'];
			$value = $item['value'];
			$userId = $item['userid'];

			$sql = "INSERT INTO `settings` (name, Value, userid) VALUES (?, ?, ?)";
			$stmt3 = $conn->prepare($sql);
			$stmt3->bind_param("ssi", $name, $value, $userId);

			if ($stmt3->execute()) {
				echo "Data inserted successfully for name: $name, value: $value, userid: $userId<br>";
			} else {
				echo "Error inserting data: " . $stmt3->error . "<br>";
			}

			$stmt3->close();
		}	
	}
	
    // Close the statement and connection
    $stmt->close();
    $stmt2->close();
    $conn->close();

    // ... (verify 2FA code and handle redirects accordingly)

    // Set session variables and redirect to index.php
    //$_SESSION["loggedinuser"] = $username;
    //$_SESSION["justcreatedauser"] = true;
    //$_SESSION["userid"] = $userId;
    header("Location: index.php");
    exit;
} else {
    // If the form was not submitted, redirect to the index page
    header("Location: index.php");
    exit;
}
?>