/**
 * YARAMAY Computer Maintenance Services
 * Invoice Record System - Core JavaScript Application
 * Features:
 *  - Official YARAMAY Navy Blue & Crimson Red Brand Identity
 *  - Partner Share Division (Mr. Ibraheem / Mr. Fawas / Both 50-50 Split)
 *  - Installment Plan Formula (Full Payment, 3 Months, 6 Months, Custom Term)
 *  - GCash: Ferdenand Sabado (09617190588) or Cash options
 *  - Bilingual Support: English (Default) & Arabic
 */

const App = {
    // Current Language: defaults to English
    lang: localStorage.getItem('app_lang') || 'en',

    currentView: 'dashboard',
    currency: 'PHP',
    taxRate: 0,
    settings: {},
    clients: [],
    products: [],
    invoices: [],
    users: [],
    editingInvoiceId: null,

    // Bilingual Dictionary
    i18n: {
        en: {
            brand_title: 'YARAMAY',
            brand_subtitle: 'Computer Maintenance Services',
            version_text: 'v2.7 Corporate Edition',
            page_subtitle: 'Billing, Client Dossier & Invoice Management System',

            nav_dashboard: 'Admin Dashboard',
            nav_invoices: 'Invoices Record',
            nav_clients: 'Clients Directory',
            nav_users: 'Admin Users',
            nav_security: 'Security Shield & Logs',
            nav_client_portal: 'Client Invoice Portal',
            nav_products: 'Products & Services',
            nav_settings: 'Company Settings',
            nav_backup: 'Backup & Restore',

            title_dashboard: 'Admin Dashboard & Partner Earnings',
            title_invoices: 'Invoices Record & Payment Management',
            title_clients: 'Clients Directory & Financial Statements',
            title_users: 'Admin Users & Access Control',
            title_security: 'Security Shield & Threat Intelligence',
            title_client_portal: 'Client Invoice Request & Installment Plan',
            title_products: 'Products & Services Catalog',
            title_settings: 'Company & Invoicing Settings',
            title_backup: 'Data Backup & Recovery',

            btn_new_invoice: 'New Invoice',
            btn_export_excel: 'Export Excel',
            btn_view_all_invoices: 'View All Invoices',
            btn_add_client: 'Add Client',
            btn_add_product: 'Add Service',
            btn_save_invoice: 'Save & Confirm Invoice',
            btn_save_client: 'Save Client',
            btn_save_product: 'Save Service',
            btn_save_settings: 'Save Settings',
            btn_cancel: 'Cancel',
            btn_close: 'Close',
            btn_reset: 'Reset Filters',
            btn_add_item: 'Add Line Item',
            btn_edit: 'Edit',
            btn_delete: 'Delete',
            btn_view: 'View',
            btn_print: 'Print',
            btn_print_official: 'Print YARAMAY Invoice',
            btn_whatsapp: 'Send WhatsApp',
            btn_download_backup: 'Download Backup (JSON)',
            btn_restore_now: 'Restore Backup Now',
            btn_record_payment: 'Record Payment',
            btn_view_dossier: 'Client Dossier',
            btn_submit_request: 'Submit Invoice Request',

            kpi_total_sales: 'Total Invoiced (Price)',
            kpi_total_sub: 'Grand total of issued invoices',
            kpi_paid_total: 'Total Payments Received',
            kpi_paid_sub: 'Successfully collected funds',
            kpi_unpaid_total: 'Remaining Balance Due',
            kpi_unpaid_sub: 'Total outstanding receivables',
            kpi_client_count: 'Total Clients',
            kpi_client_sub: 'Registered corporate accounts',
            kpi_invoice_count: 'Total Invoices',
            kpi_invoice_sub: 'Issued transaction documents',

            partner_split_title: 'Partner Revenue Division (Mr. Ibraheem & Mr. Fawas)',
            partner_split_desc: 'Individual assignments credit 100% of collected client payments to that partner. When set to Both, payments are divided equally (50% / 50%).',
            kpi_ibraheem_total: 'Mr. Ibraheem Total Share',
            kpi_fawas_total: 'Mr. Fawas Total Share',

            client_info_summary_title: 'Client Information & Receivables Overview',
            recent_invoices_title: 'Recent Invoices & Transactions',
            client_portal_headline: 'Client Fill-up Form for Invoice Generation',
            client_portal_sub: 'Fill out this form to request or generate an invoice with full payment, 3 months, or 6 months installments.',

            search_invoice_placeholder: 'Search by invoice #, client name, contact, phone...',
            search_client_placeholder: 'Search clients by company name, contact, phone, address...',
            status_all: 'All Statuses',
            status_paid: 'Paid in Full',
            status_partial: 'Partial Payment',
            status_unpaid: 'Unpaid (Due)',
            status_pending: 'Pending',
            status_cancelled: 'Cancelled',
            date_to: 'To',

            partner_all: 'All Partners',
            partner_both: 'Both (Mr. Ibraheem & Mr. Fawas - 50/50 Split)',
            partner_ibraheem: 'Mr. Ibraheem',
            partner_fawas: 'Mr. Fawas',

            term_full: 'Full Payment (1x)',
            term_3_months: '3 Months Installment',
            term_6_months: '6 Months Installment',
            term_custom: 'Custom Term (Months)',

            th_invoice_num: 'Invoice #',
            th_client: 'Client / Company',
            th_contact_person: 'Contact Person',
            th_issue_date: 'Issue Date',
            th_due_date: 'Due Date',
            th_price: 'Total Cost / Price',
            th_paid: 'Amount Paid',
            th_balance: 'Remaining Balance',
            th_plan: 'Payment Plan / Formula',
            th_partner: 'Assigned Partner',
            th_status: 'Status',
            th_actions: 'Actions',
            th_phone: 'Phone / Mobile',
            th_email: 'Email',
            th_address: 'Address / Location',
            th_tax_num: 'Tax ID / TIN',
            th_invoices_count: 'Invoices',
            th_item_name: 'Item / Service Name',
            th_description: 'Description',
            th_unit_price: 'Unit Price',
            th_tax_rate: 'Tax %',
            th_quantity: 'Qty',
            th_discount: 'Discount',

            lbl_invoice_num: 'Invoice Number *',
            lbl_client: 'Select Client *',
            lbl_or_new_client: 'Or Create New Client',
            lbl_new_client_name: 'Company / Organization Name',
            lbl_new_contact_person: 'Contact Person Name (e.g. Mr. Allan Rom)',
            lbl_new_client_phone: 'Phone / Mobile',
            lbl_new_client_address: 'Address / City',
            lbl_issue_date: 'Issue Date *',
            lbl_due_date: 'Due Date',
            lbl_status: 'Invoice Status *',
            lbl_payment_method: 'Payment Channel *',
            lbl_payment_term: 'Payment Plan / Term *',
            lbl_partner_assigned: 'Partner Assignment / Division *',
            lbl_invoice_items: 'Invoice Line Items & Services',
            lbl_price_note: 'Price Note (e.g. PHP 20,000 / 3 Months = PHP 6,666.67 / Month)',
            lbl_amount_due_note: 'Amount Due Note',
            lbl_paid_amount: 'Amount Paid (PHP)',
            lbl_remaining_balance: 'Remaining Balance (PHP)',
            lbl_notes_terms: 'Payment Transfer Terms & Instructions',
            calc_subtotal: 'Subtotal:',
            calc_discount: 'Total Discount:',
            calc_tax: 'VAT / Tax:',
            calc_grand_total: 'Total Cost / Price:',
            calc_monthly: 'Monthly Installment:',

            method_gcash: 'GCash (Ferdenand Sabado - 09617190588)',
            method_cash: 'Cash Payment',

            client_modal_title_new: 'Add New Client',
            client_modal_title_edit: 'Edit Client Information',
            lbl_client_name: 'Company / Organization Name *',
            lbl_contact_person: 'Contact Person Name (e.g. Mr. Allan Rom, Ms. Pilar Corporal) *',
            lbl_client_phone: 'Phone / Mobile Number',
            lbl_client_email: 'Email Address',
            lbl_client_tax: 'TIN / Tax Number (If available)',
            lbl_client_address: 'Complete Address / Location',

            product_modal_title_new: 'Add New Product / Service',
            product_modal_title_edit: 'Edit Product / Service',
            lbl_product_name: 'Service Name *',
            lbl_product_price: 'Default Price (PHP) *',
            lbl_product_tax: 'Tax Rate (%)',
            lbl_product_desc: 'Service Package Description & Inclusions',

            settings_title: 'Company Information & Billing Profile',
            lbl_company_name: 'Company Name *',
            lbl_company_cr: 'Registration / Unit *',
            lbl_company_vat: 'Tax / TIN Number',
            lbl_company_phone: 'Contact Numbers (SMS/Call)',
            lbl_company_email: 'Billing Email Address',
            lbl_company_manager: 'General Manager Name *',
            lbl_company_title: 'Manager Title',
            lbl_company_cert: 'Certifications',
            lbl_company_website: 'Website Domain',
            lbl_company_gcash: 'GCash Number *',
            lbl_gcash_account: 'GCash Account Name *',
            lbl_currency: 'Currency Symbol / Code',
            lbl_default_tax: 'Default Tax Rate (%)',
            lbl_invoice_prefix: 'Invoice Number Prefix',
            lbl_company_address: 'Complete Company Address',
            lbl_invoice_footer: 'Default Payment Note & Suggestions',

            backup_export_title: 'Export Full Database Backup',
            backup_export_desc: 'Download a complete JSON snapshot containing all clients, invoices, service items, and company settings.',
            backup_import_title: 'Restore Database Backup',
            backup_import_desc: 'Upload a previously exported JSON backup to restore all data.',
            confirm_restore: 'WARNING: Restoring will overwrite all current clients and invoices with backup data. Proceed?',

            toast_invoice_saved: 'Invoice saved successfully',
            toast_invoice_deleted: 'Invoice deleted successfully',
            toast_status_updated: 'Invoice status updated successfully',
            toast_payment_recorded: 'Payment recorded and partner shares updated successfully',
            toast_client_saved: 'Client information saved successfully',
            toast_client_deleted: 'Client deleted successfully',
            toast_product_saved: 'Service package saved successfully',
            toast_product_deleted: 'Service package deleted successfully',
            toast_settings_saved: 'Settings updated successfully',
            toast_item_required: 'Invoice must contain at least one service item',
            confirm_delete_invoice: 'Are you sure you want to delete this invoice?',
            confirm_delete_client: 'Are you sure you want to delete this client? Associated invoices will remain with client reference cleared.',
            confirm_delete_product: 'Are you sure you want to delete this service?',
            cash_client: 'Direct Client',
            invoice_word: 'Invoices',
            no_records: 'No matching records found',
            no_clients: 'No registered clients found',
            no_products: 'No products or services found'
        },
        ar: {
            brand_title: 'ياراماي',
            brand_subtitle: 'لخدمات صيانة الحاسب والأنظمة',
            version_text: 'الإصدار 2.7 المطور',
            page_subtitle: 'نظام الفواتير الإلكترونية وإدارة حسابات الشركاء والعملاء',

            nav_dashboard: 'لوحة التحكم',
            nav_invoices: 'سجل الفواتير',
            nav_clients: 'دليل العملاء',
            nav_users: 'مدراء النظام',
            nav_security: 'درع الأمان وسجلات الحماية',
            nav_client_portal: 'بوابة طلب فاتورة بالأقساط',
            nav_products: 'المنتجات والخدمات',
            nav_settings: 'إعدادات المنشأة',
            nav_backup: 'النسخ الاحتياطي',

            title_dashboard: 'لوحة التحكم وأرباح الشركاء (السيد إبراهيم والسيد فواز)',
            title_invoices: 'سجل الفواتير ومتابعة التحصيل',
            title_clients: 'دليل العملاء وكشوف الحسابات',
            title_users: 'إدارة المستخدمين وصلاحيات المدراء',
            title_security: 'درع الحماية وسجلات التهديدات الأمنية',
            title_client_portal: 'نموذج تعبئة طلب فاتورة وخطة الدفع والأقساط',
            title_products: 'دليل الخدمات والأسعار',
            title_settings: 'إعدادات الشركة والفواتير',
            title_backup: 'النسخ الاحتياطي واستعادة البيانات',

            btn_new_invoice: 'فاتورة جديدة',
            btn_export_excel: 'تصدير Excel',
            btn_view_all_invoices: 'عرض جميع الفواتير',
            btn_add_client: 'إضافة عميل',
            btn_add_product: 'إضافة خدمة',
            btn_save_invoice: 'حفظ وتأكيد الفاتورة',
            btn_save_client: 'حفظ العميل',
            btn_save_product: 'حفظ الخدمة',
            btn_save_settings: 'حفظ الإعدادات',
            btn_cancel: 'إلغاء',
            btn_close: 'إغلاق',
            btn_reset: 'إعادة ضبط',
            btn_add_item: 'إضافة بند جديد',
            btn_edit: 'تعديل',
            btn_delete: 'حذف',
            btn_view: 'معاينة',
            btn_print: 'طباعة',
            btn_print_official: 'طباعة فاتورة ياراماي الرسمية',
            btn_whatsapp: 'إرسال واتساب',
            btn_download_backup: 'تحميل نسخة احتياطية (JSON)',
            btn_restore_now: 'استرجاع النسخة الآن',
            btn_record_payment: 'تسجيل دفعة',
            btn_view_dossier: 'ملف العميل',
            btn_submit_request: 'إرسال طلب الفاتورة',

            kpi_total_sales: 'إجمالي المبيعات (السعر)',
            kpi_total_sub: 'المجموع الكلي للفواتير الصادرة',
            kpi_paid_total: 'إجمالي المبالغ المسددة',
            kpi_paid_sub: 'المبالغ المحصلة بنجاح',
            kpi_unpaid_total: 'الرصيد المتبقي المستحق',
            kpi_unpaid_sub: 'إجمالي المستحقات المتبقية',
            kpi_client_count: 'عدد العملاء',
            kpi_client_sub: 'الشركات والعملاء المسجلين',
            kpi_invoice_count: 'إجمالي الفواتير',
            kpi_invoice_sub: 'عدد المعاملات الصادرة',

            partner_split_title: 'توزيع إيرادات الشركاء (السيد إبراهيم والسيد فواز)',
            partner_split_desc: 'عند اختيار أحدهما فقط فإن كامل الدفعة تذهب لحسابه، وعند اختيار كلاهما تقسم الدفعة مناصفة (50% لكل منهما).',
            kpi_ibraheem_total: 'حصة السيد إبراهيم الإجمالية',
            kpi_fawas_total: 'حصة السيد فواز الإجمالية',

            client_info_summary_title: 'بيانات العملاء والأرصدة المستحقة',
            recent_invoices_title: 'أحدث الفواتير والعمليات',
            client_portal_headline: 'نموذج العميل لتعبئة وإصدار الفاتورة وخطة الأقساط',
            client_portal_sub: 'يمكن اختيار الدفع بالكامل أو بالتقسيط على 3 أشهر أو 6 أشهر مع حساب القسط الشهري تلقائياً.',

            search_invoice_placeholder: 'ابحث برقم الفاتورة، اسم العميل، المسؤول، الهاتف...',
            search_client_placeholder: 'ابحث عن عميل بالاسم، المسؤول، الهاتف، العنوان...',
            status_all: 'جميع الحالات',
            status_paid: 'مدفوعة بالكامل',
            status_partial: 'دفعة جزئية',
            status_unpaid: 'غير مدفوعة (مستحقة)',
            status_pending: 'قيد الانتظار',
            status_cancelled: 'ملغاة',
            date_to: 'إلى',

            partner_all: 'جميع الشركاء',
            partner_both: 'كلاهما (السيد إبراهيم والسيد فواز - مناصفة 50/50)',
            partner_ibraheem: 'السيد إبراهيم',
            partner_fawas: 'السيد فواز',

            term_full: 'دفع كامل (دفعة واحدة)',
            term_3_months: 'أقساط على 3 أشهر',
            term_6_months: 'أقساط على 6 أشهر',
            term_custom: 'أقساط مخصصة (أشهر)',

            th_invoice_num: 'رقم الفاتورة',
            th_client: 'العميل / الشركة',
            th_contact_person: 'الشخص المسؤول',
            th_issue_date: 'تاريخ الإصدار',
            th_due_date: 'تاريخ الاستحقاق',
            th_price: 'السعر / التكلفة',
            th_paid: 'المبلغ المدفوع',
            th_balance: 'الرصيد المتبقي',
            th_plan: 'خطة الدفع / القسط',
            th_partner: 'الشريك المخصص',
            th_status: 'الحالة',
            th_actions: 'العمليات',
            th_phone: 'الهاتف / الجوال',
            th_email: 'البريد الإلكتروني',
            th_address: 'العنوان',
            th_tax_num: 'الرقم الضريبي',
            th_invoices_count: 'عدد الفواتير',
            th_item_name: 'اسم البند / الخدمة',
            th_description: 'الوصف',
            th_unit_price: 'سعر الوحدة',
            th_tax_rate: 'الضريبة %',
            th_quantity: 'الكمية',
            th_discount: 'الخصم',

            lbl_invoice_num: 'رقم الفاتورة *',
            lbl_client: 'اختيار العميل *',
            lbl_or_new_client: 'أو إضافة عميل جديد',
            lbl_new_client_name: 'اسم الشركة / المنشأة',
            lbl_new_contact_person: 'اسم المسؤول (مثال: Mr. Allan Rom)',
            lbl_new_client_phone: 'رقم الهاتف / الجوال',
            lbl_new_client_address: 'العنوان / المدينة',
            lbl_issue_date: 'تاريخ الإصدار *',
            lbl_due_date: 'تاريخ الاستحقاق',
            lbl_status: 'حالة الفاتورة *',
            lbl_payment_method: 'طريقة الدفع *',
            lbl_payment_term: 'خطة الدفع والأقساط *',
            lbl_partner_assigned: 'تخصيص الدفعة للشريك *',
            lbl_invoice_items: 'بنود الفاتورة والخدمات',
            lbl_price_note: 'ملاحظة السعر / معادلة القسط',
            lbl_amount_due_note: 'ملاحظة المبلغ المستحق',
            lbl_paid_amount: 'المبلغ المدفوع (PHP)',
            lbl_remaining_balance: 'الرصيد المتبقي (PHP)',
            lbl_notes_terms: 'شروط التحويل والملاحظات',
            calc_subtotal: 'المجموع الفرعي:',
            calc_discount: 'إجمالي الخصم:',
            calc_tax: 'الضريبة:',
            calc_grand_total: 'التكلفة الإجمالية / السعر:',
            calc_monthly: 'القسط الشهري:',

            method_gcash: 'GCash (Ferdenand Sabado - 09617190588)',
            method_cash: 'دفع نقدي (كاش)',

            client_modal_title_new: 'إضافة عميل جديد',
            client_modal_title_edit: 'تعديل بيانات العميل',
            lbl_client_name: 'اسم الشركة / العميل *',
            lbl_contact_person: 'الشخص المسؤول (مثال: Mr. Allan Rom) *',
            lbl_client_phone: 'رقم الهاتف / الجوال',
            lbl_client_email: 'البريد الإلكتروني',
            lbl_client_tax: 'الرقم الضريبي للعميل',
            lbl_client_address: 'العنوان بالتفصيل',

            product_modal_title_new: 'إضافة خدمة أو باقة جديدة',
            product_modal_title_edit: 'تعديل الخدمة',
            lbl_product_name: 'اسم الخدمة أو الباقة *',
            lbl_product_price: 'السعر الافتراضي (PHP) *',
            lbl_product_tax: 'نسبة الضريبة (%)',
            lbl_product_desc: 'تفاصيل ومكونات الباقة',

            settings_title: 'بيانات المنشأة وإعدادات الفواتير',
            lbl_company_name: 'اسم المنشأة / الشركة *',
            lbl_company_cr: 'رقم الوحدة / التسجيل',
            lbl_company_vat: 'الرقم الضريبي',
            lbl_company_phone: 'أرقام التواصل (SMS/اتصال)',
            lbl_company_email: 'البريد الإلكتروني',
            lbl_company_manager: 'اسم المدير العام *',
            lbl_company_title: 'المسمى الوظيفي',
            lbl_company_cert: 'الشهادات والمؤهلات',
            lbl_company_website: 'الموقع الإلكتروني',
            lbl_company_gcash: 'رقم GCash *',
            lbl_gcash_account: 'اسم صاحب حساب GCash *',
            lbl_currency: 'رمز العملة',
            lbl_default_tax: 'نسبة الضريبة الافتراضية (%)',
            lbl_invoice_prefix: 'بادئة رقم الفاتورة',
            lbl_company_address: 'عنوان الشركة بالتفصيل',
            lbl_invoice_footer: 'ملاحظة الفاتورة وتفاصيل التحويل الافتراضية',

            backup_export_title: 'تصدير نسخة احتياطية كاملة',
            backup_export_desc: 'قم بتحميل ملف JSON يحتوي على كافة الفواتير والعملاء والخدمات والإعدادات.',
            backup_import_title: 'استعادة نسخة احتياطية',
            backup_import_desc: 'حدد ملف النسخة الاحتياطية (JSON) لاسترجاع البيانات.',
            confirm_restore: 'تنبيه: ستؤدي الاستعادة إلى استبدال البيانات الحالية. هل تريد المتابعة؟',

            toast_invoice_saved: 'تم حفظ الفاتورة بنجاح',
            toast_invoice_deleted: 'تم حذف الفاتورة بنجاح',
            toast_status_updated: 'تم تحديث حالة الفاتورة بنجاح',
            toast_payment_recorded: 'تم تسجيل الدفعة وتوزيع حصص الشركاء وتحديث الرصيد المتبقي بنجاح',
            toast_client_saved: 'تم حفظ بيانات العميل بنجاح',
            toast_client_deleted: 'تم حذف العميل بنجاح',
            toast_product_saved: 'تم حفظ الخدمة بنجاح',
            toast_product_deleted: 'تم حذف الخدمة بنجاح',
            toast_settings_saved: 'تم حفظ الإعدادات بنجاح',
            toast_item_required: 'يجب أن تحتوي الفاتورة على بند واحد على الأقل',
            confirm_delete_invoice: 'هل أنت متأكد من حذف هذه الفاتورة؟',
            confirm_delete_client: 'هل أنت متأكد من حذف هذا العميل؟',
            confirm_delete_product: 'هل أنت متأكد من حذف هذه الخدمة؟',
            cash_client: 'عميل نقدي',
            invoice_word: 'فاتورة',
            no_records: 'لا توجد نتائج مطابقة',
            no_clients: 'لا يوجد عملاء مسجلين',
            no_products: 'لا توجد خدمات مسجلة'
        }
    },

    // Translation helper
    t(key) {
        return (this.i18n[this.lang] && this.i18n[this.lang][key]) 
            || (this.i18n['en'] && this.i18n['en'][key]) 
            || key;
    },

    toggleLanguage() {
        const newLang = this.lang === 'en' ? 'ar' : 'en';
        this.setLanguage(newLang);
    },

    setLanguage(lang) {
        this.lang = lang;
        localStorage.setItem('app_lang', lang);

        const html = document.documentElement;
        html.lang = lang;
        html.dir = (lang === 'ar' ? 'rtl' : 'ltr');

        const langBtnText = document.getElementById('langToggleText');
        if (langBtnText) {
            langBtnText.innerText = (lang === 'en' ? 'العربية' : 'English');
        }

        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            const translation = this.t(key);
            if (translation) {
                el.innerText = translation;
            }
        });

        document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
            const key = el.getAttribute('data-i18n-placeholder');
            const translation = this.t(key);
            if (translation) {
                el.placeholder = translation;
            }
        });

        this.switchView(this.currentView);
    },

    // App Initialization
    init() {
        this.bindEvents();
        this.setLanguage(this.lang);
        this.loadSettings();
        this.loadClients();
        this.loadProducts();
        this.loadDashboard();
        this.loadInvoices();
    },

    bindEvents() {
        // Navigation links
        document.querySelectorAll('[data-view]').forEach(el => {
            el.addEventListener('click', (e) => {
                e.preventDefault();
                const view = el.getAttribute('data-view');
                this.switchView(view);
                this.closeSidebar();
            });
        });

        // Mobile drawer toggle
        const toggleBtn = document.getElementById('mobileToggleBtn');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const closeSidebarBtn = document.getElementById('closeSidebarBtn');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.add('open');
                backdrop.classList.add('active');
            });
        }
        if (closeSidebarBtn) {
            closeSidebarBtn.addEventListener('click', () => this.closeSidebar());
        }
        if (backdrop) {
            backdrop.addEventListener('click', () => this.closeSidebar());
        }

        // Global Modal Esc key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });

        // Search & Filters for Invoices
        const invoiceSearch = document.getElementById('invoiceSearchInput');
        const statusFilter = document.getElementById('statusFilterSelect');
        const partnerFilter = document.getElementById('partnerFilterSelect');
        const dateFromFilter = document.getElementById('dateFromFilter');
        const dateToFilter = document.getElementById('dateToFilter');

        if (invoiceSearch) {
            let timeout = null;
            invoiceSearch.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(() => this.loadInvoices(), 300);
            });
        }
        if (statusFilter) statusFilter.addEventListener('change', () => this.loadInvoices());
        if (partnerFilter) partnerFilter.addEventListener('change', () => this.loadInvoices());
        if (dateFromFilter) dateFromFilter.addEventListener('change', () => this.loadInvoices());
        if (dateToFilter) dateToFilter.addEventListener('change', () => this.loadInvoices());

        // Client search in Directory
        const clientSearch = document.getElementById('clientSearchInput');
        if (clientSearch) {
            let cTimeout = null;
            clientSearch.addEventListener('input', () => {
                clearTimeout(cTimeout);
                cTimeout = setTimeout(() => this.loadClients(clientSearch.value), 300);
            });
        }

        // Settings Form Submit
        const settingsForm = document.getElementById('settingsForm');
        if (settingsForm) {
            settingsForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveSettings();
            });
        }

        // Client Form Submit
        const clientForm = document.getElementById('clientForm');
        if (clientForm) {
            clientForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveClient();
            });
        }

        // Product Form Submit
        const productForm = document.getElementById('productForm');
        if (productForm) {
            productForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveProduct();
            });
        }

        // Invoice Form Submit
        const invoiceForm = document.getElementById('invoiceForm');
        if (invoiceForm) {
            invoiceForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveInvoice();
            });
        }

        // Client Portal Self Fill-Up Form Submit
        const clientPortalForm = document.getElementById('clientPortalForm');
        if (clientPortalForm) {
            clientPortalForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitClientInvoice();
            });
        }

        // Quick Payment Form Submit
        const quickPaymentForm = document.getElementById('quickPaymentForm');
        if (quickPaymentForm) {
            quickPaymentForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitQuickPayment();
            });
        }

        // Add Line Item Button
        const addItemBtn = document.getElementById('addItemBtn');
        if (addItemBtn) {
            addItemBtn.addEventListener('click', () => {
                this.addInvoiceItemRow();
            });
        }

        // Quick Client Toggle in Invoice Modal
        const toggleNewClientCheck = document.getElementById('toggleNewClientCheck');
        if (toggleNewClientCheck) {
            toggleNewClientCheck.addEventListener('change', (e) => {
                const box = document.getElementById('quickNewClientBox');
                const selectGroup = document.getElementById('existingClientGroup');
                if (e.target.checked) {
                    box.style.display = 'block';
                    selectGroup.style.display = 'none';
                    document.getElementById('invoiceClientSelect').value = '';
                } else {
                    box.style.display = 'none';
                    selectGroup.style.display = 'block';
                }
            });
        }

        // Paid Amount Auto Calculation
        const paidInput = document.getElementById('invoicePaidAmountInput');
        if (paidInput) {
            paidInput.addEventListener('input', () => {
                this.recalculateTotals();
            });
        }

        // Invoice Modal Installment Term Change
        const invoiceTermSelect = document.getElementById('invoicePaymentTerm');
        if (invoiceTermSelect) {
            invoiceTermSelect.addEventListener('change', () => {
                this.onInvoiceTermChange();
            });
        }

        // Portal Form Price input & Term Radio change
        const portalPriceInput = document.getElementById('portalPrice');
        if (portalPriceInput) {
            portalPriceInput.addEventListener('input', () => {
                this.updatePortalFormula();
            });
        }

        document.querySelectorAll('input[name="payment_term"]').forEach(radio => {
            radio.addEventListener('change', () => {
                this.updatePortalFormula();
            });
        });
    },

    closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (sidebar) sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('active');
    },

    switchView(viewName) {
        this.currentView = viewName;

        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('data-view') === viewName) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });

        document.querySelectorAll('.view-section').forEach(sec => {
            sec.classList.remove('active-view');
        });

        const targetView = document.getElementById(`view-${viewName}`);
        if (targetView) {
            targetView.classList.add('active-view');
        }

        const titleEl = document.getElementById('pageTitle');
        if (titleEl) {
            titleEl.innerText = this.t(`title_${viewName}`);
        }

        if (viewName === 'dashboard') this.loadDashboard();
        if (viewName === 'invoices') this.loadInvoices();
        if (viewName === 'clients') this.loadClients();
        if (viewName === 'products') this.loadProducts();
        if (viewName === 'users') this.loadUsers();
        if (viewName === 'security') this.loadSecurityLogs();
        if (viewName === 'client_portal') this.updatePortalFormula();
    },

    showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast ${type === 'error' ? 'toast-error' : ''}`;
        toast.innerHTML = `
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'error' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' 
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                }
            </svg>
            <span>${message}</span>
        `;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    },

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('active');
    },

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('active');
    },

    closeAllModals() {
        document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
    },

    // ==========================================
    // 1. Settings & Branding
    // ==========================================
    async loadSettings() {
        try {
            const res = await fetch('api.php?action=get_settings');
            const result = await res.json();
            if (result.success) {
                this.settings = result.data;
                this.currency = this.settings.currency || 'PHP';
                this.taxRate = parseFloat(this.settings.default_tax_rate) || 0;

                for (const [key, val] of Object.entries(this.settings)) {
                    const input = document.getElementById(`setting_${key}`);
                    if (input) input.value = val;
                }

                const brandNameEl = document.getElementById('sidebarBrandName');
                if (brandNameEl && this.settings.company_name) {
                    brandNameEl.innerText = this.settings.company_name;
                }
            }
        } catch (err) {
            console.error('Error loading settings', err);
        }
    },

    async saveSettings() {
        const form = document.getElementById('settingsForm');
        const formData = new FormData(form);
        formData.append('action', 'save_settings');

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_settings_saved'));
                this.loadSettings();
            } else {
                this.showToast(result.message || 'Error', 'error');
            }
        } catch (err) {
            this.showToast('Connection error', 'error');
        }
    },

    // ==========================================
    // 2. Admin Dashboard & Partner Revenue Division
    // ==========================================
    async loadDashboard() {
        try {
            const res = await fetch('api.php?action=get_dashboard_stats');
            const result = await res.json();
            if (result.success) {
                const d = result.data;
                
                // KPIs
                const kpiSales = document.getElementById('kpiTotalSales');
                if (kpiSales) kpiSales.innerText = `${this.formatMoney(d.total_sales)} ${this.currency}`;
                const kpiPaid = document.getElementById('kpiPaidTotal');
                if (kpiPaid) kpiPaid.innerText = `${this.formatMoney(d.paid_total)} ${this.currency}`;
                const kpiUnpaid = document.getElementById('kpiUnpaidTotal');
                if (kpiUnpaid) kpiUnpaid.innerText = `${this.formatMoney(d.remaining_balance_total)} ${this.currency}`;
                const kpiClient = document.getElementById('kpiClientCount');
                if (kpiClient) kpiClient.innerText = `${d.client_count}`;
                const kpiInvoice = document.getElementById('kpiInvoiceCount');
                if (kpiInvoice) kpiInvoice.innerText = `${d.invoice_count} ${this.t('invoice_word')}`;

                // Partner Earnings
                const ibraheemEl = document.getElementById('kpiIbraheemTotal');
                if (ibraheemEl) ibraheemEl.innerText = `${this.formatMoney(d.ibraheem_total)} ${this.currency}`;

                const fawasEl = document.getElementById('kpiFawasTotal');
                if (fawasEl) fawasEl.innerText = `${this.formatMoney(d.fawas_total)} ${this.currency}`;

                // Render Client Information Overview Table on Admin Dashboard
                const clientSummaryTbody = document.getElementById('adminClientsSummaryTableBody');
                if (clientSummaryTbody) {
                    if (!d.clients_summary || d.clients_summary.length === 0) {
                        clientSummaryTbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:20px; color:#94a3b8;">${this.t('no_clients')}</td></tr>`;
                    } else {
                        clientSummaryTbody.innerHTML = d.clients_summary.map(c => `
                            <tr>
                                <td>
                                    <strong style="color:var(--text-heading); font-size:14px;">${this.escapeHtml(c.name)}</strong>
                                    ${c.address ? `<div style="font-size:11.5px; color:var(--text-muted);">${this.escapeHtml(c.address)}</div>` : ''}
                                </td>
                                <td>
                                    <div style="font-weight:600; color:#334155;">${this.escapeHtml(c.contact_person || '-')}</div>
                                    <div style="font-size:11px; color:#64748b;">${this.escapeHtml(c.phone || '')}</div>
                                </td>
                                <td><span class="badge badge-paid">${c.invoice_count}</span></td>
                                <td><strong>${this.formatMoney(c.total_price)} ${this.currency}</strong></td>
                                <td style="color:#15803d; font-weight:600;">${this.formatMoney(c.total_paid)} ${this.currency}</td>
                                <td>${this.renderBalanceBadge(c.remaining_balance)}</td>
                                <td>
                                    <div class="actions-cell">
                                        <button class="btn btn-sm btn-primary" onclick="App.viewClientDossier(${c.id})">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>${this.t('btn_view')}</span>
                                        </button>
                                        <button class="btn btn-sm btn-outline" onclick="App.openNewInvoiceForClient(${c.id})">
                                            <span>+ ${this.t('btn_new_invoice')}</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                    }
                }

                // Render Recent Invoices Table on Dashboard
                const tbody = document.getElementById('recentInvoicesTableBody');
                if (tbody) {
                    if (!d.recent_invoices || d.recent_invoices.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:20px; color:#94a3b8;">${this.t('no_records')}</td></tr>`;
                    } else {
                        tbody.innerHTML = d.recent_invoices.map(inv => `
                            <tr>
                                <td>
                                    <strong style="color:var(--primary); font-family:monospace; font-size:13.5px;">${this.escapeHtml(inv.invoice_number)}</strong>
                                    <div style="font-size:11px; color:#64748b; margin-top:2px;">📅 ${inv.issue_date}</div>
                                </td>
                                <td>
                                    <div style="font-weight:700;">${this.escapeHtml(inv.client_name || this.t('cash_client'))}</div>
                                    ${inv.contact_person ? `<div style="font-size:11.5px; color:#64748b;">👤 ${this.escapeHtml(inv.contact_person)}</div>` : ''}
                                </td>
                                <td>
                                    <div><strong>${this.formatMoney(inv.grand_total)} ${this.currency}</strong></div>
                                    ${this.renderInstallmentPill(inv)}
                                </td>
                                <td style="color:#15803d; font-weight:600;">${this.formatMoney(inv.paid_amount)}</td>
                                <td>${this.renderBalanceBadge(inv.remaining_balance)}</td>
                                <td>${this.renderPartnerBadge(inv.partner_assigned, inv.ibraheem_share, inv.fawas_share, inv.id, inv.invoice_number)}</td>
                                <td>${this.renderStatusBadge(inv.status)}</td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="${this.getPrintUrl(inv.id)}" target="_blank" class="btn btn-sm btn-outline" title="${this.t('btn_print_official')}">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </a>
                                        <button class="btn btn-sm btn-light" onclick="App.openQuickPaymentModal(${inv.id})" title="${this.t('btn_record_payment')}">
                                            💳
                                        </button>
                                        <button class="btn btn-sm btn-light" onclick="App.viewInvoice(${inv.id})">${this.t('btn_view')}</button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                    }
                }
            }
        } catch (err) {
            console.error('Error loading dashboard', err);
        }
    },

    // ==========================================
    // 3. Invoices Management
    // ==========================================
    async loadInvoices() {
        const search = document.getElementById('invoiceSearchInput')?.value || '';
        const status = document.getElementById('statusFilterSelect')?.value || '';
        const partner = document.getElementById('partnerFilterSelect')?.value || '';
        const fromDate = document.getElementById('dateFromFilter')?.value || '';
        const toDate = document.getElementById('dateToFilter')?.value || '';

        const params = new URLSearchParams({
            action: 'get_invoices',
            search: search,
            status: status,
            partner: partner,
            from_date: fromDate,
            to_date: toDate
        });

        try {
            const res = await fetch(`api.php?${params.toString()}`);
            const result = await res.json();
            if (result.success) {
                this.invoices = result.data;
                this.renderInvoicesTable();
            }
        } catch (err) {
            console.error('Error loading invoices', err);
        }
    },

    renderInvoicesTable() {
        const tbody = document.getElementById('invoicesTableBody');
        if (!tbody) return;

        if (this.invoices.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" style="text-align:center; padding:30px; color:#94a3b8;">${this.t('no_records')}</td></tr>`;
            return;
        }

        tbody.innerHTML = this.invoices.map(inv => `
            <tr>
                <td>
                    <strong style="color:var(--primary); font-family:monospace; font-size:14px;">${this.escapeHtml(inv.invoice_number)}</strong>
                    <div style="font-size:11.5px; color:#475569; margin-top:3px; display:flex; align-items:center; gap:4px;">
                        <span>📅 ${inv.issue_date}</span>
                    </div>
                    ${inv.due_date ? `<div style="font-size:10.5px; color:#94a3b8; margin-top:1px;">Due: ${inv.due_date}</div>` : ''}
                </td>
                <td>
                    <div style="font-weight:700; color:var(--text-heading);">${this.escapeHtml(inv.client_name || this.t('cash_client'))}</div>
                    ${inv.contact_person ? `<div style="font-size:12px; color:#475569;">👤 ${this.escapeHtml(inv.contact_person)}</div>` : ''}
                    ${inv.client_address ? `<div style="font-size:11px; color:#94a3b8;">📍 ${this.escapeHtml(inv.client_address)}</div>` : ''}
                </td>
                <td>
                    <div><strong>${this.formatMoney(inv.grand_total)} ${this.currency}</strong></div>
                    ${this.renderInstallmentPill(inv)}
                </td>
                <td style="color:#15803d; font-weight:600;">
                    <div>${this.formatMoney(inv.paid_amount)} ${this.currency}</div>
                    <div style="font-size:11px; color:#64748b;">via ${inv.payment_method ? inv.payment_method.toUpperCase() : 'GCASH'}</div>
                    ${inv.payment_receipt ? `
                        <a href="${this.escapeHtml(inv.payment_receipt)}" target="_blank" style="display:inline-flex; align-items:center; gap:3px; background:#dcfce7; color:#15803d; padding:2px 6px; border-radius:4px; font-size:10px; font-weight:700; text-decoration:none; margin-top:3px;" title="View Uploaded GCash Payment Screenshot">
                            📎 GCash Proof
                        </a>
                    ` : ''}
                </td>
                <td>${this.renderBalanceBadge(inv.remaining_balance)}</td>
                <td>${this.renderPartnerBadge(inv.partner_assigned, inv.ibraheem_share, inv.fawas_share, inv.id, inv.invoice_number)}</td>
                <td>${this.renderStatusBadge(inv.status)}</td>
                <td>
                    <div class="actions-cell">
                        <a href="${this.getPrintUrl(inv.id)}" target="_blank" class="btn btn-sm btn-primary" title="${this.t('btn_print_official')}">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>${this.t('btn_print')}</span>
                        </a>
                        <button class="btn btn-sm btn-outline" title="${this.t('btn_record_payment')}" onclick="App.openQuickPaymentModal(${inv.id})">
                            <span>💳 ${this.t('btn_record_payment')}</span>
                        </button>
                        <button class="btn btn-sm btn-light" title="${this.t('btn_view')}" onclick="App.viewInvoice(${inv.id})">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                        <button class="btn btn-sm btn-outline" title="${this.t('btn_edit')}" onclick="App.editInvoice(${inv.id})">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button class="btn btn-sm btn-outline" style="color:#ef4444;" title="${this.t('btn_delete')}" onclick="App.deleteInvoice(${inv.id})">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    },

    // ==========================================
    // 4. Clients Directory & Dossier
    // ==========================================
    async loadClients(search = '') {
        try {
            const url = search ? `api.php?action=get_clients&search=${encodeURIComponent(search)}` : 'api.php?action=get_clients';
            const res = await fetch(url);
            const result = await res.json();
            if (result.success) {
                this.clients = result.data;
                this.renderClientsTable();
                this.populateClientsDropdown();
            }
        } catch (err) {
            console.error('Error loading clients', err);
        }
    },

    renderClientsTable() {
        const tbody = document.getElementById('clientsTableBody');
        if (!tbody) return;

        if (this.clients.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:20px; color:#94a3b8;">${this.t('no_clients')}</td></tr>`;
            return;
        }

        tbody.innerHTML = this.clients.map(c => `
            <tr>
                <td>
                    <strong style="color:var(--text-heading); font-size:14px;">${this.escapeHtml(c.name)}</strong>
                    ${c.address ? `<div style="font-size:12px; color:var(--text-muted);">${this.escapeHtml(c.address)}</div>` : ''}
                </td>
                <td>
                    <div style="font-weight:600; color:#334155;">${this.escapeHtml(c.contact_person || '-')}</div>
                    <div style="font-size:11px; color:#64748b;">${this.escapeHtml(c.phone || '')}</div>
                </td>
                <td><span class="badge badge-paid">${c.invoice_count} ${this.t('invoice_word')}</span></td>
                <td><strong>${this.formatMoney(c.total_purchases)} ${this.currency}</strong></td>
                <td style="color:#15803d; font-weight:600;">${this.formatMoney(c.total_paid)} ${this.currency}</td>
                <td>${this.renderBalanceBadge(c.total_balance)}</td>
                <td>
                    <div class="actions-cell">
                        <button class="btn btn-sm btn-primary" onclick="App.viewClientDossier(${c.id})">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>${this.t('btn_view_dossier')}</span>
                        </button>
                        <button class="btn btn-sm btn-outline" onclick="App.openNewInvoiceForClient(${c.id})">
                            <span>+ ${this.t('btn_new_invoice')}</span>
                        </button>
                        <button class="btn btn-sm btn-outline" onclick="App.editClient(${c.id})">${this.t('btn_edit')}</button>
                        <button class="btn btn-sm btn-outline" style="color:#ef4444;" onclick="App.deleteClient(${c.id})">${this.t('btn_delete')}</button>
                    </div>
                </td>
            </tr>
        `).join('');
    },

    populateClientsDropdown() {
        const select = document.getElementById('invoiceClientSelect');
        if (!select) return;
        const currentVal = select.value;
        const promptText = (this.lang === 'ar' ? '-- اختر عميلاً مسجلاً --' : '-- Select Registered Client --');
        select.innerHTML = `<option value="">${promptText}</option>` + 
            this.clients.map(c => `<option value="${c.id}">${this.escapeHtml(c.name)} - ${this.escapeHtml(c.contact_person || c.phone || '')}</option>`).join('');
        if (currentVal) select.value = currentVal;
    },

    openNewClientModal() {
        const idInput = document.getElementById('clientIdInput');
        if (idInput) idInput.value = '';
        const nameInput = document.getElementById('clientNameInput');
        if (nameInput) nameInput.value = '';
        const contactInput = document.getElementById('clientContactPersonInput');
        if (contactInput) contactInput.value = '';
        const phoneInput = document.getElementById('clientPhoneInput');
        if (phoneInput) phoneInput.value = '';
        const emailInput = document.getElementById('clientEmailInput');
        if (emailInput) emailInput.value = '';
        const addrInput = document.getElementById('clientAddressInput');
        if (addrInput) addrInput.value = '';
        const taxInput = document.getElementById('clientTaxInput');
        if (taxInput) taxInput.value = '';
        const titleEl = document.getElementById('clientModalTitle');
        if (titleEl) titleEl.innerText = this.t('client_modal_title_new');
        this.openModal('clientModal');
    },

    editClient(id) {
        const client = this.clients.find(c => c.id == id);
        if (!client) return;
        const idInput = document.getElementById('clientIdInput');
        if (idInput) idInput.value = client.id;
        const nameInput = document.getElementById('clientNameInput');
        if (nameInput) nameInput.value = client.name;
        const contactInput = document.getElementById('clientContactPersonInput');
        if (contactInput) contactInput.value = client.contact_person || '';
        const phoneInput = document.getElementById('clientPhoneInput');
        if (phoneInput) phoneInput.value = client.phone || '';
        const emailInput = document.getElementById('clientEmailInput');
        if (emailInput) emailInput.value = client.email || '';
        const addrInput = document.getElementById('clientAddressInput');
        if (addrInput) addrInput.value = client.address || '';
        const taxInput = document.getElementById('clientTaxInput');
        if (taxInput) taxInput.value = client.tax_number || '';
        const titleEl = document.getElementById('clientModalTitle');
        if (titleEl) titleEl.innerText = this.t('client_modal_title_edit');
        this.openModal('clientModal');
    },

    async saveClient() {
        const form = document.getElementById('clientForm');
        const formData = new FormData(form);
        formData.append('action', 'save_client');

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_client_saved'));
                this.closeModal('clientModal');
                this.loadClients();
                this.loadDashboard();
            } else {
                this.showToast(result.message || 'Error', 'error');
            }
        } catch (err) {
            this.showToast('Failed to save client', 'error');
        }
    },

    async deleteClient(id) {
        if (!confirm(this.t('confirm_delete_client'))) return;
        try {
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_client', id: id })
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_client_deleted'));
                this.loadClients();
                this.loadDashboard();
            } else {
                this.showToast(result.message || 'Error', 'error');
            }
        } catch (err) {
            this.showToast('Failed to delete client', 'error');
        }
    },

    // Client Dossier & Financial Summary
    async viewClientDossier(clientId) {
        try {
            const res = await fetch(`api.php?action=get_client&id=${clientId}`);
            const result = await res.json();
            if (result.success) {
                const c = result.data;
                const modalBody = document.getElementById('clientDossierContent');
                if (!modalBody) return;

                modalBody.innerHTML = `
                    <div style="background:var(--primary-light); border:1px solid var(--primary-border); padding:20px; border-radius:var(--radius-lg); margin-bottom:20px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
                            <div>
                                <h3 style="font-size:20px; font-weight:800; color:var(--text-heading);">${this.escapeHtml(c.name)}</h3>
                                <div style="font-size:14px; font-weight:600; color:#334155; margin-top:2px;">👤 Contact Person: ${this.escapeHtml(c.contact_person || 'Not specified')}</div>
                                <div style="font-size:13px; color:#64748b; margin-top:4px;">📍 Address: ${this.escapeHtml(c.address || 'Manila')}</div>
                                <div style="font-size:13px; color:#64748b;">📞 Phone: ${this.escapeHtml(c.phone || '-')} | ✉️ ${this.escapeHtml(c.email || '-')}</div>
                            </div>
                            <div style="text-align:right;">
                                <button class="btn btn-primary btn-sm" onclick="App.openNewInvoiceForClient(${c.id})">
                                    + ${this.t('btn_new_invoice')}
                                </button>
                            </div>
                        </div>

                        <!-- Financial Snapshot -->
                        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:12px; margin-top:16px;">
                            <div style="background:#fff; padding:12px; border-radius:8px; border:1px solid var(--border);">
                                <div style="font-size:11px; color:#64748b;">TOTAL BILLED</div>
                                <div style="font-size:16px; font-weight:800; color:var(--primary);">${this.formatMoney(c.total_purchases)} ${this.currency}</div>
                            </div>
                            <div style="background:#fff; padding:12px; border-radius:8px; border:1px solid var(--border);">
                                <div style="font-size:11px; color:#64748b;">TOTAL PAID</div>
                                <div style="font-size:16px; font-weight:800; color:#15803d;">${this.formatMoney(c.total_paid)} ${this.currency}</div>
                            </div>
                            <div style="background:#fff; padding:12px; border-radius:8px; border:1px solid var(--border);">
                                <div style="font-size:11px; color:#64748b;">REMAINING BALANCE</div>
                                <div style="font-size:16px; font-weight:800; color:${parseFloat(c.total_balance) > 0 ? '#b91c1c' : '#15803d'};">${this.formatMoney(c.total_balance)} ${this.currency}</div>
                            </div>
                        </div>
                    </div>

                    <h4 style="font-size:15px; font-weight:700; margin-bottom:12px; color:var(--text-heading);">Client Invoices History (${c.invoices ? c.invoices.length : 0})</h4>
                    <div class="table-container" style="border:1px solid var(--border);">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Price</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Assigned Partner</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${(!c.invoices || c.invoices.length === 0) ? `<tr><td colspan="8" style="text-align:center; padding:16px;">No invoices for this client</td></tr>` : 
                                    c.invoices.map(inv => `
                                        <tr>
                                            <td><strong style="font-family:monospace; color:var(--primary);">${this.escapeHtml(inv.invoice_number)}</strong></td>
                                            <td>${inv.issue_date}</td>
                                            <td><strong>${this.formatMoney(inv.grand_total)} ${this.currency}</strong></td>
                                            <td style="color:#15803d; font-weight:600;">${this.formatMoney(inv.paid_amount)}</td>
                                            <td>${this.renderBalanceBadge(inv.remaining_balance)}</td>
                                            <td>${this.renderPartnerBadge(inv.partner_assigned, inv.ibraheem_share, inv.fawas_share, inv.id, inv.invoice_number)}</td>
                                            <td>${this.renderStatusBadge(inv.status)}</td>
                                            <td>
                                                <div class="actions-cell">
                                                    <a href="${this.getPrintUrl(inv.id)}" target="_blank" class="btn btn-sm btn-primary">🖨️ ${this.t('btn_print')}</a>
                                                    <button class="btn btn-sm btn-outline" onclick="App.openQuickPaymentModal(${inv.id})">💳 Pay</button>
                                                </div>
                                            </td>
                                        </tr>
                                    `).join('')
                                }
                            </tbody>
                        </table>
                    </div>
                `;

                this.openModal('clientDossierModal');
            }
        } catch (err) {
            this.showToast('Failed to load client dossier', 'error');
        }
    },

    openNewInvoiceForClient(clientId) {
        this.closeAllModals();
        this.openNewInvoiceModal();
        const clientSelect = document.getElementById('invoiceClientSelect');
        if (clientSelect) clientSelect.value = clientId;
    },

    // ==========================================
    // 5. Client Self Fill-Up Portal & Installment Formula
    // ==========================================
    selectPortalPackage(pkgType) {
        document.querySelectorAll('.package-choice-card').forEach(el => el.classList.remove('selected'));
        const selectedCard = document.getElementById(`pkgCard_${pkgType}`);
        if (selectedCard) selectedCard.classList.add('selected');

        const serviceNameInput = document.getElementById('portalServiceName');
        const priceInput = document.getElementById('portalPrice');
        const descInput = document.getElementById('portalDescription');

        if (pkgType === 'monthly_rescue') {
            serviceNameInput.value = 'Package of E-Commerce Maintenance Services and Web maintenance including Rescue System';
            priceInput.value = '7500.00';
            descInput.value = "Includes: 1. Web design / Web system\n2. Job post / Job Offer\n3. Deployment System (QMS)\n4. Maintenance System\n5. Location Rescue system\n6. E-commerce site with SSL Security\n7. PRA Deployment\n8. FRA/SRA SYSTEM (IF NEEDED)\n9. Block Listing for OFW\n10. Voucher System\nHosting and domain, with SSL security\n--- Nothing Follows ---";
        } else if (pkgType === 'annual_rescue') {
            serviceNameInput.value = 'Package of E-Commerce Maintenance & Web Maintenance (Annual Contract)';
            priceInput.value = '90000.00';
            descInput.value = "Includes: 1. Web design / Web system\n2. Job post / Job Offer\n3. Deployment System\n4. Company E-mail Server Maintenance\n5. Location Rescue system\n6. E-commerce site with SSL Security\n7. PRA Deployment\n8. FRA/SRA SYSTEM (IF NEEDED)\n9. Block Listing for OFW\n10. Voucher System\nHosting and domain, with SSL security\n--- Nothing Follows ---";
        } else if (pkgType === 'web_online') {
            serviceNameInput.value = 'Web and System (Online) & Maintenance Services';
            priceInput.value = '5000.00';
            descInput.value = "Web and System (Online) & Maintenance Services for the Month";
        } else if (pkgType === 'hardware_support') {
            serviceNameInput.value = 'Hardware & Server Maintenance Technical Support';
            priceInput.value = '5500.00';
            descInput.value = "Includes: 1. Web design / Web system\n2. Job post / Job Offer\n3. Technical Support (Hardware Maintenance)\n4. Company E-mail Server Maintenance\n5. Location Rescue system\n6. E-commerce site with SSL Security\n7. PRA Deployment\n8. FRA/SRA SYSTEM\n9. Company Email Maintenance\n10. Voucher System\nHosting and domain, with SSL security\n--- Nothing Follows ---";
        } else {
            serviceNameInput.value = 'Custom Services Package';
            priceInput.value = '20000.00';
            descInput.value = '';
        }

        this.updatePortalFormula();
    },

    updatePortalFormula() {
        const price = parseFloat(document.getElementById('portalPrice')?.value) || 0;
        const selectedTermRadio = document.querySelector('input[name="payment_term"]:checked');
        const term = selectedTermRadio ? selectedTermRadio.value : 'full';

        let months = 1;
        if (term === '3_months') months = 3;
        if (term === '6_months') months = 6;
        if (term === 'custom') {
            months = Math.max(1, parseInt(document.getElementById('portalCustomMonths')?.value) || 1);
        }

        const monthlyPayment = (months > 0) ? (price / months) : price;

        const formulaBox = document.getElementById('portalFormulaDisplay');
        const monthlyAmountEl = document.getElementById('portalMonthlyAmount');
        const termSummaryEl = document.getElementById('portalTermSummary');

        if (term === 'full') {
            if (formulaBox) formulaBox.style.display = 'none';
        } else {
            if (formulaBox) formulaBox.style.display = 'block';
            if (monthlyAmountEl) monthlyAmountEl.innerText = `PHP ${this.formatMoney(monthlyPayment)} / month`;
            if (termSummaryEl) {
                termSummaryEl.innerText = `Formula: Total PHP ${this.formatMoney(price)} ÷ ${months} Months = PHP ${this.formatMoney(monthlyPayment)} per month`;
            }
        }
    },

    async submitClientInvoice() {
        const form = document.getElementById('clientPortalForm');
        const formData = new FormData(form);
        formData.append('action', 'submit_client_invoice');

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                const data = result.data;
                document.getElementById('portalFormSection').style.display = 'none';
                const successSection = document.getElementById('portalSuccessSection');
                successSection.style.display = 'block';

                document.getElementById('portalSuccessInvoiceNum').innerText = data.invoice_number;
                document.getElementById('portalSuccessClient').innerText = data.client_name;
                document.getElementById('portalSuccessPrice').innerText = `${this.formatMoney(data.grand_total)} PHP`;
                document.getElementById('portalSuccessBalance').innerText = `${this.formatMoney(data.remaining_balance)} PHP`;
                
                const planText = (data.installment_months > 1) 
                    ? `${data.installment_months} Months Installment (PHP ${this.formatMoney(data.monthly_payment)} / mo)`
                    : `Full Payment (1x)`;
                document.getElementById('portalSuccessPlan').innerText = planText;

                const partnerText = (data.partner_assigned === 'both') 
                    ? `Both (Mr. Ibraheem & Mr. Fawas - 50/50 Split)`
                    : ((data.partner_assigned === 'ibraheem') ? `Mr. Ibraheem` : `Mr. Fawas`);
                document.getElementById('portalSuccessPartner').innerText = partnerText;

                document.getElementById('portalSuccessPrintBtn').href = this.getPrintUrl(data.invoice_id);

                this.showToast(result.message);
                this.loadDashboard();
                this.loadInvoices();
                this.loadClients();
            } else {
                this.showToast(result.message || 'Error submitting request', 'error');
            }
        } catch (err) {
            this.showToast('Network error while submitting invoice request', 'error');
        }
    },

    resetPortalForm() {
        document.getElementById('clientPortalForm').reset();
        document.getElementById('portalFormSection').style.display = 'block';
        document.getElementById('portalSuccessSection').style.display = 'none';
        this.selectPortalPackage('monthly_rescue');
    },

    // ==========================================
    // 6. Quick Payment Modal & Partner Split Preview
    // ==========================================
    openQuickPaymentModal(invoiceId) {
        const inv = this.invoices.find(i => i.id == invoiceId);
        if (!inv) return;

        const idEl = document.getElementById('quickPayInvoiceId');
        if (idEl) idEl.value = inv.id;
        const numEl = document.getElementById('quickPayInvoiceNum');
        if (numEl) numEl.innerText = inv.invoice_number;
        const clientEl = document.getElementById('quickPayClientName');
        if (clientEl) clientEl.innerText = inv.client_name || '';
        const grandEl = document.getElementById('quickPayGrandTotal');
        if (grandEl) grandEl.innerText = `${this.formatMoney(inv.grand_total)} ${this.currency}`;
        const paidEl = document.getElementById('quickPayCurrentPaid');
        if (paidEl) paidEl.innerText = `${this.formatMoney(inv.paid_amount)} ${this.currency}`;
        const balEl = document.getElementById('quickPayCurrentBalance');
        if (balEl) balEl.innerText = `${this.formatMoney(inv.remaining_balance)} ${this.currency}`;

        // Partner assignment info
        const partner = inv.partner_assigned || 'both';
        const partnerLabelEl = document.getElementById('quickPayPartnerLabel');
        if (partnerLabelEl) {
            partnerLabelEl.innerText = (partner === 'both') 
                ? 'Both (Mr. Ibraheem & Mr. Fawas - 50/50 Split)' 
                : ((partner === 'ibraheem') ? 'Mr. Ibraheem (100%)' : 'Mr. Fawas (100%)');
        }

        const amtInput = document.getElementById('quickPayAmountInput');
        if (amtInput) amtInput.value = inv.remaining_balance > 0 ? inv.remaining_balance : '';
        const methodSelect = document.getElementById('quickPayMethodSelect');
        if (methodSelect) methodSelect.value = 'gcash';
        const noteInput = document.getElementById('quickPayNoteInput');
        if (noteInput) noteInput.value = '';

        this.openModal('quickPaymentModal');
    },

    async submitQuickPayment() {
        const form = document.getElementById('quickPaymentForm');
        const formData = new FormData(form);
        formData.append('action', 'record_payment');

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_payment_recorded'));
                this.closeModal('quickPaymentModal');
                this.loadInvoices();
                this.loadDashboard();
                this.loadClients();
            } else {
                this.showToast(result.message || 'Error', 'error');
            }
        } catch (err) {
            this.showToast('Failed to record payment', 'error');
        }
    },

    // ==========================================
    // 7. Full Invoice Builder (Create & Edit)
    // ==========================================
    openNewInvoiceModal() {
        this.editingInvoiceId = null;
        const idInput = document.getElementById('invoiceIdInput');
        if (idInput) idInput.value = '';
        const formTitle = document.getElementById('invoiceFormTitle');
        if (formTitle) formTitle.innerText = this.t('btn_new_invoice');
        
        const newClientCheck = document.getElementById('toggleNewClientCheck');
        if (newClientCheck) newClientCheck.checked = false;
        const quickBox = document.getElementById('quickNewClientBox');
        if (quickBox) quickBox.style.display = 'none';
        const existGroup = document.getElementById('existingClientGroup');
        if (existGroup) existGroup.style.display = 'block';

        const quickName = document.getElementById('quickNewClientName');
        if (quickName) quickName.value = '';
        const quickContact = document.getElementById('quickNewContactPerson');
        if (quickContact) quickContact.value = '';
        const quickPhone = document.getElementById('quickNewPhone');
        if (quickPhone) quickPhone.value = '';
        const quickAddr = document.getElementById('quickNewAddress');
        if (quickAddr) quickAddr.value = '';

        const nextNum = Math.floor(100 + Math.random() * 900);
        const numInput = document.getElementById('invoiceNumberInput');
        if (numInput) numInput.value = `00${nextNum}`;

        const issueDate = document.getElementById('invoiceIssueDate');
        if (issueDate) issueDate.value = new Date().toISOString().split('T')[0];
        
        const dueDateObj = new Date();
        dueDateObj.setDate(dueDateObj.getDate() + 15);
        const dueDate = document.getElementById('invoiceDueDate');
        if (dueDate) dueDate.value = dueDateObj.toISOString().split('T')[0];

        const statusSelect = document.getElementById('invoiceStatusSelect');
        if (statusSelect) statusSelect.value = 'paid';
        const payMethod = document.getElementById('invoicePaymentMethod');
        if (payMethod) payMethod.value = 'gcash';
        const payTerm = document.getElementById('invoicePaymentTerm');
        if (payTerm) payTerm.value = 'full';
        const instMonths = document.getElementById('invoiceInstallmentMonths');
        if (instMonths) instMonths.value = '1';
        const partnerAssigned = document.getElementById('invoicePartnerAssigned');
        if (partnerAssigned) partnerAssigned.value = 'both';
        const priceNote = document.getElementById('invoicePriceNote');
        if (priceNote) priceNote.value = 'PHP 7,500 Per Month';
        const dueNote = document.getElementById('invoiceAmountDueNote');
        if (dueNote) dueNote.value = '7,500 PHP';
        const paidInput = document.getElementById('invoicePaidAmountInput');
        if (paidInput) paidInput.value = '7500.00';
        const balInput = document.getElementById('invoiceRemainingBalanceInput');
        if (balInput) balInput.value = '0.00';
        const notesInput = document.getElementById('invoiceNotesInput');
        if (notesInput) notesInput.value = "We are open for your suggestions on improving our services with you.\nPayment can be transferred through STC Pay or GCash (Account Name: Ferdenand Sabado, GCash #: 09617190588).\nThanking you for doing Business with us!";

        const tbody = document.getElementById('invoiceItemsTableBody');
        tbody.innerHTML = '';
        this.addInvoiceItemRow(
            'Package of E-Commerce Maintenance Services and Web maintenance including Rescue System',
            "Includes: 1. Web design / Web system\n2. Job post / Job Offer\n3 Deployment System (QMS)\n4. Maintenance System\n5. Location Rescue system\n6. E-commerce site with SSL Security\n7. PRA Deployment\n8. FRA/SRA SYSTEM (IF NEEDED)\n9. Block Listing for OFW\n10. Voucher System\nHosting and domain, with SSL security\n--- Nothing Follows ---",
            1,
            7500.00,
            0,
            0
        );

        this.openModal('invoiceModal');
    },

    applyAdminPackagePreset(pkgKey) {
        if (!pkgKey) return;
        const packages = {
            pkg_jobconnection: {
                name: "Package of E-Commerce Maintenance Services and Web maintenance including Rescue System (QMS)",
                price: 7500.00,
                desc: "Includes: 1. Web design / Web system\n2. Job post / Job Offer\n3 Deployment System (QMS)\n4. Maintenance System\n5. Location Rescue system\n6. E-commerce site with SSL Security\n7. PRA Deployment\n8. FRA/SRA SYSTEM (IF NEEDED)\n9. Block Listing for OFW\n10. Voucher System\nHosting and domain, with SSL security\n--- Nothing Follows ---",
                priceNote: "PHP 7,500.00 Per Month",
                amountDue: "7,500 PHP"
            },
            pkg_brilliant: {
                name: "Package of E-Commerce Maintenance Services and Web maintenance including Rescue System (Annual Contract)",
                price: 90000.00,
                desc: "Includes: 1. Web design / Web system\n2. Job post / Job Offer\n3 Deployment System\n4. Company E-mail Server Maintenance\n5. Location Rescue system\n6. E-commerce site with SSL Security\n7. PRA Deployment\n8. FRA/SRA SYSTEM (IF NEEDED)\n9. Block Listing for OFW\n10. Voucher System\nHosting and domain, with SSL security\n--- Nothing Follows ---",
                priceNote: "PHP 90,000.00 Per Year Or PHP 7,500.00 Per Month",
                amountDue: "15,000 php partial payment / 7,500 php monthly"
            },
            pkg_joseline: {
                name: "Package of E-Commerce Maintenance Services and Web maintenance including Hardware & Email Server",
                price: 5500.00,
                desc: "Includes: 1. Web design / Web system\n2. Job post / Job Offer\n3. Technical Support (Hardware Maintenance)\n4. Company E-mail Server Maintenance\n5. Location Rescue system\n6. E-commerce site with SSL Security\n7. PRA Deployment\n8. FRA/SRA SYSTEM (IF NEEDED)\n9. Company Email Maintenance\n10. Voucher System\nHosting and domain, with SSL security\n--- Nothing Follows ---",
                priceNote: "PHP 5,500.00 Maintenance",
                amountDue: "5,500 PHP"
            },
            pkg_simsoh: {
                name: "Web and System (Online) & Maintenance Services",
                price: 5000.00,
                desc: "Web and System (Online) & Maintenance Services for the Month\n--- Nothing Follows ---",
                priceNote: "Php 5,000.00",
                amountDue: "5,000 Php"
            }
        };

        const pkg = packages[pkgKey];
        if (!pkg) return;

        const tbody = document.getElementById('invoiceItemsTableBody');
        if (tbody) tbody.innerHTML = '';
        this.addInvoiceItemRow(pkg.name, pkg.desc, 1, pkg.price, 0, 0);

        if (pkg.priceNote) document.getElementById('invoicePriceNote').value = pkg.priceNote;
        if (pkg.amountDue) document.getElementById('invoiceAmountDueNote').value = pkg.amountDue;

        this.recalculateTotals();
    },

    async editInvoice(id) {
        try {
            const res = await fetch(`api.php?action=get_invoice&id=${id}`);
            const text = await res.text();
            let result;
            try {
                result = JSON.parse(text);
            } catch (jsonErr) {
                console.error('Non-JSON get_invoice response:', text);
                this.showToast('Server returned invalid data. Please refresh and try again.', 'error');
                return;
            }

            if (!result.success) {
                this.showToast(result.message || 'Failed to load invoice details', 'error');
                return;
            }

            const inv = result.data;
            this.editingInvoiceId = inv.id;
            
            const idInput = document.getElementById('invoiceIdInput');
            if (idInput) idInput.value = inv.id;
            const titleEl = document.getElementById('invoiceFormTitle');
            if (titleEl) titleEl.innerText = `${this.t('btn_edit')} #${inv.invoice_number}`;
            const numInput = document.getElementById('invoiceNumberInput');
            if (numInput) numInput.value = inv.invoice_number;

            const newClientCheck = document.getElementById('toggleNewClientCheck');
            if (newClientCheck) newClientCheck.checked = false;
            const quickBox = document.getElementById('quickNewClientBox');
            if (quickBox) quickBox.style.display = 'none';
            const existGroup = document.getElementById('existingClientGroup');
            if (existGroup) existGroup.style.display = 'block';
            const clientSelect = document.getElementById('invoiceClientSelect');
            if (clientSelect) clientSelect.value = inv.client_id || '';

            const issueDateEl = document.getElementById('invoiceIssueDate');
            if (issueDateEl) issueDateEl.value = (inv.issue_date || '').substring(0, 10);
            const dueDateEl = document.getElementById('invoiceDueDate');
            if (dueDateEl) dueDateEl.value = (inv.due_date || '').substring(0, 10);
            const statusEl = document.getElementById('invoiceStatusSelect');
            if (statusEl) statusEl.value = inv.status;
            const payMethodEl = document.getElementById('invoicePaymentMethod');
            if (payMethodEl) payMethodEl.value = inv.payment_method || 'gcash';
            const payTermEl = document.getElementById('invoicePaymentTerm');
            if (payTermEl) payTermEl.value = inv.payment_term || 'full';
            const monthsEl = document.getElementById('invoiceInstallmentMonths');
            if (monthsEl) monthsEl.value = inv.installment_months || 1;
            const partnerEl = document.getElementById('invoicePartnerAssigned');
            if (partnerEl) partnerEl.value = inv.partner_assigned || 'both';
            const priceNoteEl = document.getElementById('invoicePriceNote');
            if (priceNoteEl) priceNoteEl.value = inv.price_note || '';
            const amountDueNoteEl = document.getElementById('invoiceAmountDueNote');
            if (amountDueNoteEl) amountDueNoteEl.value = inv.amount_due_note || '';
            const paidEl = document.getElementById('invoicePaidAmountInput');
            if (paidEl) paidEl.value = inv.paid_amount || '0.00';
            const balanceEl = document.getElementById('invoiceRemainingBalanceInput');
            if (balanceEl) balanceEl.value = inv.remaining_balance || '0.00';
            const notesEl = document.getElementById('invoiceNotesInput');
            if (notesEl) notesEl.value = inv.notes || '';

            const tbody = document.getElementById('invoiceItemsTableBody');
            if (tbody) {
                tbody.innerHTML = '';
                if (inv.items && inv.items.length > 0) {
                    inv.items.forEach(it => {
                        this.addInvoiceItemRow(it.item_name, it.description, it.quantity, it.unit_price, it.discount, it.tax_rate);
                    });
                } else {
                    this.addInvoiceItemRow();
                }
            }

            this.recalculateTotals();
            this.openModal('invoiceModal');
        } catch (err) {
            console.error('editInvoice error:', err);
            this.showToast('Failed to load invoice details: ' + (err.message || ''), 'error');
        }
    },

    onInvoiceTermChange() {
        const term = document.getElementById('invoicePaymentTerm').value;
        const monthsInput = document.getElementById('invoiceInstallmentMonths');
        if (term === '3_months') monthsInput.value = 3;
        else if (term === '6_months') monthsInput.value = 6;
        else if (term === 'full') monthsInput.value = 1;
        this.recalculateTotals();
    },

    addInvoiceItemRow(name = '', desc = '', qty = 1, price = 0, discount = 0, tax = 0) {
        const tbody = document.getElementById('invoiceItemsTableBody');
        if (!tbody) return;

        const row = document.createElement('tr');
        row.className = 'item-builder-row';
        row.innerHTML = `
            <td>
                <input type="text" class="form-control item-name" value="${this.escapeHtml(name)}" placeholder="Service / Item name *" required style="font-weight:700; margin-bottom:4px;">
                <textarea class="form-control item-desc" rows="3" placeholder="Includes details / specifications...">${this.escapeHtml(desc)}</textarea>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" class="form-control item-qty text-center" value="${qty}">
            </td>
            <td>
                <input type="number" step="0.01" min="0" class="form-control item-price" value="${price}">
            </td>
            <td>
                <input type="number" step="0.01" min="0" class="form-control item-discount" value="${discount}">
            </td>
            <td>
                <input type="number" step="0.01" min="0" class="form-control item-tax text-center" value="${tax}">
            </td>
            <td style="text-align:center; font-weight:700; vertical-align:middle;">
                <span class="item-line-total">0.00</span>
            </td>
            <td style="text-align:center; vertical-align:middle;">
                <button type="button" class="btn btn-sm btn-outline" style="color:#ef4444; padding:4px 6px;" onclick="this.closest('tr').remove(); App.recalculateTotals();">
                    ✕
                </button>
            </td>
        `;

        tbody.appendChild(row);

        row.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', () => this.recalculateTotals());
        });

        this.recalculateTotals();
    },

    recalculateTotals() {
        let subtotal = 0;
        let totalDiscount = 0;
        let totalTax = 0;

        document.querySelectorAll('.item-builder-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const discount = parseFloat(row.querySelector('.item-discount').value) || 0;
            const taxRate = parseFloat(row.querySelector('.item-tax').value) || 0;

            const lineGross = qty * price;
            const lineDiscount = Math.min(lineGross, discount);
            const lineTaxable = Math.max(0, lineGross - lineDiscount);
            const lineTax = (lineTaxable * taxRate) / 100;
            const lineTotal = lineTaxable + lineTax;

            subtotal += lineGross;
            totalDiscount += lineDiscount;
            totalTax += lineTax;

            const lineTotalEl = row.querySelector('.item-line-total');
            if (lineTotalEl) lineTotalEl.innerText = this.formatMoney(lineTotal);
        });

        const grandTotal = Math.max(0, subtotal - totalDiscount + totalTax);

        const subEl = document.getElementById('calcSubtotal');
        if (subEl) subEl.innerText = `${this.formatMoney(subtotal)} ${this.currency}`;

        const discEl = document.getElementById('calcDiscount');
        if (discEl) discEl.innerText = `${this.formatMoney(totalDiscount)} ${this.currency}`;

        const taxEl = document.getElementById('calcTax');
        if (taxEl) taxEl.innerText = `${this.formatMoney(totalTax)} ${this.currency}`;

        const grandEl = document.getElementById('calcGrandTotal');
        if (grandEl) grandEl.innerText = `${this.formatMoney(grandTotal)} ${this.currency}`;

        // Compute monthly installment
        const months = Math.max(1, parseInt(document.getElementById('invoiceInstallmentMonths')?.value) || 1);
        const monthly = (months > 0) ? (grandTotal / months) : grandTotal;
        const monthlyCalcEl = document.getElementById('calcMonthlyPayment');
        if (monthlyCalcEl) monthlyCalcEl.innerText = `${this.formatMoney(monthly)} ${this.currency} (${months} mos)`;

        const paidInput = document.getElementById('invoicePaidAmountInput');
        const paidAmount = paidInput ? (parseFloat(paidInput.value) || 0) : 0;
        const balanceField = document.getElementById('invoiceRemainingBalanceInput');
        if (balanceField && !balanceField.dataset.manualOverride) {
            balanceField.value = Math.max(0, grandTotal - paidAmount).toFixed(2);
        }

        const balCalcEl = document.getElementById('calcRemainingBalance');
        if (balCalcEl && balanceField) balCalcEl.innerText = `${this.formatMoney(balanceField.value)} ${this.currency}`;
    },

    async saveInvoice() {
        const id = document.getElementById('invoiceIdInput').value;
        const invoiceNumber = document.getElementById('invoiceNumberInput').value.trim();
        const clientId = document.getElementById('invoiceClientSelect').value;
        const isNewClient = document.getElementById('toggleNewClientCheck')?.checked;
        const newClientName = document.getElementById('quickNewClientName')?.value.trim();
        const newContactPerson = document.getElementById('quickNewContactPerson')?.value.trim();
        const newClientPhone = document.getElementById('quickNewPhone')?.value.trim();
        const newClientAddress = document.getElementById('quickNewAddress')?.value.trim();

        const issueDate = document.getElementById('invoiceIssueDate').value;
        const dueDate = document.getElementById('invoiceDueDate').value;
        const status = document.getElementById('invoiceStatusSelect').value;
        const paymentMethod = document.getElementById('invoicePaymentMethod').value;
        const paymentTerm = document.getElementById('invoicePaymentTerm').value;
        const installmentMonths = document.getElementById('invoiceInstallmentMonths').value;
        const partnerAssigned = document.getElementById('invoicePartnerAssigned').value;

        const priceNote = document.getElementById('invoicePriceNote').value.trim();
        const amountDueNote = document.getElementById('invoiceAmountDueNote').value.trim();
        const paidAmount = document.getElementById('invoicePaidAmountInput').value;
        const remainingBalance = document.getElementById('invoiceRemainingBalanceInput').value;
        const notes = document.getElementById('invoiceNotesInput').value.trim();

        const items = [];
        document.querySelectorAll('.item-builder-row').forEach(row => {
            const itemName = row.querySelector('.item-name').value.trim();
            if (itemName) {
                items.push({
                    item_name: itemName,
                    description: row.querySelector('.item-desc').value.trim(),
                    quantity: parseFloat(row.querySelector('.item-qty').value) || 1,
                    unit_price: parseFloat(row.querySelector('.item-price').value) || 0,
                    discount: parseFloat(row.querySelector('.item-discount').value) || 0,
                    tax_rate: parseFloat(row.querySelector('.item-tax').value) || 0
                });
            }
        });

        if (items.length === 0) {
            this.showToast(this.t('toast_item_required'), 'error');
            return;
        }

        const payload = {
            action: 'save_invoice',
            id: id,
            invoice_number: invoiceNumber,
            client_id: clientId,
            new_client_name: isNewClient ? newClientName : '',
            new_contact_person: isNewClient ? newContactPerson : '',
            new_client_phone: isNewClient ? newClientPhone : '',
            new_client_address: isNewClient ? newClientAddress : '',
            issue_date: issueDate,
            due_date: dueDate,
            status: status,
            payment_method: paymentMethod,
            payment_term: paymentTerm,
            installment_months: installmentMonths,
            partner_assigned: partnerAssigned,
            price_note: priceNote,
            amount_due_note: amountDueNote,
            paid_amount: paidAmount,
            remaining_balance: remainingBalance,
            notes: notes,
            items: items
        };

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_invoice_saved'));
                this.closeModal('invoiceModal');
                this.loadInvoices();
                this.loadDashboard();
                this.loadClients();
            } else {
                this.showToast(result.message || 'Error saving invoice', 'error');
            }
        } catch (err) {
            this.showToast('Failed to save invoice', 'error');
        }
    },

    async deleteInvoice(id) {
        if (!confirm(this.t('confirm_delete_invoice'))) return;
        try {
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_invoice', id: id })
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_invoice_deleted'));
                this.loadInvoices();
                this.loadDashboard();
                this.loadClients();
            } else {
                this.showToast(result.message || 'Error', 'error');
            }
        } catch (err) {
            this.showToast('Failed to delete invoice', 'error');
        }
    },

    async viewInvoice(id) {
        try {
            const res = await fetch(`api.php?action=get_invoice&id=${id}`);
            const text = await res.text();
            let result;
            try {
                result = JSON.parse(text);
            } catch (e) {
                console.error("Non-JSON viewInvoice response:", text);
                this.showToast("Failed to preview invoice: Invalid server response", "error");
                return;
            }

            if (!result.success) {
                this.showToast(result.message || 'Invoice details not available', 'error');
                return;
            }

            const inv = result.data;
            const container = document.getElementById('viewInvoiceModalContent');
            if (!container) return;

            const partnerText = (inv.partner_assigned === 'both') 
                ? 'Both (Mr. Ibraheem & Mr. Fawas - 50/50 Split)' 
                : ((inv.partner_assigned === 'ibraheem') ? 'Mr. Ibraheem' : 'Mr. Fawas');

            const items = inv.items || [];

            container.innerHTML = `
                <div style="border-bottom:1px solid var(--border); padding-bottom:16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
                    <div style="display:flex; align-items:flex-start; gap:14px;">
                        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:4px 8px; box-shadow:0 1px 4px rgba(0,0,0,0.06); flex-shrink:0;">
                            <img src="assets/images/yaramay-logo.png" alt="YARAMAY" style="height:44px; width:auto; display:block;">
                        </div>
                        <div>
                            <h3 style="font-size:20px; font-weight:800; color:var(--primary); font-family:monospace; margin:0;">${this.escapeHtml(inv.invoice_number)}</h3>
                            <div style="font-size:14px; font-weight:700; color:#334155; margin-top:2px;">${this.escapeHtml(inv.client_name || this.t('cash_client'))}</div>
                            ${inv.contact_person ? `<div style="font-size:12px; color:#64748b;">👤 ${this.escapeHtml(inv.contact_person)}</div>` : ''}
                            ${inv.client_address ? `<div style="font-size:12px; color:#64748b;">📍 ${this.escapeHtml(inv.client_address)}</div>` : ''}
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div>${this.renderStatusBadge(inv.status)}</div>
                        <div style="font-size:12.5px; font-weight:700; color:#1e293b; margin-top:5px;">📅 Date: ${inv.issue_date}</div>
                        <div style="font-size:11.5px; color:#64748b;">Due Date: ${inv.due_date || 'N/A'}</div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(130px, 1fr)); gap:12px; background:var(--bg-muted); padding:14px; border-radius:8px; margin-bottom:16px;">
                    <div>
                        <span style="font-size:11px; color:#64748b; display:block;">TOTAL COST</span>
                        <strong style="font-size:16px; color:var(--primary);">${this.formatMoney(inv.grand_total)} ${this.currency}</strong>
                    </div>
                    <div>
                        <span style="font-size:11px; color:#64748b; display:block;">AMOUNT PAID</span>
                        <strong style="font-size:16px; color:#15803d;">${this.formatMoney(inv.paid_amount)} ${this.currency}</strong>
                    </div>
                    <div>
                        <span style="font-size:11px; color:#64748b; display:block;">REMAINING BALANCE</span>
                        <strong style="font-size:16px; color:${parseFloat(inv.remaining_balance) > 0 ? '#b91c1c' : '#15803d'};">${this.formatMoney(inv.remaining_balance)} ${this.currency}</strong>
                    </div>
                </div>

                <!-- Partner & Payment Plan Snapshot -->
                <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:12px; background:#f8fafc; border:1px solid var(--border); padding:12px; border-radius:6px; margin-bottom:16px; font-size:12px;">
                    <div>
                        <span style="color:#64748b;">Assigned Account:</span> 
                        <strong style="cursor:pointer;" onclick="App.openPartnerModal(${inv.id}, '${inv.partner_assigned}', '${this.escapeHtml(inv.invoice_number)}')" title="Click to edit">${partnerText} ✏️</strong>
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">
                            Share: Ibraheem ₱${this.formatMoney(inv.ibraheem_share)} | Fawas ₱${this.formatMoney(inv.fawas_share)}
                        </div>
                    </div>
                    <div>
                        <span style="color:#64748b;">Payment Plan:</span>
                        <strong>${inv.installment_months > 1 ? `${inv.installment_months} Months Installment (₱${this.formatMoney(inv.monthly_payment)}/mo)` : 'Full Payment'}</strong>
                        <div style="font-size:11px; color:#64748b; margin-top:2px;">Method: ${inv.payment_method ? inv.payment_method.toUpperCase() : 'GCASH'} (Ferdenand Sabado 09617190588)</div>
                    </div>
                </div>

                <table class="data-table" style="margin-bottom:16px;">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${items.map(it => `
                            <tr>
                                <td>
                                    <strong>${this.escapeHtml(it.item_name)}</strong>
                                    ${it.description ? `<div style="font-size:11.5px; color:#475569; white-space:pre-line; margin-top:4px;">${this.escapeHtml(it.description)}</div>` : ''}
                                </td>
                                <td>${this.formatMoney(it.unit_price)}</td>
                                <td><strong>${this.formatMoney(it.total)}</strong></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>

                ${inv.payment_receipt ? `
                    <div style="background:#f0fdf4; border:1.5px solid #86efac; padding:14px; border-radius:8px; margin-bottom:16px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="background:#16a34a; color:#fff; font-size:11px; font-weight:800; padding:2px 8px; border-radius:4px;">GCASH PROOF</span>
                                <strong style="font-size:13px; color:#14532d;">Uploaded Payment Screenshot / Receipt</strong>
                            </div>
                            <a href="${this.escapeHtml(inv.payment_receipt)}" target="_blank" class="btn btn-sm btn-outline" style="border-color:#16a34a; color:#16a34a; font-size:11px; text-decoration:none; padding:4px 10px;">
                                🔍 Open Full Image ↗
                            </a>
                        </div>
                        <div style="text-align:center; background:#ffffff; border:1px solid #bbf7d0; border-radius:6px; padding:10px;">
                            <a href="${this.escapeHtml(inv.payment_receipt)}" target="_blank">
                                <img src="${this.escapeHtml(inv.payment_receipt)}" alt="Payment Receipt" style="max-height:260px; max-width:100%; object-fit:contain; border-radius:4px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                            </a>
                            <div style="font-size:11px; color:#64748b; margin-top:6px;">Click screenshot to view in full resolution</div>
                        </div>
                    </div>
                ` : ''}

                ${inv.notes ? `
                    <div style="background:#fef2f2; border:1px solid #fecaca; padding:12px; border-radius:6px; font-size:12px; color:#991b1b; margin-bottom:16px;">
                        <strong>Notes & Payment Info:</strong>
                        <div style="white-space:pre-line; margin-top:3px;">${this.escapeHtml(inv.notes)}</div>
                    </div>
                ` : ''}

                <div style="display:flex; justify-content:flex-end; flex-wrap:wrap; gap:10px;">
                    <a href="${this.getPrintUrl(inv.id)}" target="_blank" class="btn btn-primary">
                        🖨️ ${this.t('btn_print_official')}
                    </a>
                    <button class="btn btn-outline" onclick="App.openQuickPaymentModal(${inv.id})">
                        💳 ${this.t('btn_record_payment')}
                    </button>
                    <button class="btn btn-light" onclick="App.closeModal('viewInvoiceModal')">
                        ${this.t('btn_close')}
                    </button>
                </div>
            `;

            this.openModal('viewInvoiceModal');
        } catch (err) {
            console.error(err);
        }
    },

    // ==========================================
    // 8. Products Catalog
    // ==========================================
    async loadProducts() {
        try {
            const res = await fetch('api.php?action=get_products');
            const result = await res.json();
            if (result.success) {
                this.products = result.data;
                this.renderProductsTable();
            }
        } catch (err) {
            console.error('Error loading products', err);
        }
    },

    renderProductsTable() {
        const tbody = document.getElementById('productsTableBody');
        if (!tbody) return;

        if (this.products.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:20px; color:#94a3b8;">${this.t('no_products')}</td></tr>`;
            return;
        }

        tbody.innerHTML = this.products.map(p => `
            <tr>
                <td><strong>${this.escapeHtml(p.name)}</strong></td>
                <td><div style="font-size:12px; color:var(--text-muted); max-width:360px;">${this.escapeHtml(p.description || '-')}</div></td>
                <td><strong>${this.formatMoney(p.price)} ${this.currency}</strong></td>
                <td>${p.tax_rate}%</td>
                <td>
                    <div class="actions-cell">
                        <button class="btn btn-sm btn-outline" onclick="App.editProduct(${p.id})">${this.t('btn_edit')}</button>
                        <button class="btn btn-sm btn-outline" style="color:#ef4444;" onclick="App.deleteProduct(${p.id})">${this.t('btn_delete')}</button>
                    </div>
                </td>
            </tr>
        `).join('');
    },

    openNewProductModal() {
        const idInput = document.getElementById('productIdInput');
        if (idInput) idInput.value = '';
        const nameInput = document.getElementById('productNameInput');
        if (nameInput) nameInput.value = '';
        const priceInput = document.getElementById('productPriceInput');
        if (priceInput) priceInput.value = '';
        const taxInput = document.getElementById('productTaxInput');
        if (taxInput) taxInput.value = '0';
        const descInput = document.getElementById('productDescInput');
        if (descInput) descInput.value = '';
        const titleEl = document.getElementById('productModalTitle');
        if (titleEl) titleEl.innerText = this.t('product_modal_title_new');
        this.openModal('productModal');
    },

    editProduct(id) {
        const p = this.products.find(x => x.id == id);
        if (!p) return;
        const idInput = document.getElementById('productIdInput');
        if (idInput) idInput.value = p.id;
        const nameInput = document.getElementById('productNameInput');
        if (nameInput) nameInput.value = p.name;
        const priceInput = document.getElementById('productPriceInput');
        if (priceInput) priceInput.value = p.price;
        const taxInput = document.getElementById('productTaxInput');
        if (taxInput) taxInput.value = p.tax_rate;
        const descInput = document.getElementById('productDescInput');
        if (descInput) descInput.value = p.description || '';
        const titleEl = document.getElementById('productModalTitle');
        if (titleEl) titleEl.innerText = this.t('product_modal_title_edit');
        this.openModal('productModal');
    },

    async saveProduct() {
        const form = document.getElementById('productForm');
        const formData = new FormData(form);
        formData.append('action', 'save_product');

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_product_saved'));
                this.closeModal('productModal');
                this.loadProducts();
            } else {
                this.showToast(result.message || 'Error', 'error');
            }
        } catch (err) {
            this.showToast('Failed to save service', 'error');
        }
    },

    async deleteProduct(id) {
        if (!confirm(this.t('confirm_delete_product'))) return;
        try {
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete_product', id: id })
            });
            const result = await res.json();
            if (result.success) {
                this.showToast(this.t('toast_product_deleted'));
                this.loadProducts();
            } else {
                this.showToast(result.message || 'Error', 'error');
            }
        } catch (err) {
            this.showToast('Failed to delete service', 'error');
        }
    },

    // ==========================================
    // 9. Badges & Helpers
    // ==========================================
    renderStatusBadge(status) {
        const labels = {
            paid: { text: this.t('status_paid'), class: 'badge-paid' },
            partial: { text: this.t('status_partial'), class: 'badge-partial' },
            unpaid: { text: this.t('status_unpaid'), class: 'badge-unpaid' },
            pending: { text: this.t('status_pending'), class: 'badge-pending' },
            cancelled: { text: this.t('status_cancelled'), class: 'badge-cancelled' },
        };
        const s = labels[status] || { text: status, class: 'badge-pending' };
        return `<span class="badge ${s.class}">${s.text}</span>`;
    },

    renderBalanceBadge(balance) {
        const num = parseFloat(balance) || 0;
        if (num <= 0) {
            return `<span class="balance-pill-paid">✓ 0.00 (Paid)</span>`;
        }
        return `<span class="balance-pill-due">₱ ${this.formatMoney(num)} (Due)</span>`;
    },

    renderPartnerBadge(partner, ibraheemShare = 0, fawasShare = 0, invoiceId = 0, invoiceNumber = '') {
        const isClickable = invoiceId > 0;
        const clickAttr = isClickable 
            ? `onclick="App.openPartnerModal(${invoiceId}, '${partner || 'both'}', '${this.escapeHtml(invoiceNumber)}'); event.stopPropagation();"` 
            : '';
        const titleAttr = isClickable 
            ? `title="Click to change partner assignment (Mr. Ibraheem / Mr. Fawas / Both)"` 
            : '';
        const extraClass = isClickable ? 'partner-clickable-badge' : '';

        if (partner === 'ibraheem') {
            return `<span class="badge badge-partner-ibraheem ${extraClass}" ${clickAttr} ${titleAttr}>👤 Mr. Ibraheem ${isClickable ? '✏️' : ''}</span>`;
        } else if (partner === 'fawas') {
            return `<span class="badge badge-partner-fawas ${extraClass}" ${clickAttr} ${titleAttr}>👤 Mr. Fawas ${isClickable ? '✏️' : ''}</span>`;
        } else {
            return `<span class="badge badge-partner-both ${extraClass}" ${clickAttr} ${titleAttr}>🤝 Both (50/50) ${isClickable ? '✏️' : ''}</span>`;
        }
    },

    openPartnerModal(invoiceId, currentPartner = 'both', invoiceNumber = '') {
        const idInput = document.getElementById('quickPartnerInvoiceId');
        if (idInput) idInput.value = invoiceId;
        const numEl = document.getElementById('quickPartnerInvoiceNum');
        if (numEl) numEl.textContent = invoiceNumber ? `#${invoiceNumber}` : `#${invoiceId}`;

        const radios = document.querySelectorAll('input[name="quickPartnerChoice"]');
        radios.forEach(r => {
            r.checked = (r.value === currentPartner);
        });
        this.openModal('partnerModal');
    },

    async savePartnerAssignment() {
        const invoiceId = document.getElementById('quickPartnerInvoiceId').value;
        const choice = document.querySelector('input[name="quickPartnerChoice"]:checked')?.value || 'both';
        const btn = document.getElementById('saveQuickPartnerBtn');

        if (!invoiceId) return;

        btn.disabled = true;
        btn.innerHTML = 'Saving...';

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'update_partner_assignment',
                    id: invoiceId,
                    partner_assigned: choice
                })
            });
            const text = await res.text();
            let result;
            try {
                result = JSON.parse(text);
            } catch (e) {
                throw new Error(text.substring(0, 100));
            }

            if (result.success) {
                this.showToast('Partner assignment updated & shares recalculated!', 'success');
                this.closeModal('partnerModal');
                this.loadDashboard();
                this.loadInvoices();
                this.loadClients();
                if (this.currentDossierClientId) {
                    this.viewClientDossier(this.currentDossierClientId);
                }
            } else {
                this.showToast(result.message || 'Failed to update partner assignment', 'error');
            }
        } catch (err) {
            console.error('savePartnerAssignment error:', err);
            this.showToast('Failed to update partner assignment', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Save Assignment</span>`;
        }
    },

    renderInstallmentPill(inv) {
        const months = parseInt(inv.installment_months) || 1;
        if (months > 1) {
            return `<div class="installment-pill">📅 ${months} Mos (₱${this.formatMoney(inv.monthly_payment)}/mo)</div>`;
        }
        return `<div class="installment-pill" style="background:#e0f2fe; color:#0369a1;">Full Payment</div>`;
    },

    getPrintUrl(id, lang) {
        const page = window.YARAMAY_STATIC_MODE ? 'print.html' : 'print.php';
        return `${page}?id=${id}&lang=${lang || this.lang}`;
    },

    formatMoney(amount) {
        return (parseFloat(amount) || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    },

    escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    },

    // ==========================================
    // Admin Users Management Methods
    // ==========================================
    async loadUsers() {
        try {
            const res = await fetch('api.php?action=get_users');
            const data = await res.json();
            if (data.success) {
                this.users = data.data || [];
                this.renderUsersTable();
            } else {
                this.showToast(data.message || 'Failed to load admin users', 'error');
            }
        } catch (err) {
            console.error('Error loading users:', err);
        }
    },

    renderUsersTable() {
        const tbody = document.getElementById('usersTableBody');
        if (!tbody) return;

        if (!this.users.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center" style="padding:24px; color:var(--text-muted);">No admin users registered.</td></tr>`;
            return;
        }

        tbody.innerHTML = this.users.map(u => {
            const isSuper = (u.role === 'superadmin');
            const roleBadge = isSuper
                ? `<span class="badge" style="background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5; font-weight:700;">★ SUPERADMIN</span>`
                : `<span class="badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; font-weight:600;">ADMIN</span>`;

            return `
                <tr>
                    <td><strong>#${u.id}</strong></td>
                    <td>
                        <strong style="color:var(--primary); font-size:14px;">${this.escapeHtml(u.username)}</strong>
                        ${u.username === 'ibraheem01' ? '<span style="font-size:10px; background:#fef2f2; color:#dc2626; padding:2px 5px; border-radius:4px; margin-left:4px; font-weight:700;">DEFAULT ROOT</span>' : ''}
                    </td>
                    <td>${this.escapeHtml(u.full_name)}</td>
                    <td>${roleBadge}</td>
                    <td>${this.escapeHtml(u.email || '—')}</td>
                    <td>${u.created_at ? u.created_at.substring(0, 10) : '—'}</td>
                    <td style="text-align:right;">
                        <button class="btn btn-outline btn-xs" onclick="App.openEditUserModal(${u.id})" title="Edit Admin User" style="margin-right:4px;">
                            ✏️ Edit
                        </button>
                        <button class="btn btn-outline btn-xs" onclick="App.openUserPasswordModal(${u.id}, '${this.escapeHtml(u.username)}')" title="Change Password" style="margin-right:4px;">
                            🔑 Password
                        </button>
                        ${u.id != 1 ? `
                            <button class="btn btn-outline btn-xs" onclick="App.deleteUser(${u.id}, '${this.escapeHtml(u.username)}')" title="Delete User" style="color:#dc2626; border-color:#fca5a5;">
                                🗑️ Delete
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `;
        }).join('');
    },

    openNewUserModal() {
        const titleEl = document.getElementById('userModalTitle');
        if (titleEl) titleEl.innerText = 'Add New Admin User';
        const idInput = document.getElementById('userIdInput');
        if (idInput) idInput.value = '';
        const userInput = document.getElementById('userUsernameInput');
        if (userInput) {
            userInput.value = '';
            userInput.disabled = false;
        }
        const nameInput = document.getElementById('userFullNameInput');
        if (nameInput) nameInput.value = '';
        const roleSelect = document.getElementById('userRoleSelect');
        if (roleSelect) roleSelect.value = 'admin';
        const emailInput = document.getElementById('userEmailInput');
        if (emailInput) emailInput.value = '';
        const passInput = document.getElementById('userPasswordInput');
        if (passInput) {
            passInput.value = '';
            passInput.required = true;
        }
        const passLabel = document.getElementById('userPasswordLabel');
        if (passLabel) passLabel.innerText = 'Password *';
        const passHint = document.getElementById('userPasswordHint');
        if (passHint) passHint.innerText = 'Minimum 6 characters.';
        this.openModal('userModal');
    },

    openEditUserModal(id) {
        const user = this.users.find(u => u.id == id);
        if (!user) return;

        const titleEl = document.getElementById('userModalTitle');
        if (titleEl) titleEl.innerText = `Edit Admin User: ${user.username}`;
        const idInput = document.getElementById('userIdInput');
        if (idInput) idInput.value = user.id;
        const userInput = document.getElementById('userUsernameInput');
        if (userInput) {
            userInput.value = user.username;
            userInput.disabled = false;
        }
        const nameInput = document.getElementById('userFullNameInput');
        if (nameInput) nameInput.value = user.full_name;
        const roleSelect = document.getElementById('userRoleSelect');
        if (roleSelect) roleSelect.value = user.role;
        const emailInput = document.getElementById('userEmailInput');
        if (emailInput) emailInput.value = user.email || '';
        const passInput = document.getElementById('userPasswordInput');
        if (passInput) {
            passInput.value = '';
            passInput.required = false;
        }
        const passLabel = document.getElementById('userPasswordLabel');
        if (passLabel) passLabel.innerText = 'New Password (Optional)';
        const passHint = document.getElementById('userPasswordHint');
        if (passHint) passHint.innerText = 'Leave empty to keep existing password.';
        this.openModal('userModal');
    },

    openUserPasswordModal(id, username) {
        this.openEditUserModal(id);
        const passInput = document.getElementById('userPasswordInput');
        if (passInput) {
            passInput.focus();
        }
    },

    async saveUser(e) {
        if (e) e.preventDefault();
        const id = document.getElementById('userIdInput').value;
        const username = document.getElementById('userUsernameInput').value.trim();
        const fullName = document.getElementById('userFullNameInput').value.trim();
        const role = document.getElementById('userRoleSelect').value;
        const email = document.getElementById('userEmailInput').value.trim();
        const password = document.getElementById('userPasswordInput').value.trim();

        if (!username || !fullName) {
            alert('Username and Full Name are required.');
            return;
        }

        if (!id && !password) {
            alert('Password is required for new admin users.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'save_user');
        formData.append('id', id);
        formData.append('username', username);
        formData.append('full_name', fullName);
        formData.append('role', role);
        formData.append('email', email);
        if (password) {
            formData.append('password', password);
        }

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                this.showToast(data.message || 'Admin user saved successfully');
                this.closeModal('userModal');
                this.loadUsers();

                // If updated current username in session, update topbar display
                const topbarUser = document.getElementById('topbarUsername');
                if (topbarUser && (!id || id == 1)) {
                    topbarUser.textContent = username;
                }
            } else {
                alert('Error: ' + data.message);
            }
        } catch (err) {
            alert('Failed to save user. Check server logs.');
        }
    },

    async deleteUser(id, username) {
        if (!confirm(`Are you sure you want to delete admin user "${username}"? This cannot be undone.`)) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'delete_user');
        formData.append('id', id);

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                this.showToast(`Admin user "${username}" deleted successfully`);
                this.loadUsers();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (err) {
            alert('Failed to delete user.');
        }
    },

    openChangeMyPasswordModal() {
        document.getElementById('changeMyPasswordForm').reset();
        this.openModal('changeMyPasswordModal');
    },

    async submitChangeMyPassword(e) {
        if (e) e.preventDefault();
        const currentPassword = document.getElementById('myCurrentPasswordInput').value;
        const newPassword = document.getElementById('myNewPasswordInput').value;
        const confirmPassword = document.getElementById('myConfirmPasswordInput').value;

        if (newPassword !== confirmPassword) {
            alert('New password and Confirm password do not match.');
            return;
        }

        if (newPassword.length < 6) {
            alert('Password must be at least 6 characters long.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'change_my_password');
        formData.append('current_password', currentPassword);
        formData.append('new_password', newPassword);

        try {
            const res = await fetch('api.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                this.showToast('Your password has been changed successfully!');
                this.closeModal('changeMyPasswordModal');
            } else {
                alert('Error: ' + data.message);
            }
        } catch (err) {
            alert('Failed to update password.');
        }
    },

    // ==========================================
    // Security Shield & Incident Audit Methods
    // ==========================================
    async loadSecurityLogs() {
        try {
            const res = await fetch('api.php?action=get_security_logs');
            const data = await res.json();
            if (data.success) {
                const logs = data.data.logs || [];
                const stats = data.data.stats || {};

                // Update KPI Cards
                const attacksEl = document.getElementById('kpiAttacksBlocked');
                const failedEl = document.getElementById('kpiFailedLogins');
                const lockoutsEl = document.getElementById('kpiLockoutsTriggered');

                if (attacksEl) attacksEl.textContent = stats.total_attacks_blocked || 0;
                if (failedEl) failedEl.textContent = stats.total_failed_logins || 0;
                if (lockoutsEl) lockoutsEl.textContent = stats.total_lockouts || 0;

                this.renderSecurityLogs(logs);
            } else {
                this.showToast(data.message || 'Failed to load security logs', 'error');
            }
        } catch (err) {
            console.error('Error loading security logs:', err);
        }
    },

    renderSecurityLogs(logs) {
        const tbody = document.getElementById('securityLogsTableBody');
        if (!tbody) return;

        if (!logs.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center" style="padding:24px; color:var(--text-muted);">No security incidents recorded. System is 100% secure.</td></tr>`;
            return;
        }

        tbody.innerHTML = logs.map(l => {
            let badgeStyle = 'background:#f1f5f9; color:#475569;';
            if (l.event_type === 'ATTACK_BLOCKED') {
                badgeStyle = 'background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5; font-weight:700;';
            } else if (l.event_type === 'LOGIN_FAILED') {
                badgeStyle = 'background:#fef3c7; color:#b45309; border:1px solid #fcd34d; font-weight:700;';
            } else if (l.event_type === 'LOGIN_SUCCESS') {
                badgeStyle = 'background:#dcfce7; color:#15803d; border:1px solid #86efac; font-weight:700;';
            } else if (l.event_type === 'BRUTE_FORCE_LOCKOUT') {
                badgeStyle = 'background:#fce7f3; color:#be185d; border:1px solid #fbcfe8; font-weight:800;';
            } else if (l.event_type === 'MALWARE_REJECTED') {
                badgeStyle = 'background:#450a0a; color:#fca5a5; border:1px solid #dc2626; font-weight:800;';
            }

            return `
                <tr>
                    <td><strong>#${l.id}</strong></td>
                    <td>
                        <span class="badge" style="${badgeStyle}">${this.escapeHtml(l.event_type)}</span>
                    </td>
                    <td><code>${this.escapeHtml(l.ip_address)}</code></td>
                    <td><strong>${this.escapeHtml(l.username || 'Anonymous')}</strong></td>
                    <td style="max-width:320px; font-size:12px; word-break:break-word;">${this.escapeHtml(l.details || '—')}</td>
                    <td style="max-width:200px; font-size:11px; color:var(--text-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${this.escapeHtml(l.user_agent || '')}">${this.escapeHtml(l.user_agent ? l.user_agent.substring(0, 45) + '...' : '—')}</td>
                    <td style="font-size:12px; white-space:nowrap;">${l.created_at || '—'}</td>
                </tr>
            `;
        }).join('');
    },

    async clearSecurityLogs() {
        if (!confirm('Are you sure you want to clear all security and audit logs? This requires Superadmin privileges.')) {
            return;
        }

        try {
            const res = await fetch('api.php?action=clear_security_logs');
            const data = await res.json();
            if (data.success) {
                this.showToast('Security audit logs cleared successfully.');
                this.loadSecurityLogs();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (err) {
            alert('Failed to clear logs.');
        }
    },

    exportInvoicesToCSV() {
        if (!this.invoices || this.invoices.length === 0) {
            this.showToast(this.t('no_records'), 'error');
            return;
        }

        const headers = ['Invoice #', 'Client Name', 'Contact Person', 'Issue Date', 'Total Cost', 'Paid Amount', 'Remaining Balance', 'Payment Plan', 'Assigned Partner', 'Status'];
        const rows = this.invoices.map(i => [
            `"${i.invoice_number}"`,
            `"${i.client_name || ''}"`,
            `"${i.contact_person || ''}"`,
            `"${i.issue_date}"`,
            `"${i.grand_total}"`,
            `"${i.paid_amount || 0}"`,
            `"${i.remaining_balance || 0}"`,
            `"${i.installment_months > 1 ? i.installment_months + ' Months' : 'Full Payment'}"`,
            `"${i.partner_assigned || 'both'}"`,
            `"${i.status}"`
        ]);

        const csvContent = "\uFEFF" + [headers.join(','), ...rows.map(e => e.join(','))].join("\n");
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", `yaramay_invoices_${new Date().toISOString().slice(0, 10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    App.init();
});
