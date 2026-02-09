<?php 
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function is_valid_name($name) {
        return preg_match("/^[a-zA-Z-' ]*$/", $name);
    }

    function is_valid_url($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    $nameErr = $emailErr = $genderErr = $websiteErr = $commentErr = "";
    $name = $email = $gender = $comment = $website = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["name"])) {
            $nameErr = "Name is ";
        } else {
            if (!is_valid_name($_POST["name"])) {
                $nameErr = "Only letters and white space allowed";
            } else {
                $name = test_input($_POST["name"]);
            }
        }

        if (empty($_POST["email"])) {
            $emailErr = "Email is ";
        } else {
            if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
            } else {
                $email = test_input($_POST["email"]);
            }
        }
        
        if (empty($_POST["website"])) {
            $website = "";
        } else {
            if(!is_valid_url($_POST["website"])) {
                $websiteErr = "Invalid URL";
            } else {
                $website = test_input($_POST["website"]);
            }
        }
        
        if (empty($_POST["comment"])) {
            $comment = "";
        } else {
            $comment = test_input($_POST["comment"]);
        }
        
        if (empty($_POST["gender"])) {
            $genderErr = "Gender is ";
        } else {
            $gender = test_input($_POST["gender"]);
        }

        if (empty($_POST["website"]) && !is_valid_url($_POST["website"])) {
            $websiteErr = "Invalid URL";
        } else {
            $website = test_input($_POST["website"]);
        }

        if (empty($_POST["comment"]) || strlen($_POST["comment"]) < 10) {
            $commentErr = "Comment must be at least 10 characters long";
        } else {
            $comment = test_input($_POST["comment"]);
        }

        echo "<div class=\"container\">";
        echo "<h2>Your Input:</h2>";
        echo "Name: " . $name . "<br>";
        echo "Email: " . $email . "<br>";
        echo "Website: " . $website . "<br>";
        echo "Comment: " . $comment . "<br>";
        echo "Gender: " . $gender . "<br>";
        echo "</div>";
  
    }



    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 90vh;
            flex-direction: column;
            gap: 30px;
        }

        form {
            background-color: #f0f0f0;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input[type="text"], input[type="email"], textarea {
            width: 95%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button, input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .container {
            height: auto;
            width: 20vw;
            background-color: #f0f0f0;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            /* text-align: center; */
            padding: 20px;
            justify-content: flex-start;
            font-size: 20px;
            position: absolute;
            left: 50px;
            padding-top: 20px;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>PHP Form Validation Example</h1>
    <form method="post" action="form.php">
        
        Name: 
        <span class="error">* <?php echo $nameErr;?></span>
        <input type="text" name="name" > 
        
        <br><br>
        E-mail:
        <span class="error">* <?php echo $emailErr;?></span>
        <input type="text" name="email" >
        <br><br>
        Website:
        <span class="error">* <?php echo $websiteErr;?></span>
        <input type="text" name="website" >
        <br><br>
        Comment: 
        <span class="error">* <?php echo $commentErr;?></span>
        <textarea name="comment" rows="5" cols="40" ></textarea>
        <br><br>
        Gender:
        <input type="radio" name="gender" value="female" >Female
        <input type="radio" name="gender" value="male" >Male
        <input type="radio" name="gender" value="other" >Other
        <span class="error">* <?php echo $genderErr;?></span>
        <br><br>
        <input type="submit" name="submit" value="Submit">
        <input type="submit" value="Reset" onclick="window.location.href='form.php'">

    </form>


    
</body>
</html>