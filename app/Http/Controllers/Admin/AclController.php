<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Config;

use App\Models\Acl;
use App\Models\AclAdminAction;

class AclController extends Controller
{
	public $model =	'acl';
	public function __construct(Request $request)
	{
		$this->listRouteName = 'admin-acl.index';
		View()->share('model', $this->model);
		View()->share('listRouteName', $this->listRouteName);
		$this->request = $request;
	}

	public function index(Request $request)
	{
		$DB 					= 	Acl::query();
		$searchVariable			=	array();
		$inputGet				=	$request->all();
		if ($request->all()) {
			$searchData			=	$request->all();
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
			foreach ($searchData as $fieldName => $fieldValue) {
				if ($fieldName == "title") {
					$DB->where("acls.title", 'LIKE', '%' . $fieldValue . '%');
				}
				if ($fieldName == "parent_id") {
					$DB->where("acls.parent_id", 'LIKE', '%' . $fieldValue . '%');
				}
				$searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
			}
		}
		$sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'acls.module_order';
		$order  = ($request->input('order')) ? $request->input('order')   : 'DESC';
		$offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
		$limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");

		$results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
		$totalResults = $DB->count();
		$parent_list 	= 	Acl::get();

		if ($request->ajax()) {

			return  View("admin.$this->model.load_more_data", compact('results', 'totalResults', 'parent_list'));
		} else {

			return  View("admin.$this->model.index", compact('results', 'totalResults', 'parent_list'));
		}
	}

	public function create()
	{
		$parent_list =  Acl::get();
		return View("admin.$this->model.add", compact('parent_list'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'title' => 'required',
			'path' => 'required',
			'module_order'  => 'required|numeric',
		]);
		$obj                =  new Acl;
		$obj->parent_id     =   !empty($request->input('parent_id')) ? $request->input('parent_id') : 0;
		$obj->title         =  $request->title;
		$obj->path          =  $request->path;
		$obj->icon          =  $request->icon;
		$obj->module_order  =  $request->module_order;
		$SavedResponse = $obj->save();
		if (!$SavedResponse) {
			Session()->flash('error', trans("Something went wrong."));
			return Redirect()->back()->withInput();
		} else {
			Session()->flash('success', " Module added successfully");
			return Redirect()->route('admin-' . $this->model . ".index");
		}
	}

	public function edit($enaclid)
	{
		$acl_id = '';
		if (!empty($enaclid)) {
			$acl_id = base64_decode($enaclid);
			$aclDetails   =  Acl::find($acl_id);
			$modelss =	Acl::with('get_admin_module_action')->where('id', $acl_id)->first();
			$parent_list  = Acl::where('parent_id', '!=', $acl_id)->get();
			return  View("admin.$this->model.edit", compact('parent_list', 'acl_id', 'modelss', 'aclDetails'));
		} else {
			return redirect()->route('admin-' . $this->model . ".index");
		}
	}

	public function update(Request $request, $enaclid)
	{
		$acl_id = '';
		if (!empty($enaclid)) {
			$acl_id = base64_decode($enaclid);
		} else {
			return redirect()->route('admin-' . $this->model . ".index");
		}
		$thisData = $request->all();
		$validated = $request->validate([
			'title' => 'required',
			'path' => 'required',
			'module_order'  => 'required|numeric',
		]);
		$obj   =  Acl::find($acl_id);
		$obj->parent_id     =   !empty($request->input('parent_id')) ? $request->input('parent_id') : 0;
		$obj->title         =  $request->title;
		$obj->path          =  $request->path;
		$obj->icon          =  $request->icon;
		$obj->module_order  =  $request->module_order;
		$obj->permission_name  = json_encode(['create_product', 'edit_product', 'delete_user', 'view_product']);
		$SavedResponse = $obj->save();

		if (isset($thisData['data']) && !empty($thisData['data'])) {

			foreach ($thisData['data'] as $record) {
				if (!empty($record['name']) && !empty($record['function_name'])) {
					$AclAdminAction	=	AclAdminAction::where('admin_module_id', $acl_id)->where("function_name", $record['function_name'])->first();
					if (empty($AclAdminAction)) {
						$obj1 						=  new AclAdminAction;
						$obj1['admin_module_id']	=  $acl_id;
						$obj1['name']				=  $record['name'];
						$obj1['function_name']		=  $record['function_name'];
						$obj1->save();
					} else {
						$obj1 						=  $AclAdminAction;
						$obj1['admin_module_id']	=  $acl_id;
						$obj1['name']				=  $record['name'];
						$obj1['function_name']		=  $record['function_name'];
						$obj1->save();
					}
				}
			}
		}

		if (!$SavedResponse) {
			Session()->flash('error', trans("Something went wrong."));
			return Redirect()->back()->withInput();
		} else {
			Session()->flash('success', " Module updated successfully");
			return Redirect()->route('admin-' . $this->model . ".index");
		}
	}

	public function destroy($enaclid)
	{
		$acl_id = '';
		if (!empty($enaclid)) {
			$acl_id = base64_decode($enaclid);
		}
		$aclDetails   =  Acl::find($acl_id);
		if ($aclDetails) {
			$aclDetails->delete();
			Acl::where('parent_id', $acl_id)->delete();
			AclAdminAction::where('admin_module_id', $acl_id)->delete();
			Session()->flash('flash_notice', " Module removed successfully");
		}
		return back();
	}

	public function changeStatus($modelId = 0, $status = 0)
	{
		if ($status == 1) {
			$statusMessage   =   " Module deactivated successfully";
		} else {
			$statusMessage   =   " Module activated successfully";
		}
		$user = Acl::find($modelId);
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

	public function addMoreRow(Request $request)
	{
		$counter	=	$request->input('counter');
		return View("admin.$this->model.add_more", compact('counter'));
	}

	public function delete_function($id, Request $request)
	{
		AclAdminAction::where('function_name', $id)->delete();
		return back();
	}
}
