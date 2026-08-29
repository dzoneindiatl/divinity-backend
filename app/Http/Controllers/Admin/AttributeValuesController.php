<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Acl;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Department;
use App\Models\Designation;
use App\Models\AclAdminAction;
use App\Models\DesignationPermission;
use App\Models\DesignationPermissionAction;

class AttributeValuesController extends Controller
{
    public $model =    'attribute-values';
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
        $attributesDetails  =  Attribute::where('attributes.id', $dep_id)->first();
        if (empty($dep_id)) {
            return Redirect()->back();
        }
        $DB                    =    AttributeValue::query();
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
                $DB->whereBetween('attribute_values.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('attribute_values.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('attribute_values.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("attribute_values.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("attribute_values.is_active", $fieldValue);
                    }
                }
                $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $DB->where("is_deleted", 0);
        $DB->where("attribute_id", $dep_id);
        $DB->select("attribute_values.*");
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
        $records_per_page    =    ($request->input('per_page')) ? $request->input('per_page') : Config("Reading.records_per_page");
        $results = $DB->orderBy($sortBy, $order)->paginate($records_per_page);
        $complete_string        =    $request->query();
        unset($complete_string["sortBy"]);
        unset($complete_string["order"]);
        $query_string            =    http_build_query($complete_string);
        $results->appends($inputGet)->render();
        return  View("admin.$this->model.index", compact('results', 'searchVariable', 'sortBy', 'order', 'query_string', 'dep_id'));
    }
    
    public function add(Request $request, $endesid = null)
    {   

        // dd($request);
        if (!empty($endesid)) {
            $dep_id = base64_decode($endesid);
        }
        if (empty($endesid)) {
            return Redirect()->back();
        }
        $formData =    $request->all();
        $attributeDetails  =  Attribute::where('id', $dep_id)->first();
        $attributevalue = AttributeValue::where('attribute_id', $attributeDetails->id)->first();
      if (!is_null($attributevalue)) {
                            return redirect()->back()->with(['error' => $attributeDetails->name.'Attribute value is already added']);
                        }

        if ($request->isMethod('POST')) {
            $validated = $request->validate([
                'name' => 'required'
            ]);
            $obj                        =  new AttributeValue;
            $obj->attribute_id         =  $dep_id;
            $obj->name                  =  $request->name;
            $SavedResponse = $obj->save();
            $userId                    =    $obj->id;
           
            if (!$SavedResponse) {
                Session()->flash('error', trans("Something went wrong."));
                return Redirect()->back()->withInput();
            } else {
                Session()->flash('success', Config('constant.ATTRIBUTE_VALUED.ATTRIBUTE_VALUED_TITLE') . " has been added successfully");
                return Redirect()->route('admin-'.$this->model . ".index", $endesid);
            }
        }
        $aclModules        =    Acl::where('is_active', 1)->where('parent_id', 0)->get();
         
        return  View("admin.$this->model.add", compact('dep_id', 'aclModules'));
    }

    public function update(Request $request, $endesid = null)
    {
        $des_id = '';
        if (!empty($endesid)) {
            $des_id = base64_decode($endesid);
        }
        $modell =    AttributeValue::where('id', $des_id)->first();
        if (empty($modell)) {
            return Redirect()->back();
        }
        if ($request->isMethod('POST'))
         {
            $formData =    $request->all();
            $validated = $request->validate([
                'name' => 'required'
            ]);
            $obj             =  AttributeValue::find($des_id);
            $obj->name       =  $request->input('name');
            $obj->save();
            $userId          =    $obj->id;
            if (!$userId) {
                Session()->flash('error', trans("Something went wrong."));
                return Redirect()->back()->withInput();
            }
          
            Session()->flash('success', trans(Config('constant.DESIGNATION.DESIGNATION_TITLE') . " has been updated successfully"));
            return Redirect()->route('admin-'.$this->model . ".index", base64_encode($modell->attribute_id));
        }
      //  $aclModules    = Acl::select('title', 'id', DB::Raw("(select is_active from designation_permissions where designation_id = $modell->id AND admin_module_id = acls.id LIMIT 1) as active"))->where('is_active', 1)->where('parent_id', 0)->get();
        // dd(  $aclModules);
          
        return  View("admin.$this->model.edit", compact('modell'));
    }

    public function changeStatus($modelId = 0, $status = 0){
        if ($status == 1) {
            $statusMessage   =   trans(Config('constant.ATTRIBUTE_VALUED.ATTRIBUTE_VALUED_TITLE') . " has been deactivated successfully");
        } else {
            $statusMessage   =   trans(Config('constant.ATTRIBUTE_VALUED.ATTRIBUTE_VALUED_TITLE') . " has been activated successfully");
        }
        $user = AttributeValue::find($modelId);
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
        
        $depDetails   =   AttributeValue::find($des_id);
        if (empty($depDetails)) {
            return Redirect()->route('admin-'.$this->model . '.index');
        }
        //dd($des_id);
        if ($des_id) {
            AttributeValue::where('id', $des_id)->update(array('is_deleted' => 1));
            Session()->flash('flash_notice', trans(Config('constant.ATTRIBUTE_VALUED.ATTRIBUTE_VALUED_TITLE') . " has been removed successfully"));
        }
        return back();
    }

    // public function destroy($endepid){
    //     $dep_id = '';
    //     if (!empty($endepid)) {
    //         $dep_id     = base64_decode($endepid);
    //     }
    //     $depDetails     =   AttributeValue::find($dep_id);
    //     if (empty($depDetails)) {
    //         return Redirect()->route('admin-'.$this->model . '.index');
    //     }
    //     if ($dep_id) {
    //         Attribute::where('id', $dep_id)->update(array('is_deleted' => 1));
    //         Session()->flash('flash_notice', trans(Config('constant.ATTRIBUTE_VALUED.ATTRIBUTE_VALUED_TITLE') . " has been removed successfully"));
    //     }
    //     return back();
    // }
}
