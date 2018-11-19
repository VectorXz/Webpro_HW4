<?php
session_start();

if(isset($_GET["action"]) && $_GET["action"] == "logout") {
    session_destroy();
    header("Location: index.php");
}

function error_box($text) {
    echo '<div class="alert alert-danger" role="alert">'.$text.'</div>';
}

function ok_box($text) {
    echo '<div class="alert alert-success" role="alert">'.$text.'</div>';
}

?>
<DOCTYPE html>
<head>
<title>Upload Image Service | Patiphol Pussawong 6088136</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<style>
body {
    padding-top: 5%;
}

.main-con {
    padding: 10px;
    background-color: #F0F0F0;
    border-radius: 10px;
}

.header-left {
    padding: 2%;
}
</style>
<body>

<div class="container main-con">
    <div class="row">
        <div class="col-sm header-left">
            <h1>File upload system</h1><small>developed by Patiphol Pussawong 6088136</small>
        </div>
        <div class="col-sm text-right">
            Today is <?php echo date("d-m-Y"); ?>
        </div>
    </div>
    <div class="row">
        <hr>
    </div>
    <?php if(isset($_SESSION["username"]) && $_SESSION["username"] != "") { ?>
    <div class="row">
        <div class="col-sm-3">
            <div class="card bg-light mb-3">
                <div class="card-header">User Panel</div>
                <div class="card-body">
                    <h5 class="card-title">Welcome! <?php echo $_SESSION["username"]; ?></h5>
                    <p class="card-text"><a class="btn btn-danger btn-sm" href="index.php?action=logout" role="button">LOGOUT</a></p>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="card bg-light mb-3">
                <div class="card-header">Upload image</div>
                <div class="card-body">
                    <h5 class="card-title">You can upload image below.</h5>
                    <p class="card-text">
                    <?php
                        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "upload") {
                            if(isset($_FILES["file"]) && ($_FILES["file"]["name"][0] != "")) {
                                echo '<pre>';
                                var_dump($_FILES["file"]);
                                echo '</pre>';
                            } else {
                                error_box("Please upload some file!");
                            }
                        }
                    ?>
                        <form action="index.php" method="post" enctype="multipart/form-data">
                            <input type="file" name="file[]" id="file" multiple /><br />
                            <input type="hidden" name="action" value="upload"><br>
                            <input type="submit" name="submit" value="Upload!" />
                        </form>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm">
            Files in system
        </div>
    </div>
    <?php } else { ?>
    <div class="row">
        <div class="col-sm">
            <?php
                if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["action"]) && $_POST["action"] == "login") {
                    if(isset($_POST["username"]) && $_POST["username"] != "") {
                        if(isset($_POST["password"]) && $_POST["password"] != "") {
                            $file = "users.json";
                            $usersfile = fopen($file, "r") or die("Unable to open file!");
                            $contents = fread($usersfile, filesize($file));
                            fclose($usersfile);
                            
                            $users = json_decode($contents, TRUE);

                            $check = in_array($_POST["username"], array_column($users,"username"));
                            if($check) {
                                $index = array_search($_POST["username"], array_column($users,"username"));
                                
                                $password = $users[$index]["password"];

                                if(password_verify($_POST["password"],$password)) {
                                    $_SESSION["username"] = $_POST["username"];
                                    header("Location: index.php");
                                } else {
                                    error_box("Password is invalid!");
                                }
                            } else {
                                error_box("Username not found. Please register first.");
                            }
                        } else {
                            error_box("Please fill in password.");
                        }
                    } else {
                        error_box("Please fill in username.");
                    }
                } else {
                    echo '<div class="alert alert-primary" role="alert">
                    LOGIN
                </div>';
                }
            ?>
            <div class="col-5 mx-auto">
            <form method="post" action="index.php">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" aria-describedby="username" placeholder="Username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                </div>
                <input type="hidden" name="action" value="login">
                <button type="submit" class="btn btn-primary">LOG IN</button>
            </form>
            </div>
        </div>
        <div class="col-sm">
            <?php
                if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["action"]) && $_POST["action"] == "register") {
                    if(isset($_POST["fullname"]) && $_POST["fullname"] != "") {
                        if(isset($_POST["username"]) && $_POST["username"] != "") {
                            if(isset($_POST["password"]) && $_POST["password"] != "") {
                                if(count(explode(" ",$_POST["fullname"])) > 1) {
                                    if(strpos(strtolower($_POST["username"]), "admin") === false) {
                                        if(strlen($_POST["password"]) >= 6) {

                                            //OPEN FILE AND READ ALL DATA KEEP IN $contents
                                            $file = "users.json";
                                            $usersfile = fopen($file, "r") or die("Unable to open file!");
                                            $contents = fread($usersfile, filesize($file));
                                            fclose($usersfile);

                                            //Decode the json from file!
                                            $users = json_decode($contents, TRUE);

                                            //check whether this username is already exists?
                                            $check = in_array($_POST["username"], array_column($users,"username"));
                                            if(!$check) {
                                                //if not exists so open file and write new user.
                                                $usersfile = fopen($file, "w+") or die("Unable to open file!");
                                                $name = explode(" ",$_POST["fullname"]);
                                                $thisuser["name"] = $name[0];
                                                $thisuser["surname"] = $name[1];
                                                $thisuser["username"] = $_POST["username"];
                                                $thisuser["password"] = password_hash($_POST["password"], PASSWORD_DEFAULT);
                                                array_push($users,$thisuser);

                                                $writeToFile = json_encode($users);

                                                $write = fwrite($usersfile, $writeToFile);
                                                fclose($usersfile);

                                                if($write) {
                                                    ok_box("Your account <strong>".$_POST["username"]."</strong> is registered! You can now login!");
                                                } else {
                                                    error_box("There is some problem with the system, please contact admin.");
                                                }

                                            } else {
                                                error_box("Username is already exists!");
                                            }
                                            
                                        } else {
                                            error_box("Please use 6 or more character in your password.");
                                        }
                                    } else {
                                        error_box("This username is not allow to use!");
                                    }
                                } else {
                                    error_box("Please enter your surname too.");
                                }
                            } else {
                                error_box("Please fill in password!");
                            }
                        } else {
                            error_box("Please fill in username!");
                        }
                    } else {
                        error_box("Please fill in your fullname");
                    }
                } else {
            ?>
            <div class="alert alert-success" role="alert">
                REGISTER
            </div>
                <?php } ?>
            <div class="col-5 mx-auto">
            <form method="post" action="index.php">
                <div class="form-group">
                    <label for="fullname">Full name</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" aria-describedby="fullname" placeholder="Name Surname">
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" aria-describedby="username" placeholder="Username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                </div>
                <input type="hidden" name="action" value="register">
                <button type="submit" class="btn btn-success">REGISTER</button>
            </form>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>

</html>