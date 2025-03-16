<?php

namespace App\Exports;

use App\Services\CommonService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CrawlerExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return CommonService::getModel('CrawlMovieLog')->getList();
    }

    public function headings(): array
    {
        return ["Date", "Total Movies", "Success", "Failed", "Success Rate (%)"];
    }
}
