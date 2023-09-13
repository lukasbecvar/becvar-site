<main class="register-page">
    <?php // admin account register (for new site setup)

        // check if register submited
        if (isset($_POST["submitRegister"])) {
            
            // check if user table is empty
            if (!$user_manager->is_users_empty()) {
                echo "<br><h2 class=page-title>This feature can be used only to create an admin account</h2>";
            } else {
               
                // get data from post and escapeit
                $username = $escape_utils->special_chars_strip($_POST["username"]);
                $password = $escape_utils->special_chars_strip($_POST["password"]);
                $re_rassword = $escape_utils->special_chars_strip($_POST["repassword"]);
                
                // check if values not empty
                if (empty($username) or empty($password) or empty($re_rassword)) {
                    $alert_manager->flash_error("You must add all values in form!");
                } else {
                    
                    // check password minimal length
                    if (strlen($password) < 5) {
                        $alert_manager->flash_error("Password must have more than 5 characters");
                    // check password match
                    } elseif ($password != $re_rassword) {
                        $alert_manager->flash_error("Passwords do not match");
                    } else {
                        if ($user_manager->is_users_empty()) {

                            // init basic values
                            $role = "Owner";
                            $image_base64 = "/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBw4RDQ0OEA0QDhANDQ0NDw4NDhsNDg0OFREWFxcTFRUYICggGBolGxMTITEhJSkrLi4uFx8zODMsNygtLisBCgoKDQ0NDg0NDisZFRkrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrKysrK//AABEIAOYA2wMBIgACEQEDEQH/xAAaAAEAAwEBAQAAAAAAAAAAAAAAAQQFAwIH/8QAMhABAQABAQYEBAQGAwAAAAAAAAECEQMEEiFRkSIxQWEFcYGhQnKxwSMyUoLh8DNi0f/EABUBAQEAAAAAAAAAAAAAAAAAAAAB/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8A+qAKgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIebtMf6p3B7HObXH+qd49ygkQkAAAAAAAAAAAAAAAAAAAEWgjLKSa26SKe232/hn1v/jhvG3uV9vSfu5A9Z7TK+eVv1eNEiiNHrHKzytnyqAFnZb5lPPxT7r2y2kyxlmul6shY3Ta2Zaa8ulvJBpCEgAAAAAAAAAAAAAAAAK2/bSTCzXnfT10WMrpLb6c/oyNpncsrlfX7QHkBQAAAAdN2kueOt05uYDZSr7nteLDn5zlVhAAAAAAAAAAAAAAAABX37LTC+9mP+9mau/EbywnvapAAKAAAAAALPw/LxWdcf0aLL3O/wATH31n2aiAAAAAAAAAAAAAAADjvW14cdZ53lAVfiF8WP5f3VXrabS5XW3V5UAAAAAAAAdN3v8AEw/NGqxpdLrPTmv7nvFytmXPSayoLYAAAAAAAAAAAAACp8Qnhntl+y28bXCZY2X1BkD1tMLjdLNHlQAAAAAAAAWdwnjvtjVaRpbnseHHn53z9vZB3SAAAAAAAAAAAAACEgK2/wD/AB/3Ys5o7/PB/dGcAAoAAAAAAtfD74svy/u0FD4dj4sr6Sad19BCQAAAAAAAAAAAAAABz281wyn/AFrJbNjHzx0tnS6AgBQAAAAAkBf+Hzw29clpz3fDhwxl8/V1QAAAAAAAAAAAAAAAAFLf9l5ZSeXnp0XUWAxha2+52S2XWTW6XlZFVQAAAAWNy2VuUvpOf1eNhsLneknnWls8JjJJ5T7+6D0kAAAAAAAAAAAAAAQCRFrxdrjPxTuDoOGW94T8Wvyjllv2Ppjb9gd95vgy+TKd9tvWWUs0klcFAAAAF74deWU95+i4ydhtrjrppz6rOO/T1x7VBdFeb5h1s+ce8dvhfxQHUeZlOsv1egAAAAAAAAAU983jTwzz9b09gdNvvWOPL+a9J6fNT2m9Z3109pycQC29UaJFAAAAAAAAAAAAB0w2+c8sr8rzjmAvbHfZeWU0955f4W5WMsbrvHDdL/Lfsg0hCQAAAAc9vtOHG325fNk2+t875rvxDK+HGS9byU+G9L2BAnhvS9jhvS9lECeG9L2OG9L2BAnhvS9jhvS9gQJ4b0vY4b0vYECeG9L2OG9L2BAnhvS9jhvS9gQJ4b0vY4b0vYECeG9L2OG9L2BAnhvS9jhvS9gQJ4b0vY4b0vYF/cNrrjcb54/otMzdLcc5yvPleXVpoAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP/9k=";
                          
                            // hash password to save in database
                            $password = $hash_utils->gen_main_hash($password);
                          
                            // generate user identify token
                            $token = $string_utils->gen_random_sring(40);

                            // get user ip
                            $remote_addr = $main_utils->get_remote_adress();

                            // insert user account to database
                            $mysql->insert("INSERT INTO `users`(`username`, `password`, `role`, `image_base64`, `remote_addr`, `token`) VALUES ( '$username', '$password', '$role', '$image_base64', '$remote_addr', '$token')");   

                            // redirect to login page
                            $url_utils->redirect("?admin=login");
                       
                        } else {
                            echo "<br><h2 class=page-title>This feature can be used only to create an admin account</h2>";
                        }
                    }
                }
            }
        }

        // check if users table is realy empty
        if (!$user_manager->is_users_empty()) {
            echo "<br><h2 class=page-title>This feature can be used only to create an admin account</h2>";
        } else {
            include($_SERVER['DOCUMENT_ROOT'].'/../site/admin/elements/forms/RegisterForm.php');
        }
    ?>
</main>