<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use  App\Models\Department;

class DepartmentsController extends Controller
{
    public $model        =    'departments';
    public function __construct(Request $request){
        $this->middleware('permission:view_department|create_department|edit_department|delete_department', ['only' => ['index','store']]);
        $this->middleware('permission:create_department', ['only' => ['create','store']]);
        $this->middleware('permission:edit_department', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_department', ['only' => ['destroy']]);

        View()->share('model', $this->model);
        $this->request = $request;
    }
    public function index(Request $request){

        $DB                    =    Department::query();
        $searchVariable      =   array();
        $inputGet         =   $request->all();
        if ($request->all()) {
            $searchData            =    $request->all();
            unset($searchData['display']);
            unset($searchData['_token']);
            if (isset($searchData['order'])) {
                unset($searchData['order']);
            }
            if (isset($searchData['sortBy'])) {
                unset($searchData['sortBy']);
            }
            if (isset($searchData['page'])) {
                unset($searchData['page']);
            }
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('departments.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('departments.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('departments.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("departments.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("departments.is_active", $fieldValue);
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->where("is_deleted", 0);
        $DB->select("departments.*");
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page  =   ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($inputGet)->render();
        return  View("admin.$this->model.index", compact('results', 'searchVariable', 'sortBy', 'order', 'query_string'));
    }

    public function create(){
        return view("admin.$this->model.add");
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|unique:departments,name',
        ]);
        $obj           =  new Department;
        $obj->name     =  $request->name;
        $SavedResponse =      $obj->save();
        if (!$SavedResponse) {
            Session()->flash('error', trans("Something went wrong."));
            return Redirect()->back()->withInput();
        } else {
            Session()->flash('success', Config('constant.DEPARTMENT.DEPARTMENT_TITLE') . " has been added successfully");
            return Redirect()->route('admin-'.$this->model . ".index");
        }
    }

    public function edit($endepid){
        $dep_id = '';
        if (!empty($endepid)) {
            $dep_id = base64_decode($endepid);
            $depDetails   =   Department::find($dep_id);
            return  View("admin.$this->model.edit", compact('depDetails'));
        } else {
            return redirect()->route('admin-'.$this->model . ".index");
        }
    }

    public function update(Request $request, $endepid){
        $dep_id = '';
        if (!empty($endepid)) {
            $dep_id = base64_decode($endepid);
        } else {
            return redirect()->route('admin-'.$this->model . ".index");
        }
        $validated = $request->validate([
            'name' => 'required|unique:departments,name',
        ]);
        $obj           =  Department::find($dep_id);
        $obj->name     =  $request->name;
        $SavedResponse =  $obj->save();
        if (!$SavedResponse) {
            Session()->flash('error', trans("Something went wrong."));
            return Redirect()->back()->withInput();
        }
        Session()->flash('success', Config('constant.DEPARTMENT.DEPARTMENT_TITLE') . " has been updated successfully");
        return Redirect()->route('admin-'.$this->model . ".index");
    }

    public function destroy($endepid){
        $dep_id = '';
        if (!empty($endepid)) {
            $dep_id     = base64_decode($endepid);
        }
        $depDetails     =   Department::find($dep_id);
        if (empty($depDetails)) {
            return Redirect()->route('admin-'.$this->model . '.index');
        }
        if ($dep_id) {
            Department::where('id', $dep_id)->update(array('is_deleted' => 1));
            Session()->flash('flash_notice', trans(Config('constant.DEPARTMENT.DEPARTMENT_TITLE') . " has been removed successfully"));
        }
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0){
        if ($status == 1) {
            $statusMessage   =   trans(Config('constant.DEPARTMENT.DEPARTMENT_TITLE') . " has been deactivated successfully");
        } else {
            $statusMessage   =   trans(Config('constant.DEPARTMENT.DEPARTMENT_TITLE') . " has been activated successfully");
        }
        $user = Department::find($modelId);
        if ($user) {
            $currentStatus = $user->is_active;
            if (isset($currentStatus) && $currentStatus == 0) {
                $NewStatus = 1;
            } else {
                $NewStatus = 0;
            }
            $user->is_active = $NewStatus;
            $ResponseStatus = $user->save();
        }
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }
}
