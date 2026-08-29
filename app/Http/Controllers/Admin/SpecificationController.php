<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use  App\Models\SpecificationGroup;
use  App\Models\Specification;
use  App\Models\SpecificationValue;
use DB,Redirect;

class SpecificationController extends Controller
{
    public $model =    'specifications';
    public function __construct(Request $request)
    {   
        
        $this->listRouteName = 'admin-specifications.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }

    public function index(Request $request, $endesid = null)
    {
        if (!empty($endesid)) {
            $record_id = base64_decode($endesid);
        }
        if (empty($record_id)) {
            return Redirect()->back();
        }
        $recordDetails  =  SpecificationGroup::where('specification_groups.id', $record_id)->first();
        
        if(empty($recordDetails)){
            return Redirect()->back();

        }
        $DB = Specification::query();
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'specifications.created_at';
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
                $DB->whereBetween('specifications.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('specifications.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('specifications.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("specifications.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("specifications.is_active", $fieldValue);
                    }
                   
                }
            }
        }
        $DB->where("is_deleted", 0);
        $DB->where("specification_group_id", $record_id);
        $DB->select("specifications.*");
        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();
        if($request->ajax()){

            return  View("admin.$this->model.load_more_data", compact('results','totalResults','record_id'));
        }else{
            
            return  View("admin.$this->model.index", compact('results','totalResults','record_id'));
        }
    }

    public function add(Request $request, $endesid = null)
    {   

        // dd($request);
        if (!empty($endesid)) {
            $record_id = base64_decode($endesid);
        }
        if (empty($endesid)) {
            return Redirect()->back();
        }
        $formData =    $request->all();
        $recordDetails  =  SpecificationGroup::where('id', $record_id)->first();
        if(empty($recordDetails)){
            return Redirect()->back();

        }
        if ($request->isMethod('POST')) {
            $validated = $request->validate([
                'name' => 'required'
            ]);
            $obj                        =  new Specification;
            $obj->specification_group_id         =  $record_id;
            $obj->name                  =  $request->name;
            $SavedResponse = $obj->save();
            $userId                    =    $obj->id;
           
            if (empty($userId)) {
                Session()->flash('error', trans("Something went wrong."));
                return Redirect()->back()->withInput();
            } else {
                if(!empty($request->dataArr)){
                    foreach($request->dataArr as $specValue){
                        if(!empty($specValue['name']) ){
                           $obj2   =  new SpecificationValue;
                           $obj2->specification_id = $userId;
                           $obj2->name = $specValue['name'];
                           $obj2->save();
                          
                        }
                    }
                }
                Session()->flash('success', Config('constant.SPECIFICATION.SPECIFICATION_TITLE') . " has been added successfully");
                return Redirect()->route('admin-'.$this->model . ".index", $endesid);
            }
        }
       
        return  View("admin.$this->model.add", compact('record_id'));
    }

    public function update(Request $request, $endesid = null)
    {
        $record_id = '';
        if (!empty($endesid)) {
            $record_id = base64_decode($endesid);
        }
        $recordDetails =    Specification::where('specifications.id', $record_id)->first();
        if (empty($recordDetails)) {
            return Redirect()->back();
        }
        if ($request->isMethod('POST')) {
            $formData =    $request->all();
            $validated = $request->validate([
                'name' => 'required'
            ]);
            $obj             =  Specification::find($record_id);
            $obj->name       =  $request->input('name');
            $obj->save();
            $userId          =    $obj->id;
            if (!$userId) {
                Session()->flash('error', trans("Something went wrong."));
                return Redirect()->back()->withInput();
            }

            if(!empty($request->dataArr)){
                SpecificationValue::where('specification_id',$userId)->delete();
                foreach($request->dataArr as $specValue){
                    if(!empty($specValue['name']) ){
                       $obj2   =  new SpecificationValue;
                       $obj2->specification_id = $userId;
                       $obj2->name = $specValue['name'];
                       $obj2->save();
                      
                    }
                }
            }

            Session()->flash('success', trans(Config('constant.SPECIFICATION.SPECIFICATION_TITLE') . " has been updated successfully"));
            return Redirect()->route('admin-'.$this->model . ".index", base64_encode($recordDetails->specification_group_id));
        }
        $specificationValuesData = SpecificationValue::where('specification_id',$record_id)->get()->toArray();
        
        return  View("admin.$this->model.edit", compact('recordDetails','specificationValuesData'));
    }


    public function changeStatus($modelId = 0, $status = 0){
        if ($status == 1) {
            $statusMessage   =   trans(Config('constant.SPECIFICATION.SPECIFICATION_TITLE') . " has been deactivated successfully");
        } else {
            $statusMessage   =   trans(Config('constant.SPECIFICATION.SPECIFICATION_TITLE') . " has been activated successfully");
        }
        $user = Specification::find($modelId);
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
        $record_id = '';
        if (!empty($endesid)) {
            $record_id = base64_decode($endesid);
        }
        
        $depDetails   =   Specification::find($record_id);
        if (empty($depDetails)) {
            return Redirect()->route('admin-'.$this->model . '.index');
        }
        if ($record_id) {
            Specification::where('id', $record_id)->update(array('is_deleted' => 1));
            SpecificationValue::where('specification_id', $record_id)->delete();
            Session()->flash('flash_notice', trans(Config('constant.SPECIFICATION.SPECIFICATION_TITLE') . " has been removed successfully"));
        }
        return back();
    }
}
