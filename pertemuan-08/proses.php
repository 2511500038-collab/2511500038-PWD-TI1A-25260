<?php
session_start();
 if (isset($_POST['kirim'])) {
  $_SESSION["sesnim"] = $_POST["nim"];
  $_SESSION["sesnama"] = $_POST["nama"];
  $_SESSION["sestempat"] = $_POST["tempat"];
  $_SESSION["sestgllahir"] = $_POST["tanggallahir"];
  $_SESSION["seshobi"] = $_POST["hobi"];
  $_SESSION["sespasangan"] = $_POST["pasangan"];
  $_SESSION["sespekerjaan"] = $_POST["pekerjaan"];
  $_SESSION["sesortu"] = $_POST["ortu"];
  $_SESSION["seskakak"] = $_POST["kakak"];
  $_SESSION["sesadik"] = $_POST["adik"];
  header("Location: index.php");
  }
?>