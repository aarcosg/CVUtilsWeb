<?php

function scrape_between($data, $start, $end){
    $data = stristr($data, $start);
    $data = substr($data, strlen($start));
    $stop = stripos($data, $end);
    $data = substr($data, 0, $stop);
    return $data;
}

function getInnerHTML($node) {
    $doc = new DOMDocument();
    foreach ($node->childNodes as $child)
        $doc->appendChild($doc->importNode($child, true));
    return $doc->saveHTML();
}