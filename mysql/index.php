<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, price, imagepath  FROM Products";
// Execute the SQL query
$result = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        body {
            display: flex;
            gap: 40px;
        }

        main {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content:center;
            gap: 10px;
        }


    </style>
</head>
<body>
    <main>
        
    <?php

        $i = 1;
        // Process the result set
        if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            $image = $row["imagepath"];
            echo "<div class=\"container\">";
            echo "<img src=\"$image\"><br>";
            echo "<div class=\"texts\"> id: " . $row["id"]. "<br> Name: ". $row["name"]. "<br> Price: ". $row["price"] . "</div></div>";
        }
        } else {
        echo "0 results";
        }

        $conn->close();
    ?>
    
    </main>
</body>
</html>