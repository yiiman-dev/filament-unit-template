<?php

return [

    "api_url" => env('FINNOTECH_API_URL', 'https://apibeta.finnotech.ir'),

    /**
     * شناسه اپ
     */
    "client_id" => env('FINNOTECH_CLIENT_ID', ''),
    /**
     * گذرواژه اپ
     */
    'client_secret' => env('FINNOTECH_CLIENT_SECRET', ''),


    "client_credentials" => [
        /*
         * یک کد ملی که به اپ دسترسی داره
         */
        "nid" => env('FINNOTECH_CLIENT_NID', ''),
        /**
         * اسکوپ هایی که نیاز داریم برای فراخوانی
         */
        "scopes" => explode(',', env("FINNOTECH_CLIENT_SCOPE", "boomrang:token:delete,boomrang:wages:get,card:shahkar:get,credit:back-cheques:get,credit:facility-inquiry:get,health-check:inquiry-health-check:get,kyb:authorized-signatories:get,kyb:corporate-journal:get,kyc:identification-inquiry:get")),
    ]
];
