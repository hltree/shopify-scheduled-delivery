<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CsvController extends Controller
{
    private const TARGET_LABEL = [
        'unfulfilled' => '未発送',
//        'partial' => '一部発送済',
        'fulfilled' => '発送済'
    ];
    private const INCLUDE_ARCHIVE_ORDER = [
        'false' => '含めない',
        'true' => '含める'
    ];

    private const ALLOW_READ_PROPERTIES = [

        /**
         * Order APIから取得できるデータ
         */
        'buyer_accepts_marketing' => [
            'return' => '1 か 0（空文字）',
            'description' => '顧客がショップからの更新メールの受信に同意したかどうか'
        ],
        'cancel_reason' => [
            'return' => 'customer, fraud, inventory, declined, other のいずれか',
            'description' => 'キャンセルされた理由'
        ],
        'cancelled_at' => [
            'return' => 'null（空文字。キャンセルされていない) か キャンセルされた日時',
            'description' => 'キャンセルされた日時'
        ],
        'close_at' => [
            'return' => 'null(空文字。クローズされていない) か 日時',
            'description' => '注文をクローズ（アーカイブ）した日時'
        ],
        'currency' => [
            'return' => 'ショップ通貨の3文字コード',
            'description' => 'ISO 4217で定められた通貨の3文字コード'
        ],
        'confirmed' => [
            'return' => '1 か 0（空文字）',
            'description' => '確定済（支払い完了）の注文かどうか'
        ],
        'contact_email' => [
            'return' => '注文時に入力したメールアドレス',
            'description' => '注文時に入力したメールアドレス'
        ],
        'current_subtotal_price' => [
            'return' => '税抜きの注文合計料金',
            'description' => '注文の合計料金（税抜き）'
        ],
        'current_total_discounts' => [
            'return' => '割引額の合計',
            'description' => '割引額の合計（注文合計料金から割引額を引いた値ではない）'
        ],
        'current_total_price' => [
            'return' => '税も含めた注文合計金額',
            'description' => '税も含めた注文合計金額'
        ],
        'current_total_tax' => [
            'return' => '税額',
            'description' => '税額'
        ],
        'order_number' => [
            'return' => '注文番号',
            'description' => '注文番号'
        ],
        'phone' => [
            'return' => 'null(空文字。電話番号なし) か 電話番号',
            'description' => '電話番号'
        ],
        'shipping_address-first_name' => [
            'return' => '注文者の名前',
            'description' => '注文者の名前'
        ],
        'shipping_address-address1' => [
            'return' => '注文者の住所１',
            'description' => '注文者の住所１'
        ],
        'shipping_address-address2' => [
            'return' => '注文者の住所２',
            'description' => '注文者の住所２'
        ],
        'shipping_address-city' => [
            'return' => '注文者の住所町名',
            'description' => '注文者の住所町名'
        ],
        'shipping_address-zip' => [
            'return' => '注文者の郵便番号',
            'description' => '注文者の郵便番号'
        ],
        'shipping_address-province' => [
            'return' => '注文者の県名',
            'description' => '注文者の県名'
        ],
        'shipping_address-country' => [
            'return' => '注文者の国名',
            'description' => '注文者の国名'
        ],
        'shipping_address-last_name' => [
            'return' => '注文者の苗字',
            'description' => '注文者の苗字'
        ],
        'shipping_address-company' => [
            'return' => '注文者の会社名など',
            'description' => '注文者の会社名など'
        ],
        'billing_address-first_name' => [
            'return' => '発送先の名前',
            'description' => '発送先の名前（ない場合はshipping_address-first_nameが入ります）'
        ],
        'billing_address-address1' => [
            'return' => '配送先の住所１',
            'description' => '配送先の住所１発送先の名前（ない場合はshipping_address-address1が入ります）'
        ],
        'billing_address-address2' => [
            'return' => '配送先の住所２',
            'description' => '配送先の住所２（ない場合はshipping_address-address2が入ります）'
        ],
        'billing_address-city' => [
            'return' => '配送先の住所町名',
            'description' => '配送先の住所町名（ない場合はshipping_address-cityが入ります）'
        ],
        'billing_address-zip' => [
            'return' => '配送先の郵便番号',
            'description' => '配送先の郵便番号（ない場合はshipping_address-zipが入ります）'
        ],
        'billing_address-province' => [
            'return' => '配送先の県名',
            'description' => '配送先の県名（ない場合はshipping_address-provinceが入ります）'
        ],
        'billing_address-country' => [
            'return' => '配送先の国名',
            'description' => '配送先の国名（ない場合はshipping_address-countryが入ります）'
        ],
        'billing_address-last_name' => [
            'return' => '配送先の苗字',
            'description' => '配送先の苗字（ない場合はshipping_address-last_nameが入ります）'
        ],
        'billing_address-company' => [
            'return' => '配送先の会社名など',
            'description' => '配送先の会社名など（ない場合はshipping_address-companyが入ります）'
        ],
        'shipping_lines-code' => [
            'return' => '注文時に選択したデリバリーコード',
            'description' => '注文時に選択したデリバリーコード'
        ],
        'shipping_lines-delivery_category' => [
            'return' => '注文時に選択したデリバリーのカテゴリー',
            'description' => '注文時に選択したデリバリーのカテゴリー'
        ],
        'shipping_lines-price' => [
            'return' => '注文時に選択したデリバリーの金額',
            'description' => '注文時に選択したデリバリーの金額'
        ],
        'shipping_lines-title' => [
            'return' => '注文時に選択したデリバリー名',
            'description' => '注文時に選択したデリバリー名'
        ],
        '配送希望日' => [
            'return' => '注文時に選択した配送希望日',
            'description' => 'このアプリのカレンダーに入力された配送希望日の値'
        ]
    ];

    private array $fullOrder = [];

    public function index()
    {
        $this->fullOrder = $this->ShopifySDK->Order->get([
            'status' => 'any'
        ]);

        return $this->View('csv.index', [
            'TARGET_LABEL' => self::TARGET_LABEL,
            'INCLUDE_ARCHIVE_ORDER' => self::INCLUDE_ARCHIVE_ORDER,
            'ALLOW_READ_PROPERTIES' => self::ALLOW_READ_PROPERTIES
        ]);
    }

    public function document()
    {
        return $this->View('csv.document', [
            'properties' => self::ALLOW_READ_PROPERTIES
        ]);
    }

    public function export(Request $request)
    {
        $Validator = $this->validator($request->all());
        if ($Validator->fails()) {
            return new JsonResponse([
                'errors' => $Validator->messages()->toArray()
            ], 422);
        }

        $orders = $this->getOrders($request->all());

        if (!is_array($orders) || 0 === count($orders)) throw ValidationException::withMessages(['order' => __('出力できる注文がありません')]);
        return $this->ExportCsv($orders, $request);
    }

    protected function validator(array $data): \Illuminate\Validation\Validator
    {
        $validations = [
            'select' => ['required', 'array'],
            'select.*' => ['required', 'string'],
            'target_label' => ['required', 'equal:' . implode('|', array_keys(self::TARGET_LABEL))],
            'include_archive_order' => ['required', 'equal:' . implode('|', array_keys(self::INCLUDE_ARCHIVE_ORDER))]
        ];

        $messages = [
            'select.required' => '選択してください',
            'select.array' => '不正な値が入ってしまったようです',
            'select.*.string' => '文字列にしてください',
            'target_label.required' => '選択してください',
            'target_label.equal' => '不正な値が入ってしまったようです',
            'include_archive_order.required' => '選択してください',
            'include_archive_order.equal' => '不正な値が入ってしまったようです'
        ];

        return Validator::make($data, $validations, $messages);
    }

    /**
     * 60日以上経過したオーダーはShopify管理者（＝Shopify開発チーム？）に許可申請を提出する必要あり
     * @return array
     */
    protected function getOrders(array $requestAll): array
    {
        // 全件取得してしまう
        $fullData = $this->ShopifySDK->Order->get([
            'status' => 'any'
        ]);

        foreach ($fullData as $keyIndex => $order) {
            if ('false' === $requestAll['include_archive_order']) {
                if (!is_null($order['closed_at'])) unset($fullData[$keyIndex]);
            }

            // unfulfilled は null になった？のでnullに変換する
            $status = $requestAll['target_label'];
            if ('unfulfilled' === $status) $status = null;

            if ($status !== $order['fulfillment_status']) unset($fullData[$keyIndex]);
        }

        return $fullData;
    }

    /**
     * @param array $orders
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \PHPShopify\Exception\ApiException
     * @throws \PHPShopify\Exception\CurlException
     */
    protected function ExportCsv(array $orders, Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $callback = function () use ($orders, $request) {
            $stream = fopen('php://output', 'w');

            $csvHeaders = $request->get('select');

            // csv header
            fputcsv($stream, $csvHeaders);
            foreach ($orders as $order) {
                $input = [];

                foreach ($csvHeaders as $header) {
                    $splitAssocAr = explode('-', $header);
                    $value = '';
                    array_map(function ($name) use ($order, &$value, &$splitAssocAr) {
                        if (!$value) {
                            // shipping_lineだけなぜか配列になっているので、特殊に処理を挟む
                            if ('shipping_lines' === $name) {
                                $value = $order[$name][0];
                            } else if ('配送希望日' === $name || 'note_attributes' === $name) {
                                $keep = $order['note_attributes'];
                                $value = '';
                                if ('配送希望日' === $name) $name = 'form-scheduled-delivery-key-name';
                                if ('note_attributes' === $name) $name = $splitAssocAr[array_key_last($splitAssocAr)];
                                foreach ($keep as $attribute) {
                                    if ($name === $attribute['name']) {
                                        $value = $attribute['value'];
                                        break;
                                    }
                                }
                                if ('form-scheduled-delivery-key-name' === $name) {
                                    $name = $value;
                                    $value = '';
                                    foreach ($keep as $attribute) {
                                        if ($name === $attribute['name']) {
                                            $value = $attribute['value'];
                                            break;
                                        }
                                    }
                                }
                            } else if (!isset($order[$name])) {
                                $value = __('このプロパティは存在しません');
                            } else {
                                $value = $order[$name];
                            }
                        } else {
                            $keep = $value;
                            if (is_string($value)) {
                            } else if (!isset($value[$name])) {
                                $value = __('このプロパティは存在しません');
                            } else {
                                $value = $value[$name];
                            }
                            if (is_array($keep) && 'billing_address' === key($keep)) {
                                if (isset($order['shipping_address'][$name])) $value = $order['shipping_address'][$name];
                            }
                        }
                    }, $splitAssocAr);

                    if (!is_string($value) && !is_int($value)) {
                        $value = '';
                    } else {
                        $value = strval($value);
                    }

                    $input[] = $value;
                }

                fputcsv($stream, $input);
            }
            fclose($stream);
        };

        $filename = sprintf('order-%s.csv', date('Ymd'));
        $header = [
            'Content-Type' => 'application/octet-stream',
        ];

        return response()->streamDownload($callback, $filename, $header);
    }
}
