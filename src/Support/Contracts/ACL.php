<?php

namespace Masoudi\NovaAcl\Support\Contracts;

interface ACL
{
    /**
     * Permissions for abilities
     *
     * @return array
     */
    public static function permissionsForAbilities(): array;
}