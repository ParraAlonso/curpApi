<?php

namespace App\Http\Controllers;

use App\Services\CurpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Curp;

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
                'data' => json_decode(json_decode($resultado->datos))
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Descarga PDF del CURP
     */
    function downloadCurp(Request $request) {
        $curp = trim($request->input('curp'));
        $data = Curp::whereCurp($curp)->firstOrFail();

        $datos = json_decode(json_decode($data->datos));
        if(property_exists($datos->registros[0],'parametro') && $datos->registros[0]->parametro != null){
            $url = 'https://consultas.curp.gob.mx/CurpSP/pdfgobmx' . $datos->registros[0]->parametro;
            $base64 = file_get_contents($url);
            $response = Response::make(base64_decode($base64), 200);
            $response->header('Content-Type', 'application/pdf');
            $response->header('Content-Disposition', 'attachment; filename="'.$curp.'.pdf"');
            return $response;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'El registro no tiene los datos necesarios para su descarga.'
            ], 400);

        }
        
    }
}
