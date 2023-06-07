<?php

namespace App\Models;

class Issue extends StaticDataModel
{
    public function report()
    {
        return $this->hasMany(Report::class);
    }
    public function issuetype()
    {
        return $this->belongsTo(IssueType::class, "issue_type_id", "id");
    }
}
