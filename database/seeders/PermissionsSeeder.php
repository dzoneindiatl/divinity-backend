<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to avoid constraint issues
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the model_has_permissions table
        DB::table('model_has_permissions')->truncate();

        // Truncate the permissions table
        DB::table('permissions')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Users permissions
        Permission::firstOrCreate(['name' => 'view_user', 'menu_name' => 'User_Management']);
        Permission::firstOrCreate(['name' => 'create_user', 'menu_name' => 'User_Management']);
        Permission::firstOrCreate(['name' => 'edit_user', 'menu_name' => 'User_Management']);
        Permission::firstOrCreate(['name' => 'delete_user', 'menu_name' => 'User_Management']);

        // Product permissions
        Permission::firstOrCreate(['name' => 'view_product', 'menu_name' => 'Product_Management']);
        Permission::firstOrCreate(['name' => 'create_product', 'menu_name' => 'Product_Management']);
        Permission::firstOrCreate(['name' => 'edit_product', 'menu_name' => 'Product_Management']);
        Permission::firstOrCreate(['name' => 'delete_product', 'menu_name' => 'Product_Management']);

        // Category Management
        Permission::firstOrCreate(['name' => 'view_category', 'menu_name' => 'Category_Management']);
        Permission::firstOrCreate(['name' => 'create_category', 'menu_name' => 'Category_Management']);
        Permission::firstOrCreate(['name' => 'edit_category', 'menu_name' => 'Category_Management']);
        Permission::firstOrCreate(['name' => 'delete_category', 'menu_name' => 'Category_Management']);

        // Partner Management
        Permission::firstOrCreate(['name' => 'view_partner', 'menu_name' => 'Partner_Management']);
        Permission::firstOrCreate(['name' => 'create_partner', 'menu_name' => 'Partner_Management']);
        Permission::firstOrCreate(['name' => 'edit_partner', 'menu_name' => 'Partner_Management']);
        Permission::firstOrCreate(['name' => 'delete_partner', 'menu_name' => 'Partner_Management']);

        // Staff Management
        Permission::firstOrCreate(['name' => 'view_staff', 'menu_name' => 'Staff_Management']);
        Permission::firstOrCreate(['name' => 'create_staff', 'menu_name' => 'Staff_Management']);
        Permission::firstOrCreate(['name' => 'edit_staff', 'menu_name' => 'Staff_Management']);
        Permission::firstOrCreate(['name' => 'delete_staff', 'menu_name' => 'Staff_Management']);

        // Department Management
        Permission::firstOrCreate(['name' => 'view_department', 'menu_name' => 'Department_Management']);
        Permission::firstOrCreate(['name' => 'create_department', 'menu_name' => 'Department_Management']);
        Permission::firstOrCreate(['name' => 'edit_department', 'menu_name' => 'Department_Management']);
        Permission::firstOrCreate(['name' => 'delete_department', 'menu_name' => 'Department_Management']);

        // Attribute Management
        Permission::firstOrCreate(['name' => 'view_attribute',  'menu_name' => 'Attributes']);
        Permission::firstOrCreate(['name' => 'create_attribute',  'menu_name' => 'Attributes']);
        Permission::firstOrCreate(['name' => 'edit_attribute',  'menu_name' => 'Attributes']);
        Permission::firstOrCreate(['name' => 'delete_attribute',  'menu_name' => 'Attributes']);

        // Tags Management
        Permission::firstOrCreate(['name' => 'view_tag', 'menu_name' => 'Tags']);
        Permission::firstOrCreate(['name' => 'create_tag', 'menu_name' => 'Tags']);
        Permission::firstOrCreate(['name' => 'edit_tag', 'menu_name' => 'Tags']);
        Permission::firstOrCreate(['name' => 'delete_tag', 'menu_name' => 'Tags']);

        // Brand Management
        Permission::firstOrCreate(['name' => 'view_brand', 'menu_name' => 'Brand_Management']);
        Permission::firstOrCreate(['name' => 'create_brand', 'menu_name' => 'Brand_Management']);
        Permission::firstOrCreate(['name' => 'edit_brand', 'menu_name' => 'Brand_Management']);
        Permission::firstOrCreate(['name' => 'delete_brand', 'menu_name' => 'Brand_Management']);

        // Partner Plan Management
        Permission::firstOrCreate(['name' => 'view_partner_plan', 'menu_name' => 'Partner_Plan_Management']);
        Permission::firstOrCreate(['name' => 'create_partner_plan', 'menu_name' => 'Partner_Plan_Management']);
        Permission::firstOrCreate(['name' => 'edit_partner_plan', 'menu_name' => 'Partner_Plan_Management']);
        Permission::firstOrCreate(['name' => 'delete_partner_plan', 'menu_name' => 'Partner_Plan_Management']);

        // Payment Method Management
        Permission::firstOrCreate(['name' => 'view_payment_method', 'menu_name' => 'Payment_Method_Management']);
        Permission::firstOrCreate(['name' => 'create_payment_method', 'menu_name' => 'Payment_Method_Management']);
        Permission::firstOrCreate(['name' => 'edit_payment_method', 'menu_name' => 'Payment_Method_Management']);
        Permission::firstOrCreate(['name' => 'delete_payment_method', 'menu_name' => 'Payment_Method_Management']);

        // Sizechart Management
        Permission::firstOrCreate(['name' => 'view_sizechart', 'menu_name' => 'Sizechart_Management']);
        Permission::firstOrCreate(['name' => 'create_sizechart', 'menu_name' => 'Sizechart_Management']);
        Permission::firstOrCreate(['name' => 'edit_sizechart', 'menu_name' => 'Sizechart_Management']);
        Permission::firstOrCreate(['name' => 'delete_sizechart', 'menu_name' => 'Sizechart_Management']);

        // Orders Management
        Permission::firstOrCreate(['name' => 'view_order', 'menu_name' => 'Order_Management']);

        // Blog Management
        Permission::firstOrCreate(['name' => 'view_blog', 'menu_name' => 'Blog_Management']);
        Permission::firstOrCreate(['name' => 'create_blog', 'menu_name' => 'Blog_Management']);
        Permission::firstOrCreate(['name' => 'edit_blog', 'menu_name' => 'Blog_Management']);
        Permission::firstOrCreate(['name' => 'delete_blog', 'menu_name' => 'Blog_Management']);

        // CMS Management
        Permission::firstOrCreate(['name' => 'view_cms', 'menu_name' => 'CMS_Management']);
        Permission::firstOrCreate(['name' => 'create_cms', 'menu_name' => 'CMS_Management']);
        Permission::firstOrCreate(['name' => 'edit_cms', 'menu_name' => 'CMS_Management']);
        Permission::firstOrCreate(['name' => 'delete_cms', 'menu_name' => 'CMS_Management']);

        // Banner Management
        Permission::firstOrCreate(['name' => 'view_banner', 'menu_name' => 'Banner_Management']);
        Permission::firstOrCreate(['name' => 'create_banner', 'menu_name' => 'Banner_Management']);
        Permission::firstOrCreate(['name' => 'edit_banner', 'menu_name' => 'Banner_Management']);
        Permission::firstOrCreate(['name' => 'delete_banner', 'menu_name' => 'Banner_Management']);

        // SEO Management
        Permission::firstOrCreate(['name' => 'view_seo', 'menu_name' => 'Seo_Management']);
        Permission::firstOrCreate(['name' => 'create_seo', 'menu_name' => 'Seo_Management']);
        Permission::firstOrCreate(['name' => 'edit_seo', 'menu_name' => 'Seo_Management']);
        Permission::firstOrCreate(['name' => 'delete_seo', 'menu_name' => 'Seo_Management']);

        // Email Template Management
        Permission::firstOrCreate(['name' => 'view_email_template', 'menu_name' => 'Email_Templates']);
        Permission::firstOrCreate(['name' => 'create_email_template', 'menu_name' => 'Email_Templates']);
        Permission::firstOrCreate(['name' => 'edit_email_template', 'menu_name' => 'Email_Templates']);
        Permission::firstOrCreate(['name' => 'delete_email_template', 'menu_name' => 'Email_Templates']);

        // Tax Management
        Permission::firstOrCreate(['name' => 'view_taxmanagement', 'menu_name' => 'Tax_Management']);
        Permission::firstOrCreate(['name' => 'create_taxmanagement', 'menu_name' => 'Tax_Management']);
        Permission::firstOrCreate(['name' => 'edit_taxmanagement', 'menu_name' => 'Tax_Management']);
        Permission::firstOrCreate(['name' => 'delete_taxmanagement', 'menu_name' => 'Tax_Management']);

        // Currency Management
        Permission::firstOrCreate(['name' => 'view_currency', 'menu_name' => 'Currency_Management']);
        Permission::firstOrCreate(['name' => 'create_currency', 'menu_name' => 'Currency_Management']);
        Permission::firstOrCreate(['name' => 'edit_currency', 'menu_name' => 'Currency_Management']);
        Permission::firstOrCreate(['name' => 'delete_currency', 'menu_name' => 'Currency_Management']);

        // FAQ Management
        Permission::firstOrCreate(['name' => 'view_faq', 'menu_name' => 'Faq_Management']);
        Permission::firstOrCreate(['name' => 'create_faq', 'menu_name' => 'Faq_Management']);
        Permission::firstOrCreate(['name' => 'edit_faq', 'menu_name' => 'Faq_Management']);
        Permission::firstOrCreate(['name' => 'delete_faq', 'menu_name' => 'Faq_Management']);

        // Slider Management
        Permission::firstOrCreate(['name' => 'view_slider', 'menu_name' => 'Slider_Management']);
        Permission::firstOrCreate(['name' => 'create_slider', 'menu_name' => 'Slider_Management']);
        Permission::firstOrCreate(['name' => 'edit_slider', 'menu_name' => 'Slider_Management']);
        Permission::firstOrCreate(['name' => 'delete_slider', 'menu_name' => 'Slider_Management']);

        // Veriant Management
        Permission::firstOrCreate(['name' => 'view_variant', 'menu_name' => 'Variants_Management']);
        Permission::firstOrCreate(['name' => 'create_variant', 'menu_name' => 'Variants_Management']);
        Permission::firstOrCreate(['name' => 'edit_variant', 'menu_name' => 'Variants_Management']);
        Permission::firstOrCreate(['name' => 'delete_variant', 'menu_name' => 'Variants_Management']);

        // City Management
        Permission::firstOrCreate(['name' => 'view_city', 'menu_name' => 'City_Management']);
        Permission::firstOrCreate(['name' => 'create_city', 'menu_name' => 'City_Management']);
        Permission::firstOrCreate(['name' => 'edit_city', 'menu_name' => 'City_Management']);
        Permission::firstOrCreate(['name' => 'delete_city', 'menu_name' => 'City_Management']);

        // Shipping Management
        Permission::firstOrCreate(['name' => 'view_shipping_companies', 'menu_name' => 'Shipping_Management']);
        Permission::firstOrCreate(['name' => 'create_shipping_companies', 'menu_name' => 'Shipping_Management']);
        Permission::firstOrCreate(['name' => 'edit_shipping_companies', 'menu_name' => 'Shipping_Management']);
        Permission::firstOrCreate(['name' => 'delete_shipping_companies', 'menu_name' => 'Shipping_Management']);

        // Testimonial Management
        Permission::firstOrCreate(['name' => 'view_testimonial', 'menu_name' => 'Testimonial_Management']);
        Permission::firstOrCreate(['name' => 'create_testimonial', 'menu_name' => 'Testimonial_Management']);
        Permission::firstOrCreate(['name' => 'edit_testimonial', 'menu_name' => 'Testimonial_Management']);
        Permission::firstOrCreate(['name' => 'delete_testimonial', 'menu_name' => 'Testimonial_Management']);

        // Price Drop
        Permission::firstOrCreate(['name' => 'view_price_drop', 'menu_name' => 'Price_Drop']);
        Permission::firstOrCreate(['name' => 'create_price_drop', 'menu_name' => 'Price_Drop']);
        Permission::firstOrCreate(['name' => 'edit_price_drop', 'menu_name' => 'Price_Drop']);
        Permission::firstOrCreate(['name' => 'delete_price_drop', 'menu_name' => 'Price_Drop']);

        // Coupon Management
        Permission::firstOrCreate(['name' => 'view_coupan', 'menu_name' => 'Coupon_Management']);
        Permission::firstOrCreate(['name' => 'create_coupan', 'menu_name' => 'Coupon_Management']);
        Permission::firstOrCreate(['name' => 'edit_coupan', 'menu_name' => 'Coupon_Management']);
        Permission::firstOrCreate(['name' => 'delete_coupan', 'menu_name' => 'Coupon_Management']);

        // Referral Histories
        Permission::firstOrCreate(['name' => 'view_referral_histories', 'menu_name' => 'Referral_Histories']);
        Permission::firstOrCreate(['name' => 'create_referral_histories', 'menu_name' => 'Referral_Histories']);
        Permission::firstOrCreate(['name' => 'edit_referral_histories', 'menu_name' => 'Referral_Histories']);
        Permission::firstOrCreate(['name' => 'delete_referral_histories', 'menu_name' => 'Referral_Histories']);

        // Specification Groups
        Permission::firstOrCreate(['name' => 'view_specification_group', 'menu_name' => 'Specification_Groups']);
        Permission::firstOrCreate(['name' => 'create_specification_group', 'menu_name' => 'Specification_Groups']);
        Permission::firstOrCreate(['name' => 'edit_specification_group', 'menu_name' => 'Specification_Groups']);
        Permission::firstOrCreate(['name' => 'delete_specification_group', 'menu_name' => 'Specification_Groups']);

        // Site settings  
        Permission::firstOrCreate(['name' => 'view_site_setting', 'menu_name' => 'Settings']);
        Permission::firstOrCreate(['name' => 'view_social_setting', 'menu_name' => 'Settings']);
        Permission::firstOrCreate(['name' => 'view_reading_setting', 'menu_name' => 'Settings']);
        Permission::firstOrCreate(['name' => 'view_contact_setting', 'menu_name' => 'Settings']);
        Permission::firstOrCreate(['name' => 'view_homepage_setting', 'menu_name' => 'Settings']);
        Permission::firstOrCreate(['name' => 'view_product_setting', 'menu_name' => 'Settings']);
        Permission::firstOrCreate(['name' => 'view_referral_setting', 'menu_name' => 'Settings']);
        Permission::firstOrCreate(['name' => 'view_category_icons', 'menu_name' => 'Settings']);

        //Role  Management
        Permission::firstOrCreate(['name' => 'view_role', 'menu_name' => 'Roles_Manegement']);
        Permission::firstOrCreate(['name' => 'create_role', 'menu_name' => 'Roles_Manegement']);
        Permission::firstOrCreate(['name' => 'edit_role', 'menu_name' => 'Roles_Manegement']);
        Permission::firstOrCreate(['name' => 'delete_role', 'menu_name' => 'Roles_Manegement']);

        //Footer Management
        Permission::firstOrCreate(['name' => 'view_footer_manage', 'menu_name' => 'Footer_Management']);
        Permission::firstOrCreate(['name' => 'create_footer_manage', 'menu_name' => 'Footer_Management']);
        Permission::firstOrCreate(['name' => 'edit_footer_manage', 'menu_name' => 'Footer_Management']);
        Permission::firstOrCreate(['name' => 'delete_footer_manage', 'menu_name' => 'Footer_Management']);

        //About Us Management
        Permission::firstOrCreate(['name' => 'view_about_us_manage', 'menu_name' => 'About_Us_Management']);
        Permission::firstOrCreate(['name' => 'create_about_us_manage', 'menu_name' => 'About_Us_Management']);
        Permission::firstOrCreate(['name' => 'edit_about_us_manage', 'menu_name' => 'About_Us_Management']);
        Permission::firstOrCreate(['name' => 'delete_about_us_manage', 'menu_name' => 'About_Us_Management']);

        //Product Collections Management
        Permission::firstOrCreate(['name' => 'view_collection', 'menu_name' => 'Collections']);
        Permission::firstOrCreate(['name' => 'create_collection', 'menu_name' => 'Collections']);
        Permission::firstOrCreate(['name' => 'edit_collection', 'menu_name' => 'Collections']);
        Permission::firstOrCreate(['name' => 'delete_collection', 'menu_name' => 'Collections']);

        //Shipping Charges Management
        Permission::firstOrCreate(['name' => 'view_shipping', 'menu_name' => 'Shipping_Charges']);
        Permission::firstOrCreate(['name' => 'create_shipping', 'menu_name' => 'Shipping_Charges']);
        Permission::firstOrCreate(['name' => 'edit_shipping', 'menu_name' => 'Shipping_Charges']);
        Permission::firstOrCreate(['name' => 'delete_shipping', 'menu_name' => 'Shipping_Charges']);

        //Zone Manager
        Permission::firstOrCreate(['name' => 'view_zone_manager', 'menu_name' => 'Zone_MANAGER']);
        Permission::firstOrCreate(['name' => 'create_zone_manager', 'menu_name' => 'Zone_MANAGER']);
        Permission::firstOrCreate(['name' => 'edit_zone_manager', 'menu_name' => 'Zone_MANAGER']);
        Permission::firstOrCreate(['name' => 'delete_zone_manager', 'menu_name' => 'Zone_MANAGER']);

        //Shipping Zone
        Permission::firstOrCreate(['name' => 'view_shipping_zone', 'menu_name' => 'Shpping_Zone']);
        Permission::firstOrCreate(['name' => 'create_shipping_zone', 'menu_name' => 'Shpping_Zone']);
        Permission::firstOrCreate(['name' => 'edit_shipping_zone', 'menu_name' => 'Shpping_Zone']);
        Permission::firstOrCreate(['name' => 'delete_shipping_zone', 'menu_name' => 'Shpping_Zone']);

        


    }
}
