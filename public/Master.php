<?php
/*
 * Master page for all pages. Standardizes page format accros the site.
 *
 * PHP version used: 5.5.4
 * SQLite version used: 3.28.0
 *
 * Styling guide: PSR-12: Extended Coding Style
 *     (https://www.php-fig.org/psr/psr-12/)
 *
 * @category  PHPUserManager
 * @package   public
 * @author    Rob Garcia <rgarcia@rgprogramming.com>
 * @copyright 2019-2020 Rob Garcia
 * @license   https://opensource.org/licenses/MIT The MIT License
 * @link      https://github.com/garciart/PHPUserManager
 *
 * IMPORTANT! Sessions must be started by calling page, if necessary.
 */
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <link rel="icon" href="img/favicon.ico" type="image/x-icon">
        <link rel="icon" href="img/logo.png" type="image/png">
        <!-- Bootstrap goes first -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- Content placeholder for head content -->
        <?php echo $contentPlaceHolderHead; ?>
    </head>
    <body>
        <?php
        /* Get the name of the child page. Use this for ternary checks on links to avoid unnecessary calls to the server */
        $childPage = (basename($_SERVER["PHP_SELF"]));
        ?>
        <nav class="navbar navbar-expand-lg navbar-dark static-top" role="navigation" style="background-color: #000000;">
            <div class="container">
                <!-- If the user is already on the page, replace link with URL fragment to avoid unnecessary calls to the server -->
                <a class="navbar-brand" <?php echo ($childPage == "index.php" ? "" : "href=\"index.php\""); ?> title="Home">
                    <img src="img/logo.png" alt="" class="nav_logo">
                </a>
                <a class="navbar-brand d-none d-md-block" <?php echo ($childPage == "index.php" ? "" : "href=\"index.php\""); ?> title="Home">PHP User Manager</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item <?php if ($childPage == "index.php") echo "active"; ?>">
                            <a class="nav-link" <?php echo ($childPage == "index.php" ? "" : "href=\"index.php\""); ?> title="Home">Home
                                <?php if ($childPage == "index.php") echo "<span class=\"sr-only\">(current)</span>"; ?>
                            </a>
                        </li>
                        <li class="nav-item <?php if ($childPage == "About.php") echo "active"; ?>">
                            <a class="nav-link" <?php echo ($childPage == "About.php" ? "" : "href=\"About.php\""); ?> title="About">About
                                <?php if ($childPage == "About.php") echo "<span class=\"sr-only\">(current)</span>"; ?>
                            </a>
                        </li>
                        <?php
                        $logID = "login";
                        $logPage = "../UserManager/LoginPage.php";
                        $logStatus = "Log In";
                        if (isset($_SESSION["Authenticated"])) {
                            if ($_SESSION["Authenticated"] == TRUE) {
                                ?>
                                <li class="nav-item <?php if ($childPage == "MainPage.php") echo "active"; ?>">
                                    <a class="nav-link" <?php echo ($childPage == "MainPage.php" ? "" : "href=\"../UserManager/MainPage.php\""); ?> title="About">User Admin
                                        <?php if ($childPage == "MainPage.php") echo "<span class=\"sr-only\">(current)</span>"; ?>
                                    </a>
                                </li>
                                <?php
                                // Use $logPage and $logStatus to prevent duplication of code
                                $logID = "logout";
                                $logPage = "../UserManager/Logout.php";
                                $logStatus = "Log Out";
                            }
                        }
                        ?>
                        <li class="nav-item <?php if ($childPage == $logPage) echo "active"; ?>">
                            <a class="nav-link" <?php echo ($childPage == $logPage ? "" : "href=\"{$logPage}\" id=\"{$logID}\" title=\"{$logStatus}\""); ?>>
                                <?php echo $logStatus; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://github.com/garciart/PHPUserManager" target="_blank" title="GitHub Repository">GitHub <i class="fab fa-github"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <header class="container header">
            <div class="row">
                <div class="col-md-12 text-center">
                    <!-- Content placeholder for header element content -->
                    <?php echo $contentPlaceHolderHeader; ?>
                </div>
            </div>
        </header>
        <main class="container">
            <div class="row">
                <div class="col-md-12">
                    <!-- Content placeholder for main element content -->
                    <?php echo $contentPlaceHolderMain; ?>
                </div>
            </div>
        </main>
        <footer class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <hr>
                    <p class="my-3 text-muted">Copyright &copy; 2017<span id="currentYear"></span> Rob Garcia. All Rights Reserved.</p>
                    <!-- Content placeholder for main element content -->
                    <?php echo $contentPlaceHolderFooter; ?>
                </div>
            </div>
        </footer>
        <script>
            // Set copyright ending year to current year
            var date = new Date();
            var fullYear = date.getFullYear();
            if (fullYear !== 2017) {
                document.getElementById("currentYear").innerHTML = " - " + fullYear;
            }
        </script>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" crossorigin="anonymous"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript">
            // Using jQuery
            $(document).ready(function () {
                $("a#logout").click(function () {
                    if (confirm("Are you sure you want to log out?")) {
                        return true;
                    }
                    return false;
                });
            });
        </script>
    </body>
</html>