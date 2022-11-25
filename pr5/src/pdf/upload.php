<?php

// get details of the uploaded file
//$fileTmpPath = $_FILES['uploadedFile']['tmp_name'];
$fileName = $_FILES['uploadedFile']['name'];
//$fileSize = $_FILES['uploadedFile']['size'];
//$fileType = $_FILES['uploadedFile']['type'];
$fileNameCmps = explode(".", $fileName);
$fileExtension = strtolower(end($fileNameCmps));

$blob = addslashes(file_get_contents($_FILES['uploadedFile']['tmp_name']));
$con = new mysqli("MYSQL", "user", "toor", "appDB");
$con->query("INSERT INTO files (content, author, title, `type`) VALUES ('". $blob . "','" . $_COOKIE["name"] . "','" . $_POST["title"] . "','" . $fileExtension . "');");
//$stmt = $con->prepare("INSERT INTO files (content, author, title, `type`) VALUES (?, ?, ?, ?)");
//$stmt->bind_param('bsss', $blob, $_COOKIE["name"], $_POST["title"], $fileExtension);
//$stmt->execute();
$con->close();
header("Location: index.php");
?>
