<?php
include "db_conn.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <title>PHP CRUD Application</title>
</head>

<body>
  <nav class="navbar navbar-light justify-content-center fs-3 mb-5" style="background-color: #00ff5573;">
    PHP Complete CRUD Application
  </nav>

  <div class="container">
    <?php
    if (isset($_GET["msg"])) {
      $msg = $_GET["msg"];
      echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
      ' . $msg . '
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }
    ?>
    <a href="add_new_menu.php" class="btn btn-dark mb-3">Add New Menu</a>


    <table class="table table-hover text-center">
      <thead class="table-dark">
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Name</th>
          <th scope="col">Date Created</th>
          <th scope="col">Date Updated</th>
          <th scope="col">Date Deleted</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT * FROM `menus` WHERE `dateDeleted` IS NULL";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
          ?>
          <tr>
            <td><?php echo $row["id"] ?></td>
            <td><?php echo $row["name"] ?></td>
            <td><?php echo $row["dateCreated"] ?></td>
            <td><?php echo $row["dateUpdated"] ?></td>
            <td><?php echo $row["dateDeleted"] ?></td>
            <td>
              <a href="raw.php?id=<?php echo $row["id"] ?>" class="link-dark"><i
                  class="fa-solid fa-pen-to-square fs-5 me-3"></i></a>
              <a href="delete.php?id=<?php echo $row["id"] ?>" class="link-dark"><i
                  class="fa-solid fa-trash fs-5"></i></a>
            </td>
          </tr>
          <?php
        }
        ?>
      </tbody>
    </table>

    <br>
    <br>

    <a href="add_new_products.php" class="btn btn-dark mb-3">Add New Product</a>
    <table class="table table-hover text-center">
      <thead class="table-dark">
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Name</th>
          <th scope="col">Price</th>
          <th scope="col">Image</th>
          <th scope="action">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT * FROM `products`";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
          ?>
          <tr>
            <td>
              <?php echo $row["id"] ?>
            </td>
            <td>
              <?php echo $row["name"] ?>
            </td>
            <td>
              <?php echo $row["price"] ?>
            </td>
            <td>
              <img src="<?php echo $row["imagePath"] ?>" alt="Product Image" width="100" height="100">
            </td>
            <td>
              <a href="edit_product.php?id=<?php echo $row["id"] ?>" class="link-dark"><i
                  class="fa-solid fa-pen-to-square fs-5 me-3"></i></a>
              <a href="delete_product.php?id=<?php echo $row["id"] ?>" class="link-dark"><i
                  class="fa-solid fa-trash fs-5"></i></a>
            </td>
          </tr>
          <?php
        }
        ?>
      </tbody>
    </table>

    <br>
    <br>

    <a href="add_new_menuproducts.php" class="btn btn-dark mb-3">Add New Menu Product</a>
    <table class="table table-hover text-center">
      <thead class="table-dark">
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Menu</th>
          <th scope="col">Products</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT m.id AS menu_id, m.name AS m_name, GROUP_CONCAT(p.name SEPARATOR ', ') AS p_names FROM `menus` m INNER JOIN `menuproducts` mp ON m.id = mp.menu_id INNER JOIN `products` p ON mp.product_id = p.id WHERE m.dateDeleted IS NULL GROUP BY m.id, m.name";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
          ?>
          <tr>
            <td>
              <?php echo $row["menu_id"] ?>
            </td>
            <td>
              <?php echo htmlspecialchars($row["m_name"]) ?>
            </td>
            <td>
              <?php echo htmlspecialchars($row["p_names"]) ?>
            </td>
            <td>
              <a href="edit_menuproduct.php?id=<?php echo $row["menu_id"] ?>" class="link-dark"><i
                  class="fa-solid fa-pen-to-square fs-5 me-3"></i></a>
              <a href="delete_menuproduct.php?id=<?php echo $row["menu_id"] ?>" class="link-dark"><i
                  class="fa-solid fa-trash fs-5"></i></a>
            </td>
          </tr>
          <?php
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
    crossorigin="anonymous"></script>

</body>

</html>