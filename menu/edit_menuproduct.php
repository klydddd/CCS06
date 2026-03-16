<?php
include "db_conn.php";
$id = $_GET["id"];

if (isset($_POST["submit"])) {
    $menu_id = $_POST['menu_id'];
    $product_ids = isset($_POST['product_id']) ? $_POST['product_id'] : [];

    if (empty($menu_id) || empty($product_ids)) {
        echo "<script>alert('Please select a menu and at least one product.');</script>";
    } else {
        $delete_sql = "DELETE FROM `menuproducts` WHERE menu_id = $id";
        mysqli_query($conn, $delete_sql);

        $success = true;
        foreach ($product_ids as $p_id) {
            $insert_sql = "INSERT INTO `menuproducts`(`menu_id`, `product_id`) VALUES ('$menu_id', '$p_id')";
            if (!mysqli_query($conn, $insert_sql)) {
                $success = false;
                echo "Failed: " . mysqli_error($conn);
                break;
            }
        }

        if ($success) {
            header("Location: index.php?msg=Menu product updated successfully");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
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
    <div class="text-center mb-4">
      <h3>Edit Menu Product Information</h3>
      <p class="text-muted">Click update after changing any information</p>
    </div>

    <?php
    $selectedMenuId = $id;
    $selectedProductIds = [];
    $prod_sql = "SELECT product_id FROM `menuproducts` WHERE menu_id = $id";
    $prod_result = mysqli_query($conn, $prod_sql);
    while($row = mysqli_fetch_assoc($prod_result)) {
        $selectedProductIds[] = $row['product_id'];
    }
    ?>

    <div class="container d-flex justify-content-center">
      <form action="" method="post" style="width:50vw; min-width:300px;">
        <div class="mb-3">
          <label class="form-label">Menu:</label>
          <select class="form-select" name="menu_id" required>
            <?php
            $menus_sql = "SELECT * FROM `menus` WHERE `dateDeleted` IS NULL";
            $menus_result = mysqli_query($conn, $menus_sql);
            while ($menu = mysqli_fetch_assoc($menus_result)) {
                $menuName = isset($menu['name']) ? $menu['name'] : $menu['Name'];
                $selected = ($menu['id'] == $selectedMenuId) ? 'selected' : '';
                echo "<option value='".$menu['id']."' $selected>".$menuName."</option>";
            }
            ?>
          </select>
          <br>
          <label class="form-label">Products:</label>
          <div id="products-container">
            <?php
            $products_sql = "SELECT * FROM `products`";
            $products_result = mysqli_query($conn, $products_sql);
            $all_products = [];
            while ($product = mysqli_fetch_assoc($products_result)) {
                $all_products[] = $product;
            }

            if (empty($selectedProductIds)) {
                $selectedProductIds = ['']; // Provide at least one dropdown
            }

            foreach($selectedProductIds as $index => $sel_id) {
                echo '<div class="product-entry d-flex mb-2">';
                echo '<select class="form-select" name="product_id[]" required>';
                echo '<option value="" disabled ' . (empty($sel_id) ? 'selected' : '') . '>Select a product</option>';
                foreach ($all_products as $product) {
                    $productName = isset($product['name']) ? $product['name'] : $product['Name'];
                    $selected = ($product['id'] == $sel_id) ? 'selected' : '';
                    echo "<option value='".$product['id']."' ".$selected.">".$productName."</option>";
                }
                echo '</select>';
                $disabledBtn = (count($selectedProductIds) <= 1) ? 'disabled' : '';
                echo '<button type="button" class="btn btn-danger ms-2 remove-product-btn" '.$disabledBtn.'><i class="fa-solid fa-trash"></i></button>';
                echo '</div>';
            }
            ?>
          </div>
          <div class="text-end mb-3">
              <button type="button" class="btn btn-dark btn-sm" id="add-product-btn"><i class="fa-solid fa-plus"></i> Add Product</button>
          </div>
        </div>

        <div>
          <button type="submit" class="btn btn-success" name="submit">Update</button>
          <a href="index.php" class="btn btn-danger">Cancel</a>
        </div>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
    crossorigin="anonymous"></script>

  <script>
  document.addEventListener("DOMContentLoaded", function() {
      const container = document.getElementById('products-container');
      const addBtn = document.getElementById('add-product-btn');

      addBtn.addEventListener('click', function() {
          const firstEntry = container.querySelector('.product-entry');
          const newEntry = firstEntry.cloneNode(true);
          newEntry.querySelector('select').value = '';
          newEntry.querySelector('.remove-product-btn').disabled = false;
          container.appendChild(newEntry);
          updateRemoveButtons();
          updateProductOptions();
      });

      container.addEventListener('click', function(e) {
          if (e.target.closest('.remove-product-btn')) {
              const entry = e.target.closest('.product-entry');
              if (container.children.length > 1) {
                  entry.remove();
                  updateRemoveButtons();
                  updateProductOptions();
              }
          }
      });

      function updateRemoveButtons() {
          const entries = container.querySelectorAll('.product-entry');
          entries.forEach((entry) => {
              const btn = entry.querySelector('.remove-product-btn');
              btn.disabled = entries.length === 1;
          });
      }

      function updateProductOptions() {
          const selects = Array.from(container.querySelectorAll('select[name="product_id[]"]'));
          const selectedValues = selects.map(select => select.value).filter(val => val !== '');
          
          selects.forEach(select => {
              const options = Array.from(select.options);
              options.forEach(option => {
                  if (option.value === '') return;
                  
                  if (selectedValues.includes(option.value) && select.value !== option.value) {
                      option.style.display = 'none';
                      option.disabled = true;
                  } else {
                      option.style.display = '';
                      option.disabled = false;
                  }
              });
          });
      }

      container.addEventListener('change', function(e) {
          if (e.target.tagName === 'SELECT') {
              updateProductOptions();
          }
      });

      // Initialize on load for existing entries
      updateProductOptions();
  });
  </script>
</body>
</html>
