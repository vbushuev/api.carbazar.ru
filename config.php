<?php
$cbConfig = [
    "CSVReader" =>[
        "path" => "input/",
        "separator" => ";",
        "encoding" => "cp1251"
    ],
    "rucaptcha" => [
        "key" => "ccca532951426427e0afa43b710f683f"
    ],
    "unisend" =>[
        "host" => "https://api.unisender.com/ru/api",
        "api_key" => "67qkxk1xyqe87ekasq37w1ri1ej3hprfuqrf8tna",
        "list_ids" => "10093335"
    ],
    "clientBase_prod" => [
        "key" => "d9db2c21bc7497ab48749655c430995a",
        "host" =>"http://v2.prof-context.ru/api",
        "version" =>"2.0",
        "login" => "admin",
        "reportTable" => "380"
    ],
    "clientBase" => [
        "key" => "615cdb1dd19ef65bcb7c81a97360abc0",
        "host" =>"http://cb.bs2/api",
        "version" =>"1.0",
        "login" => "admin",
        "reportTable" => "280"
    ],
    "db"=>[
        "host" => "127.0.0.1",
        "user" => "carbazar",
        "pass" => "carbazar",
        "schema" => "carbazar",
        "prefix" => "cb_"
    ],
    "ksenmart" => [
        "price"=>[
            "adds"=>"10",
            "type"=>"percent" //absolute,percent
        ],
        "images"=>[
            //"path"=>"../tutmodno/media/com_ksenmart/images/product/original/"
            "path"=>"../tutmodno/media/com_ksenmart/images/products/"
        ]
    ],
    "woocommerce" => [
        "price"=>[
            "rate"=>55,
            "adds"=>"20",
            "type"=>"percent" //absolute,percent
        ],
        "site"=>[
            "url"=>"http://dixipay.bs2"
        ],
        "images"=>[
            //"path"=>"../tutmodno/media/com_ksenmart/images/product/original/"
            "path"=>"../dixipay/wp-content/uploads"
        ]
    ],
    "http_"=>[
        "proxy"=>[
            ["url"=>"127.0.0.1:9150","type"=>"socks4"],
            // ["url"=>"217.126.5.224:8080","type"=>"http"],
            // ["url"=>"151.237.80.166:8080","type"=>"http"],
            // ["url"=>"198.50.219.230:3128","type"=>"http"],
            // ["url"=>"198.50.212.32:8799","type"=>"http"],
            // ["url"=>"144.217.48.75:8080","type"=>"http"],
            // ["url"=>"149.56.147.33:80","type"=>"http"],
            // ["url"=>"89.36.215.70:1189","type"=>"http"]
        ]
    ]
];
?>
