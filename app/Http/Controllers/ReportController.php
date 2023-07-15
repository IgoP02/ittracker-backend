<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Issue;
use App\Models\Report;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $active = $request->boolean("active");
        $pending = $request->boolean("pending");
        $assignee = $request->query("assignee") ? $request->query("assignee") : false;
        $department = $request->query("department") ? $request->query("department") : false;
        $type = $request->query("type");

        $reports = DB::table("reports")->join("departments", "departments.id", "=", "reports.department_id")
            ->join("issues", "issues.id", "=", "reports.issue_id")
            ->join("issue_types", "issue_types.id", "=", "issues.issue_type_id")
            ->select(
                "reports.id",
                "departments.name as department",
                "issue_types.name as type",
                "issues.name as issue",
                "reports.description",
                "reports.status",
                "reports.priority",
                "reports.created_at as date",
                "reports.assignee as assignee"
            )
            ->when($active, function (Builder $query) {
                return $query->where("status", "!=", "C")->where("status", "!=", "S");
            })
            ->when($pending, function (Builder $query) {
                return $query->where("status", "!=", "A");
            })
            ->when($assignee, function (Builder $query, $assignee) {
                return $query->where("assignee", "like", "%$assignee%");
            })
            ->when($department, function (Builder $query, $department) {
                return $query->where("departments.name", "like", "%$department%");
            })
            ->when($type, function (Builder $query, $type) {
                return $query->where("issue_types.name", "like", "%$type%");
            })
            ->latest()->paginate($request["perpage"]);

        return $reports;

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request,
            [
                "department" => "required",
                "issue" => "required",
                "priority" => "required",
                "status" => "required",

            ]);

        $report = Report::create([
            "department_id" => $request["department"],
            "issue_id" => $request["issue"],
            "status" => $request["status"],
            "priority" => $request["priority"],
            "description" => $request["description"] ? $request["description"] : "No provista.",
        ]);

        activity("report_creation")->log("creado reporte #" . $report->id);

        return response($report->id, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Report $report)
    {

        $report = Report::join("departments", "departments.id", "=", "reports.department_id")
            ->join("issues", "issues.id", "=", "reports.issue_id")
            ->join("issue_types", "issue_types.id", "=", "issues.issue_type_id")
            ->select(
                "reports.id",
                "reports.status",
                "departments.name as department",
                "reports.priority",
                "reports.description",
                "issues.name as issue",
                "issue_types.name as type",
                "reports.created_at as date",
                "reports.assignee as assignee"
            )
            ->where("reports.id", $request["id"])->firstOrFail();
        activity("report_query")->log("consulta a reporte #" . $report->id);

        return $report;
    }

    public function getOwnAssignedReports(Request $request, )
    {
        if (!Auth::user()) {
            activity("auth_fail")->log("Intento de acceso no autorizado a cliente " . getHostByName(getHostName()));
            return;
        }
        return Report::join("departments", "departments.id", "=", "reports.department_id")
            ->join("issues", "issues.id", "=", "reports.issue_id")
            ->join("issue_types", "issue_types.id", "=", "issues.issue_type_id")
            ->select(
                "reports.id",
                "reports.status",
                "departments.name as department",
                "reports.priority",
                "reports.description",
                "issues.name as issue",
                "issue_types.name as type",
                "reports.created_at as date",
            )->where("assignee", Auth::user()->name)->where("status", "!=", "C")->where("status", "!=", "S")
            ->latest()
            ->paginate($request->query("perpage"));
    }

    public function update(Request $request)
    {
        $report = Report::find($request["id"]);
        $report->status = $request->query("status");
        if ($request->query("status") == "A") {
            $report->assignee = $request->user()->name;
        }
        if ($request->query("status") == "P") {
            $report->assignee = "NA";
        }
        $report->save();
        activity("report_update")->log("reporte #" . $report->id
            . " actualizado a "
            . $request["status"]
            . " por "
            . $request->user()->name);

        return response(["assignee" => $request->user()->name], $status = 200);
    }

    public function getField(Request $request)
    {

        return DB::table($request["field"])->get();
    }

    public function getIssues(Request $request)
    {
        return Issue::where("issue_type_id", $request["type_id"])->get();
    }

    public function getStats(Request $request)
    {
        $field = $request["field"];

        if ($field == "type") {
            $reports = Report::join("issues", "reports.issue_id", "=", "issues.id")
                ->join("issue_types", "issues.issue_type_id", "=", "issue_types.id")
                ->select("reports.*", "issue_types.name as type")
                ->where("reports.status", "!=", "C")
                ->Where('reports.status', "!=", 'S')
                ->get();
            $stats = $reports->countBy(function ($report) {
                return $report->type;
            })->all();

        } elseif ($field == "department") {
            $reports = Report::join("departments", "reports.department_id", "=", "departments.id")
                ->select("reports.id", "reports.status", "departments.name as department")
                ->where("reports.status", "!=", "C")
                ->Where('reports.status', "!=", 'S')
                ->get();

            $stats = $reports->countBy(function ($report) {
                return $report->department;
            })->all();
        } elseif ($field == "status") {
            $preStats = Report::all()->countBy(function ($report) {
                return $report->status;
            })->all();
            $stats = ["asignado" => $preStats["A"], "pendiente" => $preStats["P"], "solucionado" => $preStats["S"], "cerrado" => $preStats["C"]];
        }
        if ($field == "assignee") {
            $reports = Report::where("reports.status", "!=", "C")
                ->Where('reports.status', "!=", 'S')
                ->get();
            $stats = $reports->countBy(function ($report) {
                return $report->assignee;
            })->all();
        }
        return response()->json($stats);
    }

    public function clear(Request $request)
    {
        if (Auth::user()->username != "admin") {
            activity("auth_fail")->log("Intento de acceso no autorizado a cliente " . getHostByName(getHostName()));
            return response(null, 401);
        }
        $reports = null;
        $logMessage = null;

        if ($request->query("days") && $request->query("days") >= 0) {
            $reports = Report::where('created_at', '<=', Carbon::now()
                    ->subDays($request->query("days"))->toDateTimeString());
            $logMessage = "reportes mayores a " . $request["days"];
        } else if ($request->query("status") && $request->query("days") >= 0) {
            $reports = Report::where("status", $request->query("status"))
                ->where('created_at', '<=', Carbon::now()->subDays($request->query("days"))->toDateTimeString());
            "reportes " . $reports["status"] . " mayores a " . $request["days"];
        }

        $reportCount = $reports->count();
        $reports->delete();
        activity("report_deletion")->log($logMessage . ", " . $reportCount . " total");

        return response()->json($reportCount);

    }
}
