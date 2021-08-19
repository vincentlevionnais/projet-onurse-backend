<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210819084137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment ADD patient_id INT DEFAULT NULL, ADD nurse_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8446B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8447373BFAA FOREIGN KEY (nurse_id) REFERENCES nurse (id)');
        $this->addSql('CREATE INDEX IDX_FE38F8446B899279 ON appointment (patient_id)');
        $this->addSql('CREATE INDEX IDX_FE38F8447373BFAA ON appointment (nurse_id)');
        $this->addSql('ALTER TABLE nurse CHANGE phone phone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE patient ADD nurse_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB7373BFAA FOREIGN KEY (nurse_id) REFERENCES nurse (id)');
        $this->addSql('CREATE INDEX IDX_1ADAD7EB7373BFAA ON patient (nurse_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F8446B899279');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F8447373BFAA');
        $this->addSql('DROP INDEX IDX_FE38F8446B899279 ON appointment');
        $this->addSql('DROP INDEX IDX_FE38F8447373BFAA ON appointment');
        $this->addSql('ALTER TABLE appointment DROP patient_id, DROP nurse_id');
        $this->addSql('ALTER TABLE nurse CHANGE phone phone INT NOT NULL');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EB7373BFAA');
        $this->addSql('DROP INDEX IDX_1ADAD7EB7373BFAA ON patient');
        $this->addSql('ALTER TABLE patient DROP nurse_id');
    }
}
