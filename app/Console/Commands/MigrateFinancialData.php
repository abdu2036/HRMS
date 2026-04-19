<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\FinancialTransaction;

class MigrateFinancialData extends Command
{
    protected $signature = 'finance:migrate';
    protected $description = 'نقل البيانات المالية مع تجاهل السجلات غير المرتبطة بموظفين حاليين';

    public function handle()
    {
        $this->warn('جاري تنظيف الحاوية المالية قبل البدء لتجنب التكرار...');
        FinancialTransaction::truncate(); // يمسح البيانات القديمة لتبدأ من جديد

        $this->info('بدء الترحيل الذكي...');

        // 1. ترحيل المكافآت
        $this->migrateTable('rewards', 'bonus', 'مكافأة');

        // 2. ترحيل الجزاءات
        $this->migrateTable('penalties', 'penalty', 'جزاء');

        // 3. ترحيل السلف النشطة
        $this->migrateLoans();

        $this->info('✅ اكتملت العملية بنجاح! تفقد صفحة السجل المالي الآن.');
    }

    private function migrateTable($tableName, $type, $label)
    {
        $items = DB::table($tableName)->get();
        $count = 0;

        foreach ($items as $item) {
            // التحقق من وجود الموظف في جدول employees
            if (DB::table('employees')->where('id', $item->employee_id)->exists()) {
                FinancialTransaction::create([
                    'employee_id'      => $item->employee_id,
                    'type'             => $type,
                    'amount'           => $item->amount,
                    'description'      => "ترحيل: " . ($item->description ?? $label),
                    'transaction_date' => $item->date ?? now(),
                ]);
                $count++;
            }
        }
        $this->info("تم ترحيل ($count) سجل من $tableName.");
    }

    private function migrateLoans()
    {
        $loans = DB::table('loans')->where('status', 'active')->get();
        $count = 0;

        foreach ($loans as $loan) {
            if (DB::table('employees')->where('id', $loan->employee_id)->exists()) {
                FinancialTransaction::create([
                    'employee_id'      => $loan->employee_id,
                    'type'             => 'advance',
                    'amount'           => $loan->amount,
                    'description'      => "ترحيل سلفة: " . ($loan->reason ?? 'بدون سبب'),
                    'transaction_date' => $loan->created_at ?? now(),
                ]);
                $count++;
            } else {
                $this->error("تخطي سلفة للموظف (ID: $loan->employee_id) لأنه غير موجود في جدول الموظفين.");
            }
        }
        $this->info("تم ترحيل ($count) سلفة نشطة.");
    }
}