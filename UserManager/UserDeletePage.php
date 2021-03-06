<?php
/**
 * Delete user page.
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

// Include this file to access common functions and variables
require_once "CommonCode.php";
// Include database class to access database methods
require_once "UserDB.class.php";

// Get the class name. Must be declared in the global scope of the file: see https://www.php.net/manual/en/language.namespaces.importing.php 
use UserManager\UserDB;

// Ensure the user is authenticated and authorized
if (!isset($_SESSION["Authenticated"]) || $_SESSION["Authenticated"] == false || $_SESSION["Authenticated"] == 0) {
    header("Location: LoginPage.php");
    exit();
} else if ($_SESSION["AccessLevel"] >= 1 && $_SESSION["AccessLevel"] <= 10) {
    header("Location: MainPage.php");
    exit();
} else {
    $result = "";
    $errorAlert = "";
    /* Start placing content into an output buffer */
    ob_start();
    ?>
    <!-- Head Content Start -->
    <title>Delete User | PHP User Manager</title>
    <!-- Head Content End -->
    <?php
    /* Store the content of the buffer for later use */
    $contentPlaceHolderHead = ob_get_contents();
    /* Clean out the buffer, but do not destroy the output buffer */
    ob_clean();
    ?>
    <!-- Body Content Start -->
    <!-- Header Element Content -->
    <div class="mt-3 row">
        <div>
            <h2>Delete User:&nbsp;</h2>
            <h2 id="errorAlert" class="text-danger">
                <?php echo $errorAlert; ?>
            </h2>
        </div>
    </div>
    <hr>
    <?php
    /* Store the content of the buffer for later use */
    $contentPlaceHolderHeader = ob_get_contents();
    /* Clean out the buffer, but do not destroy the output buffer */
    ob_clean();
    ?>
    <!-- Main Element Content -->
    <?php
    // Connect to the database
    $userDB = new UserDB();
    /*
     * VALIDATE INPUT BUT DO NOT SANITIZE!
     * Sanitizing may allow incorrect data processing (e.g., 1 = 1 turns to 11.0, etc)
     * Display error if input is not valid
     */
    if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == "POST") {
        $userID = cleanText(filter_input(INPUT_POST, "UserID"));

        $valid = true;

        if (validateID($userID) != true) {
            $valid = false;
            $userIDError = "User ID number must be greater than 0.";
        }

        if ($valid == true) {
            $success = $userDB->deleteUser($userID);
            if ($success == 1) {
                header("Location: UserAdminPage.php?success=3");
                die();
            } else if ($success == 0) {
                header("Location: UserAdminPage.php?success=-3");
                die();
            } else {
                header("Location: UserAdminPage.php?success=-666");
                die();
            }
        } else {
            $errorAlert = "Format error: Check your data!";
            unset($_SESSION['PasswordHash']);
        }
    } else if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == "GET") {
        $userID = cleanText(filter_input(INPUT_GET, "UserID", FILTER_SANITIZE_NUMBER_INT));
        if ($userID == $_SESSION['UserID']) {
            header("Location: UserAdminPage.php?success=-4");
            exit();
        } else {
            $result = $userDB->getUserByUserID($userID);
            if (!empty($result)) {
                $userID = $result['UserID'];
            } else {
                header("Location: UserAdminPage.php?success=-3");
            }
        }
    }
    ?>
    <div class="row">
        <?php
        $result = $userDB->getUserByUserID(cleanText(filter_input(INPUT_GET, "UserID", FILTER_SANITIZE_NUMBER_INT)));
        if (!empty($result)) {
            echo "<div class=\"table-responsive\">";
            echo "<table class='table table-bordered table-striped'>";
            echo "<tr><th>User ID:</th><td style=\"width: 100%;\">{$result['UserID']}</td></tr>";
            echo "<tr><th>User Name:</th><td>{$result['Username']}</td></tr>";
            echo "<tr><th>Nickname:</th><td>{$result['Nickname']}</td></tr>";
            $role = $userDB->getRole($result['RoleID']);
            echo "<tr><th>Role:</th><td>" . $role['Title'] . "</td></tr>";
            echo "<tr><th>Email:</th><td>{$result['Email']}</td></tr>";
            $lockedOut = $result['IsLockedOut'] == 0 ? "No" : "<span class=\"text-danger\"><strong>Yes</strong></span>";
            echo "<tr><th>Locked Out?</th><td>{$lockedOut}</td></tr>";
            echo "<tr><th>Last Login Date:</th><td>{$result['LastLoginDate']}</td></tr>";
            echo "<tr><th>Account Creation Date:</th><td>{$result['CreationDate']}</td></tr>";
            echo "<tr><th>Comments:</th>";
            echo "<td><textarea rows=\"4\" class=\"w-100\">{$result['Comment']}</textarea></td></tr>";
            echo "</table>";
            echo "</div>";
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <p>Are you sure you want to delete this user?</p>
                <input type="hidden" name="UserID" value="<?php echo $userID; ?>"/>
                <input type="submit" value="Yes" class="btn btn-danger">
                <a href="UserAdminPage.php" class="btn btn-secondary">No</a>
            </form>
            <?php
            unset($result);
        } else {
            header("Location: UserAdminPage.php?success=0");
        }
        ?>
    </div>
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