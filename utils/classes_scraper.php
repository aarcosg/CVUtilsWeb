<?php
require '../../computervision/config/common_require.php';

$sources=[
    ["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_reglamentaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][1]/li[@class='gallerybox']/div/div[@class='gallerytext']"
        ,"image_query" => "//ul[@class='gallery mw-gallery-traditional'][1]/li[@class='gallerybox']/div/div[@class='thumb']/div/a/img/@src"
        ,"subclass" => 1
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_reglamentaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][2]/li[@class='gallerybox']/div/div[@class='gallerytext']"
        ,"image_query" => "//ul[@class='gallery mw-gallery-traditional'][2]/li[@class='gallerybox']/div/div[@class='thumb']/div/a/img/@src"
        ,"subclass" => 2
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_reglamentaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][3]/li[@class='gallerybox']/div/div[@class='gallerytext']"
        ,"image_query" => "//ul[@class='gallery mw-gallery-traditional'][3]/li[@class='gallerybox']/div/div[@class='thumb']/div/a/img/@src"
        ,"subclass" => 2
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_reglamentaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][4]/li[@class='gallerybox']/div/div[@class='gallerytext']"
        ,"image_query" => "//ul[@class='gallery mw-gallery-traditional'][4]/li[@class='gallerybox']/div/div[@class='thumb']/div/a/img/@src"
        ,"subclass" => 2
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_reglamentaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][5]/li[@class='gallerybox']/div/div[@class='gallerytext']"
        ,"image_query" => "//ul[@class='gallery mw-gallery-traditional'][5]/li[@class='gallerybox']/div/div[@class='thumb']/div/a/img/@src"
        ,"subclass" => 4
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_reglamentaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][6]/li[@class='gallerybox']/div/div[@class='gallerytext']"
        ,"image_query" => "//ul[@class='gallery mw-gallery-traditional'][6]/li[@class='gallerybox']/div/div[@class='thumb']/div/a/img/@src"
        ,"subclass" => 6
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Se%C3%B1ales_de_advertencia_de_peligro"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][1]/li[@class='gallerybox']/div/div[@class='gallerytext']"
        ,"image_query" => "//ul[@class='gallery mw-gallery-traditional'][1]/li[@class='gallerybox']/div/div[@class='thumb']/div/a/img/@src"
        ,"subclass" => 3
    ],
    ["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_indicaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//table[@class='wikitable'][1]/tr[position()>1]/td[2]"
        ,"image_query" => "//table[@class='wikitable'][1]/tr/td[1]//a/img/@src"
        ,"subclass" => 5
        ,"is_table" => true
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_indicaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//table[@class='wikitable'][2]/tr[position()>1]/td[2]"
        ,"image_query" => "//table[@class='wikitable'][2]/tr/td[1]//a/img/@src"
        ,"subclass" => 5
        ,"is_table" => true
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_indicaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//table[@class='wikitable'][3]/tr[position()>1]/td[2]"
        ,"image_query" => "//table[@class='wikitable'][3]/tr/td[1]//a/img/@src"
        ,"subclass" => 5
        ,"is_table" => true
    ]


];
foreach($sources as $source){
    $html = file_get_contents($source['url']);
    $dom = new DOMDocument();
    @$dom->loadHtml($html);
    $xpath = new DOMXPath($dom);
    $classes = array();
    $texts = $xpath->query($source['text_query']);
    $images = $xpath->query($source['image_query']);
    for($i=0; $i<$images->length; $i++){
        if(isset($source['is_table']) && $source['is_table']){
            $text = strip_tags(html_entity_decode(scrape_between(getInnerHTML($texts->item($i)),'<b>','<br>')));
        }else{
            $text = strip_tags(html_entity_decode(scrape_between(getInnerHTML($texts->item($i)),'<br>','</center>')));
        }
        $image = "https:".$images->item($i)->nodeValue;
        $classes[$i]['text'] = $text;
        $classes[$i]['image'] = $image;

    }
    saveClasses($classes,$source['subclass']);
}

function saveClasses($classes,$subclass){
    foreach($classes as $class){
        //Download image
        $file_name = uniqid('img_').".png";
        $file_path = "images/signs/classes/".$file_name;
        file_put_contents($file_path, file_get_contents($class['image']));

        //Save class in db
        $image_class = new ImageClass();
        $image_class->name = $class['text'];
        $image_class->image = $file_name;
        $image_class->subclass_id = $subclass;
        $image_class->save();
    }
}


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
