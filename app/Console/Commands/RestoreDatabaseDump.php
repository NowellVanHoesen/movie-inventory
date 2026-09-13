<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RestoreDatabaseDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore-dump
        {path : Path to the SQL dump file (CREATE TABLE + INSERT statements)}
        {--database= : Name of the database connection to restore into}
        {--force : Skip the confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop every table in a database and rebuild it from a full SQL dump';

    public function handle(): int
    {
        $path = $this->argument('path');
        $database = $this->option('database');

        if (! $database) {
            $this->components->error('The --database option is required, so this can never run against the wrong database by accident.');

            return self::FAILURE;
        }

        if (! is_file($path) || ! is_readable($path)) {
            $this->components->error("Dump file not found or not readable: {$path}");

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm("This will DROP ALL TABLES in database \"{$database}\" and rebuild it from {$path}. Continue?")) {
            $this->components->warn('Aborted.');

            return self::SUCCESS;
        }

        $this->components->task("Ensuring database \"{$database}\" exists", function () use ($database) {
            config(['database.connections.mysql.database' => null]);
            DB::purge('mysql');
            DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$database}`");
        });

        config(['database.connections.mysql.database' => $database]);
        DB::purge('mysql');
        $connection = DB::connection('mysql');

        $this->components->task("Dropping existing tables in {$database}", function () use ($connection) {
            $connection->statement('SET FOREIGN_KEY_CHECKS=0');

            $tables = $connection->select('SHOW TABLES');
            $column = "Tables_in_{$connection->getDatabaseName()}";

            foreach ($tables as $table) {
                $connection->statement('DROP TABLE IF EXISTS `'.$table->$column.'`');
            }

            $connection->statement('SET FOREIGN_KEY_CHECKS=1');
        });

        $this->components->task('Importing dump', function () use ($connection, $path) {
            $connection->unprepared(file_get_contents($path));
        });

        $this->components->info("Database \"{$database}\" restored from {$path}.");

        return self::SUCCESS;
    }
}
