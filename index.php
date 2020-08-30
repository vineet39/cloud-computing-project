<!DOCTYPE html>
<?php
session_start();
session_unset();
ini_set('display_errors',1);
error_reporting(E_ALL);
require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
$bucketName = 'ccptvapp';
$IAM_KEY = 'YOUR IAM KEY HERE';
$IAM_SECRET = 'YOUR SECRET HERE';
$allowed_ext = array('txt', 'csv');
if (isset($_POST["submit"])) {
    $file = $_FILES['filesel']['name'];
    $fileinfo = pathinfo($file);
    if (!in_array($fileinfo['extension'], $allowed_ext)){
        echo '<script type="text/javascript">alert("Invalid file selected")</script>';
    }
else {

try {
    $s3 = S3Client::factory(
    array(
    'credentials' => array(
    'key' => $IAM_KEY,
    'secret' => $IAM_SECRET
    ),
    'version' => 'latest',
    'region'  => 'ap-southeast-2'
        )
    );
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

$keyName = 'key/' . basename($_FILES['filesel']['tmp_name']);
$pathInS3 = 'https://s3.ap-southeast-2.amazonaws.com/' . $bucketName . '/' . $keyName;

try {
    $file = $_FILES['filesel']['tmp_name'];
    $s3->putObject(
        array(
            'Bucket'=>$bucketName,
            'Key' =>  $keyName,
            'SourceFile' => $file,
            'StorageClass' => 'REDUCED_REDUNDANCY'
        )
        );
    $_SESSION['filename'] = $_FILES['filesel']['name'];
    $_SESSION['success'] = true;
    header("Location: map.php");
} catch (S3Exception $e) {
    die('Error:' . $e->getMessage());
} catch (Exception $e) {
    die('Error:' . $e->getMessage());
}
}
}
?>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>GTFS Shapes Mapper</title>
  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
    <div class="container">
      <a class="navbar-brand" href="#">Cloud Computing</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
        </ul>
      </div>
    </div>
  </nav>
  <!-- Page Content -->
  <div class="col-md-5 p-lg-5 mx-auto my-5">
  <div class="container">
  <div class="card">
  <div class="card-body">
    <div class="row">
      <div class="col-lg-12 text-center">
        <h1 class="mt-5">Coordinates Mapping Application</h1>
        <p class="lead">Cloud Computing Semester 2 2019</p>
        <p class="lead">Ryan Cassidy & Vineet Bugtani</p>
        <ul class="list-unstyled">
          <li>Please select a csv coordinates file</li>
          <form action="index.php" id="fileform" method="post"
	enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="filesel" id="filesel" accept=".txt, .csv" /> 
<br />
<input type="submit" name="submit" value="Submit" />
</form>
<script>
document.getElementById("fileform").onsubmit = function () {
    if (!document.getElementById("filesel").value) {
        return false;
    }
}
</script>
        </ul>
      </div>
    </div>
    </div>
    </div>
  </div>
  </div>
  
  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.slim.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
