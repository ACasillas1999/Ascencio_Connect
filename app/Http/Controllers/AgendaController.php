<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Evento;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /**
     * Almacena un nuevo bloque de horario en la agenda del evento.
     */
    public function store(Request $request, Evento $evento)
    {
        $data = $request->validate([
            'Actividad' => 'required|string|max:255',
            'Fecha'     => 'required|date',
            'Horario'   => ['required', 'string', 'regex:/^\d{2}:\d{2}-\d{2}:\d{2}$/'], // formato HH:MM-HH:MM
            'Salon'     => 'nullable|string|max:100',
        ], [
            'Horario.regex' => 'El formato del horario debe ser HH:MM-HH:MM (ej. 09:00-10:00).'
        ]);

        $data['ID_Evento'] = $evento->ID;
        $data['Puntos_Asistencia'] = 0;

        if ($error = $this->validarEmpalme($data)) {
            return back()->withErrors(['Horario' => $error])->withInput();
        }

        Agenda::create($data);

        return redirect()->route('eventos.show', [$evento, 'active_tab' => $request->input('active_tab', 'tab-general')])->with('success', 'Horario agregado a la agenda.');
    }

    /**
     * Actualiza un bloque de horario existente en la agenda.
     */
    public function update(Request $request, Agenda $agenda)
    {
        $data = $request->validate([
            'Actividad' => 'required|string|max:255',
            'Fecha'     => 'required|date',
            'Horario'   => ['required', 'string', 'regex:/^\d{2}:\d{2}-\d{2}:\d{2}$/'],
            'Salon'     => 'nullable|string|max:100',
        ], [
            'Horario.regex' => 'El formato del horario debe ser HH:MM-HH:MM (ej. 09:00-10:00).'
        ]);

        $data['ID_Evento'] = $agenda->ID_Evento;
        
        if ($error = $this->validarEmpalme($data, $agenda->ID)) {
            return back()->withErrors(['Horario' => $error])->withInput();
        }

        $agenda->update($data);

        return redirect()->route('eventos.show', [$agenda->ID_Evento, 'active_tab' => $request->input('active_tab', 'tab-general')])->with('success', 'Horario actualizado en la agenda.');
    }

    /**
     * Elimina un bloque de horario de la agenda.
     */
    public function destroy(Agenda $agenda)
    {
        $evento_id = $agenda->ID_Evento;
        $agenda->delete();

        return redirect()->route('eventos.show', [$evento_id, 'active_tab' => request('active_tab', 'tab-general')])->with('success', 'Horario eliminado de la agenda.');
    }

    /**
     * Valida si el horario choca con otra actividad en el mismo salón y fecha.
     */
    private function validarEmpalme($data, $ignore_id = null)
    {
        preg_match('/^(\d{2}):(\d{2})-(\d{2}):(\d{2})$/', $data['Horario'], $m);
        $ini = (int)$m[1] * 60 + (int)$m[2];
        $fin = (int)$m[3] * 60 + (int)$m[4];
        
        if ($fin <= $ini) {
            return "El rango de horario es inválido (la hora de fin debe ser mayor a la de inicio).";
        }

        if (empty($data['Salon'])) {
            return null; // Si no hay salón asignado, no choca físicamente
        }

        $query = Agenda::where('ID_Evento', $data['ID_Evento'])
            ->where('Fecha', $data['Fecha'])
            ->where('Salon', $data['Salon'])
            ->where('Actividad', '!=', 'Vacio');

        if ($ignore_id) {
            $query->where('ID', '!=', $ignore_id);
        }

        $existentes = $query->get();

        foreach ($existentes as $ex) {
            if (preg_match('/^(\d{2}):(\d{2})-(\d{2}):(\d{2})$/', $ex->Horario, $mm)) {
                $a = (int)$mm[1] * 60 + (int)$mm[2];
                $b = (int)$mm[3] * 60 + (int)$mm[4];

                // Condición de empalme: inicioNuevo < finExistente Y inicioExistente < finNuevo
                if ($ini < $b && $a < $fin) {
                    return "El horario se empalma con otra actividad existente ({$ex->Horario}) en el salón {$data['Salon']}.";
                }
            }
        }

        return null;
    }
}
