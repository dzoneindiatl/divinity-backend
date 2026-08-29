<?php
$WEBSITE_URL				=	env("APP_URL");
$FRONT_WEBSITE_URL = env('WEBSITE_URL');
$BASE_PATH = '/home/divinityjewels/public_html/public/';
if(!empty($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1')){
	$BASE_PATH =  'C:/xampp/htdocs/divinity-frontend/public/';
}

return [
	'FRONT_WEBSITE_URL' => $FRONT_WEBSITE_URL,
	'EMAIL_LOGO' => $FRONT_WEBSITE_URL . 'public/assets/images/tjap-logo.png',

	'PRODUCTS_IMAGES' => 'products/images',
	'ROOT'     				=> base_path(),
	'APP_PATH'     			=> app_path(),
	'DS'     				=> '/',
	'WEBSITE_URL'							=> $WEBSITE_URL,
	'ADMIN_WEBSITE_URL'							=> env("ADMIN_APP_URL"),

	'USER_IMAGE_PATH'                       => $FRONT_WEBSITE_URL . 'public/uploads/users/',
	'USER_IMAGE_ROOT_PATH'                       => $BASE_PATH . 'uploads/users/',
	'USER_IMAGE_URL'                       => $FRONT_WEBSITE_URL . 'uploads/users/',

	'SIZECHART_IMAGE_PATH'                       => $FRONT_WEBSITE_URL . 'uploads/size_charts/',
	'SIZECHART_IMAGE_ROOT_PATH'                       => $BASE_PATH . 'uploads/size_charts/',
	'SIZECHART_IMAGE_URL'                       => $FRONT_WEBSITE_URL . 'uploads/size_charts/',

	'STAFF_IMAGE_PATH'                       => $FRONT_WEBSITE_URL . 'uploads/staffs/',
	'STAFF_IMAGE_ROOT_PATH'                       => $BASE_PATH . 'uploads/staffs/',
	'STAFF_IMAGE_URL'                       => $FRONT_WEBSITE_URL . 'uploads/staffs/',
	'CATEGORY_IMAGE_PATH'                       => $FRONT_WEBSITE_URL . 'uploads/categories/',
	'CATEGORY_IMAGE_ROOT_PATH'                  => $BASE_PATH . 'uploads/categories/',
	'CATEGORY_IMAGE_URL'                       => $FRONT_WEBSITE_URL . 'uploads/categories/',
	

	'CATEGORY_VIDEO_PATH'                       => $FRONT_WEBSITE_URL . 'uploads/categories-video/',
	'CATEGORY_VIDEO_ROOT_PATH'                       => $BASE_PATH . 'uploads/categories-video/',
	'CATEGORY_VIDEO_URL'                       => $FRONT_WEBSITE_URL . 'uploads/categories-video/',

	'INSTAGRAM_SLIDERS_IMAGE_PATH'                       => $FRONT_WEBSITE_URL . 'uploads/instagram_sliders/',
	'INSTAGRAM_SLIDERS_IMAGE_ROOT_PATH'                       => $BASE_PATH . 'uploads/instagram_sliders/',
	'INSTAGRAM_SLIDERS_IMAGE_URL'                       => $FRONT_WEBSITE_URL . 'uploads/instagram_sliders/',

	'BANNER_IMAGE_PATH'                       => $FRONT_WEBSITE_URL . 'uploads/banners/',
	'BANNER_IMAGE_ROOT_PATH'                       => $BASE_PATH . 'uploads/banners/',
	'BANNER_IMAGE_URL'                       => $FRONT_WEBSITE_URL . 'uploads/banners/',

	'BANNER_VIDEO_PATH'                       => $FRONT_WEBSITE_URL . 'uploads/banners-video/',
	'BANNER_VIDEO_ROOT_PATH'                       => $BASE_PATH . 'uploads/banners-video/',
	'BANNER_VIDEO_URL'                       => $FRONT_WEBSITE_URL . 'uploads/banners-video/',

	'SETTINGS_IMAGE_PATH'                       => $FRONT_WEBSITE_URL . 'public/uploads/settings/',
	'SETTINGS_IMAGE_ROOT_PATH'                       => $BASE_PATH . 'uploads/settings/',
	'SETTINGS_IMAGE_URL'                       => $FRONT_WEBSITE_URL . 'uploads/settings/',

	'PRODUCT_IMAGE_PATH'                       => $FRONT_WEBSITE_URL . 'uploads/products/',
	'PRODUCT_IMAGE_PATH_LOCAL'                       => $FRONT_WEBSITE_URL . 'uploads/products/',
	'PRODUCT_IMAGE_ROOT_PATH'                  =>  $BASE_PATH . 'uploads/products/',
	'PRODUCT_IMAGE_URL'                       => $FRONT_WEBSITE_URL . 'uploads/products/',
	'PRODUCT_IMAGE_URL_THUMBNAIL'                => $FRONT_WEBSITE_URL . 'uploads/products/thumbnail/',

	'ORDER_INVOICE_PATH'                       => $FRONT_WEBSITE_URL . 'public/uploads/invoices/',
	'ORDER_INVOICE_ROOT_PATH'                       => $BASE_PATH . 'uploads/invoices/',
	'ORDER_INVOICE_URL'                       => $FRONT_WEBSITE_URL . 'uploads/invoices/',

	'TESTIMONIAL_IMAGE_PATH'                       => $FRONT_WEBSITE_URL . 'uploads/testimonials/',
	'TESTIMONIAL_IMAGE_ROOT_PATH'                       => $BASE_PATH . 'uploads/testimonials/',
	'TESTIMONIAL_IMAGE_URL'                       => $FRONT_WEBSITE_URL . 'uploads/testimonials/',

	'IMAGE_PATH'                       => $FRONT_WEBSITE_URL . 'public/img/',
	'IMAGE_ROOT_PATH'                       => $BASE_PATH . 'public/img/',
	'IMAGE_URL'                       => $FRONT_WEBSITE_URL . 'public/img/',

	'REVIEW_IMAGE_ROOT_PATH'          => $BASE_PATH . 'uploads/reviews/',
	'REVIEW_IMAGE_URL'          => $FRONT_WEBSITE_URL . 'uploads/reviews/',

	'Country_IMAGE_PATH'                       => $FRONT_WEBSITE_URL . 'public/uploads/countries/',
	'Country_IMAGE_ROOT_PATH'                       => $BASE_PATH . 'uploads/countries/',
	'Country_IMAGE_URL'                       => $FRONT_WEBSITE_URL . 'public/uploads/countries/',
	'SIGNATURE_IMAGE_ROOT_PATH'                       => $BASE_PATH . 'uploads/signatures/',
	'SIGNATURE_IMAGE_URL'                       => $FRONT_WEBSITE_URL . 'uploads/',
	'SPECIFICATION_IMAGE_ROOT_PATH'                       => $BASE_PATH . 'uploads/specifications/',
	'SPECIFICATION_IMAGE_PATH'                       => $FRONT_WEBSITE_URL . 'uploads/specifications/',



	'ACL' => [
		'ACLS_TITLE' => "Acl",
		'ACL_TITLE' => "Acl Management",
	],
	'ROLE_ID' => [
		'SUPER_ADMIN_ROLE_ID' 		=> 1,
		'ADMIN_ROLE_ID' 			=> 2,
		'MANAGER_ROLE_ID' 			=> 3,
		'CUSTOMER_ROLE_ID' 			=> 4,
		'STAFF_ROLE_ID' 			=> 5,
	],
	'DESIGNATION' => [
		'DESIGNATIONS_TITLE' 	=> "Designations",
		'DESIGNATION_TITLE' 	=> "Designation",
	],

	'DEPARTMENT' => [
		'DEPARTMENTS_TITLE' 	=> "Departments",
		'DEPARTMENT_TITLE' 		=> "Department",
	],
	'ATTRIBUTE' => [
		'ATTRIBUTE_TITLE' 	=> "Attribute",
		'ATTRIBUTE_TITLE' 	=> "Attribute",
	],
	'ATTRIBUTE_VALUED' => [
		'ATTRIBUTE_VALUED_TITLE' 	=> "Attribute Value",
		'ATTRIBUTE_VALUED_TITLE' 	=> "Attribute Value",
	],
	'VARIANT' => [
		'VARIANTS_TITLE' 	=> "Variants",
		'VARIANT_TITLE' 		=> "Variant",
	],
	'SPECIFICATION' => [
		'SPECIFICATIONS_TITLE' 	=> "Specifications",
		'SPECIFICATION_TITLE' 		=> "Specification",
	],
	'SPECIFICATION_GROUP' => [
		'SPECIFICATION_GROUPS_TITLE' 	=> "Specification Groups",
		'SPECIFICATION_GROUP_TITLE' 		=> "Specification Group",
	],


	'SETTING_FILE_PATH'	=> base_path() . "/" . 'config' . "/" . 'settings.php',
	'FRONT_SETTING_FILE_PATH'	=> 'F:/projects/divinityindia/frontend/' . "/" . 'config' . "/" . 'settings.php',
];
