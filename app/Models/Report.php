<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        "department_id",
        "issue_id",
        "status",
        "priority",
        "description",
        "assignee",

    ];
    public function getCreatedAtAttribute(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value) => Carbon::parse($value, 'America/Caracas')->diffForHumans(),
        );
    }

    public function department()
    {
        return $this->belongsTo(Department::class, "department_id");
    }
    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}
