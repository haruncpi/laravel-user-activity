<?php

namespace Haruncpi\LaravelUserActivity\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Haruncpi\LaravelUserActivity\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    private $userInstance = "\App\User";

    public function __construct() {
        $userInstance = config('user-activity.model.user');
        if(!empty($userInstance)) $this->userInstance = $userInstance;
    }

    private function handleData(Request $request)
    {
        $this->validate($request, [
            'action'    => 'required|string',
            'user_id'   => 'sometimes|numeric',
            'log_type'  => 'sometimes|string',
            'table'     => 'sometimes|string',
            'from_date' => 'sometimes|date_format:Y-m-d',
            'to_date'   => 'sometimes|date_format:Y-m-d'
        ]);

        $data = Log::with('user')->orderBy('id', 'desc');
        if ($request->has('user_id')) {
            $data = $data->where('user_id', request('user_id'));
        }
        if ($request->has('log_type')) {
            $data = $data->where('log_type', request('log_type'));
        }
        if ($request->has('table')) {
            $data = $data->where('table_name', request('table'));
        }
        if ($request->has('from_date') && $request->has('to_date')) {
            $from = request('from_date') . " 00:00:00";
            $to = request('to_date') . " 23:59:59";
            $data = $data->whereBetween('log_date', [$from, $to]);
        }

        return $data->paginate(10);
    }

    private function handleCurrentData(Request $request)
    {
        $this->validate($request, [
            'table'  => 'required|string',
            'id'     => 'required|numeric',
            'log_id' => 'required|numeric'
        ]);

        $table = request('table');
        $id = request('id');
        $logId = request('log_id');
        $currentData = DB::table($table)->find($id);
        if ($currentData) {
            $editHistory = Log::with('user')
                ->orderBy('log_date', 'desc')
                ->whereNotIn('id', [$logId])
                ->where(['table_name' => $table, 'log_type' => 'edit'])
                ->whereRaw('data like ?', array('%"id":"' . $id . '"%'))->get();
            return ['current_data' => $currentData, 'edit_history' => $editHistory];
        }
        return [];
    }

    private function handleUserAutocomplete(Request $request)
    {
        $this->validate($request, [
            'user' => 'required|string|max:50'
        ]);

        $user = request('user');
        return $this->userInstance::select('id', 'name', 'email')
            ->where('name', 'like', '%' . $user . '%')
            ->orWhere('id', $user)
            ->limit(10)->get();
    }

    public function getIndex(Request $request)
    {

        if ($request->has('action')) {
            $action = $request->get('action');
            switch ($action) {
                case 'data':
                    return response()->json($this->handleData($request));
                    break;

                case 'current_data':
                    return response()->json($this->handleCurrentData($request));
                    break;

                case 'user_autocomplete':
                    return response()->json($this->handleUserAutocomplete($request));
                    break;
            }
        }

        $connection = config('database.default');
        $driver = DB::connection($connection)->getDriverName();
        switch ($driver) {
            case 'pgsql':
                $sql = sprintf(
                    "SELECT table_name FROM information_schema.tables where table_schema = '%s' ORDER BY table_schema,table_name;",
                    DB::connection($connection)->getConfig('schema') ?: 'public'
                );
                $all = array_map('current', DB::select($sql));
                break;
            default:
                $all = array_map('current', DB::select('SHOW TABLES'));
        }
        $exclude = ['failed_jobs', 'password_resets', 'migrations', 'logs'];
        $tables = array_diff($all, $exclude);

        return view('LaravelUserActivity::index', ['tables' => $tables]);


    }

    public function handlePostRequest(Request $request)
    {
        if ($request->has('action')) {
            $action = $request->get('action');
            switch ($action) {
                case 'delete':
                    $dayLimit = config('user-activity.delete_limit');
                    Log::whereRaw('log_date < NOW() - INTERVAL ? DAY', [$dayLimit])->delete();
                    return ['success' => true, 'message' => "Successfully deleted log data older than $dayLimit days"];
                    break;
            }
        }
    }
}