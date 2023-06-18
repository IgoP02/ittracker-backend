<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $reports = Report::join("departments", "departments.id", "=", "reports.department_id")
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

        return $report;
    }

    public function update(Request $request)
    {
        $report = Report::find($request["id"]);
        $report->status = $request->query("status");
        if ($request->query("status") == "A") {
            $report->assignee = $request->user()->name;
        }
        $report->save();
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
            $stats = ["Asignado" => $preStats["A"], "Pendiente" => $preStats["P"], "Solucionado" => $preStats["S"], "Cerrado" => $preStats["C"]];
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
        if ($request->query("days")) {
            Report::where('created_at', '>=', Carbon::now()
                    ->subDays($request->query("days"))->toDateTimeString())
                ->delete();

            return response();
        } else if ($request->query("status") && $request->query("days")) {

            Report::where("status", $request->query("status"))
                ->where('created_at', '>=', Carbon::now()->subDays($request->query("days"))->toDateTimeString())
                ->delete();
        }

    }
}
