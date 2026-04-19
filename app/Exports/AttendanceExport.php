<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $employeeId;
    protected $month;

    public function __construct($employeeId, $month)
    {
        $this->employeeId = $employeeId;
        $this->month = $month;
    }

    public function collection()
    {
        return Attendance::where('employee_id', $this->employeeId)
            ->where('date', 'like', $this->month . '%')
            ->get();
    }

    public function headings(): array
    {
        return ["التاريخ", "وقت الحضور", "وقت الانصراف", "دقائق التأخير", "انصراف مبكر"];
    }

    public function map($attendance): array
    {
        return [
            $attendance->date,
            $attendance->signin_time,
            $attendance->signout_time ?? '--',
            $attendance->late_minutes,
            $attendance->early_out_minutes,
        ];
    }
}