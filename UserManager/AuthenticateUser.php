<?php

/**
 * Authenticates credentials and creates user session.
 *
 * PHP version 5.3
 *
 * @author  Rob Garcia <rgarcia@rgprogramming.com>
 * @license https://opensource.org/licenses/MIT The MIT License
 * @version 1.0
 * @link    https://github.com/garciart/PHPUserManager GitHub Repository
 */
session_start();

require_once "User.class.php";
require_once "UMCommonCode.php";
require_once "UserDB.class.php";

// Get the class name
use UserManager\UserDB;
use UserManager\User;

// Get and filter the posted data
$username = filter_input(INPUT_POST, "username");
$password = filter_input(INPUT_POST, "password");
if (!isset($username, $password)) {
    header("Location: LoginPage.php");
    exit();
}

// Connect to the database
$userDB = new UserDB();
$authenticated = $userDB->AuthenticateUser($username, $password);
if ($authenticated) {
    $result = $userDB->getUserByUsername($username);
    $user = new User($result["UserID"], $result["Username"], $result["Nickname"], $result["PasswordHash"], $result["RoleID"], $result["Email"], $result["IsLockedOut"], $result["LastLoginDate"], $result["CreationDate"], $result["Comment"]);
    // Create session
    session_regenerate_id();
    $_SESSION["IsLockedOut"] = $user->getIsLockedOut();
    if ($user->getIsLockedOut()) {
        header("Location: LoginPage.php");
        exit();
    } else {
        $userDB->updateLoginDate($user->getUserID());
    }
    $_SESSION["Authenticated"] = true;
    $_SESSION["UserID"] = $user->getUserID();
    $_SESSION["Username"] = $user->getUsername();
    $_SESSION["Nickname"] = $user->getNickname();
    $_SESSION["RoleID"] = $user->getRoleID();
    header("Location: UMMainPage.php");
    exit();
} else {
    $_SESSION["Authenticated"] = false;
    header("Location: LoginPage.php");
    exit();
}
