<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250523215123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE lovemessage (id INT AUTO_INCREMENT NOT NULL, message LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lovemessage_user (lovemessage_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_3ACC89254140D99 (lovemessage_id), INDEX IDX_3ACC8925A76ED395 (user_id), PRIMARY KEY(lovemessage_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lovemessage_user ADD CONSTRAINT FK_3ACC89254140D99 FOREIGN KEY (lovemessage_id) REFERENCES lovemessage (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lovemessage_user ADD CONSTRAINT FK_3ACC8925A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE lovemessage_user DROP FOREIGN KEY FK_3ACC89254140D99
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lovemessage_user DROP FOREIGN KEY FK_3ACC8925A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lovemessage
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lovemessage_user
        SQL);
    }
}
