<?php

declare(strict_types=1);

namespace App\Model;

class Carte extends Model
{
    use TraitInstance;

    protected $tableName = APP_TABLE_PREFIX . 'carte';

    public function getRandomCard(int $deckId)
    {
        $sql = "SELECT * FROM carte WHERE id_deck = :deckId ORDER BY RAND() LIMIT 1"; // Récupère une carte aléatoire
        $stmt = $this->query($sql, [':deckId' => $deckId]);
        if ($stmt) {
            return $stmt->fetch();
        }
        return null;
    }

    public function getCardById(int $id)
    {
        $sql = "SELECT * FROM carte WHERE id_carte = :id";
        $stmt = $this->query($sql, [':id' => $id]);
        if ($stmt) {
            return $stmt->fetch();
        }
        return null;
    }
}
