<?php

namespace Atnic\AdminLTE\Console\Commands;

use Illuminate\Console\Command;

/**
 * AdminLTE Make Command
 */
class AdminLTEMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin-lte';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic make:auth with AdminLTE Template';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'auth/login.stub' => 'auth/login.blade.php',
        'auth/register.stub' => 'auth/register.blade.php',
        'auth/passwords/email.stub' => 'auth/passwords/email.blade.php',
        'auth/passwords/reset.stub' => 'auth/passwords/reset.blade.php',
        'layouts/app.stub' => 'layouts/app.blade.php',
        'home.stub' => 'home.blade.php',
    ];

    /**
     * The assets that need to be exported.
     *
     * @var array
     */
    protected $assets = [
        'js/auth.stub' => 'js/auth.js',
        'js/admin-lte.stub' => 'js/admin-lte.js',
        'sass/auth.stub' => 'sass/auth.scss',
        'sass/admin-lte.stub' => 'sass/admin-lte.scss',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Execute make:auth');
        $this->call('make:auth', [ '--force' => true ]);

        $this->info('Start AdminLTE scaffolding');
        $this->info('Copying views...');
        foreach ($this->views as $key => $value) {
            copy(
                __DIR__.'/stubs/make/views/'.$key,
                resource_path('views/'.$value)
            );
        }
        $this->info('Copying assets...');
        foreach ($this->assets as $key => $value) {
            copy(
                __DIR__.'/stubs/make/assets/'.$key,
                resource_path('assets/'.$value)
            );
        }
        $this->info('Copying public...');
        $this->xcopy(__DIR__.'/stubs/make/public', base_path('public'));
        file_put_contents(
            base_path('webpack.mix.js'),
            file_get_contents(__DIR__.'/stubs/make/webpack.mix.stub'),
            FILE_APPEND
        );

        $this->info('AdminLTE scaffolding generated successfully.');
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @param       int      $permissions New folder creation permissions
     * @return      bool     Returns true on success, false on failure
     */
    private function xcopy($source, $dest, $permissions = 0755)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            $this->xcopy("$source/$entry", "$dest/$entry", $permissions);
        }

        // Clean up
        $dir->close();
        return true;
    }
}