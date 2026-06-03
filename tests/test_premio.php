<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Susuerte\Premio;

$niveles = [
    [
        'monto' => 1000,
        'hijos' => [
            [
                'monto' => 500,
                'hijos' => []
            ],
            [
                'monto' => 250,
                'hijos' => [
                    [
                        'monto' => 100,
                        'hijos' => []
                    ]
                ]
            ]
        ]
    ]
];

$resultado = Premio::calcularPremioAcumulado($niveles);

echo "Resultado: " . $resultado . PHP_EOL;