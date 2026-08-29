<?php

namespace App\Http\Controllers\Admin;

use App\Models\WholesaleEnquiry;
use App\Models\FranchiseEnquiry;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class WholesaleEnquiryController extends Controller
{

    public function wholesaleEnquiries(Request $request)
    {


        $DB = WholesaleEnquiry::orderBy('id', 'desc');
        if ($request->all()) {
            $searchData            =    $request->all();
            unset($searchData['display']);
            unset($searchData['_token']);

            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('wholesale_enquiries.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('wholesale_enquiries.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('wholesale_enquiries.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("wholesale_enquiries.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "phone") {
                        $DB->where("wholesale_enquiries.phone", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "email") {
                        $DB->where("wholesale_enquiries.email", 'like', '%' . $fieldValue . '%');
                    }
                }
            }
        }
        $wholesaleEnquiries =  $DB->orderBy('id', 'desc')->simplePaginate(10);

        return view('admin.wholesale-enquiry', compact('wholesaleEnquiries'));
    }

    public function wholesaleEnquiriesupdate(Request $request, $id)
    {


        $subscriber = WholesaleEnquiry::findOrFail($id);
        $subscriber->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'company_name' => $request->company_name,
            'city' => $request->city,
            'gst_number' => $request->gst_number,
            'message' => $request->message
        ]);

        return response()->json(['status' => 'success', 'message' => 'Data updated successfully!']);
    }

    public function wholesaleEnquiriesdestroy($id)
    {
        $subscriber = WholesaleEnquiry::findOrFail($id);
        $subscriber->delete();

        return response()->json(['status' => 'success', 'message' => 'Subscriber deleted successfully!']);
    }


    /////////////////franchiseEnquiries//////////////////////

    public function franchiseEnquiries(Request $request)
    {
        $DB = FranchiseEnquiry::orderBy('id', 'desc');
        if ($request->all()) {
            $searchData            =    $request->all();
            unset($searchData['display']);
            unset($searchData['_token']);

            if ((!empty($searchData['date_from'])) && (!empty($searchData['date_to']))) {
                $dateS = $searchData['date_from'];
                $dateE = $searchData['date_to'];
                $DB->whereBetween('franchise_enquiries.created_at', [$dateS . " 00:00:00", $dateE . " 23:59:59"]);
            } elseif (!empty($searchData['date_from'])) {
                $dateS = $searchData['date_from'];
                $DB->where('franchise_enquiries.created_at', '>=', [$dateS . " 00:00:00"]);
            } elseif (!empty($searchData['date_to'])) {
                $dateE = $searchData['date_to'];
                $DB->where('franchise_enquiries.created_at', '<=', [$dateE . " 00:00:00"]);
            }
            foreach ($searchData as $fieldName => $fieldValue) {
                if ($fieldValue != "") {
                    if ($fieldName == "name") {
                        $DB->where("franchise_enquiries.name", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "phone") {
                        $DB->where("franchise_enquiries.phone", 'like', '%' . $fieldValue . '%');
                    }
                    if ($fieldName == "email") {
                        $DB->where("franchise_enquiries.email", 'like', '%' . $fieldValue . '%');
                    }
                }
            }
        }
        $franchiseEnquiries =  $DB->orderBy('id', 'desc')->simplePaginate(10);


        return view('admin.franchise_enquiry', compact('franchiseEnquiries'));
    }

    public function franchiseEnquiriesupdate(Request $request, $id)
    {


        $subscriber = FranchiseEnquiry::findOrFail($id);
        $subscriber->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'space' => $request->space,
            'city' => $request->city,
            'investment' => $request->investment,
            'message' => $request->message
        ]);

        return response()->json(['status' => 'success', 'message' => 'Franchise Enquiry updated successfully!']);
    }

    public function franchiseEnquiriesdestroy($id)
    {
        $subscriber = FranchiseEnquiry::findOrFail($id);
        $subscriber->delete();

        return response()->json(['status' => 'success', 'message' => 'Franchise Enquiries deleted successfully!']);
    }

    public function contactEnquiries()
    {

        $contactEnquiries = Contact::orderBy('id', 'desc')->simplePaginate(10);

        return view('admin.contact_enquiry', compact('contactEnquiries'));
    }

    public function contactEnquiriesupdate(Request $request, $id)
    {


        $subscriber = Contact::findOrFail($id);
        $subscriber->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message
        ]);

        return response()->json(['status' => 'success', 'message' => 'Franchise Enquiry updated successfully!']);
    }

    public function contactEnquiriesdestroy($id)
    {
        $subscriber = Contact::findOrFail($id);
        $subscriber->delete();

        return response()->json(['status' => 'success', 'message' => 'Franchise Enquiries deleted successfully!']);
    }
}
