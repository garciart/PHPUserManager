<?php
/**
 * Landing page for user administration.
 *
 * PHP version used: 5.5.4
 * SQLite version used: 3.28.0
 *
 * Styling guide: PSR-12: Extended Coding Style
 *     (https://www.php-fig.org/psr/psr-12/)
 *
 * @category  PHPUserManager
 * @package   UserManager
 * @author    Rob Garcia <rgarcia@rgprogramming.com>
 * @copyright 2019-2020 Rob Garcia
 * @license   https://opensource.org/licenses/MIT The MIT License
 * @link      https://github.com/garciart/PHPUserManager
 */
/* Check if a session is already active */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once "CommonCode.php";
require_once "User.class.php";
require_once "UserDB.class.php";

if (!isset($_SESSION["Authenticated"]) || $_SESSION["Authenticated"] == false || $_SESSION["Authenticated"] == 0) {
    header("Location: LoginPage.php");
    exit();
} else if ($_SESSION["AccessLevel"] >= 1 && $_SESSION["AccessLevel"] <= 5) {
    header("Location: MainPage.php");
    exit();
} else {
    /* Start placing content into an output buffer */
    ob_start();
    ?>
    <!-- Head Content Start -->
    <title>Error Log | PHP User Manager</title>
    <!-- Head Content End -->
    <?php
    /* Store the content of the buffer for later use */
    $contentPlaceHolderHead = ob_get_contents();
    /* Clean out the buffer, but do not destroy the output buffer */
    ob_clean();
    ?>
    <!-- Body Content Start -->
    <!-- Header Element Content -->
    <h1 class="mt-3">PHP User Manager</h1>
    <p class="lead">Error Log</p>
    <hr>
    <?php
    /* Store the content of the buffer for later use */
    $contentPlaceHolderHeader = ob_get_contents();
    /* Clean out the buffer, but do not destroy the output buffer */
    ob_clean();
    ?>
    <a href="MainPage.php" class="btn btn-primary pull-left">Return to Main Administration Page</a>
    <hr>
    <!-- Main Element Content -->
    <?php
    $file = "ErrorLog.txt";
    if (!file_exists($file)) {
        $f = fopen($file, "w") or exit("Unable to create error log!");
        fwrite($f, "[" . date("d-M-Y H:i:s e") . "] Error log initialized.\n");
        fclose($f);
    }
    $f = fopen($file, "r") or exit("Unable to open error log!");
    while (!feof($f)) {
        echo "<span class=\"text-monospace\">" . fgets($f) . "</span><br>";
    }
    fclose($f);
    ?>
    <hr>
    <a href="MainPage.php" class="btn btn-primary pull-left">Return to Main Administration Page</a>
    <?php
    /* Store the content of the buffer for later use */
    $contentPlaceHolderMain = ob_get_contents();
    /* Clean out the buffer once again, but do not destroy the output buffer */
    ob_clean();
    ?>
    <!-- Footer Element Content -->

    <!-- Body Content End -->
    <?php
    /* Store the content of the buffer for later use */
    $contentPlaceHolderFooter = ob_get_contents();
    /* Clean out the buffer and turn off output buffering */
    ob_end_clean();
    /* Call the master page. It will echo the content of the placeholders in the designated locations */
    require_once "MasterPage.php";
}