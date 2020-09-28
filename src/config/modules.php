<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Priority weight
    |--------------------------------------------------------------------------
    |
    | Default module sort priority value if concrete module isn't provide it.
    | This value is used to sort multiples modules registered in one position. 
    |
    */

    'default_priority' => 0,
    
    /*
    |--------------------------------------------------------------------------
    | Cache time
    |--------------------------------------------------------------------------
    |
    | Default module cache time if concrete module isn't provide it.
    | This value is used to set module cache timeout.
    |
    */

    'default_cache_time' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Groups
    |--------------------------------------------------------------------------
    |
    | You can create modules group which can loaded togeher.
    |
    */

    'groups' => [
        
        // 'web' => [
        //     \App\Modules\HeaderModule::class,
        //     \App\Modules\UserInfoModule::class,
        // ],
    ],

];