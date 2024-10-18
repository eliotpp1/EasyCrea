<?php

declare(strict_types=1);

namespace App\Model;

class Admin extends Model
{
    use TraitInstance;

    protected $tableName = APP_TABLE_PREFIX . 'administrateur';


    public function getAdminEmail($id)
    {
        $sql = "SELECT ad_email_admin FROM administrateur WHERE id_administrateur = :id";
        $stmt = $this->query($sql, [':id' => $id]);
        if ($stmt) {
            return $stmt->fetchColumn();
        }
        return null;
    }
}
