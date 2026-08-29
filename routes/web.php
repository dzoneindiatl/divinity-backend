<?php

use App\Models\ChildCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\VariantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouriersController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MainProductController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\FrontendMenuController;
use App\Http\Controllers\Admin\NewSizeChartController;
use App\Http\Controllers\Admin\ChildCategoryController;
use App\Http\Controllers\Admin\ProductValuesController;
use App\Http\Controllers\Admin\FooterCategoryController;
use App\Http\Controllers\Admin\ProductOptionsController;
use App\Http\Controllers\Admin\WholesaleEnquiryController;
use App\Http\Controllers\Admin\FooterSubCategoryController;
use App\Http\Controllers\Admin\ProductCollectionController;
use App\Http\Controllers\Admin\{SizeChartTebularController, CouponController};
use App\Http\Controllers\Admin\PincodesController;
use App\Http\Controllers\Admin\ProductImportController;
use App\Models\State;
use App\Models\City;



Route::get('/', [AuthController::class, 'login']);

Route::prefix('')->name('admin-')->group(function () {

    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/verify-login', [AuthController::class, 'verifyLogin'])->name('verify-login');

    Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');


    Route::middleware('auth')->group(function () {
        Route::resource('roles', RoleController::class);

        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('dashboard/order-items-data', [DashboardController::class, 'getOrderItemsData'])->name('dashboard.order-items-data');
        Route::get('subscribe', [DashboardController::class, 'subscribe'])->name('subscribe');
        // routes/web.php
        Route::post('/subscribes/update/{id}', [DashboardController::class, 'updatesubscribe'])->name('admin-subscribes.update');
        Route::delete('/subscribes/delete/{id}', [DashboardController::class, 'destroysubscribe'])->name('admin-subscribes.delete');


        Route::get('wholesale-enquiries', [WholesaleEnquiryController::class, 'wholesaleEnquiries'])->name('wholesaleEnquiries');
        // routes/web.php
        Route::post('/wholesale-enquiries/update/{id}', [WholesaleEnquiryController::class, 'wholesaleEnquiriesupdate'])->name('wholesaleEnquiries.update');
        Route::delete('/wholesale-enquiries/delete/{id}', [WholesaleEnquiryController::class, 'wholesaleEnquiriesdestroy'])->name('wholesaleEnquiries.delete');

        Route::get('franchise-enquiries', [WholesaleEnquiryController::class, 'franchiseEnquiries'])->name('franchiseEnquiries');
        // routes/web.php
        Route::post('/franchise-enquiries/update/{id}', [WholesaleEnquiryController::class, 'franchiseEnquiriesupdate'])->name('franchiseEnquiries.update');
        Route::delete('/franchise-enquiries/delete/{id}', [WholesaleEnquiryController::class, 'franchiseEnquiriesdestroy'])->name('franchiseEnquiries.delete');

        Route::get('contact-enquiries', [WholesaleEnquiryController::class, 'contactEnquiries'])->name('contactEnquiries');
        // routes/web.php
        Route::post('/contact-enquiries/update/{id}', [WholesaleEnquiryController::class, 'contactEnquiriesupdate'])->name('contactEnquiries.update');
        Route::delete('/contact-enquiries/delete/{id}', [WholesaleEnquiryController::class, 'contactEnquiriesdestroy'])->name('contactEnquiries.delete');
        //Route::get('/dashboard', [DashboardController::class, 'showHeaderNotifications'])->name('dashboard');
        // Route to mark notification as read
        Route::put('/notifications/{id}/read', [DashboardController::class, 'markAsReadAjax'])->name('notification.read.ajax');

        Route::prefix('product')->name('product-')->group(function () {

            // Jay Routes
            Route::get('review/{token}', [ProductController::class, 'review'])->name('review');
            Route::get('reviewstatus/{id}/{status}', [ProductController::class, 'changeStatusReview'])->name('reviewstatus');
            Route::get('reviewedit/{id}', [ProductController::class, 'editReview'])->name('reviewedit');
            Route::post('reviewupdate/{reviewId}/{productId}', [ProductController::class, 'updateReview'])->name('reviewupdate');
            Route::get('reviewdelete/{reviewId}/{productId}', [ProductController::class, 'reviewDelete'])->name('reviewdelete');
            Route::post('/receive-data', function (\Illuminate\Http\Request $request) {
                $formdata = $request->all();

                $controller = app(ProductController::class);

                $vaientvaluedata = $controller->retviewData($formdata['newProductid']);

                return $html = view('admin.product_new.receivedata', compact('formdata', 'vaientvaluedata'))->render();
            })->name('receivedata');

            Route::get('details/{token}', [ProductController::class, 'productDetails'])->name('details');

            Route::get('details/{token}', [ProductController::class, 'productDetails'])->name('details');

            Route::post('/update-order', [ProductController::class, 'updateOrder'])->name('update-order');
            Route::post('/update.product.qty', [ProductController::class, 'updateQty'])->name('update.product.qty');
            Route::post('/update.product.feature', [ProductController::class, 'updateFeature'])->name('update.product.feature');
            Route::get('get-varient/{productId}', [ProductController::class, 'getVarient'])->name('get-varient');

            Route::any('updatedata', [ProductController::class, 'updatedata'])->name('updatedata');
            Route::any('create-new', [ProductController::class, 'create_new'])->name('create_new');
            Route::any('create-new-demo', [ProductController::class, 'create_new_demo'])->name('create_new_demo');
            Route::match(['get', 'post'], 'upload-images-new', [ProductController::class, 'uploadImagesNew'])->name('upload-images-new');
            Route::get('delete-image-new', [ProductController::class, 'deleteImageNew'])->name('delete-image-new');
            Route::get('delete-item', [ProductController::class, 'deleteItem'])->name('delete-item');

            Route::delete('delete-image', [ProductController::class, 'productdeleteImage'])->name('delete-image-new');

            Route::post('update-item', [ProductController::class, 'updateItem'])->name('update-item');
            Route::get('update-image-data', [ProductController::class, 'updateImageData'])->name('update-image-data');
            Route::post('get-image-data', [ProductController::class, 'getImageData'])->name('get-image-data');
            Route::post('variant-add', [VariantController::class, 'add_variant'])->name('variant-add');
            Route::any('variant-values', [VariantController::class, 'getVariantValues'])->name('variant-values');
            Route::any('get-variants', [ProductController::class, 'getVariants'])->name('get-variants');


            Route::any('attribute-values', [ProductController::class, 'getAttributeValues'])->name('attribute-values');
            Route::post('save-attribute', [ProductController::class, 'saveAttribute'])->name('save-attribute');
            Route::post('attribute-add', [ProductController::class, 'add_attribute'])->name('attribute-add');
            Route::get('ajaxsubcategory', [ProductController::class, 'ajaxsubcategory'])->name('ajax-subcategory');
            Route::get('ajaxgetproduct', [ProductController::class, 'ajaxgetproduct'])->name('ajax-getproduct');
            Route::get('ajaxgetchildcategory', [ProductController::class, 'ajaxgetchildcategory'])->name('ajax-getchildcategory');
            Route::get('ajaxgetCategoriesVarient', [ProductController::class, 'ajaxgetCategoriesVarient'])->name('ajax-getCategoriesVarient');
            Route::get('ajaxgetCategoriesAttribute', [ProductController::class, 'ajaxgetCategoriesAttribute'])->name('ajax-getCategoriesAttributes');

            Route::get('ajaxgetrelatedsubcategories', [ProductController::class, 'ajaxgetrelatedsubcategories'])->name('ajax-getrelatedsubcategories');
            Route::any('/product-gallery-img-delete', [ProductController::class, 'product_gallery_img_delete'])->name('productgalleryimgdelete');
            Route::get('copy/{id}', [ProductController::class, 'copy'])->name('copy');
            Route::get('get-attributes', [ProductController::class, 'getAttributes'])->name('get-attributes');
            Route::get('getVariantReleatedProduct', [ProductController::class, 'getVariantReleatedProduct'])->name('ajax-getVariantReleatedProduct');

            // End



            Route::post('store/step1', [MainProductController::class, 'saveStep1'])->name('save.step1');
            Route::post('previous/step',[MainProductController::class, 'previousStep'])->name('previousStep');
            Route::post('get-variant-record',[MainProductController::class,'getProductCategoryVariant'])->name('get-variant-record'); 
            Route::post('store/step2', [MainProductController::class, 'saveStep2'])->name('save.step2');
            Route::post('store/step3', [MainProductController::class, 'saveStep3'])->name('save.step3');
            Route::post('store/step4', [MainProductController::class, 'saveStep4'])->name('save.step4');

            Route::post('product/update-front-back-icon', [MainProductController::class, 'updateFrontBackIcon'])->name('updateFrontBackIcon');
            Route::post('product/file/delete', [MainProductController::class, 'fileDelete'])->name('fileDelete');
            Route::post('product/variant/stock-toggle', [MainProductController::class, 'toggleStock'])->name('variant.stock.toggle');
            Route::get('prices/{id}', [MainProductController::class, 'priceEdit'])->name('price.edit');
            Route::post('prices/update', [MainProductController::class, 'updateVariantsPrices'])->name('product.prices.update');
            Route::post('product/update-order', [MainProductController::class, 'updateProductOrder'])->name('product.updateProductOrder');


            Route::match(['get', 'post'], 'list', [ProductController::class, 'index'])->name('list');
            //Route::any('create-new-product', [MainProductController::class, 'createNewProduct'])->name('create-new-product');
            Route::get('create-new-product/{token?}', [MainProductController::class, 'addNewProduct'])->name('create-new-product');
            //Route::get('add-new-product', [MainProductController::class, 'addNewProduct'])->name('add-new-product');
            Route::any('save-product/{product_id?}', [MainProductController::class, 'store'])->name('save-new-product');
            Route::get('get-product-variant-details/{id}', [MainProductController::class, 'getProductVariantDetails'])->name('get-product-variant-details');
            Route::post('update-product-variant-details', [MainProductController::class, 'UpdateProductVariantDetails'])->name('update-product-variant-details');
            Route::post('upload-product-images/{product_id}/{graphic_type}', [MainProductController::class, 'uploadProductImages'])->name('upload-product-images');
            Route::post('upload-product-variant-images/{product_id}/{variant_id}/{graphic_type}', [MainProductController::class, 'uploadProductVariantGraphics'])->name('upload-product-images');
            Route::post('save-new-product', [MainProductController::class, 'saveProduct'])->name('save-product');

            Route::get('create', [ProductController::class, 'create'])->name('create');
            Route::post('store', [ProductController::class, 'store'])->name('store');
            Route::get('view/{token}', [ProductController::class, 'view'])->name('view');

            Route::any('edit/{token}', [ProductController::class, 'edit'])->name('edit');

            //add new
            Route::any('edit-product/{token}', [MainProductController::class, 'editProduct'])->name('edit-product');
            Route::post('/delete-media', [MainProductController::class, 'deleteMedia'])->name('delete-media');
            Route::post('/update-product-image', [MainProductController::class, 'updateProductImage'])->name('update-product-image');
            Route::post('/set-main-variant', [MainProductController::class, 'setMainVariant'])->name('set-main-variant');
            Route::post('update-product-variant-pricing', [MainProductController::class, 'UpdateProductVariantPricing'])->name('update-product-variant-pricing');
            Route::post('update-product-variant-limit', [MainProductController::class, 'UpdateProductVariantLimit'])->name('update-product-variant-limit'); // Add By Mohit



            Route::post('update/{token}', [ProductController::class, 'update'])->name('update');
            Route::get('delete/{token}', [ProductController::class, 'destory'])->name('delete');
            Route::get('sub-category-list', [ProductController::class, 'getSubCategories'])->name('sub-category-list');
            Route::get('sub-category-list-product', [ProductController::class, 'getSubCategoriesforproduct'])->name('sub-category-list-product');

            Route::get('variant-values-list', [ProductController::class, 'getVariantValues'])->name('variant-values-list');
            Route::post('child-category-list', [ProductController::class, 'getChildCategories'])->name('child-category-list');
            Route::match(['get', 'post'], 'upload-images', [ProductController::class, 'uploadImages'])->name('upload-images');
            Route::get('delete-image', [ProductController::class, 'deleteImage'])->name('delete-image');
            Route::get('update-image-meta-values', [ProductController::class, 'updateImageMetaValues'])->name('update-image-meta-values');
            Route::post('update-stock', [ProductController::class, 'updateStock'])->name('update-stock');
            Route::post('update-featured', [ProductController::class, 'updateFeatured'])->name('update-featured');
            Route::post('update-new-arrivals', [ProductController::class, 'updateNewArrivals'])->name('update-new-arrivals');
            Route::post('import', [ProductImportController::class, 'import'])->name('products.import');
            Route::get('/download/excel-format', function () {
                return response()->download(
                    public_path('uploads/excel_format.xlsx')
                );
            });
            Route::prefix('options')->name('options-')->group(function () {
                Route::get('list', [ProductOptionsController::class, 'index'])->name('list');
                Route::get('create', [ProductOptionsController::class, 'create'])->name('create');
                Route::post('store', [ProductOptionsController::class, 'store'])->name('store');
                Route::get('edit/{token}', [ProductOptionsController::class, 'edit'])->name('edit');
                Route::post('update/{token}', [ProductOptionsController::class, 'update'])->name('update');
                Route::delete('delete/{token}', [ProductOptionsController::class, 'destory'])->name('delete');
                });

            Route::prefix('options-values')->name('options-values-')->group(function () {
                Route::get('list', [ProductValuesController::class, 'index'])->name('list');
                Route::get('create', [ProductValuesController::class, 'create'])->name('create');
                Route::post('store', [ProductValuesController::class, 'store'])->name('store');
                Route::get('edit/{token}', [ProductValuesController::class, 'edit'])->name('edit');
                Route::post('update/{token}', [ProductValuesController::class, 'update'])->name('update');
                Route::delete('delete/{token}', [ProductValuesController::class, 'destory'])->name('delete');
            });


            // Route::name('categories-')->group(function () {


            //     // Route::prefix('sub-category')->name('sub-category-')->group(function () {
            //     //     Route::get('list', [SubCategoryController::class, 'index'])->name('list');
            //     //     Route::get('create', [SubCategoryController::class, 'create'])->name('create');
            //     //     Route::post('store', [SubCategoryController::class, 'store'])->name('store');
            //     //     Route::get('edit/{token}', [SubCategoryController::class, 'edit'])->name('edit');
            //     //     Route::post('update/{token}', [SubCategoryController::class, 'update'])->name('update');
            //     //     Route::delete('delete/{token}', [SubCategoryController::class, 'destory'])->name('delete');
            //     // });

            //     Route::prefix('child-category')->name('child-category-')->group(function () {
            //         Route::get('list', [ChildCategoryController::class, 'index'])->name('list');
            //         Route::get('create', [ChildCategoryController::class, 'create'])->name('create');
            //         Route::post('store', [ChildCategoryController::class, 'store'])->name('store');
            //         Route::get('edit/{token}', [ChildCategoryController::class, 'edit'])->name('edit');
            //         Route::post('update/{token}', [ChildCategoryController::class, 'update'])->name('update');
            //         Route::delete('delete/{token}', [ChildCategoryController::class, 'destory'])->name('delete');
            //         Route::get('child-sub-category-list', [ChildCategoryController::class, 'childSubCategories'])->name('child-sub-category-list');
            //     });


            // });
        });

        Route::delete('removed-product-collection/{product_id}/{collectionId}', [ProductCollectionController::class, 'removeProduct'])->name('collections.removeProduct');

        Route::resource('collections', ProductCollectionController::class);

        // Jay Routes

        // CKEditor Image Upload route
        Route::any('/base/uploder', [FileUploadController::class, 'ckeditorUploadImage'])->name('editor-upload');
        // END

        /* Category Icons Management */
        Route::match(['get', 'post'], '/settings/category-icons', [App\Http\Controllers\Admin\CategoryIconsController::class, 'index'])->name('settings.category-icons.index');
        Route::match(['get', 'post'], '/settings/category-icons/create', [App\Http\Controllers\Admin\CategoryIconsController::class, 'create'])->name('settings.category-icons.create');
        Route::match(['get', 'post'], '/settings/category-icons/edit/{id}', [App\Http\Controllers\Admin\CategoryIconsController::class, 'edit'])->name('settings.category-icons.edit');
        Route::get('settings/category-icons/destroy/{enuserid?}', [App\Http\Controllers\Admin\CategoryIconsController::class, 'destroy'])->name('settings.category-icons.delete');
        Route::get('settings/category-icons/update-status/{id}/{status}', [App\Http\Controllers\Admin\CategoryIconsController::class, 'changeStatus'])->name('settings.category-icons.status');
        // END

        /* Blog Management */
        Route::match(['get', 'post'], '/blogs', [App\Http\Controllers\Admin\BlogController::class, 'index'])->name('blogs.index');
        Route::match(['get', 'post'], '/blogs/create', [App\Http\Controllers\Admin\BlogController::class, 'create'])->name('blogs.create');
        Route::match(['get', 'post'], '/blogs/edit/{id}', [App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('blogs.edit');
        Route::get('blogs/destroy/{enuserid?}', [App\Http\Controllers\Admin\BlogController::class, 'destroy'])->name('blogs.delete');
        Route::get('blogs/update-status/{id}/{status}', [App\Http\Controllers\Admin\BlogController::class, 'changeStatus'])->name('blogs.status');
        // END

        /* promises Management */
        Route::match(['get', 'post'], '/promises', [App\Http\Controllers\Admin\PromisesController::class, 'index'])->name('promises.index');
        Route::match(['get', 'post'], '/promises/create', [App\Http\Controllers\Admin\PromisesController::class, 'create'])->name('promises.create');
        Route::match(['get', 'post'], '/promises/edit/{id}', [App\Http\Controllers\Admin\PromisesController::class, 'edit'])->name('promises.edit');
        Route::get('promises/destroy/{enuserid?}', [App\Http\Controllers\Admin\PromisesController::class, 'destroy'])->name('promises.delete');
        Route::get('promises/update-status/{id}/{status}', [App\Http\Controllers\Admin\PromisesController::class, 'changeStatus'])->name('promises.status');
        // END

        /* Aboutus Management */
        Route::match(['get', 'post'], '/aboutus', [App\Http\Controllers\Admin\AboutusController::class, 'index'])->name('aboutus.index');
        Route::match(['get', 'post'], '/aboutus/create', [App\Http\Controllers\Admin\AboutusController::class, 'create'])->name('aboutus.create');
        Route::match(['get', 'post'], '/aboutus/edit/{id}', [App\Http\Controllers\Admin\AboutusController::class, 'edit'])->name('aboutus.edit');
        Route::get('aboutus/destroy/{enuserid?}', [App\Http\Controllers\Admin\AboutusController::class, 'destroy'])->name('aboutus.delete');
        Route::get('aboutus/update-status/{id}/{status}', [App\Http\Controllers\Admin\AboutusController::class, 'changeStatus'])->name('aboutus.status');
        // END

        /* Slider routes */
        Route::match(['get', 'post'], '/sliders', [App\Http\Controllers\Admin\SliderController::class, 'index'])->name('sliders.index');
        Route::match(['get', 'post'], '/sliders/create', [App\Http\Controllers\Admin\SliderController::class, 'create'])->name('sliders.create');
        Route::match(['get', 'post'], '/sliders/edit/{enuserid}', [App\Http\Controllers\Admin\SliderController::class, 'edit'])->name('sliders.edit');
        Route::get('sliders/destroy/{enuserid?}', [App\Http\Controllers\Admin\SliderController::class, 'destroy'])->name('sliders.delete');
        Route::get('sliders/update-status/{id}/{status}', [App\Http\Controllers\Admin\SliderController::class, 'changeStatus'])->name('sliders.status');
        /* Slider routes */

        Route::match(['get', 'post'], '/coupons/create-new', [App\Http\Controllers\Admin\CouponController::class, 'create_new'])->name('coupons.create-new');
        Route::match(['get', 'post'], '/coupons/edit-new/{enuserid}', [App\Http\Controllers\Admin\CouponController::class, 'edit_new'])->name('coupons.edit-new');
        Route::post('get-sub-categories', [App\Http\Controllers\Admin\CouponController::class, 'getSubCategoryList'])->name('coupons.getSubCategoryList');
        Route::post('get-user-type', [App\Http\Controllers\Admin\CouponController::class, 'getUserTypeList'])->name('coupons.getUserTypeList');
        Route::post('update-detail-page-display-status', [App\Http\Controllers\Admin\CouponController::class, 'updateDetailPageDisplayStatus'])->name('coupons.updateDetailPageDisplayStatus');
        Route::post('get-coupon-uses', [App\Http\Controllers\Admin\CouponController::class, 'getCouponUses'])->name('coupons.couponUses');

        // Order Management Routes
        Route::match(['get', 'post'], '/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::match(['get', 'post'], '/orders-items', [OrderController::class, 'items'])->name('orders-items.index');
        Route::match(['get', 'post'], '/user-orders/{id}', [OrderController::class, 'user_orders'])->name('orders.user_orders');
        Route::match(['get', 'post'], '/orders/edit/{id}', [OrderController::class, 'edit'])->name('orders.edit');
        Route::match(['get', 'post'], '/orders/change-status', [OrderController::class, 'change_status'])->name('orders.change-status');
        Route::get('/orders/view/{id}', [OrderController::class, 'view'])->name('orders.view');
        Route::match(['get', 'post'], '/orders/generate-invoice/{id}', [OrderController::class, 'generateNewInvoice'])->name('orders.generate.invoice');
        Route::match(['get', 'post'], '/orders/generate-bulk-items-invoice', [OrderController::class, 'generateBulkItemsInvoice'])->name('orders.generate.bulkitem.invoice');
        Route::match(['post'], '/orders/generate-items-invoice', [OrderController::class, 'generateItemsInvoice'])->name('orders.generate.items.invoice');
        Route::match(['post'], '/orders/generate-bulk-invoice', [OrderController::class, 'generateBulkInvoice'])->name('orders.generate.bulk.invoice');

        Route::get('/view-email/{order}/{status}', [Controller::class, 'viewOrderStatusMail'])->name('email.view');
        Route::get('/export-orders', [App\Http\Controllers\Admin\OrderController::class, 'exportOrders'])->name('orders.export-orders');
        Route::get('/export-order-items', [App\Http\Controllers\Admin\OrderController::class, 'exportOrderItems'])->name('orders.export-order-items');
        Route::get('/export-products', [ProductController::class, 'exportProducts'])->name('product.export-product');
        Route::post('/reviews/image/delete', [ProductController::class, 'reviewdeleteImage'])->name('reviews.image.delete');

        Route::match(['get', 'post'], '/orders-items/change-status', [App\Http\Controllers\Admin\OrderController::class, 'change_item_status'])->name('orders-item.change-status');
        Route::match(['post'], '/orders-items/update-cancel-request', [App\Http\Controllers\Admin\OrderController::class, 'updateCancelRequest'])->name('order.update-cancel-request');
        Route::match(['post'], '/orders-items/update-return-request', [App\Http\Controllers\Admin\OrderController::class, 'updateReturnRequest'])->name('order.update-return-request');
        // Order Management Routes
        // End

        /** category routes **/
        Route::match(['get', 'post'], '/category', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('category.index');
        Route::match(['get', 'post'], '/category/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('category.create');
        Route::match(['get', 'post'], '/category/save', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('category.store');
        Route::match(['get', 'post'], '/category/edit/{enuserid}', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('category.edit');
        Route::match(['get', 'post'], '/category/update/{enuserid}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('category.update');
        Route::get('category/show/{enuserid}', [App\Http\Controllers\Admin\CategoryController::class, 'show'])->name('category.show');
        Route::get('/export-category', [App\Http\Controllers\Admin\CategoryController::class, 'exportCategory'])->name('category.export-category');
        //  Route::resource('category', App\Http\Controllers\Admin\CategoryController::class);
        Route::get('category/update-status/{id}/{status}', [App\Http\Controllers\Admin\CategoryController::class, 'changeStatus'])->name('category.status');
        Route::get('category/destroy/{endepid?}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('category.delete');
        Route::post('category/update-order', [App\Http\Controllers\Admin\CategoryController::class, 'updateCategoryOrder'])->name('category.updateCategoryOrder');
        Route::post('get-tax-rate-list', [App\Http\Controllers\Admin\CategoryController::class, 'getTaxRateList'])->name('category.getTaxRateList');
        Route::post('update-category-status', [CategoryController::class, 'updateCategoryStatus'])->name('update.category.status');
        Route::post('category-image-delete', [CategoryController::class, 'deleteCategoryImage'])->name('category.image.delete');
        // /* category routes */

        Route::get('category/manage-priority/{position?}', [CategoryController::class, 'managePriority'])->name('category.priority.manage');
        Route::post('category/update-priority', [CategoryController::class, 'updatePriority'])->name('category.priority.update');
        // For Sub category
        Route::get('category/manage-sub-priority/{enuserid?}/{position?}', [CategoryController::class, 'manageSubPriority'])->name('category.priority.submanage');
        Route::post('category/update-sub-priority', [CategoryController::class, 'updateSubPriority'])->name('category.priority.subupdate');
        // For Child category
        Route::get('category/manage-child-priority/{position?}', [CategoryController::class, 'manageChildPriority'])->name('category.priority.childmanage');
        Route::post('category/update-child-priority', [CategoryController::class, 'updateChildPriority'])->name('category.priority.childupdate');

        Route::match(['get', 'post'], '/category/demo-edit/{enuserid}', [App\Http\Controllers\Admin\CategoryController::class, 'editDemo'])->name('category.editDemo');
        Route::match(['get', 'post'], '/category/demo-update/{enuserid}', [App\Http\Controllers\Admin\CategoryController::class, 'updateDemo'])->name('category.updateDemo');


        /**  SubCategory routes **/
        Route::match(['get', 'post'], '/sub-category/{endesid?}', [App\Http\Controllers\Admin\SubCategoryController::class, 'index'])->name('sub-category.index');
        Route::match(['get', 'post'], 'sub-category/add/{endesid?}', [App\Http\Controllers\Admin\SubCategoryController::class, 'add'])->name('sub-category.add');
        Route::match(['get', 'post'], 'sub-category/edit/{endesid?}', [App\Http\Controllers\Admin\SubCategoryController::class, 'update'])->name('sub-category.edit');
        Route::get('sub-category/update-status/{id}/{status}', [App\Http\Controllers\Admin\SubCategoryController::class, 'changeStatus'])->name('sub-category.status');
        Route::get('sub-category/delete/{endesid?}', [App\Http\Controllers\Admin\SubCategoryController::class, 'destroy'])->name('sub-category.delete');
        /* SubCategory routes */

        /**  ChildCategory routes **/
        Route::match(['get', 'post'], '/child-category/{endesid?}', [App\Http\Controllers\Admin\ChildCategoryController::class, 'index'])->name('child-category.index');
        Route::match(['get', 'post'], 'child-category/add/{endesid?}', [App\Http\Controllers\Admin\ChildCategoryController::class, 'add'])->name('child-category.add');
        Route::match(['get', 'post'], 'child-category/edit/{endesid?}', [App\Http\Controllers\Admin\ChildCategoryController::class, 'update'])->name('child-category.edit');
        Route::get('child-category/update-status/{id}/{status}', [App\Http\Controllers\Admin\ChildCategoryController::class, 'changeStatus'])->name('child-category.status');
        Route::get('child-category/delete/{endesid?}', [App\Http\Controllers\Admin\ChildCategoryController::class, 'destroy'])->name('child-category.delete');
        /* ChildCategory routes */


        /** Attribute routes **/
        // Route::match(['get', 'post'], '/attribute', [App\Http\Controllers\Admin\AttributeController::class, 'index'])->name('attribute.index');
        // Route::match(['get', 'post'], '/attribute/create', [App\Http\Controllers\Admin\AttributeController::class, 'create'])->name('attribute.create');
        // Route::match(['get', 'post'], '/attribute/save', [App\Http\Controllers\Admin\AttributeController::class, 'store'])->name('attribute.store');
        // Route::match(['get', 'post'], '/attribute/edit/{enuserid}', [App\Http\Controllers\Admin\AttributeController::class, 'edit'])->name('attribute.edit');
        // Route::match(['get', 'post'], '/attribute/update/{enuserid}', [App\Http\Controllers\Admin\AttributeController::class, 'update'])->name('attribute.update');
        // Route::get('attribute/show/{enuserid}', [App\Http\Controllers\Admin\AttributeController::class, 'show'])->name('attribute.show');
        // Route::get('attribute/update-status/{id}/{status}', [App\Http\Controllers\Admin\AttributeController::class, 'changeStatus'])->name('attribute.status');
        // Route::get('attribute/destroy/{endepid?}', [App\Http\Controllers\Admin\AttributeController::class, 'destroy'])->name('attribute.delete');
        // Route::post('attribute/update-order', [App\Http\Controllers\Admin\AttributeController::class, 'updateAttributeOrder'])->name('attribute.updateAttributeOrder');
        // /* Attribute routes */

        /** plans routes **/
        Route::match(['get', 'post'], '/plans', [App\Http\Controllers\Admin\PlansController::class, 'index'])->name('plans.index');
        Route::match(['get', 'post'], '/plans/create', [App\Http\Controllers\Admin\PlansController::class, 'create'])->name('plans.create');
        Route::match(['get', 'post'], '/plans/save', [App\Http\Controllers\Admin\PlansController::class, 'store'])->name('plans.store');
        Route::match(['get', 'post'], '/plans/edit/{enuserid}', [App\Http\Controllers\Admin\PlansController::class, 'edit'])->name('plans.edit');
        Route::match(['get', 'post'], '/plans/update/{enuserid}', [App\Http\Controllers\Admin\PlansController::class, 'update'])->name('plans.update');
        Route::get('plans/show/{enuserid}', [App\Http\Controllers\Admin\PlansController::class, 'show'])->name('plans.show');
        Route::get('plans/update-status/{id}/{status}', [App\Http\Controllers\Admin\PlansController::class, 'changeStatus'])->name('plans.status');
        Route::get('plans/destroy/{endepid?}', [App\Http\Controllers\Admin\PlansController::class, 'destroy'])->name('plans.delete');
        /* plans routes */

        /** shipping companies routes **/
        Route::match(['get', 'post'], '/shipping-companies', [App\Http\Controllers\Admin\ShippingCompanyController::class, 'index'])->name('shipping-companies.index');
        Route::match(['get', 'post'], '/shipping-companies/create', [App\Http\Controllers\Admin\ShippingCompanyController::class, 'create'])->name('shipping-companies.create');
        Route::match(['get', 'post'], '/shipping-companies/save', [App\Http\Controllers\Admin\ShippingCompanyController::class, 'store'])->name('shipping-companies.store');
        Route::match(['get', 'post'], '/shipping-companies/edit/{enuserid}', [App\Http\Controllers\Admin\ShippingCompanyController::class, 'edit'])->name('shipping-companies.edit');
        Route::match(['get', 'post'], '/shipping-companies/update/{enuserid}', [App\Http\Controllers\Admin\ShippingCompanyController::class, 'update'])->name('shipping-companies.update');
        Route::get('shipping-companies/show/{enuserid}', [App\Http\Controllers\Admin\ShippingCompanyController::class, 'show'])->name('shipping-companies.show');
        Route::get('shipping-companies/update-status/{id}/{status}', [App\Http\Controllers\Admin\ShippingCompanyController::class, 'changeStatus'])->name('shipping-companies.status');
        Route::get('shipping-companies/destroy/{endepid?}', [App\Http\Controllers\Admin\ShippingCompanyController::class, 'destroy'])->name('shipping-companies.delete');
        /* shipping companies routes */

        /**  ShippingAreas routes **/
        Route::match(['get', 'post'], '/shipping-areas/{endesid?}', [App\Http\Controllers\Admin\ShippingAreasController::class, 'index'])->name('shipping-areas.index');
        Route::match(['get', 'post'], 'shipping-areas/add/{endesid?}', [App\Http\Controllers\Admin\ShippingAreasController::class, 'add'])->name('shipping-areas.add');
        Route::match(['get', 'post'], 'shipping-areas/edit/{endesid?}', [App\Http\Controllers\Admin\ShippingAreasController::class, 'update'])->name('shipping-areas.edit');
        Route::get('shipping-areas/update-status/{id}/{status}', [App\Http\Controllers\Admin\ShippingAreasController::class, 'changeStatus'])->name('shipping-areas.status');
        Route::get('shipping-areas/delete/{endesid?}', [App\Http\Controllers\Admin\ShippingAreasController::class, 'delete'])->name('shipping-areas.delete');
        /* ShippingAreas routes */

        /**  ShippingCosts routes **/
        Route::match(['get', 'post'], '/shipping-costs/{endesid?}', [App\Http\Controllers\Admin\ShippingCostsController::class, 'index'])->name('shipping-costs.index');
        Route::match(['get', 'post'], 'shipping-costs/add/{endesid?}', [App\Http\Controllers\Admin\ShippingCostsController::class, 'add'])->name('shipping-costs.add');
        Route::match(['get', 'post'], 'shipping-costs/edit/{endesid?}', [App\Http\Controllers\Admin\ShippingCostsController::class, 'update'])->name('shipping-costs.edit');
        Route::get('shipping-costs/update-status/{id}/{status}', [App\Http\Controllers\Admin\ShippingCostsController::class, 'changeStatus'])->name('shipping-costs.status');
        Route::get('shipping-costs/delete/{endesid?}', [App\Http\Controllers\Admin\ShippingCostsController::class, 'delete'])->name('shipping-costs.delete');
        /* ShippingCosts routes */



        /** shipping Zones routes **/
        Route::match(['get', 'post'], '/shipping-zones', [App\Http\Controllers\Admin\ShippingZoneController::class, 'index'])->name('shipping-zones.index');
        Route::match(['get', 'post'], '/shipping-zones/create', [App\Http\Controllers\Admin\ShippingZoneController::class, 'create'])->name('shipping-zones.create');
        Route::match(['get', 'post'], '/shipping-zones/save', [App\Http\Controllers\Admin\ShippingZoneController::class, 'store'])->name('shipping-zones.store');
        Route::match(['get', 'post'], '/shipping-zones/edit/{enuserid}', [App\Http\Controllers\Admin\ShippingZoneController::class, 'edit'])->name('shipping-zones.edit');
        Route::match(['get', 'post'], '/shipping-zones/update/{enuserid}', [App\Http\Controllers\Admin\ShippingZoneController::class, 'update'])->name('shipping-zones.update');
        Route::get('shipping-zones/show/{enuserid}', [App\Http\Controllers\Admin\ShippingZoneController::class, 'show'])->name('shipping-zones.show');
        Route::get('shipping-zones/update-status/{id}/{status}', [App\Http\Controllers\Admin\ShippingZoneController::class, 'changeStatus'])->name('shipping-zones.status');
        Route::get('shipping-zones/destroy/{endepid?}', [App\Http\Controllers\Admin\ShippingZoneController::class, 'destroy'])->name('shipping-zones.delete');
        /* shipping Zones routes */


        /** shipping Zones Weights routes **/
        Route::match(['get', 'post'], '/shipping-zones-weights/{endesid?}', [App\Http\Controllers\Admin\ShippingZoneWeightController::class, 'index'])->name('shipping-zones-weights.index');
        Route::match(['get', 'post'], 'shipping-zones-weights/add/{endesid?}', [App\Http\Controllers\Admin\ShippingZoneWeightController::class, 'add'])->name('shipping-zones-weights.add');
        Route::match(['get', 'post'], 'shipping-zones-weights/edit/{endesid?}', [App\Http\Controllers\Admin\ShippingZoneWeightController::class, 'update'])->name('shipping-zones-weights.edit');
        Route::get('shipping-zones-weights/update-status/{id}/{status}', [App\Http\Controllers\Admin\ShippingZoneWeightController::class, 'changeStatus'])->name('shipping-zones-weights.status');
        Route::get('shipping-zones-weights/delete/{endesid?}', [App\Http\Controllers\Admin\ShippingZoneWeightController::class, 'delete'])->name('shipping-zones-weights.delete');
        /* shipping Zones Weights routes */


        /** Shipping Charges routes **/
        Route::match(['get', 'post'], '/shipping-charges', [App\Http\Controllers\Admin\ShippingChargesController::class, 'index'])->name('shipping-charges.index');
        Route::match(['get', 'post'], '/shipping-charges/create', [App\Http\Controllers\Admin\ShippingChargesController::class, 'create'])->name('shipping-charges.create');
        Route::match(['get', 'post'], '/shipping-charges/save', [App\Http\Controllers\Admin\ShippingChargesController::class, 'store'])->name('shipping-charges.store');
        Route::match(['get', 'post'], '/shipping-charges/edit/{enuserid}', [App\Http\Controllers\Admin\ShippingChargesController::class, 'edit'])->name('shipping-charges.edit');
        Route::match(['get', 'post'], '/shipping-charges/update/{enuserid}', [App\Http\Controllers\Admin\ShippingChargesController::class, 'update'])->name('shipping-charges.update');
        Route::get('shipping-charges/show/{enuserid}', [App\Http\Controllers\Admin\ShippingChargesController::class, 'show'])->name('shipping-charges.show');
        Route::get('shipping-charges/update-status/{id}/{status}', [App\Http\Controllers\Admin\ShippingChargesController::class, 'changeStatus'])->name('shipping-charges.status');
        Route::get('shipping-charges/destroy/{endepid?}', [App\Http\Controllers\Admin\ShippingChargesController::class, 'destroy'])->name('shipping-charges.delete');
        Route::get('/getstate/{country_id}', [App\Http\Controllers\Admin\ShippingChargesController::class, 'getStateByCountry']);
        Route::get('/getcities', [App\Http\Controllers\Admin\ShippingChargesController::class, 'getCitiesByStates']);
        Route::get('/get-shipping-weight-amount-list-zone-wise', [App\Http\Controllers\Admin\ShippingChargesController::class, 'getweightAmountListZoneWise']);

        /* Shipping Charges routes */


        /** size-charts routes **/
        Route::match(['get', 'post'], '/size-charts', [App\Http\Controllers\Admin\SizeChartController::class, 'index'])->name('size-charts.index');
        Route::match(['get', 'post'], '/size-charts/create', [App\Http\Controllers\Admin\SizeChartController::class, 'create'])->name('size-charts.create');
        Route::match(['get', 'post'], '/size-charts/save', [App\Http\Controllers\Admin\SizeChartController::class, 'store'])->name('size-charts.store');
        Route::match(['get', 'post'], '/size-charts/edit/{enuserid}', [App\Http\Controllers\Admin\SizeChartController::class, 'edit'])->name('size-charts.edit');
        Route::match(['get', 'post'], '/size-charts/update/{enuserid}', [App\Http\Controllers\Admin\SizeChartController::class, 'update'])->name('size-charts.update');
        Route::get('size-charts/show/{enuserid}', [App\Http\Controllers\Admin\SizeChartController::class, 'show'])->name('size-charts.show');
        Route::get('size-charts/update-status/{id}/{status}', [App\Http\Controllers\Admin\SizeChartController::class, 'changeStatus'])->name('size-charts.status');
        Route::get('size-charts/destroy/{endepid?}', [App\Http\Controllers\Admin\SizeChartController::class, 'destroy'])->name('size-charts.delete');
        /* size-charts routes */

        /** size-chart-tebular routes **/
        Route::match(['get', 'post'], '/new-size-charts', [App\Http\Controllers\Admin\NewSizeChartController::class, 'index'])->name('new-size-charts.index');
        Route::match(['get', 'post'], '/new-size-charts/create', [App\Http\Controllers\Admin\NewSizeChartController::class, 'create'])->name('new-size-charts.create');
        Route::match(['get', 'post'], '/new-size-charts/save', [App\Http\Controllers\Admin\NewSizeChartController::class, 'store'])->name('new-size-charts.store');
        Route::match(['get', 'post'], '/new-size-charts/edit/{enuserid}', [App\Http\Controllers\Admin\NewSizeChartController::class, 'edit'])->name('new-size-charts.edit');
        Route::match(['get', 'post'], '/new-size-charts/update/{enuserid}', [App\Http\Controllers\Admin\NewSizeChartController::class, 'update'])->name('new-size-charts.update');
        Route::get('new-size-charts/show/{enuserid}', [App\Http\Controllers\Admin\NewSizeChartController::class, 'show'])->name('new-size-charts.show');
        Route::get('new-size-charts/update-status/{id}/{status}', [App\Http\Controllers\Admin\NewSizeChartController::class, 'changeStatus'])->name('new-size-charts.status');
        Route::get('new-size-charts/destroy/{endepid?}', [App\Http\Controllers\Admin\NewSizeChartController::class, 'destroy'])->name('new-size-charts.delete');
        /* new-size-charts routes */



        /** size-chart-tebular  routes **/
        Route::match(['get', 'post'], '/size-chart-tebular', [App\Http\Controllers\Admin\SizeChartTebularController::class, 'index'])->name('size-chart-tebulars.index');
        Route::match(['get', 'post'], '/size-chart-tebular/create', [App\Http\Controllers\Admin\SizeChartTebularController::class, 'create'])->name('size-chart-tebulars.create');
        Route::match(['get', 'post'], '/size-chart-tebular/save', [App\Http\Controllers\Admin\SizeChartTebularController::class, 'store'])->name('size-chart-tebulars.store');
        Route::match(['get', 'post'], '/size-chart-tebular/edit/{enuserid}', [App\Http\Controllers\Admin\SizeChartTebularController::class, 'edit'])->name('size-chart-tebulars.edit');
        Route::match(['get', 'post'], '/size-chart-tebular/update/{enuserid}', [App\Http\Controllers\Admin\SizeChartTebularController::class, 'update'])->name('size-chart-tebulars.update');
        Route::get('size-chart-tebular/show/{enuserid}', [App\Http\Controllers\Admin\SizeChartTebularController::class, 'show'])->name('size-chart-tebulars.show');
        Route::get('size-chart-tebular/update-status/{id}/{status}', [App\Http\Controllers\Admin\SizeChartTebularController::class, 'changeStatus'])->name('size-chart-tebulars.status');
        Route::get('size-chart-tebular/destroy/{endepid?}', [App\Http\Controllers\Admin\SizeChartTebularController::class, 'destroy'])->name('size-chart-tebulars.delete');
        Route::get('size-chart-tebular-show/{endepid?}', [App\Http\Controllers\Admin\SizeChartTebularController::class, 'showSizeChart'])->name('size-chart-tebulars-show');

        /* size-chart-tebular routes */

        /**  size chart details routes **/
        Route::match(['get', 'post'], '/size-chart-details/{endesid?}', [App\Http\Controllers\Admin\SizeChartDetailController::class, 'index'])->name('size-chart-details.index');
        Route::match(['get', 'post'], 'size-chart-details/add/{endesid?}', [App\Http\Controllers\Admin\SizeChartDetailController::class, 'add'])->name('size-chart-details.add');
        Route::match(['get', 'post'], 'size-chart-details/edit/{endesid?}', [App\Http\Controllers\Admin\SizeChartDetailController::class, 'update'])->name('size-chart-details.edit');
        Route::get('size-chart-details/update-status/{id}/{status}', [App\Http\Controllers\Admin\SizeChartDetailController::class, 'changeStatus'])->name('size-chart-details.status');
        Route::get('size-chart-details/delete/{endesid?}', [App\Http\Controllers\Admin\SizeChartDetailController::class, 'delete'])->name('size-chart-details.delete');
        /* size chart details routes */


        /* Referral History routes */
        Route::match(['get', 'post'], '/referral-histories', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'index'])->name('referral_histories.index');
        Route::match(['get', 'post'], '/user_referral-histories/{id}', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'user_index'])->name('user_referral_histories.index');
        Route::get('add-referral-history', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'create'])->name('referral_histories.create');
        Route::post('save-referral-history', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'save'])->name('referral_histories.save');
        Route::get('referral-history-delete/{id}', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'destroy'])->name('referral_histories.delete');
        Route::match(['get'], '/referral-map', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'referra_map'])->name('referra_map');
        Route::get('referral-histories/tree-view/{enuserid}', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'treeView'])->name('referral_histories.treeView');
        Route::post('referral-histories/user-search', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'userSearch'])->name('referral_histories.userSearch');

        //for referral Settings
        Route::match(['get', 'post'], '/referral-setting-list', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'setting_index'])->name('referral_setting.index');
        Route::match(['get', 'post'], '/referral-setting-add', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'setting_create'])->name('referral_setting.create');
        Route::get('referral-setting-delete/{id}', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'setting_destroy'])->name('referral_setting.delete');
        Route::get('referral-setting-edit/{id}', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'setting_edit'])->name('referral_setting.edit');
        Route::post('referral-setting-update/{id}', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'setting_update'])->name('referral_setting.update');
        Route::post('referral-setting_save', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'setting_save'])->name('referral_setting.save');
        Route::post('referral-setting/user-search', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'settingUserSearch'])->name('referral_setting.userSearch');

        Route::patch('/referral-settings/{id}/toggle-status', [App\Http\Controllers\Admin\ReferralHistoryController::class, 'toggleStatus'])->name('referral_setting.toggleStatus');;
        /* Referral History routes */

        /** brand routes **/
        Route::match(['get', 'post'], '/brand', [App\Http\Controllers\Admin\BrandController::class, 'index'])->name('brand.index');
        Route::match(['get', 'post'], '/brand/create', [App\Http\Controllers\Admin\BrandController::class, 'create'])->name('brand.create');
        Route::match(['get', 'post'], '/brand/save', [App\Http\Controllers\Admin\BrandController::class, 'store'])->name('brand.store');
        Route::match(['get', 'post'], '/brand/edit/{enuserid}', [App\Http\Controllers\Admin\BrandController::class, 'edit'])->name('brand.edit');
        Route::match(['get', 'post'], '/brand/update/{enuserid}', [App\Http\Controllers\Admin\BrandController::class, 'update'])->name('brand.update');
        Route::get('brand/show/{enuserid}', [App\Http\Controllers\Admin\BrandController::class, 'show'])->name('brand.show');
        //  Route::resource('brand', App\Http\Controllers\Admin\BrandController::class);
        Route::get('brand/update-status/{id}/{status}', [App\Http\Controllers\Admin\BrandController::class, 'changeStatus'])->name('brand.status');
        Route::get('brand/destroy/{id?}', [App\Http\Controllers\Admin\BrandController::class, 'destroy'])->name('brand.delete');

        Route::get('brand/update-primary-status/{id}/{status}', [App\Http\Controllers\Admin\BrandController::class, 'changePrimaryStatus'])->name('brand.primarystatus');
        // /* brand routes */

        /** Department routes **/
        //  Route::resource('departments', App\Http\Controllers\Admin\DepartmentsController::class);
        Route::match(['get', 'post'], '/departments', [App\Http\Controllers\Admin\DepartmentsController::class, 'index'])->name('departments.index');
        Route::match(['get', 'post'], '/departments/create', [App\Http\Controllers\Admin\DepartmentsController::class, 'create'])->name('departments.create');
        Route::match(['get', 'post'], '/departments/save', [App\Http\Controllers\Admin\DepartmentsController::class, 'store'])->name('departments.store');
        Route::match(['get', 'post'], '/departments/edit/{enuserid}', [App\Http\Controllers\Admin\DepartmentsController::class, 'edit'])->name('departments.edit');
        Route::match(['get', 'post'], '/departments/update/{enuserid}', [App\Http\Controllers\Admin\DepartmentsController::class, 'update'])->name('departments.update');
        Route::get('departments/show/{enuserid}', [App\Http\Controllers\Admin\DepartmentsController::class, 'show'])->name('departments.show');
        Route::get('departments/update-status/{id}/{status}', [App\Http\Controllers\Admin\DepartmentsController::class, 'changeStatus'])->name('departments.status');
        Route::get('departments/destroy/{endepid?}', [App\Http\Controllers\Admin\DepartmentsController::class, 'destroy'])->name('departments.delete');
        // /* Department routes */

        /** Attribute routes **/
        //  Route::resource('attribute', App\Http\Controllers\Admin\AttributeController::class);
        Route::match(['get', 'post'], '/attributes', [App\Http\Controllers\Admin\AttributeController::class, 'index'])->name('attributes.index');
        Route::match(['get', 'post'], '/attributes/create', [App\Http\Controllers\Admin\AttributeController::class, 'create'])->name('attributes.create');
        Route::match(['get', 'post'], '/attributes/save', [App\Http\Controllers\Admin\AttributeController::class, 'store'])->name('attributes.store');
        Route::match(['get', 'post'], '/attributes/edit/{enuserid}', [App\Http\Controllers\Admin\AttributeController::class, 'edit'])->name('attributes.edit');
        Route::match(['get', 'post'], '/attributes/update/{enuserid}', [App\Http\Controllers\Admin\AttributeController::class, 'update'])->name('attributes.update');
        Route::get('attributes/show/{enuserid}', [App\Http\Controllers\Admin\AttributeController::class, 'show'])->name('attributes.show');
        Route::get('attributes/update-status/{id}/{status}', [App\Http\Controllers\Admin\AttributeController::class, 'changeStatus'])->name('attributes.status');
        Route::get('attributes/destroy/{endepid?}', [App\Http\Controllers\Admin\AttributeController::class, 'destroy'])->name('attributes.delete');
        // /* Attribute routes */

        /** Tags routes **/
        //  Route::resource('attribute', App\Http\Controllers\Admin\AttributeController::class);
        Route::match(['get', 'post'], '/tags', [App\Http\Controllers\Admin\TagController::class, 'index'])->name('tags.index');
        Route::match(['get', 'post'], '/tags/create', [App\Http\Controllers\Admin\TagController::class, 'create'])->name('tags.create');
        Route::match(['get', 'post'], '/tags/save', [App\Http\Controllers\Admin\TagController::class, 'store'])->name('tags.store');
        Route::match(['get', 'post'], '/tags/edit/{enuserid}', [App\Http\Controllers\Admin\TagController::class, 'edit'])->name('tags.edit');
        Route::match(['get', 'post'], '/tags/update/{enuserid}', [App\Http\Controllers\Admin\TagController::class, 'update'])->name('tags.update');
        Route::get('tags/show/{enuserid}', [App\Http\Controllers\Admin\TagController::class, 'show'])->name('tags.show');
        Route::get('tags/update-status/{id}/{status}', [App\Http\Controllers\Admin\TagController::class, 'changeStatus'])->name('tags.status');
        Route::get('tags/destroy/{endepid?}', [App\Http\Controllers\Admin\TagController::class, 'destroy'])->name('tags.delete');
        // /* Tags routes */


        /**  Attribute Values routes **/
        Route::match(['get', 'post'], '/attribute-values/{endesid?}', [App\Http\Controllers\Admin\AttributeValuesController::class, 'index'])->name('attribute-values.index');
        Route::match(['get', 'post'], 'attribute-values/add/{endesid?}', [App\Http\Controllers\Admin\AttributeValuesController::class, 'add'])->name('attribute-values.add');
        Route::match(['get', 'post'], 'attribute-values/edit/{endesid?}', [App\Http\Controllers\Admin\AttributeValuesController::class, 'update'])->name('attribute-values.edit');
        Route::get('attribute-values/update-status/{id}/{status}', [App\Http\Controllers\Admin\AttributeValuesController::class, 'changeStatus'])->name('attribute-values.status');
        Route::get('attribute-values/delete/{endesid?}', [App\Http\Controllers\Admin\AttributeValuesController::class, 'delete'])->name('attribute-values.delete');
        //Route::get('attributes-values/destroy/{endepid?}', [App\Http\Controllers\Admin\AttributeValuesController::class, 'destroy'])->name('attributes-values.delete');
        /* Attribute Values routes */

        /** Attribute Values routes **/
        Route::match(['get', 'post'], '/attributes', [App\Http\Controllers\Admin\AttributeController::class, 'index'])->name('attributes.index');
        Route::post('/attributes/add', [App\Http\Controllers\Admin\AttributeController::class, 'store'])->name('attributes.store');
        Route::resource('attributes', App\Http\Controllers\Admin\AttributeController::class)->except(['index', 'store']);
        Route::get('attributes/update-status/{id}/{status}', [App\Http\Controllers\Admin\AttributeController::class, 'changeStatus'])->name('attributes.status');
        Route::get('attributes/destroy/{endepid?}', [App\Http\Controllers\Admin\AttributeController::class, 'destroy'])->name('attributes.delete');
        Route::get('allspecification/del', [App\Http\Controllers\Admin\AttributeController::class, 'DelAllspecifications'])->name('attributes.DelAllspecifications');
        Route::post('delete-attributes-value', [AttributeController::class, 'ajaxDeleteVariantValue'])->name('ajax-delete-attributes-value');
        /* Attribute Values routes */
        /** taxes routes **/
        Route::match(['get', 'post'], '/taxes', [App\Http\Controllers\Admin\TaxesController::class, 'index'])->name('taxes.index');
        Route::match(['get', 'post'], '/taxes/create', [App\Http\Controllers\Admin\TaxesController::class, 'create'])->name('taxes.create');
        Route::match(['get', 'post'], '/taxes/save', [App\Http\Controllers\Admin\TaxesController::class, 'store'])->name('taxes.store');
        Route::match(['get', 'post'], '/taxes/edit/{enuserid}', [App\Http\Controllers\Admin\TaxesController::class, 'edit'])->name('taxes.edit');
        Route::match(['get', 'post'], '/taxes/update/{enuserid}', [App\Http\Controllers\Admin\TaxesController::class, 'update'])->name('taxes.update');
        Route::get('taxes/show/{enuserid}', [App\Http\Controllers\Admin\TaxesController::class, 'show'])->name('taxes.show');
        Route::get('taxes/update-status/{id}/{status}', [App\Http\Controllers\Admin\TaxesController::class, 'changeStatus'])->name('taxes.status');
        Route::get('taxes/destroy/{endepid?}', [App\Http\Controllers\Admin\TaxesController::class, 'destroy'])->name('taxes.delete');

        // /* taxes routes */

        /** cities routes **/
        Route::match(['get', 'post'], '/cities', [App\Http\Controllers\Admin\CityController::class, 'index'])->name('cities.index');
        Route::match(['get', 'post'], '/cities/create', [App\Http\Controllers\Admin\CityController::class, 'create'])->name('cities.create');
        Route::match(['get', 'post'], '/cities/save', [App\Http\Controllers\Admin\CityController::class, 'store'])->name('cities.store');
        Route::match(['get', 'post'], '/cities/edit/{enuserid}', [App\Http\Controllers\Admin\CityController::class, 'edit'])->name('cities.edit');
        Route::match(['get', 'post'], '/cities/update/{enuserid}', [App\Http\Controllers\Admin\CityController::class, 'update'])->name('cities.update');
        Route::get('cities/show/{enuserid}', [App\Http\Controllers\Admin\CityController::class, 'show'])->name('cities.show');
        Route::get('cities/update-status/{id}/{status}', [App\Http\Controllers\Admin\CityController::class, 'changeStatus'])->name('cities.status');
        Route::get('cities/destroy/{endepid?}', [App\Http\Controllers\Admin\CityController::class, 'destroy'])->name('cities.delete');

        // /* taxes routes */

        /**  Designations routes **/
        Route::match(['get', 'post'], '/designations/{endesid?}', [App\Http\Controllers\Admin\DesignationsController::class, 'index'])->name('designations.index');
        Route::match(['get', 'post'], 'designations/add/{endesid?}', [App\Http\Controllers\Admin\DesignationsController::class, 'add'])->name('designations.add');
        Route::match(['get', 'post'], 'designations/edit/{endesid?}', [App\Http\Controllers\Admin\DesignationsController::class, 'update'])->name('designations.edit');
        Route::get('designations/update-status/{id}/{status}', [App\Http\Controllers\Admin\DesignationsController::class, 'changeStatus'])->name('designations.status');
        Route::get('designations/delete/{endesid?}', [App\Http\Controllers\Admin\DesignationsController::class, 'delete'])->name('designations.delete');
        /* Designations routes */

        /* staff routes */
        //  Route::resource('staff', App\Http\Controllers\Admin\StaffController::class);
        Route::match(['get', 'post'], '/staff', [App\Http\Controllers\Admin\StaffController::class, 'index'])->name('staff.index');
        Route::match(['get', 'post'], '/staff/create', [App\Http\Controllers\Admin\StaffController::class, 'create'])->name('staff.create');
        Route::match(['get', 'post'], '/staff/save', [App\Http\Controllers\Admin\StaffController::class, 'store'])->name('staff.store');
        Route::match(['get', 'post'], '/staff/edit/{enuserid}', [App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('staff.edit');
        Route::match(['get', 'post'], '/staff/update/{enuserid}', [App\Http\Controllers\Admin\StaffController::class, 'update'])->name('staff.update');
        Route::get('staff/show/{enuserid}', [App\Http\Controllers\Admin\StaffController::class, 'show'])->name('staff.show');
        Route::get('staff/update-status/{id}/{status}', [App\Http\Controllers\Admin\StaffController::class, 'changeStatus'])->name('staff.status');
        Route::get('staff/destroy/{enstfid?}', [App\Http\Controllers\Admin\StaffController::class, 'destroy'])->name('staff.delete');
        Route::match(['get', 'post'], 'staff/changed-password/{enstfid?}', [App\Http\Controllers\Admin\StaffController::class, 'changedPassword'])->name('staff.changerpassword');
        Route::match(['get', 'post'], 'staff/get-designations', [App\Http\Controllers\Admin\StaffController::class, 'getDesignations'])->name('staff.getDesignations');
        Route::match(['get', 'post'], 'staff/get-staff-permission', [App\Http\Controllers\Admin\StaffController::class, 'getStaffPermission'])->name('staff.getStaffPermission');


        /** Access Control Routes Starts **/
        //  Route::resource('acl', App\Http\Controllers\Admin\AclController::class);
        Route::match(['get', 'post'], '/acl', [App\Http\Controllers\Admin\AclController::class, 'index'])->name('acl.index');
        Route::match(['get', 'post'], '/acl/create', [App\Http\Controllers\Admin\AclController::class, 'create'])->name('acl.create');
        Route::match(['get', 'post'], '/acl/save', [App\Http\Controllers\Admin\AclController::class, 'store'])->name('acl.store');
        Route::match(['get', 'post'], '/acl/edit/{enuserid}', [App\Http\Controllers\Admin\AclController::class, 'edit'])->name('acl.edit');
        Route::match(['get', 'post'], '/acl/update/{enuserid}', [App\Http\Controllers\Admin\AclController::class, 'update'])->name('acl.update');
        Route::get('acl/destroy/{enaclid?}', [App\Http\Controllers\Admin\AclController::class, 'destroy'])->name('acl.delete');
        Route::get('acl/update-status/{id}/{status}', [App\Http\Controllers\Admin\AclController::class, 'changeStatus'])->name('acl.status');
        Route::post('acl/add-more/add-more', [App\Http\Controllers\Admin\AclController::class, 'addMoreRow'])->name('acl.addMoreRow');
        Route::get('acl/delete-function/{id}', [App\Http\Controllers\Admin\AclController::class, 'delete_function'])->name('acl.delete_function');
        /** Access Control Routes Ends **/


        /* users routes */
        Route::match(['get', 'post'], '/users', [App\Http\Controllers\Admin\UsersController::class, 'index'])->name('admin_users.index');
        Route::match(['get', 'post'], '/customers', [App\Http\Controllers\Admin\UsersController::class, 'customers'])->name('admin_users.customers');
        Route::match(['get', 'post'], '/users/create', [App\Http\Controllers\Admin\UsersController::class, 'create'])->name('admin_users.create');
        Route::match(['get', 'post'], '/users/save', [App\Http\Controllers\Admin\UsersController::class, 'save'])->name('admin_users.save');
        Route::match(['get', 'post'], '/users/edit/{enuserid}', [App\Http\Controllers\Admin\UsersController::class, 'edit'])->name('admin_users.edit');
        Route::match(['get', 'post'], '/users/update/{enuserid}', [App\Http\Controllers\Admin\UsersController::class, 'update'])->name('admin_users.update');
        Route::get('users/show/{enuserid}', [App\Http\Controllers\Admin\UsersController::class, 'show'])->name('admin_users.show');
        Route::get('users/destroy/{enuserid?}', [App\Http\Controllers\Admin\UsersController::class, 'destroy'])->name('admin_users.delete');
        Route::get('users/update-status/{id}/{status}', [App\Http\Controllers\Admin\UsersController::class, 'changeStatus'])->name('admin_users.status');
        Route::match(['get', 'post'], 'users/changed-password/{enuserid?}', [App\Http\Controllers\Admin\UsersController::class, 'changedPassword'])->name('admin_users.changedPassword');
        Route::get('/export-users', [App\Http\Controllers\Admin\UsersController::class, 'exportUsers'])->name('admin_users.export-users');
        Route::get('/import-users', [App\Http\Controllers\Admin\UsersController::class, 'importUsers'])->name('admin_users.import-users');
        Route::get('/export-subscriber', [App\Http\Controllers\Admin\UsersController::class, 'exportSubscriber'])->name('admin_users.export-subscriber');
        Route::get('/export-wholesale-enquiry', [App\Http\Controllers\Admin\UsersController::class, 'exportWholesaleEnquiry'])->name('wholesale-enquiry');
        Route::get('/export-franchise-enquiry', [App\Http\Controllers\Admin\UsersController::class, 'exportFranchiseEnquiryExport'])->name('franchise-enquiry');
        Route::post('users/loginhistory', [App\Http\Controllers\Admin\UsersController::class, 'loginhistory'])->name('admin_users.loginhistory');

        Route::post('/import-users', [App\Http\Controllers\Admin\UsersController::class, 'importUsersSave'])->name('admin_users.import-users-save');
        Route::match(['get'], 'users/sync-roles', [App\Http\Controllers\Admin\UsersController::class, 'syncOldUserRole'])->name('admin_users.sync-roles');

        // newly added to get city with parent
        Route::get('/getChildByParent', [App\Http\Controllers\Admin\UsersController::class, 'getChildByParent'])->name('getChildByParent');
        Route::post('/user_address_edit/{addressId}', [App\Http\Controllers\Admin\UsersController::class, 'user_address_edit'])->name('user_address_edit');
        Route::post('/user_address_save', [App\Http\Controllers\Admin\UsersController::class, 'user_address_save'])->name('user_address_save');

        Route::get('user-review/{token}', [UsersController::class, 'UserProductreview'])->name('admin_users.user-review');
        Route::get('user-reviewstatus/{id}/{status}', [UsersController::class, 'changeStatusReview'])->name('admin_users-reviewstatus');
        Route::get('user-reviewedit/{id}', [UsersController::class, 'editReview'])->name('admin_users-reviewedit');
        Route::post('user-reviewupdate/{reviewId}/{userId}', [UsersController::class, 'updateReview'])->name('admin_users-reviewupdate');
        Route::get('user-reviewdelete/{reviewId}/{userId}', [UsersController::class, 'reviewDelete'])->name('admin_users-reviewdelete');

        Route::get('user-referral-histories/{token}', [UsersController::class, 'UserReferralHistories'])->name('admin_users.user-referral-histories');
        Route::get('user-refunded-histories/{token}', [UsersController::class, 'UserRefundedHistories'])->name('admin_users.user-refunded-histories');
        Route::get('user-refundedapprovalstatus/{id}/{status}', [UsersController::class, 'RefundedApprovalStatus'])->name('admin_users-refundedapprovalstatus');
        Route::get('user-refundedapprovaledit/{id}', [UsersController::class, 'RefundedApprovalEdit'])->name('admin_users-refundedapprovaledit');
        Route::post('user-refundedapprovalupdate/{refundId}/{userId}', [UsersController::class, 'updateRefundedApproval'])->name('admin_users-refundedapprovalupdate');

        Route::post('/status/{id}/toggle-status', [UsersController::class, 'toggleStatus']);


        /* users routes */


        /* partners routes */
        Route::match(['get', 'post'], '/partners', [App\Http\Controllers\Admin\PartnerController::class, 'index'])->name('partners.index');
        Route::match(['get', 'post'], '/partners/create', [App\Http\Controllers\Admin\PartnerController::class, 'create'])->name('partners.create');
        Route::match(['get', 'post'], '/partners/save', [App\Http\Controllers\Admin\PartnerController::class, 'save'])->name('partners.save');
        Route::match(['get', 'post'], '/partners/edit/{enuserid}', [App\Http\Controllers\Admin\PartnerController::class, 'edit'])->name('partners.edit');
        Route::match(['get', 'post'], '/partners/update/{enuserid}', [App\Http\Controllers\Admin\PartnerController::class, 'update'])->name('partners.update');
        Route::get('partners/show/{enuserid}', [App\Http\Controllers\Admin\PartnerController::class, 'show'])->name('partners.show');
        Route::get('partners/destroy/{enuserid?}', [App\Http\Controllers\Admin\PartnerController::class, 'destroy'])->name('partners.delete');
        Route::get('partners/update-status/{id}/{status}', [App\Http\Controllers\Admin\PartnerController::class, 'changeStatus'])->name('partners.status');
        Route::match(['get', 'post'], 'partners/changed-password/{enuserid?}', [App\Http\Controllers\Admin\PartnerController::class, 'changedPassword'])->name('partners.changedPassword');
        Route::get('partners/fetch-plan-details/{planId}', [App\Http\Controllers\Admin\PartnerController::class, 'fetchPlanDetails'])->name('partners.fetchPlanDetails');
        /* partners routes */


        /* coupons routes */
        Route::resource('coupons', CouponController::class);
        Route::get('coupons/update-status/{id}/{status}', [App\Http\Controllers\Admin\CouponController::class, 'changeStatus'])->name('coupons.status');
        Route::match(['get', 'post'], 'coupons/changed-password/{enuserid?}', [App\Http\Controllers\Admin\CouponController::class, 'changedPassword'])->name('coupons.changedPassword');
        Route::get('coupons/fetch-plan-details/{planId}', [App\Http\Controllers\Admin\CouponController::class, 'fetchPlanDetails'])->name('coupons.fetchPlanDetails');
        Route::get('/export-coupon', [App\Http\Controllers\Admin\CouponController::class, 'exportCoupon'])->name('admin_users.export-coupon');
        Route::get('coupons/coupon-log/{couponId}', [App\Http\Controllers\Admin\CouponController::class, 'couponLog'])->name('coupons.couponlog');
        /* coupons routes */

        /** FooterCategory routes **/
        Route::resource('footer-category', App\Http\Controllers\Admin\FooterCategoryController::class);
        Route::get('footer-category/update-status/{id}/{status}', [App\Http\Controllers\Admin\FooterCategoryController::class, 'changeStatus'])->name('footer-category.status');
        Route::get('footer-category/destroy/{endepid?}', [App\Http\Controllers\Admin\FooterCategoryController::class, 'destroy'])->name('footer-category.delete');
        // /* FooterCategory routes */

        // Manage priority
        Route::get('footer-category/manage-priority/{endepid?}/{position?}', [FooterCategoryController::class, 'managePriority'])->name('footer-category.priority.manage');
        Route::post('footer-category/update-priority', [FooterCategoryController::class, 'updatePriority'])->name('footer-category.priority.update');
        // For Sub category
        Route::get('footer-sub-category/manage-priority/{endepid?}/{position?}', [FooterSubCategoryController::class, 'manageSubPriority'])->name('footer-sub-category.priority.manage');
        Route::post('footer-sub-category/update-priority', [FooterSubCategoryController::class, 'updateSubPriority'])->name('footer-sub-category.priority.update');

        /**  footer-subcategory routes **/
        Route::match(['get', 'post'], '/footer-subcategory/{endesid?}', [App\Http\Controllers\Admin\FooterSubCategoryController::class, 'index'])->name('footer-subcategory.index');
        Route::match(['get', 'post'], 'footer-subcategory/add/{endesid?}', [App\Http\Controllers\Admin\FooterSubCategoryController::class, 'add'])->name('footer-subcategory.add');
        Route::match(['get', 'post'], 'footer-subcategory/edit/{endesid?}', [App\Http\Controllers\Admin\FooterSubCategoryController::class, 'update'])->name('footer-subcategory.edit');
        Route::get('footer-subcategory/update-status/{id}/{status}', [App\Http\Controllers\Admin\FooterSubCategoryController::class, 'changeStatus'])->name('footer-subcategory.status');
        Route::get('footer-subcategory/delete/{endesid?}', [App\Http\Controllers\Admin\FooterSubCategoryController::class, 'delete'])->name('footer-subcategory.delete');
        Route::post('subcategory-update-order-logic', [App\Http\Controllers\Admin\FooterSubCategoryController::class, 'updateSubFooterCategoryOrderLogic'])->name('footer-subcategory.updateSubFooterCategoryOrderLogic');
        /* footer-subcategory routes */

        /* Lookups manager  module  routing start here */
        Route::match(['get', 'post'], '/lookups-manager/{type}', [App\Http\Controllers\Admin\LookupsController::class, 'index'])->name('lookups-manager.index');
        Route::match(['get', 'post'], '/lookups-manager/add/{type}', [App\Http\Controllers\Admin\LookupsController::class, 'add'])->name('lookups-manager.add');
        Route::get('lookups-manager/destroy/{enlokid?}', [App\Http\Controllers\Admin\LookupsController::class, 'destroy'])->name('lookups-manager.delete');
        Route::get('lookups-manager/update-status/{id}/{status}', [App\Http\Controllers\Admin\LookupsController::class, 'changeStatus'])->name('lookups-manager.status');
        Route::match(['get', 'post'], 'lookups-manager/{type?}/edit/{enlokid?}', [App\Http\Controllers\Admin\LookupsController::class, 'update'])->name('lookups-manager.edit');
        /* Lookups manager  module  routing start here */

        /* Lookups manager  module  routing start here */
        Route::match(['get', 'post'], 'seo-page-manager', [App\Http\Controllers\Admin\SeoPageController::class, 'index'])->name('SeoPage.index');
        // Route::post('seo-page-manager', [App\Http\Controllers\Admin\SeoPageController::class, 'index'])->name('SeoPage.index');
        Route::get('seo-page-manager/add-doc', [App\Http\Controllers\Admin\SeoPageController::class, 'addDoc'])->name('SeoPage.create');
        Route::post('seo-page-manager/add-doc', [App\Http\Controllers\Admin\SeoPageController::class, 'saveDoc'])->name('SeoPage.save');
        Route::get('seo-page-manager/edit-doc/{id}', [App\Http\Controllers\Admin\SeoPageController::class, 'editDoc'])->name('SeoPage.edit');
        Route::post('seo-page-manager/edit-doc/{id}', [App\Http\Controllers\Admin\SeoPageController::class, 'updateDoc'])->name('SeoPage.update');
        Route::any('seo-page-manager/delete-page/{id}', [App\Http\Controllers\Admin\SeoPageController::class, 'deletePage'])->name('SeoPage.delete');

        /** settings routing**/
        //   Route::resource('settings', App\Http\Controllers\Admin\SettingsController::class);
        Route::match(['get', 'post'], '/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::match(['get', 'post'], '/settings/create', [App\Http\Controllers\Admin\SettingsController::class, 'create'])->name('settings.create');
        Route::match(['get', 'post'], '/settings/save', [App\Http\Controllers\Admin\SettingsController::class, 'store'])->name('settings.store');
        Route::match(['get', 'post'], '/settings/edit/{enuserid}', [App\Http\Controllers\Admin\SettingsController::class, 'edit'])->name('settings.edit');
        Route::match(['get', 'post'], '/settings/update/{enuserid}', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::get('settings/show/{enuserid}', [App\Http\Controllers\Admin\SettingsController::class, 'show'])->name('cms-manager.show');
        Route::match(['get', 'post'], '/settings/prefix/{enslug?}', [App\Http\Controllers\Admin\SettingsController::class, 'prefix'])->name('settings.prefix');
        Route::get('settings/destroy/{ensetid?}', [App\Http\Controllers\Admin\SettingsController::class, 'destroy'])->name('settings.delete');
        Route::post('/admin/invoice-settings/store', [App\Http\Controllers\Admin\InvoiceSettingController::class, 'store'])->name('admin-admin.invoice-settings.store');
        Route::get('/admin/invoice-settings/states/{country}', [App\Http\Controllers\Admin\InvoiceSettingController::class, 'getStates'])->name('settings.getStates');
        Route::get('/admin/invoice-settings/cities/{state}', [App\Http\Controllers\Admin\InvoiceSettingController::class, 'getCities'])->name('settings.getCities');

        /** settings routing**/
        // Admin Change Password

        // Route for displaying the change password form
        Route::match(['get', 'post'], '/settings/changepassword', [App\Http\Controllers\Admin\SettingsController::class, 'changepassword'])->name('settings.changepassword');

        // Route for handling the password update
        Route::match(['post'], '/settings/updatepassword', [App\Http\Controllers\Admin\SettingsController::class, 'updatepassword'])->name('settings.updatepassword');
        /**** couriers ****/
        Route::resource('couriers', CouriersController::class);
        Route::get('couriers/status/{id}/{status}', [CouriersController::class, 'status'])->name('couriers.status');
        /**** country ****/
        Route::resource('country', CountryController::class);
        Route::get('country/status/{id}/{status}', [CountryController::class, 'status'])->name('country.status');

        /*** state ****/
        Route::match(['get'], '/states', [StateController::class, 'index'])->name('state.index');
        Route::match(['get'], '/state/create', [StateController::class, 'create'])->name('state.create');
        Route::match(['get'], '/state/edit/{id}', [StateController::class, 'edit'])->name('state.edit');
        Route::match(['get'], '/state/show/{id}', [StateController::class, 'show'])->name('state.show');
        Route::match(['post'], '/state/store', [StateController::class, 'store'])->name('state.store');
        Route::match(['put', 'post'], '/state/update', [StateController::class, 'update'])->name('state.update');
        Route::match(['post'], '/state/delete', [StateController::class, 'destroy'])->name('state.destroy');

        /** city */
        Route::match(['get'], '/cities', [CityController::class, 'index'])->name('city.index');
        Route::match(['get'], '/city/create', [CityController::class, 'create'])->name('city.create');
        Route::match(['get'], '/city/edit/{id}', [CityController::class, 'edit'])->name('city.edit');
        Route::match(['post'], '/city/store', [CityController::class, 'store'])->name('city.store');
        Route::match(['post'], '/city/update', [CityController::class, 'update'])->name('city.update');
        Route::match(['post'], '/city/delete', [CityController::class, 'destroy'])->name('city.destroy');



        /** currencies routing**/
        Route::match(['get', 'post'], '/currencies', [App\Http\Controllers\Admin\CurrencyController::class, 'index'])->name('currencies.index');
        Route::match(['get', 'post'], '/currencies/create', [App\Http\Controllers\Admin\CurrencyController::class, 'create'])->name('currencies.create');
        Route::match(['get', 'post'], '/currencies/save', [App\Http\Controllers\Admin\CurrencyController::class, 'save'])->name('currencies.save');
        Route::match(['get', 'post'], '/currencies/edit/{enuserid}', [App\Http\Controllers\Admin\CurrencyController::class, 'edit'])->name('currencies.edit');
        Route::match(['get', 'post'], '/currencies/update/{enuserid}', [App\Http\Controllers\Admin\CurrencyController::class, 'update'])->name('currencies.update');
        Route::get('currencies/show/{enuserid}', [App\Http\Controllers\Admin\CurrencyController::class, 'show'])->name('cms-manager.show');
        Route::get('currencies/update-status/{id}/{status}', [App\Http\Controllers\Admin\CurrencyController::class, 'changeStatus'])->name('currencies.status');
        Route::get('currencies/destroy/{ensetid?}', [App\Http\Controllers\Admin\CurrencyController::class, 'destroy'])->name('currencies.delete');
        Route::get('currencies/mark-default/{ensetid?}', [App\Http\Controllers\Admin\CurrencyController::class, 'makeDefault'])->name('currencies.makeDefault');
        /** currencies routing**/

        /** payment-methods routing**/
        Route::match(['get', 'post'], '/payment-methods', [App\Http\Controllers\Admin\PaymentMethodController::class, 'index'])->name('payment-methods.index');
        Route::match(['get', 'post'], '/payment-methods/create', [App\Http\Controllers\Admin\PaymentMethodController::class, 'create'])->name('payment-methods.create');
        Route::match(['get', 'post'], '/payment-methods/save', [App\Http\Controllers\Admin\PaymentMethodController::class, 'save'])->name('payment-methods.save');
        Route::match(['get', 'post'], '/payment-methods/edit/{enuserid}', [App\Http\Controllers\Admin\PaymentMethodController::class, 'edit'])->name('payment-methods.edit');
        Route::match(['get', 'post'], '/payment-methods/update/{enuserid}', [App\Http\Controllers\Admin\PaymentMethodController::class, 'update'])->name('payment-methods.update');
        Route::get('payment-methods/show/{enuserid}', [App\Http\Controllers\Admin\PaymentMethodController::class, 'show'])->name('cms-manager.show');
        Route::get('payment-methods/update-status/{id}/{status}', [App\Http\Controllers\Admin\PaymentMethodController::class, 'changeStatus'])->name('payment-methods.status');
        Route::get('payment-methods/destroy/{ensetid?}', [App\Http\Controllers\Admin\PaymentMethodController::class, 'destroy'])->name('payment-methods.delete');

        /** payment-methods routing**/

        /** faqs routing**/
        Route::match(['get', 'post'], '/faqs', [App\Http\Controllers\Admin\FaqController::class, 'index'])->name('faqs.index');
        Route::match(['get', 'post'], '/faqs/create', [App\Http\Controllers\Admin\FaqController::class, 'create'])->name('faqs.create');
        Route::match(['get', 'post'], '/faqs/save', [App\Http\Controllers\Admin\FaqController::class, 'save'])->name('faqs.save');
        Route::match(['get', 'post'], '/faqs/edit/{enuserid}', [App\Http\Controllers\Admin\FaqController::class, 'edit'])->name('faqs.edit');
        Route::match(['get', 'post'], '/faqs/update/{enuserid}', [App\Http\Controllers\Admin\FaqController::class, 'update'])->name('faqs.update');
        Route::get('faqs/show/{enuserid}', [App\Http\Controllers\Admin\FaqController::class, 'show'])->name('cms-manager.show');
        Route::get('faqs/update-status/{id}/{status}', [App\Http\Controllers\Admin\FaqController::class, 'changeStatus'])->name('faqs.status');
        Route::get('faqs/destroy/{ensetid?}', [App\Http\Controllers\Admin\FaqController::class, 'destroy'])->name('faqs.delete');

        /** faqs routing**/

        /** price-drops routing**/
        Route::match(['get', 'post'], '/price-drops', [App\Http\Controllers\Admin\PriceDropController::class, 'index'])->name('price-drops.index');
        Route::match(['get', 'post'], '/price-drops/create', [App\Http\Controllers\Admin\PriceDropController::class, 'create'])->name('price-drops.create');
        Route::match(['get', 'post'], '/price-drops/save', [App\Http\Controllers\Admin\PriceDropController::class, 'save'])->name('price-drops.save');
        Route::match(['get', 'post'], '/price-drops/edit/{enuserid}', [App\Http\Controllers\Admin\PriceDropController::class, 'edit'])->name('price-drops.edit');
        Route::match(['get', 'post'], '/price-drops/update/{enuserid}', [App\Http\Controllers\Admin\PriceDropController::class, 'update'])->name('price-drops.update');
        Route::get('price-drops/show/{enuserid}', [App\Http\Controllers\Admin\PriceDropController::class, 'show'])->name('cms-manager.show');
        Route::get('price-drops/update-status/{id}/{status}', [App\Http\Controllers\Admin\PriceDropController::class, 'changeStatus'])->name('price-drops.status');
        Route::get('price-drops/destroy/{ensetid?}', [App\Http\Controllers\Admin\PriceDropController::class, 'destroy'])->name('price-drops.delete');
        Route::get('get-price-drops', [App\Http\Controllers\Admin\PriceDropController::class, 'getPriceData'])->name('price-drops.getPriceData');
        /** price-drops routing**/

        /* cms manager routes */
        //   Route::resource('cms-manager', App\Http\Controllers\Admin\CmspagesController::class);
        Route::match(['get', 'post'], '/cms-manager', [App\Http\Controllers\Admin\CmspagesController::class, 'index'])->name('cms-manager.index');
        Route::match(['get', 'post'], '/cms-manager/create', [App\Http\Controllers\Admin\CmspagesController::class, 'create'])->name('cms-manager.create');
        Route::match(['get', 'post'], '/cms-manager/save', [App\Http\Controllers\Admin\CmspagesController::class, 'store'])->name('cms-manager.store');
        Route::match(['get', 'post'], '/cms-manager/edit/{enuserid}', [App\Http\Controllers\Admin\CmspagesController::class, 'edit'])->name('cms-manager.edit');
        Route::match(['get', 'post'], '/cms-manager/update/{enuserid}', [App\Http\Controllers\Admin\CmspagesController::class, 'update'])->name('cms-manager.update');
        Route::get('cms-manager/show/{enuserid}', [App\Http\Controllers\Admin\CmspagesController::class, 'show'])->name('cms-manager.show');
        Route::get('cms-manager/destroy/{encmsid?}', [App\Http\Controllers\Admin\CmspagesController::class, 'destroy'])->name('cms-manager.delete');
        //  cms manager routes

        /*Banner Route*/
        Route::get('banner', [App\Http\Controllers\Admin\BannerController::class, 'index'])->name('Banner.index');
        Route::get('slider-add', [App\Http\Controllers\Admin\BannerController::class, 'create'])->name('Banner.create');
        Route::post('slider-save', [App\Http\Controllers\Admin\BannerController::class, 'save'])->name('Banner.save');
        Route::get('slider-status/{id}/{status}', [App\Http\Controllers\Admin\BannerController::class, 'changeStatus'])->name('Banner.status');
        Route::get('banner/{id}', [App\Http\Controllers\Admin\BannerController::class, 'edit'])->name('Banner.edit');
        Route::post('slider-update/{id}', [App\Http\Controllers\Admin\BannerController::class, 'update'])->name('Banner.update');
        Route::get('slider-delete/{id}', [App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('Banner.delete');
        Route::get('sliders/show/{enuserid}', [App\Http\Controllers\Admin\BannerController::class, 'show'])->name('Banner.show');
        Route::post('/admin/update-toggle-status', [BannerController::class, 'updateToggleStatus'])->name('Banner.update.toggle.status');
        /*Banner Route*/
		
		/* Referal Update History */
        Route::match(['get', 'post'], '/referral-histories-update', [App\Http\Controllers\Admin\ReferralSettingUpdateHistoryController::class, 'index'])->name('referral-setting-update-histories.index');
        Route::match(['get', 'post'], '/referral-histories-update/create', [App\Http\Controllers\Admin\ReferralSettingUpdateHistoryController::class, 'create'])->name('referral-setting-update-histories.create');
        Route::match(['get', 'post'], '/referral-histories-update/save', [App\Http\Controllers\Admin\ReferralSettingUpdateHistoryController::class, 'save'])->name('referral-setting-update-histories.save');
        Route::match(['get', 'post'], '/referral-histories-update/edit/{enuserid}', [App\Http\Controllers\Admin\ReferralSettingUpdateHistoryController::class, 'edit'])->name('referral-setting-update-histories.edit');
        Route::match(['get', 'post'], '/referral-histories-update/update/{enuserid}', [App\Http\Controllers\Admin\ReferralSettingUpdateHistoryController::class, 'update'])->name('referral-setting-update-histories.update');
        Route::get('referral-histories-update/show/{enuserid}', [App\Http\Controllers\Admin\ReferralSettingUpdateHistoryController::class, 'show'])->name('referral-setting-update-histories.show');
        Route::get('referral-histories-update/destroy/{enuserid?}', [App\Http\Controllers\Admin\ReferralSettingUpdateHistoryController::class, 'destroy'])->name('referral-setting-update-histories.delete');
        Route::post('referral-histories-update/update-status/', [App\Http\Controllers\Admin\ReferralSettingUpdateHistoryController::class, 'changeStatus'])->name('referral-setting-update-histories.status');
		Route::post('referral-histories-update/index2', [App\Http\Controllers\Admin\ReferralSettingUpdateHistoryController::class, 'indexUpdated'])->name('referral-setting-update-histories.index2');
        /* Referal Update History */

        /* Testimonial routes */
        Route::match(['get', 'post'], '/testimonials', [App\Http\Controllers\Admin\TestimonialController::class, 'index'])->name('testimonials.index');
        Route::match(['get', 'post'], '/testimonials/create', [App\Http\Controllers\Admin\TestimonialController::class, 'create'])->name('testimonials.create');
        Route::match(['get', 'post'], '/testimonials/save', [App\Http\Controllers\Admin\TestimonialController::class, 'save'])->name('testimonials.save');
        Route::match(['get', 'post'], '/testimonials/edit/{enuserid}', [App\Http\Controllers\Admin\TestimonialController::class, 'edit'])->name('testimonials.edit');
        Route::match(['get', 'post'], '/testimonials/update/{enuserid}', [App\Http\Controllers\Admin\TestimonialController::class, 'update'])->name('testimonials.update');
        Route::get('testimonials/show/{enuserid}', [App\Http\Controllers\Admin\TestimonialController::class, 'show'])->name('testimonials.show');
        Route::get('testimonials/destroy/{enuserid?}', [App\Http\Controllers\Admin\TestimonialController::class, 'destroy'])->name('testimonials.delete');
        Route::get('testimonials/update-status/{id}/{status}', [App\Http\Controllers\Admin\TestimonialController::class, 'changeStatus'])->name('testimonials.status');
        /* Testimonial routes */

        /** email templates routing**/
        Route::resource('email-templates', App\Http\Controllers\Admin\EmailtemplateController::class);
        Route::match(['get', 'post'], 'email-templates/get-constant', [App\Http\Controllers\Admin\EmailtemplateController::class, 'getConstant'])->name('email-templates.getConstant');
        /** email templates routing**/

        /** Variant routes **/
        Route::match(['get', 'post'], '/variants', [App\Http\Controllers\Admin\VariantController::class, 'index'])->name('variants.index');
        Route::post('/variants/add', [App\Http\Controllers\Admin\VariantController::class, 'store'])->name('variants.store');
        Route::resource('variants', App\Http\Controllers\Admin\VariantController::class)->except(['index', 'store']);
        Route::get('variants/update-status/{id}/{status}', [App\Http\Controllers\Admin\VariantController::class, 'changeStatus'])->name('variants.status');
        Route::get('variants/destroy/{endepid?}', [App\Http\Controllers\Admin\VariantController::class, 'destroy'])->name('variants.delete');
        Route::get('allspecification/del', [App\Http\Controllers\Admin\VariantController::class, 'DelAllspecifications'])->name('variants.DelAllspecifications');
        Route::post('delete-variant-value', [VariantController::class, 'ajaxDeleteVariantValue'])->name('ajax-delete-variant-value');
        /* Variant routes */

        /** Specification Group routes **/
        Route::match(['get', 'post'], '/specification-groups', [App\Http\Controllers\Admin\SpecificationGroupController::class, 'index'])->name('specification_groups.index');
        Route::post('/specification-groups/add', [App\Http\Controllers\Admin\SpecificationGroupController::class, 'store'])->name('specification_groups.store');
        Route::resource('specification-groups', App\Http\Controllers\Admin\SpecificationGroupController::class)->except(['index', 'store']);
        Route::get('specification-groups/update-status/{id}/{status}', [App\Http\Controllers\Admin\SpecificationGroupController::class, 'changeStatus'])->name('specification_groups.status');
        Route::get('specification-groups/destroy/{endepid?}', [App\Http\Controllers\Admin\SpecificationGroupController::class, 'destroy'])->name('specification_groups.delete');
        Route::get('allspecificationGroups/remove', [App\Http\Controllers\Admin\SpecificationGroupController::class, 'removeSpeGroups'])->name('specification_groups.removeSpeGroups');
        /* Specification Group routes */

        /**  Specification routes **/
        Route::match(['get', 'post'], '/specifications/{endesid?}', [App\Http\Controllers\Admin\SpecificationController::class, 'index'])->name('specifications.index');
        Route::match(['get', 'post'], 'specifications/add/{endesid?}', [App\Http\Controllers\Admin\SpecificationController::class, 'add'])->name('specifications.add');
        Route::match(['get', 'post'], 'specifications/edit/{endesid?}', [App\Http\Controllers\Admin\SpecificationController::class, 'update'])->name('specifications.edit');
        Route::get('specifications/update-status/{id}/{status}', [App\Http\Controllers\Admin\SpecificationController::class, 'changeStatus'])->name('specifications.status');
        Route::get('specifications/delete/{endesid?}', [App\Http\Controllers\Admin\SpecificationController::class, 'delete'])->name('specifications.delete');
        /* Specification routes */

        Route::resource('frontend-menus', FrontendMenuController::class);
        Route::get('frontend-menus/{id}/{status}', [FrontendMenuController::class, 'changeStatus'])->name('frontend-menus.status');


        Route::resource('pincodes', PincodesController::class);
        Route::get('pincodes/status/{id}/{status}', [PincodesController::class, 'status'])->name('country.status');

        Route::get('/get-states/{countryId}', function ($countryId) {
            $states = State::where('country_id', $countryId)->get();
            return response()->json($states);
        });


        Route::get('/get-cities/{stateId}', function ($stateId) {
            $cities = City::where('state_id', $stateId)->get();
            return response()->json($cities);
        });
    });
    
    
    
});
