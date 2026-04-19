<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rats\Zkteco\Lib\Zkteco;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceDeviceController extends Controller
{
    private $deviceIp = '192.168.0.104'; 
    private $port = 4370;

    public function syncAttendance()
    {
        // ابقِ هذه القيمة true لتجربة البيانات التي تظهر في الأسفل
        $debugMode = true; 

        if ($debugMode) {
            return $this->processLogs($this->getMockLogs());
        }

        try {
            $zk = new Zkteco($this->deviceIp, $this->port);

            if ($zk->connect()) {
                $zk->disableDevice();
                $logs = $zk->getAttendance();
                $zk->enableDevice();
                $zk->disconnect();

                return $this->processLogs($logs);
            }

            return back()->with('error', 'تعذر الاتصال البرمجي بالجهاز. تأكد من أن وضع ADMS لا يمنع الاتصال المباشر ❌');

        } catch (\Exception $e) {
            return back()->with('error', 'خطأ تقني: ' . $e->getMessage());
        }
    }

    /**
     * معالجة السجلات بناءً على fingerprint_code
     */
    private function processLogs($logs)
    {
        if (empty($logs)) {
            return back()->with('info', 'لا توجد سجلات جديدة لمعالجتها.');
        }

        $count = 0;
        foreach ($logs as $log) {
            // سحب كود البصمة من السجل (في الجهاز يكون اسمه id عادةً)
            $fCode = isset($log['fingerprint_code']) ? $log['fingerprint_code'] : $log['id'];

            // البحث عن الموظف باستخدام الحقل الصحيح في قاعدة بياناتك
            $employee = Employee::where('fingerprint_code', $fCode)->first();

            if ($employee) {
                $logTime = Carbon::parse($log['timestamp']);
                $date = $logTime->toDateString();
                $time = $logTime->toTimeString();

                // التحقق من وجود سجل حضور مسبق لهذا اليوم
                $attendance = Attendance::firstOrCreate(
                    ['employee_id' => $employee->id, 'date' => $date],
                    ['signin_time' => $time, 'status' => 'present']
                );

                if ($attendance->wasRecentlyCreated) {
                    // حساب التأخير بناءً على وقت بداية الوردية المخزن للموظف
                    $officialStart = Carbon::parse($employee->shift_start ?? '08:00:00');
                    if ($logTime->format('H:i:s') > $officialStart->format('H:i:s')) {
                        $late = $logTime->diffInMinutes($officialStart);
                        $attendance->update(['late_minutes' => $late, 'status' => 'late']);
                    }
                    $count++;
                } else {
                    // تحديث وقت الانصراف بأحدث بصمة لنفس اليوم
                    if (is_null($attendance->signout_time) || $time > $attendance->signout_time) {
                        $attendance->update(['signout_time' => $time]);
                    }
                }
            }
        }

        return back()->with('success', "تمت المزامنة بنجاح. السجلات المتأثرة: $count ✅");
    }

    /**
     * بيانات وهمية تعتمد على fingerprint_code للموظفين الموجودين عندك
     */
    private function getMockLogs()
    {
        return [
            ['fingerprint_code' => '3', 'timestamp' => Carbon::now()->format('Y-m-d 08:45:00')], 
            ['fingerprint_code' => '4', 'timestamp' => Carbon::now()->format('Y-m-d 16:00:00')], 
            ['fingerprint_code' => '6', 'timestamp' => Carbon::now()->format('Y-m-d 08:00:00')], 
            ['fingerprint_code' => '6', 'timestamp' => Carbon::now()->format('Y-m-d 16:30:00')], 
            ['fingerprint_code' => '5', 'timestamp' => Carbon::now()->format('Y-m-d 08:30:00')], 
            ['fingerprint_code' => '10', 'timestamp' => Carbon::now()->format('Y-m-d 08:45:00')],
        ];
    }

    public function receivePushData(Request $request)
    {
        $rawData = $request->getContent();
        Log::info("بيانات ADMS قادمة: " . $rawData);
        return response("OK", 200)->header('Content-Type', 'text/plain');
    }
}