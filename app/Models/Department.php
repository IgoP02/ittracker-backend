<?php

namespace App\Models;

class Department extends StaticDataModel
{
    public function report()
    {
        return $this->hasMany(Report::class);
    }
}
