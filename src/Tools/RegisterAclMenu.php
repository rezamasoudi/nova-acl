<?php

namespace Masoudi\NovaAcl\Tools;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Tool;

class RegisterAclMenu extends Tool
{

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        return [

            MenuSection::make(trans('Access Panel'), [
                MenuItem::link(trans('Roles'), 'resources/roles'),
                MenuItem::link(trans('Permissions'), 'resources/permissions'),
            ])->icon('key')->collapsable(),

        ];
    }
}