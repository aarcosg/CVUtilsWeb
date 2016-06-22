<?php
header('Content-Type: text/html; charset=utf-8');

require '../../computervision/config/common_require.php';
require 'scraper_utils.php';

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
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Señales_de_tráfico_de_peligro_de_España"
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
            $sign_id = trim(strip_tags(html_entity_decode(scrape_between(getInnerHTML($texts->item($i)),'<br>','</b>'))));
            $sign_text = trim(strip_tags(html_entity_decode(scrape_between(getInnerHTML($texts->item($i)),'<b>','<br>'))));
        }else{
            $sign_id = trim(strip_tags(html_entity_decode(scrape_between(getInnerHTML($texts->item($i)),'<b>','<br>'))));
            $sign_text = trim(strip_tags(html_entity_decode(scrape_between(getInnerHTML($texts->item($i)),'<br>','</center>'))));
        }

        $sign_id = str_replace(' ','',$sign_id);
        if(empty($sign_id)){
            $sign_id = NULL;
        }

        $image = "https:".$images->item($i)->nodeValue;
        $classes[$i]['spain_id'] = $sign_id;
        $classes[$i]['text'] = $sign_text;
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
        $image_class->spain_id = $class['spain_id'];
        $image_class->name = $class['text'];
        $image_class->image = $file_name;
        $image_class->subclass_id = $subclass;
        $image_class->save();
    }
}
