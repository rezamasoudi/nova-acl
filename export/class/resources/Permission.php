<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;
use Masoudi\NovaAcl\Support\Contracts\ACL;
use Masoudi\NovaAcl\Support\InteractsWithACL;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\PermissionRegistrar;

class Permission extends Resource implements ACL
{
    use InteractsWithACL;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = SpatiePermission::class;


    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return trans('nova-acl::permission.singular_label');
    }

    /**
     * Permissions for abilities
     *
     * @return array
     */
    public static function permissionsForAbilities(): array
    {
        return [
            'all' => 'manage permissions',
            'viewAny' => 'view permissions list',
            'view' => 'view permission',
            'create' => 'create permission',
            'update' => 'update permission',
            'delete' => 'delete permission',
            'attachAnyRole' => 'create permission',
            'attachRole' => 'create permission',
            'detachRole' => 'create permission',
            'attachAnyUser' => 'create permission',
            'attachUser' => 'create permission',
            'detachUser' => 'create permission',
        ];
    }

    /**
     * Get the search result subtitle for the resource.
     *
     * @return string
     */
    public function subtitle()
    {
        return trans($this->name);
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

        $roleResource = Nova::resourceForModel(app(PermissionRegistrar::class)->getRoleClass());

        return [
            ID::make(trans('nova-acl::fields.id'), 'id')->sortable(),

            Text::make(trans('nova-acl::fields.name'), 'name')
                ->rules(['required', 'string', 'max:255'])
                ->creationRules('unique:' . config('permission.table_names.permissions'))
                ->updateRules('unique:' . config('permission.table_names.permissions') . ',name,{{resourceId}}'),

            Select::make(trans('nova-acl::fields.guard'), 'guard_name')
                ->options($guardOptions->toArray())
                ->rules(['required', Rule::in($guardOptions)]),

            BelongsToMany::make($roleResource::label(), 'roles', $roleResource)->searchable(),
        ];
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return trans('nova-acl::permission.label');
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
