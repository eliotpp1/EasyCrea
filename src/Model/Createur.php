<?php

declare(strict_types=1);

namespace App\Model;

class Createur extends Model
{
    use TraitInstance;

    protected $tableName = APP_TABLE_PREFIX . 'createur';

    public function findCreatorName(int $id)
    {
        $sql = "SELECT nom_createur FROM createur WHERE id_createur = :id";
        $stmt = $this->query($sql, [':id' => $id]);
        if ($stmt) {
            return $stmt->fetchColumn(); // Retourne directement le nom du créateur
        }
        return null;
    }
}
