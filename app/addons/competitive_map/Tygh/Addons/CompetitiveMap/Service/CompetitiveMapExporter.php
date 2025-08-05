<?php

namespace Tygh\Addons\CompetitiveMap\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tygh\Addons\VendorRating\ServiceProvider;

class CompetitiveMapExporter
{
    protected $products;
    protected $meta;

    public function __construct(array $products, array $meta)
    {
        $this->products = $products;
        $this->meta = $meta;
    }

    public function downloadXlsx(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Метаданные
        $sheet->setCellValue('A1', $this->meta['filename'] ?? '');
        $sheet->setCellValue('A2', 'URL: ' . $this->meta['referer']);
        $sheet->setCellValue('A3', 'Дата/время: ' . date('d.m.Y H:i:s'));
        $sheet->setCellValue('A4', 'Компания: ' . $this->meta['company_name']);

        // Заголовки
        $headers = ['Продавец', 'Наименование товара', 'Цена', 'Срок поставки', 'Рейтинг продавца'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '6', $header);
            $col++;
        }

        // Основная таблица
        $row = 7;
        $rating_service = ServiceProvider::getVendorService();

        foreach ($this->products as $product) {
            $sheet->setCellValue("A$row", $product['company_name'] ?? '—');
            $sheet->setCellValue("B$row", $product['product'] ?? '');
            $sheet->setCellValue("C$row", $product['price'] ?? '');
            $sheet->setCellValue("D$row", $product['stock_info'] ?? '—');
            $sheet->setCellValue("E$row", $rating_service->getRelativeRating($product['company_id']) ?? '—');
            $row++;
        }

        // Характеристики
        $row++;
        $sheet->setCellValue("A$row", 'Характеристики товаров');
        $row++;

        foreach ($this->products as $product) {
            $sheet->setCellValue("A$row", $product['product']);

            if (!empty($product['product_features'])) {
                foreach ($product['product_features'] as $feature) {
                    if (!isset($feature['variants'])) {
                        continue;
                    }

                    $sheet->setCellValue("B$row", $feature['description']);

                    $feature_variant = array_column($feature['variants'], 'variant');
                    $sheet->setCellValue("C$row", $feature_variant[0] ?? '');
                    $row++;
                }

            } else {
                $sheet->setCellValue("B$row", 'Нет характеристик');
                $row++;
            }
            $row++;
        }

        // Скачивание
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $this->meta['filename'] . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
