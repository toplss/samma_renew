<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!config('app.slow_query_log')) {
            return;
        }
    
        DB::listen(function ($query) {
            $bindings = collect($query->bindings)->map(function ($binding) {
                if ($binding instanceof \DateTimeInterface) {
                    return $binding->format('Y-m-d H:i:s');
                }
        
                if (is_bool($binding)) {
                    return $binding ? 1 : 0;
                }
        
                return $binding;
            })->toArray();
        
            $sql = Str::replaceArray('?', $bindings, $query->sql);
        
            // Log::info($sql);


            $limit = config('app.slow_query_time', 500);
    
            if ($query->time >= $limit) {
                Log::channel('slow')->warning('Slow Query', [
                    'time_ms'  => $query->time,
                    'sql'      => $query->sql,
                    'bindings' => $query->bindings,
                    'url'      => request()->fullUrl(),
                    'ip'       => request()->ip(),
                ]);
            }
        });

        // 디폴트 페이지네이션 선택
        Paginator::defaultView('pagination::default');
    }
}
