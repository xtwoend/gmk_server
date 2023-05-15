<?php

declare(strict_types=1);

namespace App\Controller;

use Carbon\Carbon;
use App\MdModel\Device;
use App\MdModel\Startup;
use App\Resource\ReportResource;
use Hyperf\Database\Model\Builder;
use App\MdModel\ProductionVerification;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class ReportController
{
    public function data($id, RequestInterface $request)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($date)->timezone('Asia/Jakarta')->format('Y-m-d');

        $device = Device::findOrFail($id);
        $startup = Startup::with('device', 'verifications', 'verifications.operator', 'verifications.foreman')
                ->where('device_id', $device->id)
                ->whereDate('started_at', $date)
                ->latest()
                ->firstOrFail();

        $productions = ProductionVerification::with('operator', 'foreman', 'production', 'production.product')
            ->withCount(['good_records', 'ng_records'])
            ->whereIn('production_id', $startup->productions->pluck('id')->toArray())
            ->orderBy('started_at')
            ->orderBy('order')
            ->get();
        
        return response([
            'startup' => $startup,
            'productions' => $productions
        ]);
    }

    public function export($id, RequestInterface $request, ResponseInterface $response)
    {
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));
        $date = Carbon::parse($date)->timezone('Asia/Jakarta')->format('Y-m-d');

        $device = Device::findOrFail($id);
        $startup = Startup::with('device', 'verifications', 'verifications.operator', 'verifications.foreman')
                ->where('device_id', $device->id)
                ->whereDate('started_at', $date)
                ->latest()
                ->firstOrFail();

        $productions = ProductionVerification::with('operator', 'foreman', 'production', 'production.product')
            ->withCount(['good_records', 'ng_records'])
            ->whereIn('production_id', $startup->productions->pluck('id')->toArray())
            ->orderBy('started_at')
            ->orderBy('order')
            ->get();

        $fileName = 'report.xlsx';
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(BASE_PATH . '/template/report.xlsx');
        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $sheet->setCellValue('C2', $device->name);
        $sheet->setCellValue('C4', $device->size_fe);
        $sheet->setCellValue('D4', $device->size_non_fe);
        $sheet->setCellValue('E4', $device->size_ss);

        $sheet->setCellValue('C6', Carbon::parse($date)->format('d/m/Y'));

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ],
            ],
        ];

        // startup verification
        $i = 9;
        foreach($startup->verifications as $verify) {
            $sheet->setCellValue('B'. $i, Carbon::parse($verify->started_at)->format('d/m/Y H:i:s') . '-' . Carbon::parse($verify->finished_at)->format('d/m/Y H:i:s'));
            $sheet->setCellValue('C'. $i, $verify->type_text);
            $sheet->setCellValue('D'. $i, $verify->fe? '✔️' : '');
            $sheet->setCellValue('E'. $i, $verify->non_fe? '✔️' : '');
            $sheet->setCellValue('F'. $i, $verify->ss? '✔️' : '');

            $sheet->setCellValue('G'. $i, $verify->operator?->name ?: '');
            $sheet->setCellValue('I'. $i, $verify->foreman?->name ?: '');
            $sheet->setCellValue('K'. $i, $verify->wor_number);
            $i++;
        }

        $sheet->getStyle('B9:K'.$i)->applyFromArray($styleArray);

        // verification
        $j = 15;
        $no = 1;
        foreach($productions as $row) {
            $sheet->setCellValue('B' . $j, $no++);
            $sheet->setCellValue('C' . $j, Carbon::parse($row->started_at)->format('d/m/Y H:i:s') . '-' . Carbon::parse($row->finished_at)->format('d/m/Y H:i:s'));
            $sheet->setCellValue('D' . $j, $row->production?->product?->name);
            $sheet->setCellValue('E' . $j, $row->production?->batch_no);
            $sheet->setCellValue('F' . $j, $row->production?->product?->unit);
            $sheet->setCellValue('G' . $j, $row->order_text);

            $sheet->setCellValue('H' . $j, $row->fe_front? '✔️' : '');
            $sheet->setCellValue('I' . $j, $row->fe_mid? '✔️' : '');
            $sheet->setCellValue('J' . $j, $row->fe_end? '✔️' : '');
            $sheet->setCellValue('K' . $j, $row->non_fe_front? '✔️' : '');
            $sheet->setCellValue('L' . $j, $row->non_fe_mid? '✔️' : '');
            $sheet->setCellValue('M' . $j, $row->non_fe_end? '✔️' : '');
            $sheet->setCellValue('N' . $j, $row->ss_front? '✔️' : '');
            $sheet->setCellValue('O' . $j, $row->ss_mid? '✔️' : '');
            $sheet->setCellValue('P' . $j, $row->ss_end? '✔️' : '');

            $sheet->setCellValue('Q' . $j, $row->good_records_count);
            $sheet->setCellValue('R' . $j, $row->ng_records_count);
            $sheet->setCellValue('S' . $j, $row->operator?->name);
            $sheet->setCellValue('U' . $j, $row->foreman?->name);
            $sheet->setCellValue('W' . $j, $row->remark);

            $j++;
        }

        $sheet->getStyle('B15:W'.$j)->applyFromArray($styleArray);

        $path = BASE_PATH . '/runtime/' . $fileName;
        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        $content = file_get_contents($path);

        return $response->withHeader('content-description', 'File Transfer')
            ->withHeader('content-type', 'application/vnd.ms-excel')
            ->withHeader('content-disposition', "attachment; filename={$fileName}")
            ->withHeader('content-transfer-encoding', 'binary')
            ->withHeader('pragma', 'public')
            ->withBody(new SwooleStream($content));
    }
}
