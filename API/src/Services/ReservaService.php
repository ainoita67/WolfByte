<?php
declare(strict_types=1);

namespace Services;

use Models\ReservaModel;
use Validation\ValidationException;

class ReservaService
{
    private ReservaModel $model;

    public function __construct()
    {
        $this->model = new ReservaModel();
    }

    /**
     * Reservas del usuario
     */
    public function getReservasUsuario(int $idUsuario): array
    {
        if ($idUsuario <= 0) {
            throw new ValidationException("Usuario no válido");
        }

        return $this->model->getByUsuario($idUsuario);
    }

    /**
     * Reserva por ID
     */
    public function getReservaById(int $id): array
    {
        $reserva = $this->model->findById($id);

        if (!$reserva) {
            throw new ValidationException("Reserva no encontrada");
        }

        return $reserva;
    }

// En ReservaService.php - Añade este método
public function verificarDisponibilidad(string $inicio, string $fin, ?int $idExcluir = null): bool
{
    // Convertir a objetos DateTime para comparación
    $nuevaInicio = new \DateTime($inicio);
    $nuevaFin = new \DateTime($fin);
    
    // Obtener todas las reservas del salón de actos
    $reservas = $this->model->getReservasSalonActos();
    
    foreach ($reservas as $reserva) {
        // Excluir la reserva actual si se está editando
        if ($idExcluir && $reserva['id_reserva'] == $idExcluir) {
            continue;
        }
        
        $existenteInicio = new \DateTime($reserva['inicio']);
        $existenteFin = new \DateTime($reserva['fin']);
        
        // Verificar solapamiento
        if ($nuevaInicio < $existenteFin && $nuevaFin > $existenteInicio) {
            return false; // Hay solapamiento
        }
    }
    
    return true; // No hay solapamiento
}

// Modificar updateFechasReserva para incluir validación
public function updateFechasReserva(
    int $idReserva,
    ?string $inicio,
    ?string $fin
): void {
    if ($idReserva <= 0) {
        throw new ValidationException("Reserva inválida");
    }

    if (!$inicio || !$fin) {
        throw new ValidationException("Fechas inválidas");
    }
    
    // Validar que la nueva fecha no se solape con otras reservas
    if (!$this->verificarDisponibilidad($inicio, $fin, $idReserva)) {
        throw new ValidationException("El horario seleccionado se solapa con otra reserva existente");
    }
    
    // Validar que la fecha de inicio sea anterior a la de fin
    if (strtotime($inicio) >= strtotime($fin)) {
        throw new ValidationException("La fecha de inicio debe ser anterior a la fecha de fin");
    }
    
    // Validar rango mínimo (ej: al menos 15 minutos)
    $diferencia = strtotime($fin) - strtotime($inicio);
    if ($diferencia < 900) { // 900 segundos = 15 minutos
        throw new ValidationException("La reserva debe tener al menos 15 minutos de duración");
    }

    $this->model->updateFechas($idReserva, $inicio, $fin);
}

}
