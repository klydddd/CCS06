<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant";

// Connect to MySQL server (without specifying a database)
$conn = mysqli_connect($servername, $username, $password);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Check if the database exists, if not create it
$result = mysqli_query($conn, "SHOW DATABASES LIKE '$dbname'");
$db_exists = mysqli_num_rows($result) > 0;

if (!$db_exists) {
  // Database does not exist — create it
  $sql = "CREATE DATABASE `$dbname`";
  if (mysqli_query($conn, $sql)) {
    // Select the newly created database
    mysqli_select_db($conn, $dbname);

    // ============================================================
    // ADD YOUR TABLE CREATION QUERIES BELOW
    // ============================================================

    // Products table
    mysqli_query($conn, "CREATE TABLE products (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      price DECIMAL(10,2) NOT NULL,
      imagePath VARCHAR(255) NOT NULL
    )");

    // Menus table
    mysqli_query($conn, "CREATE TABLE menus (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(255) NOT NULL,
      dateCreated DATETIME NOT NULL,
      dateDeleted DATETIME,
      dateUpdated DATETIME
    )");

    // Menu-Products junction table
    mysqli_query($conn, "CREATE TABLE menuproducts (
      id INT AUTO_INCREMENT PRIMARY KEY,
      menu_id INT NOT NULL,
      product_id INT NOT NULL,
      FOREIGN KEY (menu_id) REFERENCES menus(id),
      FOREIGN KEY (product_id) REFERENCES products(id)
    )");

    // ============================================================
    // END OF TABLE CREATION QUERIES
    // ============================================================

  } else {
    die("Error creating database: " . mysqli_error($conn));
  }

  
} else {
  // Database already exists — just select it
  mysqli_select_db($conn, $dbname);

  // Check if the required tables exist, create any that are missing
  $required_tables = ['products', 'menus', 'menuproducts'];

  foreach ($required_tables as $table) {
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($table_check) == 0) {

      switch ($table) {
        case 'products':
          mysqli_query($conn, "CREATE TABLE products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            imagePath VARCHAR(255) NOT NULL
          )");
          break;

        case 'menus':
          mysqli_query($conn, "CREATE TABLE menus (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            dateCreated DATETIME NOT NULL,
            dateDeleted DATETIME,
            dateUpdated DATETIME
          )");
          break;

        case 'menuproducts':
          mysqli_query($conn, "CREATE TABLE menuproducts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            menu_id INT NOT NULL,
            product_id INT NOT NULL,
            FOREIGN KEY (menu_id) REFERENCES menus(id),
            FOREIGN KEY (product_id) REFERENCES products(id)
          )");
          break;
      }
    }
  }
}

// echo "Connected successfully";
?>