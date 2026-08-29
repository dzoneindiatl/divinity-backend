<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\FooterSubcategory;
use App\Models\FooterCategory;
use Illuminate\Support\Str;
use File;

class FooterSubCategoryController extends Controller
{
    public $model =    'footer-subcategory';
    public function __construct(Request $request)
    {   
        
        View()->share('model', $this->model);
        $this->request = $request;
    }

    public function index(Request $request, $endesid = null)
    {
        if (!empty($endesid)) {
            $dep_id = base64_decode($endesid);
        }
        $departmentDetails  =  FooterCategory::where('footer_categories.id', $dep_id)->first();
        if (empty($dep_id)) {
            return Redirect()->back();
        }
        $DB                    =    FooterSubcategory::query();
        $searchVariable        =    array();
        $inputGet            =    $request->all();
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
                $DB->whereBetween('footer_subcategories.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('footer_subcategories.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('footer_subcategories.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("footer_subcategories.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("footer_subcategories.is_active", $fieldValue);
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->where("is_deleted", 0);
        $DB->where("footer_category_id", $dep_id);
        $DB->select("footer_subcategories.*");
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'order_number';
        $order  = ($request->input('order')) ? $request->input('order')   : 'ASC';
        $records_per_page    =    ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $totalResults = $DB->orderBy($sortBy, $order)->count();
        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($inputGet)->render();
        return  View("admin.$this->model.index", compact('results','totalResults', 'searchVariable', 'sortBy', 'order', 'query_string', 'dep_id'));
    }

    public function add(Request $request, $endesid = null) {   
        if (!empty($endesid)) {
            $dep_id = base64_decode($endesid);
        }
        if (empty($endesid)) {
            return Redirect()->back();
        }
        $formData = $request->all();
        $departmentDetails  =  FooterCategory::where('id', $dep_id)->first();
        if ($request->isMethod('POST')) {
            $rules = [
                'type' => 'required'
            ];
            if($request->type == 'page'){
                $rules = [
                    'title' => 'required',
                    'description' => 'required',
                    // 'slug' => 'required',
                    //'meta_description' => 'required',
                    //'meta_key' => 'required',
                    //'meta_url' => 'required',
                    //'meta_title' => 'required',
                    //'order_number' => 'required',
                ];
                if($request->banner_type == 'video'){
                    $rule = [
                        'banner_media' => 'required|mimes:mp4,wmv|max:2048'
                    ];
                    array_push($rules,$rule);
                }
                if($request->banner_type == 'image'){
                    $rule = [
                        'banner_media' => 'required|mimes:jpg,jpeg,png|max:2048'
                    ];
                    array_push($rules,$rule);
                }
            } elseif($request->type == 'url') {
                $rules = [
                    'title' => 'required',
                    //'slug' => 'required',
                    'url' => 'required|url',
                    //'order_number' => 'required',
                ];
            }
            $validated = $request->validate($rules);
            $obj = new FooterSubcategory;
            $obj->footer_category_id = $dep_id;
            $obj->type = $request->type;
            $obj->title = $request->title;
            $obj->slug = Str::slug($request->title);
            $obj->description = $request->description;
            $obj->meta_description = $request->meta_description;
            $obj->meta_key = $request->meta_key;
            $obj->meta_title = $request->meta_title;
            $obj->meta_url = $request->meta_url;
            $obj->banner_type = $request->banner_type;
            $obj->url = $request->url;
            $obj->order_number = $request->order_number;
            if ($request->hasFile('banner_media')) {
                $extension = $request->file('banner_media')->getClientOriginalExtension();
                $originalName = $request->file('banner_media')->getClientOriginalName();
                $fileName = time() . '-'.$request->media_type.'.' . $extension;

                $folderName = "cms-banner/".strtoupper(date('M') . date('Y')) . "/";
                $folderPath = Config('constant.BANNER_IMAGE_ROOT_PATH') . $folderName;
                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, $mode = 0777, true);
                }
                if ($request->file('banner_media')->move($folderPath, $fileName)) {
                    $obj->banner_media = $folderName . $fileName;
                }
            }
            $SavedResponse = $obj->save();
            
            if (!$SavedResponse) {
                Session()->flash('error', trans("Something went wrong."));
                return Redirect()->back()->withInput();
            } else {
                Session()->flash('success', "Footer subcategory has been added successfully");
                return Redirect()->route('admin-'.$this->model . ".index", $endesid);
            }
        }
        $action = "add";
        return  View("admin.$this->model.add", compact('dep_id','departmentDetails','action'));
    }

    public function update(Request $request, $endesid = null) {
        $dep_id = '';
        if (!empty($endesid)) {
            $dep_id = base64_decode($endesid);
        }
        $modell =    FooterSubcategory::where('footer_subcategories.id', $dep_id)->first();
        if (empty($modell)) {
            return Redirect()->back();
        }
        $departmentDetails  =  FooterCategory::where('id', $modell->footer_category_id)->first();
        if ($request->isMethod('POST')) {
            $rules = [
                'type' => 'required'
            ];
            if($request->type == 'page'){
                $rules = [
                    'title' => 'required',
                    'description' => 'required',
                   // 'slug' => 'required',
                    //'meta_description' => 'required',
                    //'meta_key' => 'required',
                    //'meta_url' => 'required',
                    //'meta_title' => 'required',
                    //'order_number' => 'required',
                ];
                if($request->banner_type == 'video'){
                    $rule = [
                        'banner_media' => 'required|mimes:mp4,wmv|max:2048'
                    ];
                    array_push($rules,$rule);
                }
                if($request->banner_type == 'image'){
                    $rule = [
                        'banner_media' => 'required|mimes:jpg,jpeg,png|max:2048'
                    ];
                    array_push($rules,$rule);
                }
            } elseif($request->type == 'url') {
                $rules = [
                    'title' => 'required',
                   // 'slug' => 'required',
                    'url' => 'required|url',
                    //'order_number' => 'required',
                ];
            }
            $validated = $request->validate($rules);
            $obj = $modell;
            $obj->type = $request->type;
            $obj->title = $request->title;
            $obj->slug = Str::slug($request->title);
            $obj->description = $request->description;
            $obj->meta_description = $request->meta_description;
            $obj->meta_key = $request->meta_key;
            $obj->meta_title = $request->meta_title;
            $obj->meta_url = $request->meta_url;
            $obj->banner_type = $request->banner_type;
            $obj->url = $request->url;
            $obj->order_number = $request->order_number;
            if ($request->hasFile('banner_media')) {
                $extension = $request->file('banner_media')->getClientOriginalExtension();
                $originalName = $request->file('banner_media')->getClientOriginalName();
                $fileName = time() . '-'.$request->media_type.'.' . $extension;

                $folderName = "cms-banner/".strtoupper(date('M') . date('Y')) . "/";
                $folderPath = Config('constant.BANNER_IMAGE_ROOT_PATH') . $folderName;
                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, $mode = 0777, true);
                }
                if ($request->file('banner_media')->move($folderPath, $fileName)) {
                    $obj->banner_media = $folderName . $fileName;
                }
            }
            $obj->save();
            $userId          =    $obj->id;
            if (!$userId) {
                Session()->flash('error', trans("Something went wrong."));
                return Redirect()->back()->withInput();
            }
           
            Session()->flash('success', trans("Footer subcategory has been updated successfully"));
            return Redirect()->route('admin-'.$this->model . ".index", base64_encode($modell->footer_category_id));
        }
        $action = "edit";
        return  View("admin.$this->model.add", compact('dep_id','modell','departmentDetails','action'));
    }


    public function changeStatus($modelId = 0, $status = 0){
        if ($status == 1) {
            $statusMessage   =   trans("Footer subcategory has been deactivated successfully");
        } else {
            $statusMessage   =   trans("Footer subcategory has been activated successfully");
        }
        $user = FooterSubcategory::find($modelId);
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

    public  function delete($endesid = null)
    {
  
        $dep_id = '';
        if (!empty($endesid)) {
            $des_id = base64_decode($endesid);
        }
        
        $depDetails   =   FooterSubcategory::find($des_id);
        if (empty($depDetails)) {
            return Redirect()->route('admin-'.$this->model . '.index');
        }
        if ($des_id) {
            FooterSubcategory::where('id', $des_id)->update(array('is_deleted' => 1));
            Session()->flash('flash_notice', trans("Footer subcategory has been removed successfully"));
        }
        return back();
    }

    // For sub category
    public function manageSubPriority(Request $request, $dep_id = null, $position = null)
    {
        $footer_category_id = base64_decode($dep_id);
        $subCategories = FooterSubcategory::where('is_deleted', 0)
            ->where('footer_category_id', $footer_category_id)
            ->orderBy('order_number', 'asc')
            ->get();
        return view('admin.footer-subcategory.manage_priority', compact('subCategories', 'dep_id', 'position'));
    }

    public function updateSubPriority(Request $request)
    {
        try {
            $order = $request->input('order');
            Log::info('Received order: ' . json_encode($order));

            DB::transaction(function () use ($order) {
                FooterSubcategory::whereIn('id', $order)->update(['order_number' => null]);
                foreach ($order as $index => $id) {
                    $cat = FooterSubcategory::find($id);
                    if ($cat) {
                        $cat->order_number = $index + 1;
                        $cat->save();
                    }
                }
            });

            return response()->json(['status' => 'success', 'message' => 'Priority updated successfully']);
        } catch (\Exception $e) {
            Log::error('Priority Update Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
        }
    }

    function updateSubFooterCategoryOrderLogic(Request $request)
    {
        $requestOrder =  $request->input("requestData");
        if (!empty($requestOrder)) {
            foreach ($requestOrder as $category_order) {
                FooterSubcategory::where("id", $category_order["id"])->update(array("order_number" => $category_order["order"]));
            }
        }
    }
}
