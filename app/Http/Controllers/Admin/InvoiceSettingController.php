<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoiceSetting;
use  App\Models\Setting;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class InvoiceSettingController extends Controller
{
    public function getStates($country)
    {
        $states = State::where('country_id', $country)->get();
        return response()->json($states);
    }

    public function getCities($state)
    {
        $cities = City::where('state_id', $state)->get();
        return response()->json($cities);
    }

    public function create()
    {
        $countries = Country::all();
        $invoiceSetting = null;
        return view('admin.settings.invoice.create', compact('countries', 'invoiceSetting'));
    }

    public function store(Request $request)
    {


        $data = $request->input('invoice'); // sab form fields "invoice[]" ke andar hain
        // validation
        $request->validate([
            'invoice.country_id' => 'nullable|integer',
            'invoice.state_id'   => 'nullable|integer',
            'invoice.city_id'    => 'nullable|integer',
            'invoice.pincode'    => 'nullable|string|max:50',
            'invoice.address'    => 'nullable|string|max:500',
            'invoice.name'       => 'nullable|string|max:255',
            'invoice.nature_spilly' => 'nullable|string|max:255',
            'invoice.invoice_number' => 'nullable|string|max:255',
            'invoice.packet_id'  => 'nullable|string|max:255',
            'invoice.website_name' => 'nullable|string|max:255',
            'invoice.signature'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'invoice.designation' => 'nullable|string|max:255',
            'invoice.note'       => 'nullable|string',
            'invoice.is_active'     => 'nullable|in:1,0',
            'invoice.invoice_setting' => 'nullable|string|max:255',
        ]);

        // Handle file upload for signature
        if ($request->hasFile('invoice.signature')) {
            $signatureFile = $request->file('invoice.signature');
            $filename = time() . '_' . $signatureFile->getClientOriginalName();
            $folderPath = Config('constant.SIGNATURE_IMAGE_ROOT_PATH');
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0777, true);
            }
            if ($signatureFile->move($folderPath, $filename)) {
                $data['signature'] = 'signatures/' . $filename;
            }
        }

        // save/update (agar ek hi row store karni hai to updateOrCreate use karna)
        $invoiceSetting = new InvoiceSetting();
        foreach ($data as $key => $value) {
            $invoiceSetting->$key = $value;
        }
        $invoiceSetting->save();

        return redirect()->route('admin-admin-admin.invoice-settings.create')->with('success', 'Invoice settings saved successfully!');
    }


    public function index()
    {
        $invoiceSettings = InvoiceSetting::with('country', 'state', 'city')->paginate(10);
        return view('admin.settings.invoice.index', compact('invoiceSettings'));
    }

    public function edit($id)
    {
        $invoiceSetting = InvoiceSetting::findOrFail($id);
        $countries = Country::all();
        return view('admin.settings.invoice.edit', compact('invoiceSetting', 'countries'));
    }

    public function update(Request $request, $id)
    {
        prx($id);
        $request->validate([
            'invoice.country_id' => 'nullable|integer',
            'invoice.state_id'   => 'nullable|integer',
            'invoice.city_id'    => 'nullable|integer',
            'invoice.pincode'    => 'nullable|string|max:50',
            'invoice.address'    => 'nullable|string|max:500',
            'invoice.name'       => 'nullable|string|max:255',
            'invoice.nature_spilly' => 'nullable|string|max:255',
            'invoice.invoice_number' => 'nullable|string|max:255',
            'invoice.packet_id'  => 'nullable|string|max:255',
            'invoice.website_name' => 'nullable|string|max:255',
            'invoice.signature'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'invoice.designation' => 'nullable|string|max:255',
            'invoice.note'       => 'nullable|string',
            'invoice.is_active'     => 'nullable|in:1,0',
            'invoice.invoice_setting' => 'nullable|string|max:255',
        ]);

        $data = $request->input('invoice');
        $invoiceSetting = InvoiceSetting::findOrFail($id);

        // Handle file upload for signature
        if ($request->hasFile('invoice.signature')) {
            // Delete old signature file if exists
            if ($invoiceSetting->signature && file_exists(Config('constant.SIGNATURE_IMAGE_ROOT_PATH') . basename($invoiceSetting->signature))) {
                unlink(Config('constant.SIGNATURE_IMAGE_ROOT_PATH') . basename($invoiceSetting->signature));
            }
            $signatureFile = $request->file('invoice.signature');
            $filename = time() . '_' . $signatureFile->getClientOriginalName();
            $folderPath = Config('constant.SIGNATURE_IMAGE_ROOT_PATH');
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0777, true);
            }
            if ($signatureFile->move($folderPath, $filename)) {
                $data['signature'] = 'signatures/' . $filename;
            }
        }

        foreach ($data as $key => $value) {
            $invoiceSetting->$key = $value;
        }

        $invoiceSetting->save();

        return redirect()->route('admin.invoice.settings.index')->with('success', 'Invoice settings updated successfully!');
    }


    public function destroy($id)
    {
        $invoiceSetting = InvoiceSetting::findOrFail($id);
        $invoiceSetting->delete();
        return redirect()->route('admin.invoice.settings.index')->with('success', 'Invoice settings deleted successfully!');
    }
}
