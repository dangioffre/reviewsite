<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use League\CommonMark\CommonMarkConverter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register markdown Blade directive
        Blade::directive('markdown', function ($expression) {
            return "<?php echo app('markdown')->convert($expression)->getContent(); ?>";
        });

        // Register markdown converter as singleton
        $this->app->singleton('markdown', function () {
            return new CommonMarkConverter([
                'html_input' => 'escape',
                'allow_unsafe_links' => false,
            ]);
        });
    }
}
