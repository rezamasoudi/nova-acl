<?php

namespace Masoudi\NovaAcl\Support\Contracts;

interface ACLResource
{
    /**
     * Permissions for abilities
     *
     * @return array
     */
    public static function getPermissionsForAbilities(): array;
}