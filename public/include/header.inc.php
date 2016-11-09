<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <title><?=$title?> - Computer Vision tools</title>
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
    <!-- Dropdown Structure -->
    <ul id="dropdown-annotation" class="dropdown-content">
        <li><a href="annotation.php">Annotate</a></li>
        <li class="divider"></li>
        <li><a href="cropper.php">Cropper</a></li>
        <li class="divider"></li>
        <li><a href="classes.php">Classes</a></li>
        <li class="divider"></li>
        <li><a href="results.php">Annotation results</a></li>
    </ul>
    <nav role="navigation">
        <div class="nav-wrapper">
            <a id="logo-container" href="#" class="brand-logo"><?=$title?></a>
            <ul class="right">
                <li><a href="classifier.php"><i class="material-icons left">camera_alt</i>Classifier</a></li>
                <li><a id="dropdown-annotation-button" href="#" data-activates="dropdown-annotation">Annotation<i class="material-icons right">arrow_drop_down</i></a></li>
            </ul>
        </div>
    </nav>
</div>
