<?php
session_start();

function error_box($text) {
    echo '<div class="alert alert-danger" role="alert">'.$text.'</div>';
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
        <div class="col-sm">
            Welcome! <?php echo $_SESSION["username"]; ?>
        </div>
        <div class="col-sm">
            Upload file!
        </div>
    </div>
    <?php } else { ?>
    <div class="row">
        <div class="col-sm">
            <div class="alert alert-primary" role="alert">
                LOGIN
            </div>
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
                                            $file = "users.json";
                                            $usersfile = fopen($file, "w+") or die("Unable to open file!");
                                            if(filesize($file) > 0) {
                                                echo "adding user";
                                                $contents = fread($usersfile,filesize($file));
                                            } else {
                                                $contents = json_encode(array());
                                            }
                                            

                                            $users = json_decode($contents, TRUE);
                                            $name = explode(" ",$_POST["fullname"]);
                                            $thisuser["name"] = $name[0];
                                            $thisuser["surname"] = $name[1];
                                            $thisuser["username"] = $_POST["username"];
                                            $thisuser["password"] = password_hash($_POST["password"], PASSWORD_DEFAULT);
                                            array_push($users,$thisuser);

                                            var_dump($users);

                                            $writeToFile = json_encode($users);

                                            fwrite($usersfile, $writeToFile);
                                            fclose($usersfile);
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