<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Language;
use App\Models\EmailTemplate;
use App\Models\EmailAction;
use App\Models\EmailActionOption;
use App\Models\EmailTemplateDescription;

class EmailtemplateController extends Controller
{
    public $model                =    'email-templates';
    public $listRouteName;
    public $request;
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_email_template|create_email_template|edit_email_template|delete_email_template', ['only' => ['index','store','getConstant']]);
        $this->middleware('permission:create_email_template', ['only' => ['create','store','getConstant']]);
        $this->middleware('permission:edit_email_template', ['only' => ['edit','update','getConstant']]);
        $this->middleware('permission:delete_email_template', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-email-templates.index';
        $this->request = $request;
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
    }

    public function index(Request $request)
    {
        $DB                =    EmailTemplate::query();
        $searchVariable    =    array();
        $inputGet        =    $request->all();
        if ($request->all()) {
            $searchData    =    $request->all();
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
                if (!empty($fieldValue)) {
                    if ($fieldName == "name" || $fieldName == "subject") {
                        $DB->where("$fieldName", 'like', '%' . $fieldValue . '%');
                    }
                    $searchVariable    =    array_merge($searchVariable, array($fieldName => $fieldValue));
                }
            }
        }
        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'updated_at';
        $order  = ($request->input('order')) ? $request->input('order')   : 'DESC';

        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
        $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");

        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();

        if ($request->ajax()) {

            return  View("admin.$this->model.load_more_data", compact('results', 'totalResults'));
        } else {

            return  View("admin.$this->model.index", compact('results', 'totalResults'));
        }
    }

    public function create()
    {
        $actions    =    EmailAction::get();
        $action_options    =    EmailActionOption::get();
        $categories = Category::whereNull('parent_id')->get();
        $brands = Brand::where([['is_active', 1], ['is_deleted', 0]])->get();
        return  View("admin.$this->model.add", compact('actions', 'action_options', 'categories', 'brands'));
    }

    public function store(Request $request)
    {
        // dd($request->input('sub_category_id'));
        $thisData                    =    $request->all();
        $validator = Validator::make(
            array(
                'name'         =>  $request->input('name'),
                'subject'      =>  $request->input('subject'),
                'action_id'       =>  $request->input('action_id'),
                'body'         =>  $request->input('body'),
            ),
            array(
                'name'         => 'required',
                'subject'         => 'required',
                'action_id'         => 'required',
                'body'         => 'required',
            )
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {

            $obj                                 = new EmailTemplate;
            $obj->name                             =   $request->input('name');
            $obj->subject                          =   $request->input('subject');
            $obj->body                             =   $request->input('body');
            $obj->action_id                           =   $request->input('action_id');
            if (!empty($request->brand_id)) {
                $obj->brand_id                           =   $request->input('brand_id');
            }
            if (!empty($request->category_id)) {
                $obj->category_id                           =   $request->input('category_id');
            }
            if (!empty($request->sub_category_id)) {
                $obj->sub_category_id                           =   $request->input('sub_category_id');
            }
            if (!empty($request->child_category_id)) {
                $obj->child_category_id                           =   $request->input('child_category_id');
            }

            $obj->save();
            $last_id            =    $obj->id;
            Session()->flash('flash_notice', trans("Email template created successfully"));
            return Redirect()->route("$this->listRouteName");
        }
    }

    public function edit($enmaiman)
    {
        $Id = '';
        if (!empty($enmaiman)) {

            $Id = base64_decode($enmaiman);
            $actions    =    EmailAction::get();
            $action_options    =    EmailActionOption::get();
            $categories = Category::whereNull('parent_id')->get();
            $brands = Brand::where([['is_active', 1], ['is_deleted', 0]])->get();

            $emailTemplate              =    EmailTemplate::find($Id);
            return  View("admin.$this->model.edit", compact('emailTemplate', 'actions', 'action_options', 'categories', 'brands'));
        } else {
            return Redirect()->route("$this->listRouteName");
        }
    }

    public function update(Request $request, $enmaiman)
    {
        $Id = '';
        if (!empty($enmaiman)) {
            $Id = base64_decode($enmaiman);
        } else {
            return Redirect()->route("$this->listRouteName");
        }
        $thisData                    =    $request->all();
        $validator = Validator::make(
            array(
                'name'         =>  $request->input('name'),
                'subject'      =>  $request->input('subject'),
                'body'         =>  $request->input('body'),
            ),
            array(
                'name'         => 'required',
                'subject'         => 'required',
                'body'         => 'required',
            )
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $model                    = EmailTemplate::find($Id);
            $obj                      = $model;
            $obj->name                =   $request->input('name');
            $obj->subject             =   $request->input('subject');
            $obj->body                =   $request->input('body');

            if (!empty($request->brand_id)) {
                $obj->brand_id                           =   $request->input('brand_id');
            }
            if (!empty($request->category_id)) {
                $obj->category_id                           =   $request->input('category_id');
            }
            if (!empty($request->sub_category_id)) {
                $obj->sub_category_id                           =   $request->input('sub_category_id');
            }
            if (!empty($request->child_category_id)) {
                $obj->child_category_id                           =   $request->input('child_category_id');
            }
            
            $obj->save();
            $last_id            =    $obj->id;
            Session()->flash('flash_notice', trans("Email template updated successfully"));
            return Redirect()->route("$this->listRouteName");
        }
    }


    public function destroy($enuserid)
    {
        // dd($enuserid);
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails = EmailTemplate::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($user_id) {
            EmailTemplate::where('id', $user_id)->delete();

            Session()->flash('flash_notice', trans("EmailTemplate has been removed successfully."));
        }
        return back();
    }

    public function getConstant(Request $request)
    {
        if ($request->all()) {
            $constantName      =$request->input('constant');
            $options           =EmailAction::where('action', '=', $constantName)->pluck('options', 'action');
            $a                 =explode(',', $options[$constantName]);
            return json_encode($a);
        }
        exit;
    }
}