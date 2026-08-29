<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Language;
use App\Models\Cms;

class CmspagesController extends Controller
{
    public $model = 'cms-manager';
    public $listRouteName;
    public $request;
    public function __construct(Request $request){
        $this->middleware('permission:view_cms|create_cms|edit_cms|delete_cms', ['only' => ['index', 'show']]);
        $this->middleware('permission:create_cms', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_cms', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_cms', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-cms-manager.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }
    public function index(Request $request)
    {
        $DB = Cms::query();
        $searchVariable = array();
        $inputGet = $request->all();
        if ($request->all()) {
			$searchData			=	$request->all();
			unset($searchData['display']);
			unset($searchData['_token']);

			if(isset($searchData['order'])){
				unset($searchData['order']);
			}
			if(isset($searchData['sortBy'])){
				unset($searchData['sortBy']);
			}
			if(isset($searchData['page'])){
				unset($searchData['page']);
			}
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "page_name") {
                        $DB->where("cms.page_name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "title") {
                        $DB->where("cms.title", 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'created_at';
        $order = ($request->input('order')) ? $request->input('order') : 'DESC';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0 ;
        $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");

        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();

        if($request->ajax()){

            return  View("admin.$this->model.load_more_data", compact('results','totalResults'));
        }else{

            return  View("admin.$this->model.index", compact('results','totalResults'));
        }
    }


    public function create()
    {
        return View("admin.$this->model.add");
    }

    public function store(Request $request)
    {
            $thisData                       =    $request->all();
            
            $validator = Validator::make(
                array(
                    'page_name'         => $request->input('page_name'),
                    'title'             => $request->input('title'),
                    'body'              => $request->input('body'),
                ),
                array(
                     'page_name' => 'required|unique:cms,page_name',
                    'title'     => 'required|unique:cms,title',
                    'body'      => 'required',
                )
            );
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $obj = new Cms;
                $obj->page_name = $request->input('page_name');
                $obj->slug     = $this->getSlug($request->input('page_name'),'slug',"Cms");
                $obj->title     = $request->input('title');
                $obj->body      = $request->input('body');
                $obj->save();
                $lastId = $obj->id;

                Session()->flash('success',"Cms has been added successfully");
                return Redirect()->route("admin-". $this->model . ".index");
            }
    }


    public function show($encmsid= null)
    {
        $cms_id = '';
        if (!empty($encmsid)) {
            $cms_id = base64_decode($encmsid);
        }else{
            return Redirect()->route($this->model . ".index");
        }
        $CmsDetails   =  Cms::find($cms_id);
        $data = compact('CmsDetails');
        return view("admin.$this->model.view", $data);
    }


    public function edit($encmsid)
    {
        $cms_id = '';
        if (!empty($encmsid)) {
            $cms_id = base64_decode($encmsid);
            $cmsDetails         =   Cms::find($cms_id);

            return View("admin.$this->model.edit", compact('cmsDetails'));

        }else{
            return Redirect()->route("admin-".$this->model . ".index");
        }
    }

    public function update(Request $request, $encmsid)
    {
        $cms_id = '';
        $multiLanguage =    array();
        if (!empty($encmsid)) {
            $cms_id = base64_decode($encmsid);
            $thisData                    =    $request->all();
            $validator = Validator::make(
                array(
                    'page_name'         => $request->input('page_name'),
                    'title'             => $request->input('title'),
                    'body'              => $request->input('body'),
                ),
                array(
                    'page_name' => 'required|unique:cms,page_name',
                    'title'     => 'required|unique:cms,title',
                    'body'      => 'required',
                )
            );
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $obj   =   Cms::find($cms_id);
                $obj->page_name             = $request->input('page_name');
                $obj->slug                  = $this->getSlug($request->input('page_name'),'slug',"Cms");
                $obj->title                 = $request->input('title');
                $obj->body                  = $request->input('body');
                $obj->save();
                $lastId                     =    $obj->id;

                Session()->flash('success', "Cms has been updated successfully");
                return Redirect()->route("admin-".$this->model . ".index");
            }

        }else{
            return Redirect()->route("admin-".$this->model . ".index");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($encmsid)
    {
        $cms_id = '';
        if (!empty($encmsid)) {
            $cms_id     = base64_decode($encmsid);
        }
        $CmsDetails     =  Cms::find($cms_id);

        if ($CmsDetails) {
            $CmsDetails->delete();
            Session()->flash('flash_notice', trans("Cms has been removed successfully"));
        }
        return back();
    }
}
