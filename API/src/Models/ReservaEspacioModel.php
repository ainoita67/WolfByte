<?php
declare(strict_types=1);

namespace Models;

use Core\Database;
use PDO;
use PDOException;

class ReservaEspacioModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Obtiene todas las reservas de espacio
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT 
                r.*,
                re.*
            FROM Reserva r
            INNER JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
            WHERE r.tipo = 'Reserva_espacio'
            ORDER BY r.inicio DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene reservas de un espacio específico por ID de espacio
     */
    public function getByEspacio(string $idEspacio): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                r.*,
                re.*
            FROM Reserva r
            INNER JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
            WHERE r.tipo = 'Reserva_espacio' AND re.id_espacio = :id_espacio
            ORDER BY r.inicio DESC
        ");
        $stmt->execute([':id_espacio' => $idEspacio]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene reservas de un usuario específico
     */
    public function getByUsuario(int $idUsuario): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                r.*,
                re.*
            FROM Reserva r
            INNER JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
            WHERE r.tipo = 'Reserva_espacio' AND r.id_usuario = :id_usuario
            ORDER BY r.inicio DESC
        ");
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una reserva por su ID
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT 
                r.*,
                re.*
            FROM Reserva r
            INNER JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
            WHERE r.id_reserva = :id AND r.tipo = 'Reserva_espacio'
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea una nueva reserva de espacio
     */
    public function create(array $data): array
    {
        try {
            // $this->db->beginTransaction();
            
            // 1. Validar que el espacio existe
            $espacioStmt = $this->db->prepare("SELECT id_espacio FROM Espacio WHERE id_espacio = :id_espacio");
            $espacioStmt->execute([':id_espacio' => $data['id_espacio']]);
            if (!$espacioStmt->fetch()) {
                throw new \Exception("El espacio no existe");
            }
            
            // 2. Verificar disponibilidad
            if (!$this->checkDisponibilidad($data['id_espacio'], $data['inicio'], $data['fin'])) {
                throw new \Exception("El espacio no está disponible en el horario seleccionado");
            }
            
            // 3. Insertar en tabla Reserva (PRIMERO)
            $stmtReserva = $this->db->prepare("
                INSERT INTO Reserva (
                    asignatura,
                    autorizada,
                    observaciones,
                    grupo,
                    profesor,
                    f_creacion,
                    inicio,
                    fin,
                    id_usuario,
                    tipo
                ) VALUES (
                    :asignatura,
                    :autorizada,
                    :observaciones,
                    :grupo,
                    :profesor,
                    now(),
                    :inicio,
                    :fin,
                    :id_usuario,
                    'Reserva_espacio'
                )
            ");
            
            $stmtReserva->execute([
                ':asignatura' => $data['asignatura'],
                ':autorizada' => $data['autorizada'] ?? 0,
                ':observaciones' => $data['observaciones'] ?? null,
                ':grupo' => $data['grupo'],
                ':profesor' => $data['profesor'],
                ':inicio' => $data['inicio'],
                ':fin' => $data['fin'],
                ':id_usuario' => $data['id_usuario']
            ]);
            
            // Obtener el ID generado
            $idReserva = (int)$this->db->lastInsertId();
            
            // 4. Insertar en tabla Reserva_espacio (SEGUNDO)
            $stmtEspacio = $this->db->prepare("
                INSERT INTO Reserva_espacio (
                    id_reserva,
                    actividad,
                    id_espacio
                ) VALUES (
                    :id_reserva,
                    :actividad,
                    :id_espacio
                )
            ");
            
            $stmtEspacio->execute([
                ':id_reserva' => $idReserva,
                ':actividad' => $data['actividad'] ?? null,
                ':id_espacio' => $data['id_espacio']
            ]);
            
            // $this->db->commit();
            
            return $this->findById($idReserva);
            return $data;
            
        } catch (\Exception $e) {
            // if ($this->db->inTransaction()) {
            //     $this->db->rollBack();
            // }
            throw new \Exception("Error al crear la reserva: " . $e->getMessage());
        }
    }
    
    /**
     * Actualiza una reserva existente
     */
    public function update(int $id, array $data): array
    {
        try {
            // $this->db->beginTransaction();

            // Verificar disponibilidad excluyendo la reserva actual
            if (!$this->checkDisponibilidad($data['id_espacio'], $data['inicio'], $data['fin'], $id)) {
                throw new \Exception("El espacio no está disponible en el horario seleccionado");
            }

            // Actualizar tabla Reserva
            $stmt = $this->db->prepare("
                UPDATE Reserva SET
                    asignatura = :asignatura,
                    autorizada = :autorizada,
                    observaciones = :observaciones,
                    grupo = :grupo,
                    profesor = :profesor,
                    inicio = :inicio,
                    fin = :fin,
                    id_usuario = :id_usuario
                WHERE id_reserva = :id
            ");

            $stmt->execute([
                ':id' => $id,
                ':asignatura' => $data['asignatura'],
                ':autorizada' => $data['autorizada'] ?? 0,
                ':observaciones' => $data['observaciones'] ?? null,
                ':grupo' => $data['grupo'],
                ':profesor' => $data['profesor'],
                ':inicio' => $data['inicio'],
                ':fin' => $data['fin'],
                ':id_usuario' => $data['id_usuario']
            ]);

            // Actualizar tabla Reserva_espacio - USANDO id_reserva
            $stmtEsp = $this->db->prepare("
                UPDATE Reserva_espacio SET
                    actividad = :actividad,
                    id_espacio = :id_espacio
                WHERE id_reserva = :id
            ");
            $stmtEsp->execute([
                ':id' => $id,
                ':actividad' => $data['actividad'] ?? null,
                ':id_espacio' => $data['id_espacio']
            ]);

            // $this->db->commit();

            return $this->findById($id);

        } catch (\Exception $e) {
            // $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza solo las fechas de una reserva
     */
    public function cambiarFechas(int $id, array $data): array
    {
        try {
            // $this->db->beginTransaction();

            // Obtener la reserva actual para el espacio
            $reservaActual = $this->findById($id);
            if (!$reservaActual) {
                throw new \Exception("Reserva no encontrada");
            }

            // Verificar disponibilidad excluyendo la reserva actual
            if (!$this->checkDisponibilidad($reservaActual['id_espacio'], $data['inicio'], $data['fin'], $id)) {
                throw new \Exception("El espacio no está disponible en el horario seleccionado");
            }

            // Actualizar solo las fechas en la tabla Reserva
            $stmt = $this->db->prepare("
                UPDATE Reserva SET
                    inicio = :inicio,
                    fin = :fin
                WHERE id_reserva = :id
            ");

            $stmt->execute([
                ':id' => $id,
                ':inicio' => $data['inicio'],
                ':fin' => $data['fin']
            ]);

            // $this->db->commit();

            return $this->findById($id);

        } catch (\Exception $e) {
            // $this->db->rollBack();
            // throw $e;
        }
    }

    /**
     * Elimina una reserva
     */
    public function delete(int $id): bool
    {
        try {
            // Eliminar de Reserva_espacio primero
            $stmtEsp = $this->db->prepare("
                DELETE FROM Reserva_espacio 
                WHERE id_reserva = :id
            ");
            $stmtEsp->execute([':id' => $id]);

            // Eliminar de Reserva
            $stmtRes = $this->db->prepare("
                DELETE FROM Reserva 
                WHERE id_reserva = :id
            ");
            $stmtRes->execute([':id' => $id]);
            
            return true;  // ← RETORNAR true explícitamente

        } catch (\Exception $e) {
            // No hay rollback porque no hay transacción
            throw $e;
        }
    }

    /**
     * Verifica la disponibilidad de un espacio
     */
    private function checkDisponibilidad(string $idEspacio, string $inicio, string $fin, ?int $excludeReservaId = null): bool
    {
        $sql = "
            SELECT COUNT(*) as count
            FROM Reserva r
            INNER JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva
            WHERE r.tipo = 'Reserva_espacio'
            AND r.autorizada = 1
            AND re.id_espacio = :id_espacio
            AND (
                -- Fórmula correcta para detectar solapamiento
                (r.inicio < :fin AND r.fin > :inicio)
            )
        ";
        
        $params = [
            ':id_espacio' => $idEspacio,
            ':inicio' => $inicio,
            ':fin' => $fin
        ];
        
        if ($excludeReservaId) {
            $sql .= " AND r.id_reserva != :exclude_id";
            $params[':exclude_id'] = $excludeReservaId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] == 0;
    }
}