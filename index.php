<?php
//start the session
session_start();

//if get action logout will destroy all session and redirect to index.php to update
if(isset($_GET["action"]) && $_GET["action"] == "logout") {
    session_destroy();
    header("Location: index.php");
}

/*
*   OWN CREATED FUNCTION
*/

//just display danger box from bootstrap
function error_box($text) {
    echo '<div class="alert alert-danger" role="alert">'.$text.'</div>';
}

//just display success box from bootstrap
function ok_box($text) {
    echo '<div class="alert alert-success" role="alert">'.$text.'</div>';
}

//check if mime type that send to this function is image mime or not return true or false
function is_image($mime) {
    //create allowed mime array
    $ext = array('image/jpg','image/gif','image/jpeg','image/pjpeg','image/png');
    //check if mime that sent to this function included in array or not ?
    if(in_array($mime, $ext)) {
        //include > return true
        return true;
    } else {
        //not include > return false
        return false;
    }
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

.img-fluid {
    max-width: 200px;
}
</style>
<body>

<div class="container main-con">
    <div class="row">
        <div class="col-sm header-left">
            <h1>Image upload system</h1><small>developed by Patiphol Pussawong 6088136</small>
        </div>
        <div class="col-sm text-right">
            Today is <?php /* display current date */ echo date("d-m-Y"); ?>
        </div>
    </div>
    <div class="row">
        <hr>
    </div>
    <?php
    //check if there are any session for logged in user ? if yes it will show user panel and upload file section
    if(isset($_SESSION["username"]) && $_SESSION["username"] != "") { ?>
    <div class="row">
        <div class="col-sm-3">
            <div class="card bg-light mb-3">
                <div class="card-header">User Panel</div>
                <div class="card-body">
                    <h5 class="card-title">Welcome! <?php /*show logged in username*/ echo $_SESSION["username"]; ?></h5>
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
                        //check if there is any post request with action equal to upload ?
                        if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "upload") {
                            //check if files is not empty
                            if(isset($_FILES["file"]) && ($_FILES["file"]["name"][0] != "")) {
                                //count total files that uploaded
                                $totalFiles = count($_FILES["file"]["name"]);
                                echo "Total files : ".$totalFiles;
                                //loop for each file
                                for($i=0;$i<$totalFiles;$i++) {
                                    //check if there is some error or not ?
                                    if($_FILES["file"]["error"][$i] == 0) {
                                        //check if this file is image or not by using function created from the top./
                                        if(is_image($_FILES["file"]["type"][$i])) {
                                            //check if file size is more than the limit or not
                                            //Allowed up to 2 MB
                                            if($_FILES["file"]["size"][$i] < 2000000) {
                                                //hash the file name
                                                $hash_name = base64_encode($_FILES["file"]["name"][$i]);
                                                //get the extension of file
                                                $ext = pathinfo($_FILES["file"]["name"][$i], PATHINFO_EXTENSION);
                                                //move the upload file to uploads/ folder with new hash name and extension
                                                $move = move_uploaded_file($_FILES["file"]["tmp_name"][$i],"uploads/".$hash_name.".".$ext);
                                                if($move) {
                                                    //if move success show succesfully upload
                                                    ok_box("File ".$_FILES["file"]["name"][$i]." was successfully uploaded!. <a href='uploads/".$hash_name.".".$ext."' target='_blank'>View file</a>");
                                                } else {
                                                    //if not success show error
                                                    error_box("File ".$_FILES["file"]["name"][$i]." failed to upload");
                                                }

                                            } else {
                                                error_box("File ".$_FILES["file"]["name"][$i]." size is over the limit! Please upload file less than 2MB!");
                                            }
                                        } else {
                                            error_box("File ".$_FILES["file"]["name"][$i]." is not an image! Please upload only JPG,JPEG,PNG,GIF ONLY! ");
                                        }
                                    } else {
                                        error_box("Files ".$_FILES["file"]["name"][$i]." is error! CODE:".$_FILES["file"]["error"][$i]);
                                    }
                                }
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
            <h2>Last 5 images in system</h2>
            <?php
            //getting files name lists in folder uploads
            $total = glob('uploads/*.*');

            //loop for each file
            foreach ($total as $f) {
                //each file get the modify time and set it as a key of the file list array
                //we must to concate filename after the modify time in key because we want to convert it as string
                //if we just using key with modify time it will save as int and will getting error if time is too big.
                $list[filemtime($f)."-".$f] = $f;
            }

            //check if there is some file in folder
            if(!empty($total)) { 
                //sort the file list array by key (that we save as a modify time)
                ksort($list);

                //if list is more than 5 just show only 5
                if($list > 5) {
                    $totalimg = 5;
                } else {
                //if less than 5 > show total of it
                    $totalimg = $list;
                }
                //loop for showing image
                for($i=0;$i<$totalimg;$i++) {
                    //get the latest file by using array_pop that will get the last member of array
                    $address = array_pop($list);
                echo '<a href="'.$address.'" target="_blank"><img src="'.$address.'" class="img-fluid" /></a>';
                }
            } else {
                error_box("There are no images in the system!");
            }

            
            ?>
        </div>
    </div>
    <?php } else { ?>
    <div class="row">
        <div class="col-sm">
            <?php
                //check if there any post request with action = login
                if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["action"]) && $_POST["action"] == "login") {
                    //check if username is empty ?
                    if(isset($_POST["username"]) && $_POST["username"] != "") {
                        //check if password is empty ?
                        if(isset($_POST["password"]) && $_POST["password"] != "") {
                            //open file users.json that we use to keep the database of user
                            $file = "users.json";
                            $usersfile = fopen($file, "r") or die("Unable to open file!");
                            //read file and keep in $contents
                            $contents = fread($usersfile, filesize($file));
                            fclose($usersfile);
                            
                            //decode the contents that we keep in json format
                            $users = json_decode($contents, TRUE);

                            //check first if the database contain this user or not by using in_array
                            //we use array_column for get the array that contain only username
                            $check = in_array($_POST["username"], array_column($users,"username"));
                            if($check) {
                                //get the index of that user
                                $index = array_search($_POST["username"], array_column($users,"username"));
                                //use the index to specify the password of that user
                                $password = $users[$index]["password"];
                                //use password_verify to verify password
                                if(password_verify($_POST["password"],$password)) {
                                    //if correct set session to keep username and redirect to index.php
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
                //check if there is any post request with action = register
                if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["action"]) && $_POST["action"] == "register") {
                    //check the input of each field of register is that empty or not
                    if(isset($_POST["fullname"]) && $_POST["fullname"] != "") {
                        if(isset($_POST["username"]) && $_POST["username"] != "") {
                            if(isset($_POST["password"]) && $_POST["password"] != "") {
                                //using explode to check if fullname included surname or not? if include so there must be at least one space " "
                                if(count(explode(" ",$_POST["fullname"])) > 1) {
                                    //using strpos to find that username contains "admin" word or not
                                    //we restrict admin word in username that not allowed to register
                                    if(strpos(strtolower($_POST["username"]), "admin") === false) {
                                        //check if that password contains at least 6 character
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
                                                //explode full name to get name and surname
                                                $name = explode(" ",$_POST["fullname"]);
                                                $thisuser["name"] = $name[0];
                                                $thisuser["surname"] = $name[1];
                                                $thisuser["username"] = $_POST["username"];
                                                //using password_hash to hash password
                                                $thisuser["password"] = password_hash($_POST["password"], PASSWORD_DEFAULT);
                                                //put user to the total database array of users that we decode from json
                                                array_push($users,$thisuser);
                                                
                                                //encode again
                                                $writeToFile = json_encode($users);

                                                //write and close file
                                                $write = fwrite($usersfile, $writeToFile);
                                                fclose($usersfile);

                                                if($write) {
                                                    //if write succesfull so tell the user that you are registered
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