<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Tax;
use App\Models\Variant;
use Redirect, Response;
use App\Models\Category;
use App\Models\CategoryTax;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Specification;
use App\Exports\CategoryExport;
use App\Models\CategoryVariant;
use App\Models\SizeChartTebular;
use App\Services\CategoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
// use Redirect,DB,Response;
use App\Models\CategorySpecification;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Models\{SizeChartTebularContent, Attribute, CategoryAttribute};

class CategoryController extends Controller
{

    protected $categoryService;

    public $model = 'category';
    public $listRouteName;
    public $request;

    public function __construct(Request $request, CategoryService $categoryService)
    {
        // $this->middleware('permission:view_category|create_category|edit_category|delete_category', ['only' => ['index', 'show']]);
        // $this->middleware('permission:create_category', ['only' => ['create', 'store']]);
        // $this->middleware('permission:edit_category', ['only' => ['edit', 'update', 'changeStatus']]);
        // $this->middleware('permission:delete_category', ['only' => ['destroy']]);

        $this->listRouteName = 'admin-category.index';
        View()->share('model', $this->model);
        View()->share('listRouteName', $this->listRouteName);

        $this->request = $request;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request, CategoryService $categoryService)
    {
        try {
            $data = $categoryService->getFilteredCategories($request);
            $results = $data['results'];
            $totalResults = $data['totalResults'];

            if ($request->ajax()) {
                return view("admin.$this->model.load_more_data", compact('results', 'totalResults'));
            } else {
                return view("admin.$this->model.index", compact('results', 'totalResults'));
            }
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with([
                'error' => 'Something is wrong',
                'error_msg' => $e->getMessage()
            ]);
        }
    }

    public function create()
    {
        $variants   = Variant::active()->notDeleted()->select('id', 'name')->get();
        $attributes = Attribute::active()->notDeleted()->select('id', 'name')->get();
        $taxes = Tax::where('tax_option', 'inclusive')->where('tax_type', 'flat')->where('is_active', 1)->select('id', 'tax_from', 'tax_to', 'tax_rate')->get();
        $specifications = Specification::active()->notDeleted()
            ->with('group')
            ->get()
            ->mapWithKeys(function ($spec) {
                return [$spec->id => "{$spec->group->name} > {$spec->name}"];
            });
        $chart_content = SizeChartTebularContent::first();
        $nextPriority = (Category::max('priority') ?? 0) + 1;

        return view('admin.category.create', compact('variants', 'attributes', 'taxes', 'specifications', 'chart_content', 'nextPriority'));
    }

    public function store(CreateCategoryRequest $request, CategoryService $service)
    {
        $params = [];
        if (isset($request->type)) {
            $params['type'] = $request->type;
        }

        if (isset($request->parent_id)) {
            $params['endesid'] = $request->parent_id;
        }

        $result = $service->saveCategory($request->alL());
        if (isset($result['error'])) {
            return back()->withInput()->withErrors(['error' => $result['error']]);
        }
        return redirect()->route('admin-category.index', $params)->with('success', 'Category created successfully');
    }

    public function update(CreateCategoryRequest $request, $token)
    {
        try {
            $params = [];
            if (isset($request->type)) {
                $params['type'] = $request->type;
            }

            if (isset($request->parent_id)) {
                $params['endesid'] = $request->parent_id;
            }
            $id = base64_decode($token);
            $result = $this->categoryService->saveCategory($request->all(), $id);
            if (isset($result['error'])) {
                return back()->withInput()->withErrors(['error' => $result['error']]);
            }

            return redirect()->route('admin-category.index', $params)->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit(Request $request, $token = null)
    {
        // try {
        if (empty($token)) {
            return redirect()->back()->with(['error' => 'Invalid category token.']);
        }

        $categoryId = base64_decode($token);
        $category = Category::find($categoryId);

        if (!$category) {
            return redirect()->back()->with(['error' => 'Category not found.']);
        }

        // Master data
        $variants       = Variant::active()->select('id', 'name')->get();
        $attributes     = Attribute::active()->select('id', 'name')->get();

        // Specifications with group name
        $specifications = Specification::where('specifications.is_active', 1)
            ->leftJoin('specification_groups', 'specifications.specification_group_id', '=', 'specification_groups.id')
            ->select('specifications.id', DB::raw("CONCAT(specification_groups.name, ' > ', specifications.name) as name"))
            ->get();

        // Category relations
        $categoryVariants       = CategoryVariant::where('category_id', $categoryId)->pluck('variant_id')->toArray();
        $categorySpecifications = CategorySpecification::where('category_id', $categoryId)->pluck('specification_id')->toArray();
        $categoryTaxes          = CategoryTax::where('category_id', $categoryId)->pluck('tax_id')->toArray();
        $categoryTaxData        = CategoryTax::where('category_id', $categoryId)->select('tax_option', 'tax_type', 'tax_id')->first();
        $categoryTaxesValues = [];
        if ($categoryTaxData != null) {
            $categoryTaxesValues = $categoryTaxData->toArray();
        }
        $categoryAttribute      = CategoryAttribute::where('category_id', $categoryId)->pluck('attribute_id')->toArray();
        $taxes = Tax::where('tax_option', @$categoryTaxesValues['tax_option'])->where('tax_type', @$categoryTaxesValues['tax_type'])->where('is_active', 1)->select('id', 'tax_from', 'tax_to', 'tax_rate')->get();

        // Size chart
        $chart_measurement      = SizeChartTebular::where('category_id', $categoryId)->first();
        return view("admin.$this->model.edit", compact(
            'category',
            'categoryVariants',
            'categorySpecifications',
            'categoryTaxes',
            'categoryTaxesValues',
            'categoryAttribute',
            'variants',
            'taxes',
            'specifications',
            'attributes',
            'chart_measurement',
        ));
        // } catch (\Exception $e) {
        //     Log::error($e);
        //     return redirect()->back()->with(['error' => 'Something went wrong', 'error_msg' => $e->getMessage()]);
        // }
    }


    public function destroy($token)
    {
        try {
            $categoryId = !empty($token) ? base64_decode($token) : null;

            if (!$categoryId || !($category = Category::find($categoryId))) {
                return redirect()->route($this->model . '.index')
                    ->with('error', 'Category not found.');
            }

            // Soft delete category
            // $category->update([
            //     'is_deleted' => 1,
            //     'slug' => null,
            // ]);

            $category->delete();
            // Delete related records using relationships
            $category->variants()->delete();
            $category->attributes()->delete();
            $category->taxes()->delete();

            // Flash message
            session()->flash('flash_notice', trans('Category has been removed successfully.'));
            return redirect()->back();
        } catch (Exception $e) {
            Log::error('Category Deletion Error: ' . $e->getMessage());

            return redirect()->back()->with([
                'error' => 'Something went wrong.',
                'error_msg' => $e->getMessage()
            ]);
        }
    }


    public function changeStatus($modelId = 0, $status = 0)
    {
        $category = Category::find($modelId);
        if (!$category) {
            Session()->flash('flash_error', trans("Category not found."));
            return back();
        }

        $category->is_active = $status ? 1 : 0;
        $category->save();

        $message = $status
            ? trans("Category has been activated successfully.")
            : trans("Category has been deactivated successfully.");

        Session()->flash('flash_notice', $message);
        return back();
    }


    function updateCategoryOrder(Request $request)
    {
        $requestOrder    =    $request->input("requestData");

        if (!empty($requestOrder)) {
            foreach ($requestOrder as $category_order) {
                Category::where("id", $category_order["id"])->update(array("category_order" => $category_order["order"]));
            }
        }
        die;
    }


    public function managePriority(Request $request, $position = null)
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_deleted', 0)
            ->orderBy('priority', 'asc')
            ->get();

        return view('admin.category.manage_priority', compact('categories', 'position'));
    }

    public function updatePriority(Request $request)
    {
        try {
            $order = $request->input('order');
            Log::info('Received order: ' . json_encode($order));

            DB::transaction(function () use ($order) {
                Category::whereIn('id', $order)->update(['priority' => null]);
                foreach ($order as $index => $categoryId) {
                    $category = Category::find($categoryId);
                    if ($category) {
                        $category->priority = $index + 1;
                        $category->save();
                    }
                }
            });

            return response()->json(['status' => 'success', 'message' => 'Priority updated successfully']);
        } catch (\Exception $e) {
            Log::error('Priority Update Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
        }
    }

    public function getTaxRateList(Request $request)
    {
        try {
            $tax_type = $request->tax_type;
            $tax_option = $request->tax_option;
            $taxes = Tax::where('tax_option', $tax_option)->where('tax_type', $tax_type)->where('is_active', 1)->select('id', 'tax_from', 'tax_to', 'tax_rate')->get();
            return response()->json(['data' => $taxes, 'success' => true, 'message' => 'Data fetched'], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'Something is wrong', 'success' => false, 'error_msg' => $e->getMessage()], 500);
        }
    }

    public function exportCategory()
    {
        return Excel::download(new CategoryExport, 'Category.xlsx');
    }

    public function updateCategoryStatus(Request $request)
    {
        $model = Category::find($request->id);
        if ($model && in_array($request->field, ['is_active', 'show_on_home', 'show_on_menu', 'whats_new', 'shop_by_purpose', 'explore_by_category'])) {
            $model->{$request->field} = $request->value;
            $model->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function deleteCategoryImage(Request $request)
    {
        $category = Category::where('id', $request->category_id)->first();
        if($request->type == 'image'){
            $imagePath = public_path('uploads/category/' . $category->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            $category->image = null;
        }else {
            $imagePath = public_path('uploads/category/' . $category->thumbnail_image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            $category->thumbnail_image = null;
        }
        $category->save();
        

        return response()->json([
            'status' => true,
            'message' => 'Image deleted successfully'
        ]);
    }
}
