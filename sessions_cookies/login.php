<?php
session_start();
include "cfg/dbconnect.php";
$email = $password = $err_msg = "";
$remember = "";

if (isset($_POST['submit'])) { // if Form is submitted
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    if (isset($_POST['remember'])) { // if remember me is checked
        $remember = $_POST['remember'];
    }
    $password = md5($password);

    // check if emailid and password are same as stored in the database
    $sql = "select * from users where email = '$email' and password = '$password'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);
        $_SESSION['name'] = $row['name'];
        $_SESSION['email'] = $email;
        if (isset($_POST['remember'])) { // if remember me is checked
            $remember = $_POST['remember'];
            setcookie("remember_email", $email, time() + 3600 * 24 * 365);
            setcookie("remember", $remember, time() + 3600 * 24 * 365);
        } else {
            setcookie("remember_email", "", time() - 36000);
            setcookie("remember", "", time() - 3600);
        }
        header("location:index.php");
    } else {
        $err_msg = "Incorrect Email Id/Password";
    }
}
?>
<?php include 'header.php'; ?>
<form class="form-1" action="login.php" method="post">
    <h2>Login Form</h2>
    <?php if ($err_msg != "") { ?>
        <p class="err-msg">
            <?php echo $err_msg;
            $err_msg = ""; ?>
        </p>
    <?php } ?>
    <div class="col-md-12 form-group">
        <label>Email Id</label>
        <input type="text" class="form-control" name="email" id="email" value="<?php if (!empty($email)) {
            echo $email;
        } elseif (isset($_COOKIE['remember_email'])) {
            echo $_COOKIE['remember_email'];
        } ?>" placeholder="Enter your Email Id" required>
    </div>
    <div class="col-md-12 form-group">
        <label>Password</label>
        <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password" required>
    </div>
    <div class="col-md-12 form-group">
        <input type="checkbox" name="remember" class="check" <?php if (!empty($remember)) { ?> checked <?php } elseif (isset($_COOKIE['remember'])) { ?> checked <?php } ?>> Remember Me
    </div>
    <div class="col-md-12 form-group text-right">
        <button type="submit" class="btn btn-primary" name="submit">Login</button>&nbsp;&nbsp;
        <a href="index.php" class="btn btn-danger" name="cancel">Cancel</a>
    </div>
</form>
</body>

</html>