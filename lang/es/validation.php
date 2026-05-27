<?php

return [
    'required'             => 'El campo :attribute es obligatorio.',
    'string'               => 'El campo :attribute debe ser una cadena de texto.',
    'min'                  => ['string' => 'El campo :attribute debe tener al menos :min caracteres.'],
    'max'                  => ['string' => 'El campo :attribute no debe superar :max caracteres.'],
    'email'                => 'El campo :attribute debe ser una dirección de correo válida.',
    'unique'               => 'El campo :attribute ya está en uso.',
    'confirmed'            => 'La confirmación de :attribute no coincide.',
    'date'                 => 'El campo :attribute debe ser una fecha válida.',
    'after_or_equal'       => 'El campo :attribute debe ser posterior o igual a :date.',
    'integer'              => 'El campo :attribute debe ser un número entero.',
    'in'                   => 'El valor seleccionado para :attribute no es válido.',
    'numeric'              => 'El campo :attribute debe ser un número.',
    'nullable'             => '',

    'attributes' => [
        'username'     => 'usuario',
        'password'     => 'contraseña',
        'name_evento'  => 'nombre del evento',
        'fecha_inicio' => 'fecha de inicio',
        'fecha_fin'    => 'fecha de fin',
        'ubicacion'    => 'ubicación',
        'capacidad'    => 'capacidad',
        'estado'       => 'estado',
        'tipo_puntos'  => 'tipo de puntos',
        'Nombre'       => 'nombre',
        'RFC'          => 'RFC',
        'Telefono'     => 'teléfono',
    ],
];
