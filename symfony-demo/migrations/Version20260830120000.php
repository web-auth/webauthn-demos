<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use LogicException;
use function is_array;
use const JSON_THROW_ON_ERROR;

/**
 * The upgrade to web-auth/webauthn-lib 5.x brings the backup flags and the user verification indicator of the
 * Credential Record, and stores the transports and the additional data as JSON instead of serialized PHP arrays.
 */
final class Version20260830120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add the backup flags to the credential records and store their array fields as JSON';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE pk_credential_sources ADD backup_eligible BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE pk_credential_sources ADD backup_status BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE pk_credential_sources ADD uv_initialized BOOLEAN DEFAULT NULL');
    }

    /**
     * The values of the two array columns are serialized PHP arrays, which PostgreSQL cannot cast to JSON on its own.
     * They are rewritten row by row before the column type is changed.
     */
    public function postUp(Schema $schema): void
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT id, transports, other_ui FROM pk_credential_sources'
        );

        foreach ($rows as $row) {
            $this->connection->update(
                'pk_credential_sources',
                [
                    'transports' => json_encode(self::toArray($row['transports']), JSON_THROW_ON_ERROR),
                    'other_ui' => $row['other_ui'] === null ? null : json_encode(
                        self::toArray($row['other_ui']),
                        JSON_THROW_ON_ERROR
                    ),
                ],
                [
                    'id' => $row['id'],
                ]
            );
        }

        $this->connection->executeStatement(
            'ALTER TABLE pk_credential_sources ALTER transports TYPE JSON USING transports::JSON'
        );
        $this->connection->executeStatement(
            'ALTER TABLE pk_credential_sources ALTER other_ui TYPE JSON USING other_ui::JSON'
        );
        $this->connection->executeStatement('COMMENT ON COLUMN pk_credential_sources.transports IS NULL');
        $this->connection->executeStatement('COMMENT ON COLUMN pk_credential_sources.other_ui IS NULL');
    }

    public function down(Schema $schema): void
    {
        throw new LogicException('Wild cats never go back!');
    }

    /**
     * @return array<mixed>
     */
    private static function toArray(?string $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        $unserialized = unserialize($value, [
            'allowed_classes' => false,
        ]);

        return is_array($unserialized) ? $unserialized : [];
    }
}
