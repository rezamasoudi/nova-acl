<?php

namespace App\Nova;

use App\Nova\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;
use Masoudi\NovaAcl\Support\Contracts\ACL;
use Masoudi\NovaAcl\Support\InteractsWithACL;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends Resource implements ACL
{
    use InteractsWithACL;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = SpatieRole::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return trans('nova-acl::role.label');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return trans('nova-acl::role.singular_label');
    }

    /**
     * Permissions for abilities
     *
     * @return array
     */
    public static function permissionsForAbilities(): array
    {
        return [
            'all' => 'manage roles',
            'viewAny' => 'view roles list',
            'view' => 'view role',
            'create' => 'create role',
            'update' => 'update role',
            'delete' => 'delete role',
            'attachAnyUser' => 'create role',
            'attachUser' => 'create role',
            'detachUser' => 'create role',
            'attachAnyPermission' => 'create role',
            'attachPermission' => 'create role',
            'detachPermission' => 'create role',
        ];
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $guardOptions = collect(config('auth.guards'))->mapWithKeys(function ($value, $key) {
            return [$key => $key];
        });

        return [
            ID::make(trans('nova-acl::fields.id'), 'id')->sortable(),

            Text::make(trans('nova-acl::fields.name'), 'name')
                ->rules(['required', 'string', 'max:255'])
                ->creationRules('unique:' . config('permission.table_names.roles'))
                ->updateRules('unique:' . config('permission.table_names.roles') . ',name,{{resourceId}}'),

            Select::make(trans('nova-acl::fields.guard'), 'guard_name')
                ->options($guardOptions->toArray())
                ->rules(['required', Rule::in($guardOptions)]),

            BelongsToMany::make(trans('nova-acl::permission.label'), 'permissions', Permission::class)->withSubtitles()->searchable(),
            MorphToMany::make(trans('nova-acl::fields.users'), 'users', User::class)->searchable(),
        ];
    }

    /**
     * Determine if the current user can replicate the given resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function authorizedToReplicate(Request $request)
    {
        return false;
    }
}
