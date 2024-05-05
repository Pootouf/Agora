<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version_ADMIN_USER extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add moderator and admin users';
    }

    public function up(Schema $schema): void
    {
        // Supprimer l'utilisateur s'il existe
        $this->connection->executeStatement("DELETE FROM user WHERE username IN ('moderator', 'admin')");

        $this->connection->insert('user', [
            'username' => 'moderator',
            'email' => 'moderator@univ-rouen.fr',
            'is_verified' => true,
            'roles' => json_encode(['ROLE_MODERATOR']),
            'password' => password_hash('moderagora', PASSWORD_DEFAULT),
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);

        $this->connection->insert('user', [
            'username' => 'admin',
            'email' => 'admin@univ-rouen.fr',
            'is_verified' => true,
            'roles' => json_encode(['ROLE_ADMIN']),
            'password' => password_hash('adminagora', PASSWORD_DEFAULT),
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    public function down(Schema $schema): void
    {
//        $this->connection->executeStatement("DELETE FROM user WHERE username IN ('moderator', 'admin')");
    }
}