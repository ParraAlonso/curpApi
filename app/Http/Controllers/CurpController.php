<?php

namespace App\Http\Controllers;

use App\Services\CurpService;
use Illuminate\Http\Request;

class CurpController extends Controller
{
    protected $curpService;

    public function __construct()
    {
        $this->curpService = new CurpService();
    }

    public function consultarCurp(Request $request)
    {

        $request->validate([
            'curp' => [
                'required',
                'string',
                'size:18',
                'regex:/^([A-Z][AEIOUX][A-Z]{2}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM](AS|BC|BS|CC|CS|CH|CL|CM|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d]\d)$/'
            ]
        ]);

        try {

            $curp = strtoupper($request->input('curp'));

            $resultado = $this->curpService->consultar($curp);

            return response()->json([
                'success' => true,
                'data' => $resultado
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);

        }
    }
}
