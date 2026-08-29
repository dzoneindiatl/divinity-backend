<?php

namespace App\Http\Controllers\Admin;

use App\Config;
use App\Http\Controllers\Controller;
use App\Models\Lookup;
use App\Models\Banner;
use App\Models\Language;
use App\Models\BannerDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Redirect, DB, Str;

class BannerController extends Controller
{
    public $model = 'Banner';
    public function __construct(Request $request)
    {
        $this->middleware('permission:view_banner|create_banner|edit_banner|delete_banner', ['only' => ['index', 'store']]);
        $this->middleware('permission:create_banner', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_banner', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_banner', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-Banner.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }

    public function index(Request $request)
    {
        $DB = Banner::query();
        $searchVariable = array();
        $inputGet = $request->all();
        if ($request->all()) {
            $searchData = $request->all();
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
                $DB->whereBetween('banners.created_at', [$dateS, $dateE]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('banners.created_at', '>=', [$dateS]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('banners.created_at', '<=', [$dateE]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "is_active") {
                        $DB->where("banners.is_active", 'like', '%' . $fieldValue . '%');
                    }
                }
                $searchVariable = array_merge($searchVariable, array($fieldName => $fieldValue));
            }
        }

        $sortBy = ($request->input('sortBy')) ? $request->input('sortBy') : 'banners.created_at';
        $order = ($request->input('order')) ? $request->input('order') : 'DESC';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
        $limit =  !empty($request->input('limit')) ? $request->input('limit') : Config("Reading.records_per_page");

        $results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
        $totalResults = $DB->count();

        if (!empty($results)) {
            foreach ($results as &$result) {


                if ($result->type == "full_image") {
                    $result->type_name = "Full Image";
                } else if ($result->type == "left_image_right_content") {
                    $result->type_name = "Left Image Right Content";
                } else if ($result->type == "right_image_left_content") {
                    $result->type_name = "Right Image Left Content";
                } else {
                    $result->type_name = "Video";
                }
            }
        }

        if ($request->ajax()) {

            return  View("admin.$this->model.load_more_data", compact('results', 'totalResults'));
        } else {
            return  View("admin.$this->model.index", compact('results', 'totalResults'));
        }
    }

    public function create(Request $request)
    {
        return View("admin.$this->model.add");
    }

    public function edit(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {

            $user_id = base64_decode($enuserid);
            $userDetails = Banner::where('banners.id', $user_id)->first();
            return View("admin.$this->model.edit", compact('userDetails'));
        }
    }


    public function save(Request $request)
    {
        $thisData = $request->all();
        if (!empty($thisData)) {
            $type = $request->input('type');

            $validator = Validator::make(
                $request->all(),
                [
                    'type' => 'required',
                    'image' => in_array($type, ['full_image', 'left_image', 'right_image']) ? 'required|mimes:jpg,jpeg,png,webp' : 'nullable',
                    'video' => $type === 'video' ? 'required|mimetypes:video/mp4,video/quicktime' : 'nullable',
                    'url'   => in_array($type, ['youtube', 'vimeo',]) ? 'required|url' : 'nullable',
                    'description' => in_array($type, ['left_image', 'right_image']) ? 'required' : 'nullable',
                ],
                [
                    'type.required' => trans("The Banner type field is required."),
                    'url.required' => trans("The Banner URL field is required."),
                    'image.required' => trans("The image field is required."),
                    'image.mimes' => trans("The image should be of type jpeg,jpg,png."),
                    'video.required' => trans("The video field is required."),
                    'video.mimetypes' => trans("The video should be of type mp4,mov."),
                    'description.required' => trans("The description field is required."),
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();
            try {
                $obj                                = new Banner;
                $obj->type                          = $type;
                $obj->description = $request->input('description') ?? '';
                $obj->title = $request->input('title') ?? '';
                $obj->show_on_home_slider = $request->input('show_on_home_slider') ?? '';
                $obj->show_on_home_banner = $request->input('show_on_home_banner') ?? '';
                $obj->show_on_home_offer_banner = $request->input('show_on_home_offer_banner') ?? '';
                $obj->button_active = $request->input('button_active') ?? '';
                $obj->video_auto_play = $request->input('video_auto_play') ?? '';
                $obj->height = $request->input('height') ?? NULL;
                $obj->width = $request->input('width') ?? NULL;
                $obj->url = $request->input('url') ?? '';
                if (in_array($type, ['full_image', 'left_image_right_content', 'right_image_left_content'])) {
                    $obj->url = $request->urlfdf;
                }
                $obj->order_number = $request->input('order_number') ?? '';

                if ($request->hasFile('image')) {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $originalName = $request->file('image')->getClientOriginalName();
                    $fileName = time() . '-image.' . $extension;
                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $adminPath = config('constant.BANNER_IMAGE_ROOT_PATH') . $folderName;
                    $frontendPath = base_path('../frontend/public/uploads/banners/' . $folderName);
                    if (!File::exists($adminPath)) {
                        File::makeDirectory($adminPath, 0777, true);
                    }
                    if (!File::exists($frontendPath)) {
                        File::makeDirectory($frontendPath, 0777, true);
                    }
                    if ($request->file('image')->move($adminPath, $fileName)) {
                        $obj->image = $folderName . $fileName;
                        File::copy($adminPath . $fileName, $frontendPath . $fileName);
                    }
                }
                if ($request->hasFile('video')) {
                    $extension = $request->file('video')->getClientOriginalExtension();
                    $originalName = $request->file('video')->getClientOriginalName();
                    $fileName = time() . '-video.' . $extension;

                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $adminPath = config('constant.BANNER_VIDEO_ROOT_PATH') . $folderName;
                    // $frontendPath = base_path('../public_html/public/uploads/banner_images/' . $folderName);
                    $frontendPath = base_path('../frontend/public/uploads/banners-video/' . $folderName);
                    if (!File::exists($adminPath)) {
                        File::makeDirectory($adminPath, 0777, true);
                    }
                    if (!File::exists($frontendPath)) {
                        File::makeDirectory($frontendPath, 0777, true);
                    }
                    if ($request->file('video')->move($adminPath, $fileName)) {
                        $obj->video = $folderName . $fileName;
                        File::copy($adminPath . $fileName, $frontendPath . $fileName);
                    }
                }

                if ($request->hasFile('th_image')) {
                    $extension = $request->file('th_image')->getClientOriginalExtension();
                    $originalName = $request->file('th_image')->getClientOriginalName();
                    $fileName = time() . '-image.' . $extension;

                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $adminPath = config('constant.BANNER_IMAGE_ROOT_PATH') . $folderName;
                    // $frontendPath = base_path('../public_html/public/uploads/banner_images/' . $folderName);
                    $frontendPath = base_path('../frontend/public/uploads/banners/' . $folderName);
                    if (!File::exists($adminPath)) {
                        File::makeDirectory($adminPath, 0777, true);
                    }
                    if (!File::exists($frontendPath)) {
                        File::makeDirectory($frontendPath, 0777, true);
                    }

                    if ($request->file('th_image')->move($adminPath, $fileName)) {
                        $obj->th_image = $folderName . $fileName;
                        File::copy($adminPath . $fileName, $frontendPath . $fileName);
                    }
                }

                $obj->save();
                if ($obj->id) {
                    DB::commit();
                    Session()->flash('flash_notice', trans("Banner has been added successfully."));
                } else {
                    DB::rollback();
                    Session()->flash('flash_notice', 'Something Went Wrong');
                }

                Session()->flash('flash_notice', trans("Banner has been added successfully."));
                return Redirect::route('admin-Banner.index');
            } catch (\Exception $e) {
                DB::rollback();
                Session()->flash('flash_notice', 'Something Went Wrong: ' . $e->getMessage());
                return Redirect::route('admin-Banner.index');
            }
        }
    }


    public function update(Request $request, $enuserid = null)
    {

        // echo "<pre>"; print_r($request->all()); die;
        $model = Banner::find($enuserid);
        if (empty($model)) {
            return View("admin.$this->model.edit");
        } else {
            $thisData = $request->all();

            if (!empty($thisData)) {
                $type = $request->input('type');
                $validator = Validator::make(

                    $request->all(),
                    [
                        'type' => 'required',
                        //'image' => in_array($type, ['full_image', 'left_image', 'right_image']) ? 'required|mimes:jpg,jpeg,png,webp' : 'nullable',
                        //// 'video' => $type === 'video' ? 'required|mimetypes:video/mp4,video/quicktime' : 'nullable',
                        //'url'   => in_array($type, ['youtube', 'vimeo']) ? 'required|url' : 'nullable',
                        // 'description' => in_array($type, ['left_image', 'right_image']) ? 'required' : 'nullable',
                    ],
                    [
                        'type.required' => trans("The Banner type field is required."),
                        //'url.required' => trans("The Banner URL field is required."),
                        //'image.required' => trans("The image field is required."),
                        // 'image.mimes' => trans("The image should be of type jpeg,jpg,png."),
                        // 'video.required' => trans("The video field is required."),
                        // 'video.mimetypes' => trans("The video should be of type mp4,mov."),
                        // 'description.required' => trans("The description field is required."),
                    ]
                );

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                DB::beginTransaction();
                // try {

                $obj                                = $model;
                $obj->type                          = $request->input('type');
                $obj->title = $request->input('title') ?? '';
                $obj->description                   = !empty($request->input('description')) ? $request->input('description') : $model->description;
                $obj->show_on_home_slider = $request->input('show_on_home_slider') ?? '';
                $obj->show_on_home_banner = $request->input('show_on_home_banner') ?? '';
                $obj->show_on_home_offer_banner = $request->input('show_on_home_offer_banner') ?? '';
                $obj->button_active = $request->input('button_active') ?? '';
                $obj->video_auto_play = $request->input('video_auto_play') ?? '';
                $obj->height                        = !empty($request->input('height')) ? $request->input('height') : $model->height;
                $obj->width                         = !empty($request->input('width')) ? $request->input('width') : $model->width;
                $obj->url                           = !empty($request->input('url')) ? $request->input('url') : $model->url;
                if (in_array($type, ['full_image', 'left_image_right_content', 'right_image_left_content'])) {
                    $obj->url = $request->urlfdf;
                }

                if ($request->hasFile('image')) {
                    $extension = $request->file('image')->getClientOriginalExtension();
                    $originalName = $request->file('image')->getClientOriginalName();
                    $fileName = time() . '-image.' . $extension;

                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $adminPath = config('constant.BANNER_IMAGE_ROOT_PATH') . $folderName;
                    // $frontendPath = base_path('../public_html/public/uploads/banner_images/' . $folderName);
                    $frontendPath = base_path('../frontend/public/uploads/banners/' . $folderName);
                    if (!File::exists($adminPath)) {
                        File::makeDirectory($adminPath, 0777, true);
                    }
                    if (!File::exists($frontendPath)) {
                        File::makeDirectory($frontendPath, 0777, true);
                    }

                    if ($request->file('image')->move($adminPath, $fileName)) {
                        $obj->image = $folderName . $fileName;
                        File::copy($adminPath . $fileName, $frontendPath . $fileName);
                    }
                }
                if ($request->hasFile('video')) {
                    $extension = $request->file('video')->getClientOriginalExtension();
                    $originalName = $request->file('video')->getClientOriginalName();
                    $fileName = time() . '-video.' . $extension;

                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $adminPath = config('constant.BANNER_VIDEO_ROOT_PATH') . $folderName;
                    // $frontendPath = base_path('../public_html/public/uploads/banner_images/' . $folderName);
                    $frontendPath = base_path('../frontend/public/uploads/banners-video/' . $folderName);
                    if (!File::exists($adminPath)) {
                        File::makeDirectory($adminPath, 0777, true);
                    }
                    if (!File::exists($frontendPath)) {
                        File::makeDirectory($frontendPath, 0777, true);
                    }

                    if ($request->file('video')->move($adminPath, $fileName)) {
                        $obj->video = $folderName . $fileName;
                        File::copy($adminPath . $fileName, $frontendPath . $fileName);
                    }
                }

                if ($request->hasFile('th_image')) {
                    $extension = $request->file('th_image')->getClientOriginalExtension();
                    $originalName = $request->file('th_image')->getClientOriginalName();
                    $fileName = time() . '-image.' . $extension;

                    $folderName = strtoupper(date('M') . date('Y')) . "/";
                    $adminPath = config('constant.BANNER_IMAGE_ROOT_PATH') . $folderName;
                    // $frontendPath = base_path('../public_html/public/uploads/banner_images/' . $folderName);
                    $frontendPath = base_path('../frontend/public/uploads/banners/' . $folderName);
                    if (!File::exists($adminPath)) {
                        File::makeDirectory($adminPath, 0777, true);
                    }
                    if (!File::exists($frontendPath)) {
                        File::makeDirectory($frontendPath, 0777, true);
                    }

                    if ($request->file('th_image')->move($adminPath, $fileName)) {
                        $obj->th_image = $folderName . $fileName;
                        File::copy($adminPath . $fileName, $frontendPath . $fileName);
                    }
                }

                $obj->save();
                $lastId = $obj->id;
                if (!empty($lastId)) {

                    DB::commit();
                } else {
                    DB::rollback();
                    Session()->flash('flash_notice', 'Something Went Wrong');
                    return Redirect::route('admin-Banner.index');
                }
                Session()->flash('flash_notice', trans("Banner has been updated successfully."));
                return Redirect::route('admin-Banner.index');
                // } catch (\Exception $e) {
                //     DB::rollback();
                //     Session()->flash('flash_notice', 'Something Went Wrong: ' . $e->getMessage());
                //     return Redirect::route('admin-Banner.index');
                // }
            }
        }
    }
    public function updateToggleStatus(Request $request)
    {
        $model = Banner::find($request->id);
        if ($model && in_array($request->field, ['show_on_home_slider', 'show_on_home_banner', 'show_on_home_offer_banner', 'button_active', 'video_auto_play'])) {
            $model->{$request->field} = $request->value;
            $model->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
    public function destroy($enuserid)
    {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails = Banner::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        Banner::where('id', $user_id)->delete();
        Session()->flash('flash_notice', trans("Banner has been removed successfully."));

        return back();
    }

    public function changeStatus($modelId = 0, $status = 0)
    {
        if ($status == 1) {
            $statusMessage = trans("Banner has been deactivated successfully");
        } else {
            $statusMessage = trans("Banner has been activated successfully");
        }
        $user = Banner::find($modelId);
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


    public function show(Request $request, $enuserid = null)
    {
        $user_id = '';
        if (!empty($enuserid)) {

            $user_id = base64_decode($enuserid);
            $userDetails = Banner::where('banners.id', $user_id)->select('banners.*')->first();

            $data = compact('user_id', 'userDetails');

            return View("admin.$this->model.view", $data);
        }
    }
}
