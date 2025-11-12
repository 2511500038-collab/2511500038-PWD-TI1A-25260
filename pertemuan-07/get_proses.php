 <?php
 session_start();
$_SESSION["nama"] = $_POST["txtNama"];
$_SESSION["email"] = $_POST["txtEmail"];
$_SESSION["pesan"] = $_POST["txtPesan"];
echo $_SESSION["nama"] . $_SESSION["email"] . $_SESSION["pesan"];
#header("Location: post.php");
?>   
