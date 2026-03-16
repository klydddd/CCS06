<?php include 'header.php'; ?>
<h1> My Home Page </h1>
<?php if (isset($_SESSION['email'])) {
    echo "<h2>Welcome " . $_SESSION['name'] . ". Your email id is " . $_SESSION['email'] . "</h2>"; ?>
    <h3><a href="logout.php">Logout </a></h3>
<?php } else { ?>
    <h3>Click <a href="login.php">here</a> to login</h3>
<?php } ?>
</body>

</html>