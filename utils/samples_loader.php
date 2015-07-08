<?php
require '../../computervision/config/common_require.php';

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;

$filemanager = new Filesystem(new Adapter(__DIR__."/".DIR_SIGNS));

$files = $filemanager->listContents("/samples");

foreach($files as $file){
    //$samples_img[]=DIR_SIGNS.'/samples/'.$sample['basename'];
    echo "Guardando archivo: ".$file['basename']."<br>";
    $sample = new Sample();
    $sample->image = $file['basename'];
    $sample->save();



}