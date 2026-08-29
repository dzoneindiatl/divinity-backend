<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use  App\Models\FooterCategory;
use Illuminate\Support\Facades\Log;

class FooterCategoryController extends Controller
{
    public $model        =    'footer-category';
    public function __construct(Request $request){
        View()->share('model', $this->model);
        $this->request = $request;
    }
    public function index(Request $request){

        $DB                    =    FooterCategory::query();
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
                $DB->whereBetween('footer_categories.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('footer_categories.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('footer_categories.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("footer_categories.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("footer_categories.is_active", $fieldValue);
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->select("footer_categories.*");
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'order_number';
        $order  = ($request->input('order')) ? $request->input('order')   : 'ASC';
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

    public function store(Request $request)
    {
        $validated = $request->validate([
           'name' => 'required|unique:footer_categories,name',
        ]);
        $obj           =  new FooterCategory;
        $obj->name     =  $request->name;
        $obj->is_active     =  !empty($request->is_show) ? $request->is_show : 0;
        $obj->description     =  $request->description;
        $SavedResponse =      $obj->save();
        if (!$SavedResponse) {
            Session()->flash('error', trans("Something went wrong."));
            return Redirect()->back()->withInput();
        } else {
            Session()->flash('success', "Footer category has been added successfully");
            return Redirect()->route('admin-'.$this->model . ".index");
        }
    }

    public function edit($endepid)
    {
        $dep_id = '';
        if (!empty($endepid)) {
            $dep_id = base64_decode($endepid);
            $depDetails   =   FooterCategory::find($dep_id);
            return  View("admin.$this->model.edit", compact('depDetails'));
        } else {
            return redirect()->route('admin-'.$this->model . ".index");
        }
    }

    public function update(Request $request, $endepid)
    {
        $dep_id = '';
        if (!empty($endepid)) {
            $dep_id = base64_decode($endepid);
        } else {
            return redirect()->route('admin-'.$this->model . ".index");
        }
        $validated = $request->validate([
            'name' => 'required|unique:footer_categories,name',
        ]);
        $obj           =  FooterCategory::find($dep_id);
        $obj->name     =  $request->name;
       
        $obj->is_active     =  !empty($request->is_show) ? $request->is_show : 0;
        $obj->description     =  $request->description;
        $SavedResponse =  $obj->save();
        if (!$SavedResponse) {
            Session()->flash('error', trans("Something went wrong."));
            return Redirect()->back()->withInput();
        }
        Session()->flash('success', "Footer category has been updated successfully");
        return Redirect()->route('admin-'.$this->model . ".index");
    }

    public function destroy($endepid){
        $dep_id = '';
        if (!empty($endepid)) {
            $dep_id     = base64_decode($endepid);
        }
        $depDetails     =   FooterCategory::find($dep_id);
        if (empty($depDetails)) {
            return Redirect()->route('admin-'.$this->model . '.index');
        }
        if ($dep_id) {
            FooterCategory::where('id', $dep_id)->update(array('is_deleted' => 1));
            Session()->flash('flash_notice', trans("Footer category has been removed successfully"));
        }
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0){
        if ($status == 1) {
            $statusMessage   =   trans("Footer category has been deactivated successfully");
        } else {
            $statusMessage   =   trans("Footer category has been activated successfully");
        }
        $user = FooterCategory::find($modelId);
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

    public function managePriority(Request $request, $position = null)
    {
        $footerCategories = FooterCategory::where('is_deleted', 0)
            ->orderBy('order_number', 'asc')
            ->get();
            
        return view('admin.footer-category.manage_priority', compact('footerCategories', 'position'));
    }

    public function show(Request $request, $position = null)
    {
        $footerCategories = FooterCategory::where('is_deleted', 0)
            ->orderBy('order_number', 'asc')
            ->get();
            
        return view('admin.footer-category.manage_priority', compact('footerCategories', 'position'));
    }

    public function updatePriority(Request $request)
    {
        try {
            $order = $request->input('order');
            Log::info('Received order: ' . json_encode($order));

            DB::transaction(function () use ($order) {
                FooterCategory::whereIn('id', $order)->update(['order_number' => null]);
                foreach ($order as $index => $categoryId) {
                    $category = FooterCategory::find($categoryId);
                    if ($category) {
                        $category->order_number = $index + 1;
                        $category->save();
                    }
                }
            });

            return response()->json(['status' => 'success', 'message' => 'Priority updated successfully']);
        } catch (\Exception $e) {
            Log::error('Priority Update Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
        }
    }
}
