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
        $this->db = Database::getConnection(); // Tu clase DB singleton
    }

    /**
     * Crea una reserva junto con la reserva de espacio
     */
    public function create(array $data): array
    {
        try {
            $this->db->beginTransaction();

            // Inserta en tabla Reserva
            $stmt = $this->db->prepare("
                INSERT INTO Reserva 
                (asignatura, autorizada, observaciones, grupo, profesor, f_creacion, inicio, fin, id_usuario, tipo) 
                VALUES (:asignatura, :autorizada, :observaciones, :grupo, :profesor, :f_creacion, :inicio, :fin, :id_usuario, 'Reserva_espacio')
            ");

            $stmt->execute([
                ':asignatura' => $data['asignatura'],
                ':autorizada' => $data['autorizada'] ?? false,
                ':observaciones' => $data['observaciones'] ?? null,
                ':grupo' => $data['grupo'],
                ':profesor' => $data['profesor'],
                ':f_creacion' => date('Y-m-d H:i:s'),
                ':inicio' => $data['inicio'],
                ':fin' => $data['fin'],
                ':id_usuario' => $data['id_usuario']
            ]);

            $idReserva = (int)$this->db->lastInsertId();

            // Inserta en tabla Reserva_espacio
            $stmtEsp = $this->db->prepare("
                INSERT INTO Reserva_espacio (id_reserva_espacio, actividad, id_espacio)
                VALUES (:id_reserva, :actividad, :id_espacio)
            ");
            $stmtEsp->execute([
                ':id_reserva' => $idReserva,
                ':actividad' => $data['actividad'] ?? null,
                ':id_espacio' => $data['id_espacio']
            ]);

            $this->db->commit();

            return [
                'id_reserva_espacio' => $idReserva, // Devuelve el ID de la reserva
                'actividad' => $data['actividad'],
                'id_espacio' => $data['id_espacio']
            ];

        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }


    /**
     * Obtiene todas las reservas de espacio
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT r.*, re.actividad, re.id_espacio
            FROM Reserva r
            JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva_espacio
            WHERE r.tipo = 'Reserva_espacio'
            ORDER BY r.inicio DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene reservas de un espacio específico
     */
    public function getByEspacio(string $idEspacio): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, re.actividad, re.id_espacio
            FROM Reserva r
            JOIN Reserva_espacio re ON r.id_reserva = re.id_reserva_espacio
            WHERE r.tipo = 'Reserva_espacio' AND re.id_espacio = :id_espacio
            ORDER BY r.inicio DESC
        ");
        $stmt->execute([':id_espacio' => $idEspacio]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
