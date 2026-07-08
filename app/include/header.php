<?php
require_once __DIR__ . "/../../app/config/config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tech and Fun is a website dedicated to providing engaging and informative content on programming, networking, education technology, and entertainment. Our mission is to make learning fun and accessible for everyone, whether you're a beginner or an experienced professional. We offer a wide range of articles, tutorials, and resources to help you stay up-to-date with the latest trends and developments in the tech world. Join us on this exciting journey of discovery and growth in the world of technology!">
    <meta name="author" content="Tech and Fun Team">
    <meta name="keywords" content="Education Technology, Programming, Networking, Entertainment, Tech News, Tutorials, Resources, Learning, Fun, Accessible, Trends, Developments">

    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title><?php echo $page_title ?? "Fun & Tech | welcome"; ?></title>
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- custom css -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/style/style.css?v=<?php echo time(); ?>">

    <!-- ionic icons -->
    <script type="module" src="https://unpkg.com/ionicons@7/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7/dist/ionicons/ionicons.js"></script>

    <script src="<?php echo BASE_URL; ?>public/js/main.js" defer></script>
    <script src="<?php echo BASE_URL; ?>public/js/script.js" defer></script>
     <script src="<?php echo BASE_URL; ?>public/js/feedback.js" defer></script>
     <!-- Latest intl-tel-input CSS -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css">

  
</head>

<body>