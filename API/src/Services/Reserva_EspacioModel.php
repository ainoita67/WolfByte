<?php
declare(strict_types=1);

namespace Services;

use Models\Reserva_EspacioModel;

class ReservaService
{
    private Reserva_EspacioModel $model;
    public function __construct(Reserva_EspacioModel $model)
    {
        $this->model = $model;
    }

    public function getReservas(): array
    {
        $rows = $this->model->getAll();
        $reservas = [];

        foreach ($rows as $row) {
            $id = $row['id_reserva'];

            if (!isset($reservas[$id])) {
                $reservas[$id] = [
                    'id' => $id,
                    'asignatura' => $row['asignatura'],
                    'profesor' => $row['profesor'],
                    'grupo' => $row['grupo'],
                    'inicio' => $row['inicio'],
                    'fin' => $row['fin'],
                    'actividad' => $row['actividad'],
                    'id_espacio' => $row['id_espacio'],
                    'necesidades' => []
                ];
            }

            if (!empty($row['necesidad'])) {
                $reservas[$id]['necesidades'][] = $row['necesidad'];
            }
        }

        return array_values($reservas);
    }
}
