1. MASTER SYSTEM
php artisan make:migration create_provinces_table
php artisan make:migration create_regencies_table
php artisan make:migration create_districts_table
php artisan make:migration create_products_table

2. CRM CORE (PIPELINE & STAGE)
php artisan make:migration create_crm_pipelines_table
php artisan make:migration create_crm_pipeline_stages_table

3. CRM MASTER DATA
php artisan make:migration create_crm_sources_table
php artisan make:migration create_crm_lost_reasons_table

4. ACTIVITY SYSTEM
php artisan make:migration create_crm_activity_types_table
php artisan make:migration create_crm_activity_results_table

5. LEADS (CORE TRANSAKSI)
php artisan make:migration create_crm_leads_table

6. HISTORY & TRACKING
php artisan make:migration create_crm_lead_stage_histories_table

7. ACTIVITY TRANSAKSI
php artisan make:migration create_crm_lead_activities_table


MASTER

✔ provinces
✔ regencies
✔ districts
✔ products

CRM MASTER

✔ crm_pipelines
✔ crm_pipeline_stages
✔ crm_stage_results
✔ crm_sources
✔ crm_activity_types
✔ crm_activity_results
✔ crm_lost_reasons

TRANSACTION

✔ crm_leads
✔ crm_lead_activities
✔ crm_lead_stage_histories
✔ crm_follow_ups
✔ crm_lead_attachments

OPTIONAL

✔ crm_tags
✔ crm_lead_tags