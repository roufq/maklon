<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;

class TenantManager
{
    protected static ?int $tenantId = null;

    public static function setTenant(?Tenant $tenant): void
    {
        self::$tenantId = $tenant?->id;
    }

    public static function setTenantId(?int $tenantId): void
    {
        self::$tenantId = $tenantId;
    }

    public static function getTenantId(): ?int
    {
        return self::$tenantId;
    }
}

