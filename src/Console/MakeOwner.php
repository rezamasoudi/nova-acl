<?php

namespace Masoudi\NovaAcl\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class MakeOwner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:owner {identify : unique value of user to make owner} {--field=email : Find user by this field}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create ACL owner';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        DB::beginTransaction();

        try {
            $field = $this->option('field');
            $identify = $this->argument('identify');

            $user = DB::table('users')->where($field, $identify)->first();

            if (!$user) {
                $this->error("User not found.");
                return self::FAILURE;
            }

            $permissions = Permission::where('slug', 'all')->get();

            DB::table(config('permission.table_names.model_has_permissions'))
                ->where('model_type', 'App\Models\User')
                ->where('model_id', $user->id)
                ->delete();

            $permissions->each(function ($permission) use ($user) {
                DB::table(config('permission.table_names.model_has_permissions'))->insert([
                    'permission_id' => $permission->id,
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                ]);
            });

            DB::commit();

            $this->info('User is now ACL owner.');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->error("Operation failed.");
            return self::FAILURE;
        }

        return Command::SUCCESS;
    }
}
