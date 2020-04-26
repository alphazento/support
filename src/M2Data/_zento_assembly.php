<?php
return [
    "ZentoSupport_M2Data"=> [
        "version"=> "0.0.1",
        "commands"=> [
            '\ZentoSupport\M2Data\Console\Commands\M2Migrate'
        ],
        "providers"=> [
        ],
        "depends"=>[
            "Zento_Catalog"
        ]
    ]
];