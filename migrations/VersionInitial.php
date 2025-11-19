<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class VersionInitial extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration initiale appliquée manuellement pour synchroniser Doctrine';
    }

    public function up(Schema $schema): void
    {
        // Base déjà créée manuellement, rien à exécuter
    }

    public function down(Schema $schema): void
    {
        // Ne rien faire, migration déjà appliquée
    }
}
