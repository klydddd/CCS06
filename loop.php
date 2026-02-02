<html>

<head>
    <style>
        body, .container{
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 30px;
        }
        
        /* .container {
            height: auto;
            width: 20vw;
            background-color: #f0f0f0;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
            justify-content: flex-start;
            font-size: 100px;
        } */
    </style>
</head>
<body>
    <!--     
<div class="container">
    <?php
    
    for ($i = 0; $i < 5; $i++) {
            echo str_repeat("*", $i + 1) . "<br>";
        }

    for ($i = 5; $i >= 0; $i--) {
            for ($j = 0; $j < $i; $j++) {
                echo "*";
            }
            echo "<br>";
        }

    for ($i = 1; $i <= 5; $i++) {
            // Print spaces
            echo str_repeat("&nbsp;", 5 - $i);
            echo str_repeat("*", 2 * $i - 1) . "<br>";
        }
    ?>
</div> -->
    
    <!-- <div class="container">
        <?php

        // $counter = 1;

        // while ($counter <= 5) {
        //     echo "The counter is: " . $counter . "<br>";
        //     $counter++;
        // }

        // echo "<br>";

        // for ($i = 0; $i < 5; $i++) {
        //     echo "The for loop counter is: " . $i . "<br>";
        // }

        // echo "<br>";
        // $colors = ["red", "orange", "yellow", "green", "blue", "indigo", "violet"];

        // foreach ($colors as $color) {
        //     echo "Color: " . $color . "<br>";
        // }

        $student1 = [
            "id" => 1001,
            "firstname" => "Juan",
            "lastname" => "Dela Cruz"
        ];
        
        $student2 = [
            "id" => 1002,
            "firstname" => "Bren",
            "lastname" => "Da Mage"
        ];

        $students = [$student1, $student2];
        
        // echo "<pre>";
        // var_dump($students);

        // echo "<br>";

        // foreach ($student1 as $key => $value) {
        //     echo "The student " . $key ." is " . $value . "<br>";
        // }

        foreach ($students as $student) {
            foreach ($student as $key => $value) {
                echo "Student " . $key . " is " . $value . "<br>";
            }
           echo "<br>"; 
           }
           
           ?>
    </div> -->
           <?php
               $maxRows = $_GET['maxRows'] ?? 10;
               $maxCols = $_GET['maxCols'] ?? 10;
           ?>

    <div class="container">
        <table border="1" cellspacing="5" cellpadding="5">
        <?php for ($rows = 1; $rows <= $maxRows; $rows++) { ?>
            <tr>
                <?php for ($cols = 1; $cols <= $maxCols; $cols++) { ?>
                    <?php  if ($cols % 2 == 0) { ?>
                        <td>&nbsp</td>
                    <?php } else { ?>
                    <td>*</td>
                    <?php } ?>
                <?php } ?>
            </tr>
            <?php } ?>
        </table>
    </div>

    

    <div class="container">
        <?php 

            echo "<table border='1' cellspacing='5' cellpadding='5'>";
            for ($i = 1; $i <= $maxRows; $i++) {
                echo "<tr>";
                for ($j = 1; $j <= $maxCols; $j++) {
                    if (($i + $j) % 2 == 0) {
                        echo "<td>*</td>";
                    } else {
                        echo "<td>&nbsp;</td>";
                    }
                }
                echo "</tr>";
            }

        ?>
    </div>
    
    <div class="container">
        <?php 

            echo "<table border='1' cellspacing='5' cellpadding='5'>";
            for ($i = 1; $i <= $maxRows; $i++) {
                echo "<tr>";
                for ($j = 1; $j <= $maxCols; $j++) {
                    if ($i == 1 || $i == $maxRows || $j == 1 || $j == $maxCols) {   
                        echo "<td>*</td>";
                    } else {
                        echo "<td>&nbsp;</td>";
                    }
                }
                echo "</tr>";
            }

        ?>
    </div>

    <div class="container">
        <?php 

            echo "<table border='1' cellspacing='5' cellpadding='5'>";
            for ($i = 1; $i <= $maxRows; $i++) {
                echo "<tr>";
                for ($j = 1; $j <= $maxCols; $j++) {
                    if ($i == 1 || $i == $maxRows || $j == 1 || $j == $maxCols) {   
                        echo "<td>&nbsp;</td>";
                    } else {
                        echo "<td>*</td>";
                    }
                }
                echo "</tr>";
            }

        ?>
    </div>

</body>

</html>