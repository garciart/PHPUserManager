<?php
/**
 * Edit user details page.
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
    $isLockedOut = 0;
    $isActive = 0;
    $userID = $username = $nickname = $password = $roleID = $email = $comment = $securityQuestion = $securityAnswer = "";
    $userIDError = $usernameError = $nicknameError = $passwordError = $roleIDError = $emailError = $isLockedOutError = $commentError = $isActiveError = $securityQuestionError = $securityAnswerError = "";
    /* Start placing content into an output buffer */
    ob_start();
    ?>
    <!-- Head Content Start -->
    <title>Edit User | PHP User Manager</title>
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
            <h2>Edit User Details:&nbsp;</h2>
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
        $userID = filter_input(INPUT_POST, "UserID");
        $username = filter_input(INPUT_POST, "Username");
        $nickname = filter_input(INPUT_POST, "Nickname");
        $password = filter_input(INPUT_POST, "Password");
        $roleID = filter_input(INPUT_POST, "RoleID");
        $email = filter_input(INPUT_POST, "Email");
        $isLockedOut = isset($_POST["IsLockedOut"]) ? 1 : 0;
        $comment = filter_input(INPUT_POST, "Comment");
        $isActive = isset($_POST["IsActive"]) ? 1 : 0;
        $securityQuestion = filter_input(INPUT_POST, "SecurityQuestion");
        $securityAnswer = filter_input(INPUT_POST, "SecurityAnswer");
        
        $valid = true;

        if (validateID($userID) != true) {
            $valid = false;
            $userIDError = "User ID number must be greater than 0.";
        }

        if (validateEmail($username) != true) {
            $valid = false;
            $usernameError = "Invalid email address.";
        }

        if (validateText($nickname) != true) {
            $valid = false;
            $nicknameError = "Nickname must be alphanumeric.";
        }

        if (!empty($password)) {
            if (validatePassword($password) != true) {
                $valid = false;
                $passwordError = "Password must be 8 characters long.";
            } else {
                $_SESSION['PasswordHash'] = getHash($password);
            }
        }

        if (validateID($roleID) != true) {
            $valid = false;
            $$roleIDError = "Role ID number must be greater than 0.";
        }

        if (validateEmail($email) != true) {
            $valid = false;
            $emailError = "Invalid email address.";
        }

        $isLockedOut = $isLockedOut == 1 ? 1 : 0;

        if (!empty($comment)) {
            if (validateText($comment) != true) {
                $valid = false;
                $commentError = "Comments must be alphanumeric.";
            }
        }

        $isActive = $isActive == 1 ? 1 : 0;

        if (validateText($securityQuestion) != true) {
            $valid = false;
            $securityQuestionError = "Security question must be alphanumeric.";
        }

        if (!empty($securityAnswer)) {
            if (validateText($securityAnswer) != true) {
                $valid = false;
                $securityAnswerError = "Security answer must be alphanumeric.";
            } else {
                $_SESSION['SecurityAnswerHash'] = getHash($securityAnswer);
            }
        }
        
        if ($valid == true) {
            $success = $userDB->updateUser($userID, $username, $nickname, $_SESSION['PasswordHash'], $roleID, $email, $isLockedOut, $comment, $isActive, $securityQuestion, $_SESSION['SecurityAnswerHash']);
            unset($_SESSION['PasswordHash']);
            unset($_SESSION['SecurityAnswerHash']);
            if ($success == 1) {
                header("Location: UserAdminPage.php?success=2");
                die();
            } else if ($success == 0) {
                header("Location: UserAdminPage.php?success=-2");
                die();
            } else {
                header("Location: UserAdminPage.php?success=-666");
                die();
            }
        } else {
            $errorAlert = "Format error: Check your data!";
            unset($_SESSION['PasswordHash']);
            unset($_SESSION['SecurityAnswerHash']);
        }
    } else if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == "GET") {
        $result = $userDB->getUserByUserID(cleanText(filter_input(INPUT_GET, "UserID", FILTER_SANITIZE_NUMBER_INT)));
        if (!empty($result)) {
            $userID = $result['UserID'];
            $username = $result['Username'];
            $nickname = $result['Nickname'];
            $_SESSION['PasswordHash'] = $result['PasswordHash'];
            $roleID = $result['RoleID'];
            $email = $result['Email'];
            $isLockedOut = strval($result['IsLockedOut']);
            $comment = $result['Comment'];
            $isActive = strval($result['IsActive']);
            $securityQuestion = $result['SecurityQuestion'];
            $_SESSION['SecurityAnswerHash'] = $result['SecurityAnswerHash'];
        } else {
            header("Location: UserAdminPage.php?success=-2");
        }
    }
    ?>
    <div class="row">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="w-100">
            <div class="table-responsive">
                <table class='table table-bordered table-striped'>
                    <tr>
                        <th>Username:</th>
                        <td class="w-100">
                            <input type="text" name="Username" class="form-control" value="<?php echo $username; ?>" required autofocus oninput="Email.value = Username.value; return true;">
                            <br>
                            <span class="text-danger">
                                <?php echo $usernameError; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Nickname:</th>
                        <td>
                            <input type="text" name="Nickname" class="form-control" value="<?php echo $nickname; ?>" required>
                            <br>
                            <span class="text-danger">
                                <?php echo $nicknameError; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Role:</th>
                        <td>
                            <?php
                            $roleList = $userDB->getAllRoles();
                            if ($userID == $_SESSION['UserID']) {
                                echo "<select name='RoleID' disabled>";
                            } else {
                                echo "<select name='RoleID'>";
                            }
                            foreach ($roleList as $row) {
                                unset($id, $name);
                                $id = $row['RoleID'];
                                $title = $row['Title'];
                                $selected = ($id == $roleID ? "selected" : "");
                                echo "<option value=\"{$id}\" {$selected}>{$title}</option>";
                            }
                            echo "</select>";
                            ?>
                            <br>
                            <span class="text-danger">
                                <?php echo $roleIDError; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>
                            <input type="text" name="Email" class="form-control" value="<?php echo $email; ?>" required readonly>
                            <br>
                            <span class="text-danger">
                                <?php echo $emailError; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Active?</th>
                        <td>
                            <input type="checkbox" name="IsActive"
                            <?php
                            if ($userID == $_SESSION['UserID']) {
                                echo " disabled ";
                            }
                            if ($isActive == 1) {
                                echo "checked='checked'";
                            }
                            ?>
                                   >
                            <br>
                            <span class="text-danger">
                                <?php echo $isActiveError; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Locked Out?</th>
                        <td>
                            <input type="checkbox" name="IsLockedOut"
                            <?php
                            if ($userID == $_SESSION['UserID']) {
                                echo " disabled ";
                            }
                            if ($isLockedOut == 1) {
                                echo "checked='checked'";
                            }
                            ?>
                                   >
                            <br>
                            <span class="text-danger">
                                <?php echo $isLockedOutError; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Comments:</th>
                        <td>
                            <textarea class="editarea w-100" maxlength="512" name="Comment" rows="4"><?php echo $comment; ?></textarea>
                            <br>
                            <span class="text-danger">
                                <?php echo $commentError; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <span class="text-danger">Leave blank to keep the current password.</span>
                        </th>
                    </tr>
                    <tr class='bg-warning'>
                        <th>Password:</th>
                        <td>
                            <input type="password" name="Password" autocomplete="off" />
                            <br>
                            <span class="text-danger">
                                <?php echo $passwordError; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Security Question:</th>
                        <td>
                            <input type="text" name="SecurityQuestion" class="form-control" value="<?php echo $securityQuestion; ?>" required>
                            <br>
                            <span class="text-danger">
                                <?php echo $securityQuestionError; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <span class="text-danger">Leave blank to keep the current security question answer.</span>
                        </th>
                    </tr>
                    <tr class='bg-warning'>
                        <th>Security Question Answer:</th>
                        <td>
                            <input type="password" name="SecurityAnswer" autocomplete="off" />
                            <br>
                            <span class="text-danger">
                                <?php echo $securityAnswerError; ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <input type="hidden" name="UserID" value="<?php echo $userID; ?>"/>
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="UserAdminPage.php" class="btn btn-secondary">Cancel</a>
        </form>
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
