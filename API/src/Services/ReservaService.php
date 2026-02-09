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



public function verificarDisponibilidad(string $inicio, string $fin, ?int $idExcluir = null): bool
{
    error_log("=== VERIFICAR DISPONIBILIDAD SALÓN ===");
    error_log("Inicio: {$inicio}");
    error_log("Fin: {$fin}");
    error_log("Excluir ID: " . ($idExcluir ?? 'ninguno'));
    
    try {
        // Parsear fechas
        $nuevoInicio = new \DateTime($inicio);
        $nuevoFin = new \DateTime($fin);
        
        // Obtener reservas del salón de actos
        $reservas = $this->getReservasSalonActos();
        error_log("Reservas encontradas en salón: " . count($reservas));
        
        foreach ($reservas as $i => $reserva) {
            // Excluir la reserva actual si se está editando
            if ($idExcluir && $reserva['id_reserva'] == $idExcluir) {
                error_log("Excluyendo reserva ID: {$idExcluir}");
                continue;
            }
            
            $existenteInicio = new \DateTime($reserva['inicio']);
            $existenteFin = new \DateTime($reserva['fin']);
            
            error_log("--- Comparando con reserva {$i} ---");
            error_log("ID: {$reserva['id_reserva']}");
            error_log("Existente: " . $existenteInicio->format('Y-m-d H:i:s') . " - " . $existenteFin->format('Y-m-d H:i:s'));
            error_log("Nueva: " . $nuevoInicio->format('Y-m-d H:i:s') . " - " . $nuevoFin->format('Y-m-d H:i:s'));
            
            // Verificar solapamiento
            $haySolapamiento = false;
            
            // Caso 1: Nueva empieza DENTRO de existente
            if ($nuevoInicio >= $existenteInicio && $nuevoInicio < $existenteFin) {
                $haySolapamiento = true;
                error_log("-> SOLAPA: Nueva empieza dentro de existente");
            }
            
            // Caso 2: Nueva termina DENTRO de existente
            if ($nuevoFin > $existenteInicio && $nuevoFin <= $existenteFin) {
                $haySolapamiento = true;
                error_log("-> SOLAPA: Nueva termina dentro de existente");
            }
            
            // Caso 3: Nueva CONTIENE completamente existente
            if ($nuevoInicio <= $existenteInicio && $nuevoFin >= $existenteFin) {
                $haySolapamiento = true;
                error_log("-> SOLAPA: Nueva contiene existente completamente");
            }
            
            // Caso 4: Nueva está COMPLETAMENTE DENTRO de existente
            if ($nuevoInicio >= $existenteInicio && $nuevoFin <= $existenteFin) {
                $haySolapamiento = true;
                error_log("-> SOLAPA: Nueva está completamente dentro de existente");
            }
            
            if ($haySolapamiento) {
                error_log("=== RESULTADO: HAY SOLAPAMIENTO con reserva ID: {$reserva['id_reserva']} ===");
                return false;
            }
            
            error_log("-> No hay solapamiento");
        }
        
        error_log("=== RESULTADO: NO HAY SOLAPAMIENTO ===");
        return true;
        
    } catch (\Exception $e) {
        error_log("ERROR en verificarDisponibilidad: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
        return false; // Por seguridad, si hay error no permitir
    }
}

public function updateFechasReserva(
    int $idReserva,
    ?string $inicio,
    ?string $fin
): bool {
    error_log("=== UPDATE FECHAS RESERVA ===");
    error_log("ID Reserva: {$idReserva}");
    error_log("Nuevo inicio: {$inicio}");
    error_log("Nuevo fin: {$fin}");
    
    if ($idReserva <= 0) {
        throw new ValidationException("Reserva inválida");
    }

    if (!$inicio || !$fin) {
        throw new ValidationException("Fechas inválidas");
    }
    
    // Validar formato de fechas
    if (!strtotime($inicio) || !strtotime($fin)) {
        throw new ValidationException("Formato de fecha inválido");
    }
    
    // Validar que inicio < fin
    if (strtotime($inicio) >= strtotime($fin)) {
        throw new ValidationException("La fecha de inicio debe ser anterior a la fecha de fin");
    }
    
    // Validar duración mínima (15 minutos)
    $duracion = strtotime($fin) - strtotime($inicio);
    if ($duracion < 900) {
        throw new ValidationException("La reserva debe tener al menos 15 minutos de duración");
    }
    
    //Verificar que la reserva sea del salón de actos
    error_log("Verificando si reserva es del salón...");
    $esSalon = $this->esReservaSalonActos($idReserva);
    error_log("¿Es del salón?: " . ($esSalon ? 'Sí' : 'No'));
    
    if ($esSalon) {
        // Solo verificar disponibilidad si es del salón
        error_log("Verificando disponibilidad en salón...");
        $disponible = $this->verificarDisponibilidad($inicio, $fin, $idReserva);
        error_log("Disponible: " . ($disponible ? 'Sí' : 'No'));
        
        if (!$disponible) {
            throw new ValidationException("El horario seleccionado se solapa con otra reserva del salón de actos");
        }
    }
    
    try {
        error_log("Actualizando fechas en base de datos...");
        $this->model->updateFechas($idReserva, $inicio, $fin);
        error_log("=== UPDATE COMPLETADO CON ÉXITO ===");
        return true;
    } catch (\Exception $e) {
        error_log("ERROR en updateFechas: " . $e->getMessage());
        return false;
    }
}


}
