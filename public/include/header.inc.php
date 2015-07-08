<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$title?> - Computer Vision tools - Universidad de Sevilla</title>
    <meta charset="UTF-8">
    <meta name=description content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>-->
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
            <ul class="right">
                <li><a href="index.php"><i class="material-icons left">home</i>Overview</a></li>
                <li><a href="classifier.php"><i class="material-icons left">class</i>Classifier</a></li>
                <li><a href="cropper.php"><i class="material-icons left">crop</i>Cropper</a></li>
                <li><a href="classes.php"><i class="material-icons left">layers</i>Classes</a></li>
            </ul>
<!--            <div class="right">
                <form>
                    <div class="input-field">
                        <input id="search" type="search" required>
                        <label for="search"><i class="material-icons">search</i></label>
                    </div>
                </form>
            </div>-->
        </div>
    </nav>
</div>
