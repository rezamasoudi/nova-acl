<?php

namespace Masoudi\NovaAcl\Support;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;
use Masoudi\NovaAcl\Helpers\Nova;

trait InteractsWithACL
{
    /**
     * Determine if the resource should be available for the given request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public static function authorizedToViewAny(Request $request)
    {
        if (!static::authorizable()) {
            return true;
        }

        return static::hasPermissionsTo($request, 'viewAny');
    }

    /**
     * Determine if the given resource is authorizable.
     *
     * @return bool
     */
    public static function authorizable()
    {
        return true;
    }

    public static function hasPermissionsTo(Request $request, $ability)
    {

        if (Nova::isLoginPath($request)) {
            return true;
        }


        if (isset(static::getPermissionsForAbilities()[$ability])) {
            return $request->user()->can(static::getPermissionsForAbilities()[$ability]);
        }

        if (isset(static::getPermissionsForAbilities()['all'])) {
            return $request->user()->can(static::getPermissionsForAbilities()['all']);
        }

        return false;
    }

    /**
     * Determine if the current user can create new resources.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        return static::hasPermissionsTo($request, 'create');
    }

    /**
     * Determine if the current user can view the given resource or throw an exception.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeToView(Request $request)
    {
        return $this->authorizeTo($request, 'view') && $this->authorizeToViewAny($request);
    }

    /**
     * Determine if the current user has a given ability.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $ability
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeTo(Request $request, $ability)
    {
        throw_unless($this->authorizedTo($request, $ability), AuthorizationException::class);
    }

    /**
     * Determine if the current user can view the given resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $ability
     *
     * @return bool
     */
    public function authorizedTo(Request $request, $ability)
    {
        return static::authorizable() ? static::hasPermissionsTo($request, $ability) : true;
    }

    /**
     * Determine if the resource should be available for the given request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function authorizeToViewAny(Request $request)
    {
        if (!static::authorizable()) {
            return;
        }
        return $this->authorizeTo($request, 'viewAny');
    }

    /**
     * Determine if the user can add / associate models of the given type to the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Model|string $model
     *
     * @return bool
     */
    public function authorizedToAdd(NovaRequest $request, $model)
    {
        if (!static::authorizable()) {
            return true;
        }

        $method = 'add' . class_basename($model);

        return $this->authorizedTo($request, $method);
    }

    /**
     * Determine if the user can attach any models of the given type to the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Model|string $model
     *
     * @return bool
     */
    public function authorizedToAttachAny(NovaRequest $request, $model)
    {
        if (!static::authorizable()) {
            return true;
        }

        $method = 'attachAny' . Str::singular(class_basename($model));

        return $this->authorizedTo($request, $method);
    }

    /**
     * Determine if the user can attach models of the given type to the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Model|string $model
     *
     * @return bool
     */
    public function authorizedToAttach(NovaRequest $request, $model)
    {
        if (!static::authorizable()) {
            return true;
        }

        $method = 'attach' . Str::singular(class_basename($model));

        return $this->authorizedTo($request, $method);
    }

    /**
     * Determine if the user can detach models of the given type to the resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Model|string $model
     * @param string $relationship
     *
     * @return bool
     */
    public function authorizedToDetach(NovaRequest $request, $model, $relationship)
    {
        if (!static::authorizable()) {
            return true;
        }

        $method = 'detach' . Str::singular(class_basename($model));

        return $this->authorizedTo($request, $method);
    }
}