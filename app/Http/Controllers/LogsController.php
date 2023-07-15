<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class LogsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query("type");
        $description = $request->query("description");
        $date = $request->query("date");

        if (!Auth::user()) {
            activity("auth_fail")->log("Intento de acceso no autorizado a cliente " . getHostByName(getHostName()));

            abort(401);
        }
        return DB::table("activity_log")->select("id", "log_name", "description", "updated_at")
            ->when($description, function (Builder $query, $description) {
                return $query->where("description", "like", "%$description%");
            })->when($type, function (Builder $query, $type) {
            return $query->where("log_name", $type);
        })->latest()->paginate($request->query("perpage"));
    }

    public function clear()
    {
        if (!Auth::user() || Auth::user()->username != "admin") {
            activity("auth_fail")->log("Intento de acceso no autorizado a cliente " . request()->ip());
            abort(401);
        }
        Activity::truncate();
        return;
    }
}
