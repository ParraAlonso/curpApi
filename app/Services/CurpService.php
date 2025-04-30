<?php

namespace App\Services;

use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\RuntimeException;
use App\Models\Curp;

class CurpService
{
    public function consultar($curp)
    {
        $consulta = Curp::whereCurp( trim($curp) )->first();

        if($consulta){

            return $consulta;
        }

        $scriptPath = resource_path('js/consulta-curp.cjs');

        $process = new Process(['node', $scriptPath, $curp]);

        $process->run();

        if (!$process->isSuccessful()) {

            throw new RuntimeException($process->getErrorOutput());

        }

        $output = $process->getOutput();

        $lineas = explode("\n", $output);

        $datos = [];

        $empezar = false;

        foreach ($lineas as $linea) {

            $linea = trim($linea);

            if (str_starts_with($linea, 'CURP:')) {

                $empezar = true;

            }

            if ($empezar) {

                if (strpos($linea, ':') !== false) {

                    [$campo, $valor] = explode(':', $linea, 2);

                    $datos[Str::slug($campo,'')] = trim($valor);

                }

                if (str_starts_with($linea, 'Municipio de registro')) {

                    break;

                }

            }

        }

        return Curp::updateOrCreate(
            ['curp' => $curp],
            [
                'curp' => $curp,
                'datos' => json_encode($datos)
            ]
        );
    }
}
