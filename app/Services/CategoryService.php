<?php
namespace App\Services;

use App\Models\{Category,SizeChartTebularContent,SizeChartTebular};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Exception;

class CategoryService
{

    public function getFilteredCategories(\Illuminate\Http\Request $request)
    {
       
        if($request->type == "sub-category" || $request->type == "child-category"){
            $query = Category::with('getUser')->where('parent_id',base64_decode($request->endesid))->where('is_deleted', 0); 
        }else{
            $query = Category::with('getUser')->whereNull('parent_id')->where('is_deleted', 0); 
        }
       
        
        // $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'categories.priority';
        $sortBy = $request->input('sortBy') ? $request->input('sortBy') : 'categories.category_order';
        $order = $request->input('order') ? $request->input('order') : 'ASC';
        $offset = !empty($request->input('offset')) ? $request->input('offset') : 0;
        $limit = !empty($request->input('limit')) ? $request->input('limit') : config('Reading.records_per_page');

       
        // Apply specific field filters
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        // Get the total count before applying limit and offset for accurate pagination
        $totalResults = $query->count();

        // Apply ordering and pagination
        $results = $query->orderBy($sortBy, $order)
                        ->offset($offset)
                        ->limit($limit)
                        ->get();
        return compact('results', 'totalResults');
    }


    public function saveCategory(array $data, int $id = null)
    {
        // DB::beginTransaction();
        // try {
            $category = $id ? Category::findOrFail($id) : new Category();

            $slug = Str::slug(Str::lower($data['name']));
            $duplicateSlug = Category::where('slug', $slug)->when($id, fn($q) => $q->where('id', '!=', $id))->exists();
            if ($duplicateSlug) {
                return ['error' => 'Slug already exists'];
            }

            if ($id = null && !isset($data['parent_id']) && empty($data['variantsData']) ) {
                return ['error' => 'At Least one varient parameter compulsary'];
            }

            if ($id = null && !isset($data['parent_id']) && empty($data['attributesData']) ) {
                return ['error' => 'At Least one attribute  parameter  compulsary'];
            }

            if($id = null && !isset($data['parent_id']) &&  (empty($data['tax_rate']) ||  empty($data['tax_option']) || empty($data['tax_type']))){ 
                return ['error' => 'At Least one tax parameter compulsary'];
            }

            $category->fill([
                'name'              => $data['name'],
                'slug'              => $slug,
                'parent_id'         => isset($data['parent_id']) ? base64_decode($data['parent_id']) : null,
                'category_type_id'  => isset($data['select_category_type']) ? $data['select_category_type'] : 2,
                'style_type'  => isset($data['style_type']) ? $data['style_type'] : 2,
                'priority'          => $data['priority'] ?? null,
                'description'       => $data['description'] ?? null,
                'meta_title'        => $data['meta_title'] ?? null,
                'meta_description'  => $data['meta_description'] ?? null,
                'meta_keywords'     => $data['meta_keywords'] ?? null,
                'seo_description'   => $data['seo_description'] ?? null,
                'value'             => $data['seo_data'] ?? null,
                'width'             => $data['width'] ?? null,
                'height'            => $data['height'] ?? null,
                'uppar_chart_title' => $data['uppar_chart_title'] ?? null,
                'bootom_chart_title'=> $data['bootom_chart_title'] ?? null,
                'chart_title'       => $data['chart_title'] ?? null,
                'chart_description' => $data['chart_description'] ?? null,
                //'show_on_home' => $data['show_on_home'] ?? 1,
                //'show_on_menu' => $data['show_on_menu'] ?? 1,
                //'whats_new' => $data['whats_new'] ?? 1,
                //'shop_by_purpose' => $data['shop_by_purpose'] ?? 1,
                //'explore_by_category' => $data['explore_by_category'] ?? 1,
                //'is_active' => $data['is_active'] ?? 1,
                'url'       => $data['url'] ?? null,
            ]);


            // Handle file uploads
            $fileFields = [
                'image'               => 'CATEGORY_IMAGE_ROOT_PATH',
                'image2'              => 'CATEGORY_IMAGE_ROOT_PATH',
                'thumbnail_image'     => 'CATEGORY_IMAGE_ROOT_PATH',
                'video'               => 'CATEGORY_VIDEO_ROOT_PATH',
                'uppar_chart_image'   => 'CATEGORY_IMAGE_ROOT_PATH',
                'bottom_chart_image'  => 'CATEGORY_IMAGE_ROOT_PATH',
            ];

            foreach ($fileFields as $field => $pathConst) {
                if (!empty($data[$field])) {
                    $category->$field = $this->uploadFile($data[$field], $pathConst);  
                }
            }

            $category->save();

            // Sync relationships (delete old first if update)
            if ($id) {
                $category->variants()->delete();
                $category->attributes()->delete();
                $category->specifications()->delete();
                $category->taxes()->delete();
            }

            $this->attachVariants($category, $data['variantsData'] ?? []);
            $this->attachAttributes($category, $data['attributesData'] ?? []);
            $this->attachSpecifications($category, $data['specificationsData'] ?? []);
            // $this->attachTaxes($category, $data['tax_counts'] ?? []);
            if(!empty($data['tax_rate']) && !empty($data['tax_option']) && !empty($data['tax_type'])){
                $this->attachTaxes($category, $data['tax_rate'] ?? [], $data['tax_option'], $data['tax_type']);
            }
            $this->updateSizeChart($category, $data);

            DB::commit();
            return ['success' => true];

        // } catch (\Exception $e) {
        //     dd('ddsd');
        //     DB::rollBack();
        //     return ['error' => $e->getMessage()];
        // }
    }


    private function uploadFile($file, $configPath)
    {
        if($file){
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension;
            $folder = strtoupper(date('M') . date('Y')) . '/';
            $folderPath = config("constant.{$configPath}") . $folder;

        
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0777, true);
            }

            $file->move($folderPath, $fileName);
            return $folder . $fileName;
        }
    }

    private function attachVariants($category, $variants)
    {
        $category->variants()->delete();
        foreach ($variants as $variantId) {
            $category->variants()->create(['variant_id' => $variantId]);
        }
    }

    private function attachAttributes($category, $attributes)
    {
        $category->attributes()->delete();
        foreach ($attributes as $attributeId) {
            $category->attributes()->create(['attribute_id' => $attributeId]);
        }
    }

    private function attachSpecifications($category, $specifications)
    {
        $category->specifications()->delete();
        foreach ($specifications as $specId) {
            $category->specifications()->create(['specification_id' => $specId]);
        }
    }

    /*private function attachTaxes($category, $taxes)
    {
        $category->taxes()->delete();
        foreach ($taxes as $taxId => $value) {
            $category->taxes()->create([
                'tax_id' => $taxId,
                'tax_value' => $value
            ]);
        }
    }*/
    
    private function attachTaxes($category, $taxes, $tax_option, $tax_type)
    {
        $category->taxes()->delete();
        foreach ($taxes as $taxId => $value) {
            $category->taxes()->create([
                'tax_id' => $value,
                'tax_option' => $tax_option,
                'tax_type' => $tax_type
            ]);
        }
    }

    private function updateSizeChart(Category $category, array $data)
    {
        $measurementType = $data['mesurement_type'] ?? 'inch';
        $sizeData = ['upper' => [], 'bottom' => []];

        $prefix = $measurementType === 'inch' ? '' : '_cm';

        foreach (['upper', 'bottom'] as $section) {
            $sectionKey = $section == "upper" ? 'top' : $section;
            $typeKey = "{$sectionKey}_type{$prefix}";
            foreach ($data[$typeKey] ?? [] as $i => $type) {
                $sizeData[$section][$type] = [
                    'xs' => $data["{$sectionKey}_size{$prefix}_xs"][$i] ?? 0,
                    's' => $data["{$sectionKey}_size{$prefix}_s"][$i] ?? 0,
                    'm' => $data["{$sectionKey}_size{$prefix}_m"][$i] ?? 0,
                    'l' => $data["{$sectionKey}_size{$prefix}_l"][$i] ?? 0,
                    'xl' => $data["{$sectionKey}_size{$prefix}_xl"][$i] ?? 0,
                    '2xl' => $data["{$sectionKey}_size{$prefix}_2xl"][$i] ?? 0,
                    'is_active' => 1,
                ];
            }
        }

        // Save category-specific size chart
        $chart = SizeChartTebular::firstOrNew(['category_id' => $category->id]);
        $chart->{"measurement_{$measurementType}"} = json_encode($sizeData);
        $chart->save();
    }

}
