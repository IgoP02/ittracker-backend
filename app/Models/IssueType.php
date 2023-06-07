<?php

namespace App\Models;

class IssueType extends StaticDataModel
{
    public function issue()
    {
        return $this->hasMany(Issue::class, "issue_type_id", "id");
    }
}
