<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToTenant as TenantScope;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoxType extends Model
{
    use HasFactory;

    protected $fillable = ['name','description'];

    protected static function booted()
    {
        $scope = new TenantScope();
        static::addGlobalScope($scope);
        TenantScope::bootTenant(new static);
    }

    public function projectBoxes(): HasMany { return $this->hasMany(ProjectBox::class); }
}

