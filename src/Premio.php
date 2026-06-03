<?php

namespace Susuerte;

class Premio
{
    /**
     * Calcula el total de premios de una estructura con niveles anidados.
     */
    public static function calcularPremioAcumulado(array $niveles): float
    {
        return self::sumarNiveles($niveles, 0);
    }

    /**
     * Suma el nivel actual, sus hijos y luego continúa con el siguiente nivel.
     * Se usa recursividad para cumplir con el requisito del ejercicio.
     */
    private static function sumarNiveles(array $niveles, int $indice): float
    {
        if ($indice >= count($niveles)) {
            return 0;
        }

        $nivelActual = $niveles[$indice];

        $montoActual = isset($nivelActual['monto'])
            ? (float) $nivelActual['monto']
            : 0;

        $hijos = isset($nivelActual['hijos']) && is_array($nivelActual['hijos'])
            ? $nivelActual['hijos']
            : [];

        return $montoActual
            + self::sumarNiveles($hijos, 0)
            + self::sumarNiveles($niveles, $indice + 1);
    }
}