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

    public function getNumberOfCardsInDeck(int $deckId)
    {
        $sql = "SELECT COUNT(*) FROM carte WHERE id_deck = :deckId";
        $stmt = $this->query($sql, [':deckId' => $deckId]);
        if ($stmt) {
            return $stmt->fetchColumn();
        }
        return 0;
    }

    public function getIfCreatorHasCreatedCard(int $creatorId, int $cardId)
    {
        $sql = "SELECT * FROM carte WHERE id_createur = :creatorId AND id_carte = :cardId";
        $stmt = $this->query($sql, [':creatorId' => $creatorId, ':cardId' => $cardId]);
        if ($stmt) {
            return $stmt->fetch();
        }
        return null;
    }
}
