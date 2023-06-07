<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
