<?php

namespace App\Http\Controllers\Admin;

// use App\Config;
use App\Http\Controllers\Controller;
use App\Models\ReferralSettingUpdateHistory;
use App\Models\EmailAction;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Redirect,DB,Response,Str,Config;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Admin\Auth\AuthController;
use Carbon\Carbon;

class ReferralSettingUpdateHistoryController extends Controller
{
    public $model = 'referral-setting-update-histories';
    public $listRouteName;
    public $request;
    public function __construct(Request $request)
    {
		$this->listRouteName = 'admin-referral-setting-update-histories.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);
        $this->request = $request;
    }

    public function index(Request $request)
    {
        $DB = ReferralSettingUpdateHistory::query()->with(['getUserCreated','getUserUpdated','getUpdatedRow']);
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'referral_setting_update_histories.created_at';
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
                $DB->whereBetween('referral_setting_update_histories.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('referral_setting_update_histories.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('referral_setting_update_histories.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("referral_setting_update_histories.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "is_active") {
                        $DB->where("referral_setting_update_histories.is_active", $fieldValue);
                    }
                }
            }
        }
		$DB->whereNull("referral_setting_update_histories.updated_referal_id");
		$results = $DB->orderBy($sortBy, $order)->offset($offset)->limit($limit)->get();
		$totalResults = $DB->count();

        if($request->ajax()){

            return  View("admin.$this->model.load_more_data", compact('results','totalResults','offset','request'));
        }else{

            return  View("admin.$this->model.index", compact('results','totalResults','offset','request'));
        }
    }
	
	public function indexUpdated(Request $request)
    {
		$id= $request->referal_id;
        $DB = ReferralSettingUpdateHistory::query()->with(['getUserCreated','getUserUpdated','getUpdatedRow']);
        $DB->where("referral_setting_update_histories.updated_referal_id",$id);
		$results = $DB->orderBy('id', 'desc')->get();
	    $totalResults = $DB->count();
		$historyHtml = '';
		if(!empty($results)){
			$i= 1;
			foreach($results as $result){
				$receiver_amount = ''; if(isset($result->receiver_amount)){ $receiver_amount = $result->receiver_amount; }
				$sender_amount = ''; if(isset($result->sender_amount)){ $sender_amount = $result->sender_amount; }
				$ip = ''; if(isset($result->ip)){ $ip = $result->ip; }
				
				$created_by = ''; if(isset($result->getUserCreated->name)){ $created_by = $result->getUserCreated->name; }
				$created_by_email = ''; if(isset($result->getUserCreated->email)){ $created_by_email = $result->getUserCreated->email; }
				$created_at = ''; if(isset($result->created_at)){ $created_at = $result->created_at; }
				
				$updated_by = ''; if(isset($result->getUserUpdated->name)){ $updated_by = $result->getUserUpdated->name; }
				$updated_by_email = ''; if(isset($result->getUserUpdated->email)){ $updated_by_email = $result->getUserUpdated->email; }
				$updated_at = ''; if(isset($result->updated_at)){ $updated_at = $result->updated_at; }
				
				$historyHtml .= '<tr class="list-data-row" data-total-count="4">
					<td>'.$i.'</td>
					<td>'. $receiver_amount.'</td>
					<td>'. $sender_amount.'</td>
					<td>'. $ip .'</td>
					<td>'. $created_by . " </br> " .$created_by_email .'</td>
					<td>'.$created_at.'</td>
					<td>'. $updated_by . " </br> " .$updated_by_email .'</td>.
					<td>'.$updated_at.'</td>
				</tr>';
				$i++;
			}
		}
		return $historyHtml;
    }


    public function create(Request $request)
    {
        return View("admin.$this->model.add");
    }

    public function edit(Request $request, $enreferalid = null)
    {
        $user_id = '';
        if (!empty($enreferalid)) {

            $user_id = base64_decode($enreferalid);
            $userDetails = ReferralSettingUpdateHistory::where('id',$enreferalid)->first();

            return View("admin.$this->model.edit", compact( 'userDetails'));
        }
    }
    public function save(Request $request)
    {
		$formData = $request->all();
		if (!empty($formData)) {
            $validator = Validator::make(
                $request->all(),
                array(
					'receiver_amount' => ['required','numeric'],
                    'sender_amount' =>   ['required','numeric'],
                )
            );
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                DB::beginTransaction();

                $obj                                = new ReferralSettingUpdateHistory;
                $obj->receiver_amount               = $request->input('receiver_amount');
                $obj->sender_amount                 = $request->input('sender_amount');
				$obj->ip                        = $request->ip();
				$obj->created_by                = $request->user()->id;
				$obj->updated_by                = $request->user()->id;
				$obj->created_at                = time();
				$obj->updated_at                = time();
                $obj->save();
                $lastId = $obj->id;
                if(!empty($lastId)){

                    DB::commit();
                }else{
                    DB::rollback();
                    Session()->flash('flash_notice', 'Something Went Wrong');
                    return Redirect::route('admin-referral_setting_update_histories.index');
                }
                Session()->flash('flash_notice', trans("Referal saved successfully."));
                return Redirect::route('admin-referral-setting-update-histories.index');
            }
        }
	}
	
    public function update(Request $request, $enuserid = null)
    {

        $model = Testimonial::find($enuserid);
        if (empty($model)) {
            return View("admin.$this->model.edit");
        } else {
            $formData = $request->all();
            if (!empty($formData)) {
                $validator = Validator::make(
                    $request->all(),
                    array(
                        'name' => 'required',
                        'image' => 'nullable|mimes:jpg,jpeg,png',
                    )
                );
                if ($validator->fails()) {
                    return Redirect::back()->withErrors($validator)->withInput();
                } else {
                    DB::beginTransaction();
                    $obj                                = $model;
                    $obj->name                          = $request->input('name');
                    $obj->description                          = $request->input('description');
                    $obj->rating                          = $request->input('rating');
                    $obj->city                  = $request->input('city');
                    if ($request->hasFile('image')) {
                        $extension = $request->file('image')->getClientOriginalExtension();
                        $originalName = $request->file('image')->getClientOriginalName();
                        $fileName = time() . '-image.' . $extension;

                        $folderName = strtoupper(date('M') . date('Y')) . "/";
                        $folderPath = Config('constant.TESTIMONIAL_IMAGE_ROOT_PATH') . $folderName;
                        if (!File::exists($folderPath)) {
                            File::makeDirectory($folderPath, $mode = 0777, true);
                        }
                        if ($request->file('image')->move($folderPath, $fileName)) {
                            $obj->image = $folderName . $fileName;
                        }
                    }
                    $obj->save();
                    $lastId = $obj->id;
                    if(!empty($lastId)){
                        DB::commit();
                    }else{
                        DB::rollback();
                        Session()->flash('flash_notice', 'Something Went Wrong');
                        return Redirect::route('admin-referral_setting_update_histories.index');
                    }
                    Session()->flash('flash_notice', trans("Testimonial updated successfully."));
                    return Redirect::route('admin-referral_setting_update_histories.index');
                }
            }
        }
    }

    public function destroy($enuserid) {
        $user_id = '';
        if (!empty($enuserid)) {
            $user_id = base64_decode($enuserid);
        }
        $userDetails = Testimonial::find($user_id);
        if (empty($userDetails)) {
            return Redirect()->route($this->model . '.index');
        }
        if ($user_id) {
            Testimonial::where('id', $user_id)->delete();
            Session()->flash('flash_notice', trans("Testimonial has been removed successfully."));
        }
        return back();
    }

    public function changeStatus(Request $request)
    {
		$status = $request->input('status');
		$referal_id = $request->input('referal_id');
        if ($status == 'on' || $status == '1') {
            $status = 1;
            $statusMessage = trans("Referal has been activated successfully");
        } else {
            $status = 0;
            $statusMessage = trans("Referal has been deactivated successfully");
        }
		ReferralSettingUpdateHistory::whereNull("referral_setting_update_histories.updated_referal_id")->update(array('status' =>0 ));
        $referal = ReferralSettingUpdateHistory::where('id',$referal_id)->first();
		if ($referal) {
            $oldStatus = $referal->status;
            $referal->status = $status;
            $ResponseStatus = $referal->save();
			
			$obj                            = new ReferralSettingUpdateHistory;
			$obj->updated_referal_id        = $referal->id;
			$obj->receiver_amount           = $referal->receiver_amount;
			$obj->sender_amount             = $referal->sender_amount;
			$obj->ip                        = $request->ip();
			$obj->created_by                = $request->user()->id;
			$obj->updated_by                = $request->user()->id;
			$obj->created_at                = time();
			$obj->updated_at                = time();
			$obj->status = $oldStatus;
			$obj->save();
		}
        Session()->flash('flash_notice', $statusMessage);
        return back();
    }

}
