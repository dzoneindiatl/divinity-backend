<?php

namespace App\Http\Controllers\Admin;

use App\Config;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Redirect,DB,Response,Str,Exception;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
use Carbon\Carbon;
class BlogController extends Controller
{
    public $model = 'blogs';
    public function __construct(Request $request) {
        $this->middleware('permission:view_blog|create_blog|edit_blog|delete_blog', ['only' => ['index','store']]);
        $this->middleware('permission:create_blog', ['only' => ['create','store']]);
        $this->middleware('permission:edit_blog', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_blog', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-blogs.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;

    }

    public function index(Request $request) {
        $DB = Blog::query();
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'blogs.created_at';
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
                $DB->whereBetween('blogs.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('blogs.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('blogs.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("blogs.title", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("blogs.is_active", $fieldValue);
                    }
                }
            }
        }

        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();

        if($request->ajax()){
            return  View("admin.$this->model.load_more_data", compact('results','totalResults'));
        }else{
            return  View("admin.$this->model.index", compact('results','totalResults'));
        }
    }

    public function create(Request $request) {
        $action = 'create';
        if($request->isMethod('post')){
            try{
                $rules = [
                    'title' => 'required|unique:blogs,title',
                    //'media' => 'required|mimes:jpg,jpeg,png',
                    'short_description' => 'required',
                    'long_description' => 'required'
                ];
                //echo "<pre>"; print_r($request->all()); die;
                $validator = Validator::make($request->all(),$rules);
                if($validator->fails()) {
                    return Redirect::back()->withErrors($validator);
                }
                DB::beginTransaction();
                $slug = Str::slug($request->title);

                //echo "<pre>"; print_r($request->all()); die;
                $blog = new Blog();
                $blog->title = $request->title;
                $blog->short_description = $request->short_description;
                $blog->long_description = $request->long_description;
                $blog->show_home = $request->show_home;
                 $blog->blog_slug = $slug;
                
               

                if ($request->hasFile('media')) {
                    $extension = $request->file('media')->getClientOriginalExtension();
                    $originalName = $request->file('media')->getClientOriginalName();
                    $fileName = time() . '.' . $extension;
                    $folderName = "slider/blog/".strtoupper(date('M') . date('Y')) . "/";
                    $folderPath = Config('constant.BANNER_IMAGE_ROOT_PATH') . $folderName;
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, $mode = 0777, true);
                    }
                    $img = Image::read($request->file('media'));
                    $img->save($folderPath.$fileName);
                    $blog->media =$folderName . $fileName;
                }
                $blog->save();

                DB::commit();
                return redirect()->route("admin-$this->model.index")->with(['success'=>"Blog Added Successfully."]);
            } catch(Exception $e) {
                DB::rollback();
                Log::error($e);
                return redirect()->back()->with(['error' => $e->getMessage()]);
            }
        } else {
            return view("admin.$this->model.create",compact('action'));
        }
    }

    public function edit(Request $request, $id) {
        
        $action = 'edit';
        $id = base64_decode($id);
        if($request->isMethod('post')){
            try{
                $rules = [
                    'title' => 'required|',
                    'media' => 'nullable|mimes:jpg,jpeg,png',
                    'short_description' => 'required',
                    'long_description' => 'required'
                ];
                //echo "<pre>"; print_r($request->all()); die;
                $validator = Validator::make($request->all(),$rules);
                if($validator->fails()) {
                    return Redirect::back()->withErrors($validator);
                }
                DB::beginTransaction();
                $slug = Str::slug($request->title);
                
                //echo "<pre>"; print_r($request->all()); die;
                $blog = Blog::find($id);
                $blog->title = $request->title;
                $blog->short_description = $request->short_description;
                $blog->long_description = $request->long_description;
                $blog->show_home = $request->show_home;
                 $blog->blog_slug = $slug;
                if ($request->hasFile('media')) {
                    $extension = $request->file('media')->getClientOriginalExtension();
                    $originalName = $request->file('media')->getClientOriginalName();
                    $fileName = time() . '.' . $extension;

                    $folderName = "slider/blog/".strtoupper(date('M') . date('Y')) . "/";
                    $folderPath = Config('constant.BANNER_IMAGE_ROOT_PATH') . $folderName;
                    if (!File::exists($folderPath)) {
                        File::makeDirectory($folderPath, $mode = 0777, true);
                    }
                    if ($request->file('media')->move($folderPath, $fileName)) {
                        $blog->media = $folderName . $fileName;
                    }
                }
                $blog->save();

                DB::commit();
                return redirect()->route("admin-$this->model.index")->with(['success'=>"Blog Updated Successfully."]);
            } catch(Exception $e) {
                DB::rollback();
                Log::error($e);
                return redirect()->back()->with(['error' => $e->getMessage()]);
            }
        } else {
            $blog = Blog::find($id);
            return view("admin.$this->model.create",compact('action','blog'));
        }
    }

    public function destroy($enuserid) {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails = Blog::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($user_id) {
            Blog::where('id', $user_id)->delete();
            Session()->flash('flash_notice', trans("Blog has been removed successfully."));
        }
        return back();
    }

    public function changeStatus($modelId = 0, $status = 0) {
        if ($status == 1) {
            $statusMessage = trans("Blog has been actvated successfully");
        } else {
            $statusMessage = trans("Blog has been deactivated successfully");
        }
        $user = Blog::find($modelId);
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