<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <title><?=$title?> - Computer Vision tools - Universidad de Sevilla</title>
    <meta charset="UTF-8">
    <meta name=description content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
    <link rel="stylesheet" href="css/cropper.min.css">
    <link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
<div class="navbar-fixed">
    <nav class="light-blue lighten-1" role="navigation">
        <div class="nav-wrapper container">
            <a id="logo-container" href="#" class="brand-logo"><?=$title?></a>
            <a href="#" data-activates="mobile-menu" class="button-collapse"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down">
                <li><a href="classifier.php"><i class="material-icons left">camera_alt</i>Classifier</a></li>
                <li><a href="annotation.php"><i class="material-icons left">class</i>Annotation</a></li>
                <li><a href="cropper.php"><i class="material-icons left">crop</i>Cropper</a></li>
                <li><a href="classes.php"><i class="material-icons left">layers</i>Classes</a></li>
                <li><a href="results.php"><i class="material-icons left">photo_library</i>Results</a></li>
            </ul>
            <ul class="side-nav" id="mobile-menu">
                <li><a href="classifier.php">Classifier</a></li>
                <li><a href="annotation.php">Annotation</a></li>
                <li><a href="cropper.php">Cropper</a></li>
                <li><a href="classes.php">Classes</a></li>
                <li><a href="results.php">Results</a></li>
            </ul>
        </div>
    </nav>
</div>
