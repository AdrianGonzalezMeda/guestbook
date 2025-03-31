<?php

namespace App\Api\DTO;

use Symfony\Component\HttpFoundation\Response;

/*
    DTOs (Data Transfer Objects) son objetos que se usan para transportar datos entre capas de la 
    aplicación sin exponer directamente las entidades del dominio o modelos de base de datos.
*/

class ApiResponse
{
    public int $status_code;
    public array $data;

    public function __construct(int $status_code, array $data)
    {
        // Validar que el código de estado sea un código HTTP válido
        if (!in_array($status_code, array_keys(Response::$statusTexts))) {
            throw new \InvalidArgumentException("Invalid HTTP status code: $status_code");
        }

        $this->status_code = $status_code;
        $this->data = $data;
    }
}
