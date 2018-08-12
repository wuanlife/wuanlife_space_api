<?php

namespace App\Providers;

//use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * 增加内存防止中文分词报错
         */
        ini_set('memory_limit', "256M");
//        调试输出SQL
//        DB::listen(function ($query) {
//            dump($query->sql);
//            dump($query->bindings);
//             $query->time
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
