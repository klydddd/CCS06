<?php

    $firstName = $_GET['first_name'];
    $lastName = $_GET['last_name'];

    $gender = $_GET['gender'];
    $country = $_GET['country'];

    $colors = $_GET['color'];

    // foreach ($_GET['color'] as $color) {
    //     $colors[] = $color;
    // }

?>
<html>
    <head>
        <style>
        .container {
            width: 50%;
            margin: auto;
            margin-top: 50px;
            padding: 20px;
            border: solid 1px #ccc;
            border-radius: 10px;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        </style>
    </head>
    <div class="container">
<?php
    
    echo "<h3>Hello student: " .$firstName . " " . $lastName . "! </h3>";
    echo "<br>";
    echo "Gender: " . $gender;
    echo "<br>";
    echo "Country: " . $country;
    echo "<br>";
    echo "<br>";
    echo "Selected Colors: ";
    echo "<br>";
    // for ($i = 0; $i < count($colors); $i++) {
    //     echo $colors[$i] . "<br>";
    // }

    foreach ($colors as $color) {
        echo $color . "<br>";
    }
?>
</div>
</html>