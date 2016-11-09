<?php
header('Content-Type: text/html; charset=utf-8');

require '../../computervision/config/common_require.php';
require 'scraper_utils.php';

$sources=[
    ["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_reglamentaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][1]/li[@class='gallerybox']/div/div[@class='gallerytext']"
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_reglamentaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][2]/li[@class='gallerybox']/div/div[@class='gallerytext']"
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_reglamentaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][3]/li[@class='gallerybox']/div/div[@class='gallerytext']"
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_reglamentaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][4]/li[@class='gallerybox']/div/div[@class='gallerytext']"
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_reglamentaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][5]/li[@class='gallerybox']/div/div[@class='gallerytext']"
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_reglamentaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][6]/li[@class='gallerybox']/div/div[@class='gallerytext']"
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Señales_de_tráfico_de_peligro_de_España"
        ,"text_query" => "//ul[@class='gallery mw-gallery-traditional'][1]/li[@class='gallerybox']/div/div[@class='gallerytext']"
    ],
    ["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_indicaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//table[@class='wikitable'][1]/tr[position()>1]/td[2]"
        ,"is_table" => true
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_indicaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//table[@class='wikitable'][2]/tr[position()>1]/td[2]"
        ,"is_table" => true
    ]
    ,["url" => "https://es.wikipedia.org/wiki/Anexo:Se%C3%B1ales_de_tr%C3%A1fico_de_indicaci%C3%B3n_de_Espa%C3%B1a"
        ,"text_query" => "//table[@class='wikitable'][3]/tr[position()>1]/td[2]"
        ,"is_table" => true
    ]

];

foreach($sources as $source){
    $html = file_get_contents($source['url']);
    $dom = new DOMDocument();
    @$dom->loadHtml($html);
    $xpath = new DOMXPath($dom);
    $texts = $xpath->query($source['text_query']);
    for($i=0; $i< $texts->length; $i++){
        if(isset($source['is_table']) && $source['is_table']){
            $sign_id = trim(strip_tags(html_entity_decode(scrape_between(getInnerHTML($texts->item($i)),'<br>','</b>'))));
            $sign_text = trim(strip_tags(html_entity_decode(scrape_between(getInnerHTML($texts->item($i)),'<b>','<br>'))));
        }else{
            $sign_id = trim(strip_tags(html_entity_decode(scrape_between(getInnerHTML($texts->item($i)),'<b>','<br>'))));
            $sign_text = trim(strip_tags(html_entity_decode(scrape_between(getInnerHTML($texts->item($i)),'<br>','</center>'))));
        }
        echo "Scraped: ".$sign_id." - ".$sign_text."<br>";
        $classes = TrafficSignClass::where('name', 'like', $sign_text.'%')->get();
        echo "<br><b>Matches in db [".$classes->count()."]:</b>";
        echo "<ul>";
        foreach ($classes as $class){
            // Save traffic sign spanish id in db
            $sign_id = str_replace(' ','',$sign_id);
            if(empty($sign_id)){
                $sign_id = NULL;
            }
            $class->spain_id = $sign_id;
            $class->save();
            echo "<li>".$class->name." - id saved in db</li>";
        }
        echo "</ul>";
        echo "---------------<br>";
    }
}
