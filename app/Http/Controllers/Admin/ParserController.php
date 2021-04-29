<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParserSitemapsQueue;
use App\Models\ParserStatus;
use App\Services\ParserService;
use Illuminate\Http\Request;


class ParserController extends Controller {
    /*запускает парсер товаров по существующим категориям*/
    public function start(Request $request) {
        $categoryId = $request->get('category_id');
        $subcategoryId = $request->get('sub_category_id');
        $clear = $request->get('clear');

        $clearParam = (int) $clear;

        if ($clearParam) {
            ParserService::clear($categoryId, $subcategoryId);
        } else {
            ParserService::run();
        }

        return response()->json(['message' => 'Парсер запущен!']);
    }

    public function status() {
        $parseStatuses = ParserStatus::all();
        $progresses = [];

        $processListStatus = shell_exec('ps aux | grep [p]arse:catalog');

        $parserWasRunning = empty($processListStatus);
        if ($parseStatuses->isEmpty()) {
            if ($parserWasRunning) {
                return 'Завершено!';
            }

            return '';
        }

        foreach ($parseStatuses as $parseStatus) {
            /** @var ParserStatus $parseStatus */

            $prodStatus = "{$parseStatus->processed_products}/{$parseStatus->total_products}";

            $progresses[] = "Процесс №{$parseStatus->id}: артикул $parseStatus->vendor_code ($prodStatus товаров обработано)";
        }

        return $progresses;
    }

    public function stopParsing() {

        shell_exec('pkill -9 -f parse:catalog');
        ParserStatus::truncate();
        ParserSitemapsQueue::truncate();

        return session('status');
    }
}
