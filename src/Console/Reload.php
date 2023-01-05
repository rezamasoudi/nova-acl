<?php

namespace Masoudi\NovaAcl\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use Spatie\Permission\Models\Permission;

class Reload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:reload {--clear : Clear all permissions from database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reload permissions from resources';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->call('cache:clear');

        $clear = $this->option('clear');

        if ($clear) {
            Permission::query()->delete();
            $this->comment('All permissions cleared');
        }

        $this->savePermissionsToDatabase();
        $this->info('Permissions reloaded successfully');

        return Command::SUCCESS;
    }

    /**
     * Store all abilities in database
     */
    protected function savePermissionsToDatabase()
    {
        $files = $this->getAllNovaFiles();

        foreach ($files as $name => $object) {
            // skip dirs
            if (!$object->isFile()) {
                continue;
            }

            $class = $this->convertPathToNamespace($object->getRealPath());

            // get reflection of class
            $reflectionClass = new ReflectionClass($class);

            // check class is instantiable
            if (!$reflectionClass->isInstantiable()) {
                continue;
            }

            try {
                // get resource abilities
                $getPermissionsForAbilitiesMethod = $reflectionClass->getMethod('permissionsForAbilities');
                $abilities = $getPermissionsForAbilitiesMethod->invoke(null);

                // abilities should be array
                if (!is_array($abilities)) {
                    continue;
                }

                // create permission from abilities
                foreach ($abilities as $key => $value) {
                    try {

                        $basePermissions = [
                            'all',
                            'viewAny',
                            'view',
                            'create',
                            'update',
                            'delete',
                        ];

                        if (in_array($key, $basePermissions)) {
                            Permission::where('name', $value)->update(['slug' => $key]);
                        }

                        Permission::create(['name' => $value, 'description' => trans($value), 'slug' => $key]);
                    } catch (Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            } catch (\Throwable $th) {
            }
        }
    }

    /**
     * Get all php class in app/Nova directory
     *
     * @return RecursiveIteratorIterator
     */
    protected function getAllNovaFiles(): RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator(new RecursiveDirectoryIterator(app_path('Nova')), RecursiveIteratorIterator::SELF_FIRST);
    }

    /**
     * Convert php class path to psr-4 namespace
     *
     * @param string $path
     * @return string
     */
    protected function convertPathToNamespace($path): string
    {
        $class = str_replace(app_path(), '', $path);
        $class = str_replace('/', '\\', $class);
        $class = str_replace('.php', '', $class);

        return "\App$class";
    }
}
