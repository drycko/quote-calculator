<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LineItemTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $templates = [

            // ─────────────────────────────────────────
            // DESIGN
            // ─────────────────────────────────────────
            ['name' => 'Design for layout concepts & elements for website', 'category' => 'design', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],

            // ─────────────────────────────────────────
            // DEV — Core Software
            // ─────────────────────────────────────────
            ['name' => 'Wordpress (CMS)', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 600.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Licence Cost for theme (EXCLUDES DIVI)', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 1300.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],

            // ─────────────────────────────────────────
            // DEV — Website Development
            // ─────────────────────────────────────────
            ['name' => 'Custom theme development', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => null, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Languages', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'percentage', 'default_rate' => null, 'default_percentage' => 30.00, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Plus 30% of total value of quote per language'],

            // ─────────────────────────────────────────
            // DEV — Menu & Page Structure
            // ─────────────────────────────────────────
            ['name' => 'Home page — structure and framework', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 2850.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'R1050 for one-pagers'],
            ['name' => 'Main Menu items', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 650.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per main menu item'],
            ['name' => 'Sub-Menu (Drop-down) items', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 550.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per sub-menu item'],
            ['name' => 'Click-through pages', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 350.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per click-through item / blogs to upload'],
            ['name' => 'Cascade content / Toggles', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 65.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per toggle item'],
            ['name' => 'Transfer content from old to new site (per article)', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 50.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per item'],

            // ─────────────────────────────────────────
            // DEV / PLUGIN — Mega Menu
            // ─────────────────────────────────────────
            ['name' => 'Mega Menu: Uber Menu — Configuration', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Mega Menu: Uber Menu — License cost', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 346.50, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],

            // ─────────────────────────────────────────
            // PLUGIN — Popup / Modal
            // ─────────────────────────────────────────
            ['name' => 'Plugin: Floaton license fee', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 350.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: Floaton setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Plugin: Ultimate modal windows license fee', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 380.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: Ultimate modal windows styling and setup', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],

            // ─────────────────────────────────────────
            // PLUGIN — Chat / Social
            // ─────────────────────────────────────────
            ['name' => 'Plugin: Smashballoon license fee (WP & Insta)', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 200.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: Smashballoon setup and styling (WP & Insta)', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 250.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'WhatsApp plugin: license fee', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 250.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'WhatsApp plugin: setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 250.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Plugin: Facebook Messenger setup (FREE)', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 250.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Free widget — setup time only'],
            ['name' => 'Chatty (recurring)', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 350.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => 'Recurring cost'],

            // ─────────────────────────────────────────
            // PLUGIN — Sliders
            // ─────────────────────────────────────────
            ['name' => 'Motopress layer slider', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 40.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Recurring'],
            ['name' => 'Slider Revolution', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 1300.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => 'Recurring'],

            // ─────────────────────────────────────────
            // DEV — Ecommerce System
            // ─────────────────────────────────────────
            ['name' => 'E-commerce system — setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 2500.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Load Products', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 135.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per product (add R35 per product if images included)'],
            ['name' => 'Load Product Variations', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 150.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Load Product Categories', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 80.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per category'],
            ['name' => 'Setup Payment Merchant', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Setup shipping integration — uAfrica', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 950.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Custom Fields (additional info / specifications)', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 90.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Shop Pages (Product, Checkout, Cart, Account)', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 600.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],

            // ─────────────────────────────────────────
            // DEV — Catalogue System
            // ─────────────────────────────────────────
            ['name' => 'Catalogue: E-commerce system — setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 2500.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Catalogue: Load Products', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 100.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per product'],
            ['name' => 'Catalogue: Load Product Categories', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 50.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per category'],
            ['name' => 'Contact Method setup', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],

            // ─────────────────────────────────────────
            // PLUGIN — WooCommerce Add-ons
            // ─────────────────────────────────────────
            ['name' => 'Flycart checkout plugin', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 99.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Once-off'],
            ['name' => 'Wholesale pricing', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 49.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Recurring'],
            ['name' => 'Woo Subscriptions', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 239.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Recurring'],
            ['name' => 'Woo Memberships', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 199.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Recurring'],
            ['name' => 'Donation Plugin (Woo Donation Pro)', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 29.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Recurring'],
            ['name' => 'Price by country', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 59.00, 'default_percentage' => null, 'currency' => 'EUR', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Recurring'],
            ['name' => 'Product timer', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 169.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Once-off'],
            ['name' => 'Automate Woo', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 99.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Recurring'],
            ['name' => 'Refer a friend (needs Automate Woo)', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 79.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Recurring'],
            ['name' => 'Hotel Booking addon', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 199.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Once-off'],
            ['name' => 'Hotel Booking Woocommerce payment extender', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 89.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Once-off'],
            ['name' => 'Woocommerce Filter plugin', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 45.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Once-off'],
            ['name' => 'Woocommerce Table Rate shipping', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 99.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Recurring'],
            ['name' => 'Advanced Order Export For WooCommerce', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 100.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Lifetime'],
            ['name' => 'Product Import Export Plugin For WooCommerce', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 69.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Recurring'],
            ['name' => 'Import and Export WordPress Users and WooCommerce Customers', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 69.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Recurring'],
            ['name' => 'All invite codes — Registration only access', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 30.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Lifetime'],
            ['name' => 'Hotel Booking system', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 89.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => '1 site'],

            // ─────────────────────────────────────────
            // PLUGIN — Directory Listings
            // ─────────────────────────────────────────
            ['name' => 'Directory Listings: Plugin cost', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 199.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Recurring'],
            ['name' => 'Directory Listings: Setup of plugin', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 80.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Creating pages etc.'],
            ['name' => 'Directory Listings: Listings to be loaded', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 60.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per listing'],
            ['name' => 'Directory Listings: Load Categories', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 19.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per category'],
            ['name' => 'Directory Add-on: Advanced Search', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 89.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Directory Add-on: Custom Post Type', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 89.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Directory Add-on: Pricing Manager', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 89.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Directory Add-on: Google Captcha', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 59.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Directory Add-on: Payment Gateway (Payfast / Paygate / PayU)', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 39.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => null],

            // ─────────────────────────────────────────
            // PLUGIN — Auction System
            // ─────────────────────────────────────────
            ['name' => 'Auction System: Plugin cost', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 119.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Auction System: Setup of plugin', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Auction System: Load Products', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 100.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per listing'],
            ['name' => 'Auction System: Load Categories', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 50.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per category'],

            // ─────────────────────────────────────────
            // PLUGIN — Events Management
            // ─────────────────────────────────────────
            ['name' => 'Plugin: WooEvents Licensing', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 600.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: WooEvents setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 1200.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Plugin: EventOn Licensing', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 450.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: EventOn setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 1200.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Plugin: FooEvents for Woocommerce Licensing', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 900.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: FooEvents for Woocommerce setup', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 1200.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Plugin: EventsPlus Licensing', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 600.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: EventsPlus setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 1200.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],

            // ─────────────────────────────────────────
            // PLUGIN — Ads Management
            // ─────────────────────────────────────────
            ['name' => 'AdRotate', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 39.00, 'default_percentage' => null, 'currency' => 'EUR', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Once-off'],

            // ─────────────────────────────────────────
            // DEV — Special Access & Membership
            // ─────────────────────────────────────────
            ['name' => 'Special access levels/groups', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 750.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Membership system (Wordpress)', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 2800.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],

            // ─────────────────────────────────────────
            // DEV / PLUGIN — Custom Plugins
            // ─────────────────────────────────────────
            ['name' => 'Fruit and Produce calendar', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 6500.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Table Press Filter plugin', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 15.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'USD Fixed'],
            ['name' => 'Property Listings — Custom Fields (ACF Pro)', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 45.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Recurring'],

            // ─────────────────────────────────────────
            // DEV / PLUGIN — Galleries
            // ─────────────────────────────────────────
            ['name' => 'Setup of gallery', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Robo Gallery plugin', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 800.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Video Gallery Plugin', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 800.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],

            // ─────────────────────────────────────────
            // PLUGIN — Appointment Booking
            // ─────────────────────────────────────────
            ['name' => 'Premium Plugin: Bookly license fee', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 2200.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Premium Plugin: Bookly setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 1200.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Plugin: Bookme license fee', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 500.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: Bookme setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 800.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Gravity Forms — gAppointment addon', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 1200.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Gravity Forms — gAppointment styling & setup', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 800.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],

            // ─────────────────────────────────────────
            // DEV / PLUGIN — Store Locator
            // ─────────────────────────────────────────
            ['name' => 'Plugin: Super store locator license fee', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 710.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: Store locator (Map with pins) — setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 350.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Plugin: Store locator pin setup', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 35.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per store'],

            // ─────────────────────────────────────────
            // DEV / PLUGIN — Gravity Forms
            // ─────────────────────────────────────────
            ['name' => 'Gravity Forms License', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 850.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Gravity Forms: Setup custom forms (per form)', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 150.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Gravity Forms Add-on: Salesforce', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 89.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Gravity Forms Add-on: Booking System (gAppointments)', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 80.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Gravity Forms — Fillable PDF', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 2850.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => null],

            // ─────────────────────────────────────────
            // DEV / PLUGIN — Caldera Forms
            // ─────────────────────────────────────────
            ['name' => 'Caldera Forms License', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 250.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Caldera Forms: Setup custom forms (per form)', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],

            // ─────────────────────────────────────────
            // PLUGIN — Blog / Post Styling
            // ─────────────────────────────────────────
            ['name' => 'Plugin: Content Views Pro licensing', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 700.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: Content Views setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 800.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],

            // ─────────────────────────────────────────
            // PLUGIN — File Download/Upload
            // ─────────────────────────────────────────
            ['name' => 'File Download/Upload Module: license fee', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 300.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'File Download/Upload: Files to be uploaded', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 20.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per file'],

            // ─────────────────────────────────────────
            // DEV — Job Board
            // ─────────────────────────────────────────
            ['name' => 'Job Board: Plugin listings (free) setup', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 250.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Job Board: Plugin setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],

            // ─────────────────────────────────────────
            // PLUGIN — Live Chat
            // ─────────────────────────────────────────
            ['name' => 'Plugin: FluentChat Licensing', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 550.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: FluentChat setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Plugin: Live Chat Unlimited', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 1000.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: Live Chat Unlimited — setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 600.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'TAWK chat integration', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 550.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Free service — setup cost only'],

            // ─────────────────────────────────────────
            // PLUGIN — Maps
            // ─────────────────────────────────────────
            ['name' => 'Plugin: Interactive World Maps license', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 380.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: Interactive World Maps setup', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 800.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Plugin: Mapplic license', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 525.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],
            ['name' => 'Plugin: Mapplic setup and styling', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 800.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Plugin: Mapplic fee per store', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 50.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per pin/store'],

            // ─────────────────────────────────────────
            // DEV — Miscellaneous Systems
            // ─────────────────────────────────────────
            ['name' => 'Download categories (uploading files to categories)', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 150.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Hotel and Travel Booking system', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 1100.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Team Member plugin', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 850.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Intranet System for Wordpress (plus setup of 5 users)', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 4850.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Weather for Wordpress', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 500.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Custom Integration (CRM)', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 350.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],

            // ─────────────────────────────────────────
            // DEV — Content Creation / Assistance
            // ─────────────────────────────────────────
            ['name' => 'Photographer', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 850.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'As per quote from photographer'],
            ['name' => 'Copywriting', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => null, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'As per quote from copywriter'],
            ['name' => 'Stock photos', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 180.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per item'],
            ['name' => 'Stock video', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 150.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per item'],
            ['name' => 'Prep of info / Project management / Additional meetings', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Transfer of old content to new website', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 1200.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],

            // ─────────────────────────────────────────
            // DEV — Custom Work
            // ─────────────────────────────────────────
            ['name' => 'Integration development', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 1000.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'De-infect and restore hacked websites', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Plugin Install and Setup', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],

            // ─────────────────────────────────────────
            // DEV — Responsiveness
            // ─────────────────────────────────────────
            ['name' => 'Mobile / Responsiveness check', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 550.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'R1050 for one-pagers'],

            // ─────────────────────────────────────────
            // DEV — Organic SEO & Analytics
            // ─────────────────────────────────────────
            ['name' => 'Organic SEO & optimising', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 1200.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Yoast Premium plugin', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'fixed', 'default_rate' => 1350.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => true, 'default_notes' => null],

            // ─────────────────────────────────────────
            // DEV — Training & Travelling
            // ─────────────────────────────────────────
            ['name' => 'Training on CMS system', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 400.00, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => null],
            ['name' => 'Travelling cost outside the 20km radius', 'category' => 'dev', 'template_type' => 'manual', 'calculation_type' => 'hourly', 'default_rate' => 3.60, 'default_percentage' => null, 'currency' => null, 'conversion_rate' => null, 'is_plugin' => false, 'default_notes' => 'Per km'],

            // ─────────────────────────────────────────
            // PLUGIN — Additional Plugin Costs
            // ─────────────────────────────────────────
            ['name' => 'ALL Import / Export — content transfer', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 169.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'USD Fixed'],
            ['name' => 'ACF Pro (Custom Fields)', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 50.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'USD Fixed'],
            ['name' => 'Divi Engine — Custom Fields', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 149.00, 'default_percentage' => null, 'currency' => 'GBP', 'conversion_rate' => 24.00, 'is_plugin' => true, 'default_notes' => 'Annual'],
            ['name' => 'Yoast Premium (annual)', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 99.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Annual'],
            ['name' => 'Weather Widget', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 30.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Annual'],
            ['name' => 'Imagify Image management', 'category' => 'plugin', 'template_type' => 'manual', 'calculation_type' => 'converted', 'default_rate' => 49.00, 'default_percentage' => null, 'currency' => 'USD', 'conversion_rate' => 20.00, 'is_plugin' => true, 'default_notes' => 'Annual'],

        ];

        // Add timestamps to every row
        $rows = array_map(fn($t) => array_merge($t, ['created_at' => $now, 'updated_at' => $now]), $templates);

        DB::table('line_item_templates')->insert($rows);
    }
}
