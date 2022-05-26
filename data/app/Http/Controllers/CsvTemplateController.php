<?php

namespace App\Http\Controllers;

use App\Models\CsvTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CsvTemplateController extends Controller
{
    public function save(Request $request): JsonResponse
    {
        $validations = [
            'layout_name' => ['required', 'string'],
            'data' => ['required', 'array'],
            'data.*' => ['string']
        ];

        $messages = [
            'select.required' => '選択してください',
            'select.array' => '不正な値が入ってしまったようです',
        ];

        $validator = Validator::make($request->all(), $validations, $messages);
        if (!$validator->passes()) {
            return response()->json($validator->errors()->all(), 422);
        }

        if (CsvTemplate::where('name', $request->get('layout_name'))->exists()) {
            return response()->json(['layout_name' => 'aaa'], 422);
        }

        CsvTemplate::create([
            'name' => $request->get('layout_name'),
            'columns' => implode(',', $request->get('data')),
            'created_by' => 'None'
        ]);

        return response()->json();
    }

    public function getNameList(): JsonResponse
    {
        $returnArray = [];
        $nameList = CsvTemplate::get(['name', 'id'])->toArray();

        if (is_array($nameList)) {
            $returnArray = $nameList;
        }

        return response()->json($returnArray);
    }

    public function getValues(string $key): JsonResponse
    {
        $returnString = '';

        $col = CsvTemplate::where('id', (int)$key);
        if ($col->exists()) {
            $templateValue = $col->get()->toArray();
            if (isset($templateValue[0]['columns'])) {
                $takeOne = $templateValue[0]['columns'];
                $returnString = $takeOne;
            }
        }

        return response()->json(['values' => explode(',', $returnString)]);
    }
}
