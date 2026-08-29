<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Tag; 

class TagController extends Controller
{
    public $model        =    'tags';
    public function __construct(Request $request){

        $this->middleware('permission:view_tag|create_tag|edit_tag|delete_tag', ['only' => ['index','store']]);
        $this->middleware('permission:create_tag', ['only' => ['create','store']]);
        $this->middleware('permission:edit_tag', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_tag', ['only' => ['destroy']]);

        View()->share('model', $this->model);
        $this->request = $request;
    }
    public function index(Request $request){

        $DB                    =    Tag::query();
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
                $DB->whereBetween('tags.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('tags.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('tags.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("tags.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("tags.is_active", $fieldValue);
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->where("is_deleted", 0);
        $DB->select("tags.*");
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
            'name' => 'required',
            'slug' => 'required'
        ]);
        $originalString = $request->slug ?? "";
        $lowercaseString = Str::lower($originalString);
        $slug = Str::slug($lowercaseString, '-');


        $alreadyAddedName = Tag::where('slug', $originalString)->first();

        if (!is_null($alreadyAddedName)) {
            return redirect()->back()->with(['error' => 'Slug is already added']);
        }
        $obj           =  new Tag;
        $obj->name     =  $request->name;
        $obj->slug     =  $slug;
        $SavedResponse =      $obj->save();
        if (!$SavedResponse) {
            Session()->flash('error', trans("Something went wrong."));
            return Redirect()->back()->withInput();
        } else {
            Session()->flash('success', "Tag has been added successfully");
            return Redirect()->route('admin-'.$this->model . ".index");
        }
    }

    public function edit($endepid){
        $dep_id = '';
        if (!empty($endepid)) {
            $dep_id = base64_decode($endepid);
            $depDetails   =   Tag::find($dep_id);
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
            'name' => 'required',
            'slug' => 'required'
        ]);
        $tag = Tag::find($dep_id);
        $oldSlug = $tag->slug;

        $originalString = $request->slug ?? "";
        $lowercaseString = Str::lower($originalString);
        $slug = Str::slug($lowercaseString, '-');

        $alreadyAddedName = Tag::where('slug', $originalString)->where('id', '!=', $tag->id)->first();

        if (!is_null($alreadyAddedName)) {
            return redirect()->back()->with(['error' => 'Slug is already added']);
        }
        $obj           =  Tag::find($dep_id);
        $obj->name     =  $request->name;
        $obj->slug     =   $slug;
        $SavedResponse =  $obj->save();
        if (!$SavedResponse) {
            Session()->flash('error', trans("Something went wrong."));
            return Redirect()->back()->withInput();
        }
        Session()->flash('success', "Tag has been updated successfully");
        return Redirect()->route('admin-'.$this->model . ".index");
    }

    public function destroy($endepid){
        $dep_id = '';
        if (!empty($endepid)) {
            $dep_id     = base64_decode($endepid);
        }
        $depDetails     =   Tag::find($dep_id);
        if (empty($depDetails)) {
            return Redirect()->route('admin-'.$this->model . '.index');
        }
        if ($dep_id) {
            Tag::where('id', $dep_id)->update(array('is_deleted' => 1));
            Session()->flash('flash_notice', trans("Tag has been removed successfully"));
        }
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0){
        if ($status == 1) {
            $statusMessage   =   trans("Tag has been deactivated successfully");
        } else {
            $statusMessage   =   trans("Tag has been activated successfully");
        }
        $user = Tag::find($modelId);
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
