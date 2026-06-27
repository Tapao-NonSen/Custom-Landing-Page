<?php

use Illuminate\Database\Schema\Builder;

/*
 * Flarum stores setting values in the `settings.value` column, which is a
 * MySQL TEXT field (max 65,535 bytes). A full HTML landing page easily exceeds
 * that, and MySQL silently truncates the value on save — cutting off the end of
 * the document (including any closing <script>). Widen the column to MEDIUMTEXT
 * (max ~16 MB) so large landing pages are stored intact.
 */
return [
    'up' => function (Builder $schema) {
        $connection = $schema->getConnection();
        $table = $connection->getTablePrefix() . 'settings';
        $connection->statement("ALTER TABLE `{$table}` MODIFY `value` MEDIUMTEXT NULL");
    },

    'down' => function (Builder $schema) {
        // Intentionally left as a no-op: narrowing back to TEXT could truncate
        // any setting value that now exceeds 64 KB. The wider column is harmless.
    },
];
