<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;

class ExportSupabaseSql extends Command
{
    protected $signature = 'supabase:export-sql {--output=database/supabase_ready_import.sql}';
    protected $description = 'Generates a 100% production-ready PostgreSQL SQL dump for Supabase SQL Editor';

    public function handle()
    {
        $outputFile = base_path($this->option('output'));
        $this->info("Menjana fail SQL Supabase ke: {$outputFile}...");

        $sql = [];
        $sql[] = "-- =========================================================================";
        $sql[] = "-- SISTEM PENGURUSAN STOR KERAJAAN (TPS AM 6.1 - AM 6.10)";
        $sql[] = "-- AKADEMI SAINS MALAYSIA (ASM) - SKRIP IMPORT SUPABASE (POSTGRESQL)";
        $sql[] = "-- Dijana secara automatik: " . date('Y-m-d H:i:s');
        $sql[] = "-- =========================================================================";
        $sql[] = "";
        $sql[] = "-- 1. Matikan sekatan integriti kunci asing sementara semasa import";
        $sql[] = "SET session_replication_role = replica;";
        $sql[] = "";

        // 1. Buat Schema DDL menggunakan PostgresGrammar
        $conn = new PostgresConnection(new PDO('sqlite::memory:'));
        $conn->useDefaultSchemaGrammar();
        $conn->useDefaultQueryGrammar();
        $conn->useDefaultPostProcessor();
        Schema::swap($conn->getSchemaBuilder());

        $sql[] = "-- =========================================================================";
        $sql[] = "-- 2. STRUKTUR JADUAL (DDL / TABLE DEFINITIONS)";
        $sql[] = "-- =========================================================================";

        // Migrations table DDL
        $sql[] = 'CREATE TABLE IF NOT EXISTS "migrations" (';
        $sql[] = '    "id" serial PRIMARY KEY,';
        $sql[] = '    "migration" varchar(255) NOT NULL,';
        $sql[] = '    "batch" integer NOT NULL';
        $sql[] = ');';
        $sql[] = '';

        $conn->pretend(function ($c) {
            $files = glob(database_path('migrations/*.php'));
            sort($files);
            foreach ($files as $file) {
                $migration = require $file;
                if (is_object($migration) && method_exists($migration, 'up')) {
                    $migration->up();
                }
            }
        });

        foreach ($conn->getQueryLog() as $q) {
            $query = trim($q['query']);
            if (!empty($query)) {
                $sql[] = $query . ';';
            }
        }

        // Restore default Schema builder
        Schema::swap(DB::connection('sqlite')->getSchemaBuilder());

        $sql[] = "";
        $sql[] = "-- =========================================================================";
        $sql[] = "-- 3. DATA REKOD (INSERTS: PENGGUNA, PERANAN, STOR, 981 ITEM STOK SEBENAR)";
        $sql[] = "-- =========================================================================";
        $sql[] = "";

        // Tables to dump data
        $tables = [
            'migrations',
            'roles',
            'users',
            'stores',
            'store_sections',
            'locations',
            'stock_categories',
            'stock_units',
            'stock_items',
            'stock_transactions',
            'stock_transaction_items',
            'system_settings',
            'safety_inspections',
        ];

        foreach ($tables as $table) {
            $rows = DB::connection('sqlite')->table($table)->get();
            if ($rows->isEmpty()) {
                continue;
            }

            $sql[] = "-- Data untuk jadual: {$table} (" . count($rows) . " rekod)";
            
            // Chunk inserts by 100 rows
            foreach ($rows->chunk(100) as $chunk) {
                $first = $chunk->first();
                $columns = array_keys((array)$first);
                $escapedCols = array_map(fn($c) => "\"{$c}\"", $columns);

                $valuesList = [];
                foreach ($chunk as $row) {
                    $rowVals = [];
                    foreach ($columns as $col) {
                        $val = $row->$col;
                        if (is_null($val)) {
                            $rowVals[] = 'NULL';
                        } elseif (is_bool($val)) {
                            $rowVals[] = $val ? 'TRUE' : 'FALSE';
                        } elseif (is_int($val) || is_float($val)) {
                            $rowVals[] = $val;
                        } else {
                            // Escape single quotes for PostgreSQL
                            $escaped = str_replace("'", "''", (string)$val);
                            $rowVals[] = "'{$escaped}'";
                        }
                    }
                    $valuesList[] = "(" . implode(", ", $rowVals) . ")";
                }

                $sql[] = "INSERT INTO \"{$table}\" (" . implode(", ", $escapedCols) . ") VALUES \n" . implode(",\n", $valuesList) . "\nON CONFLICT DO NOTHING;";
            }
            $sql[] = "";
        }

        $sql[] = "-- =========================================================================";
        $sql[] = "-- 4. RESET SEQUENCE GENERATOR POSTGRESQL (AUTO-INCREMENT COUNTERS)";
        $sql[] = "-- =========================================================================";

        foreach ($tables as $table) {
            $hasId = DB::connection('sqlite')->getSchemaBuilder()->hasColumn($table, 'id');
            if ($hasId) {
                $sql[] = "SELECT setval(pg_get_serial_sequence('\"{$table}\"', 'id'), COALESCE((SELECT MAX(id) FROM \"{$table}\"), 1));";
            }
        }

        $sql[] = "";
        $sql[] = "-- 5. Aktifkan semula sekatan integriti kunci asing";
        $sql[] = "SET session_replication_role = DEFAULT;";
        $sql[] = "";
        $sql[] = "-- SELESAI: Pangkalan data Supabase kini mempunyai struktur lengkap dan 981 item stok ASM.";

        file_put_contents($outputFile, implode("\n", $sql));
        $this->info("Berjaya! Fail SQL telah dicipta: " . number_format(filesize($outputFile) / 1024, 2) . " KB");

        return 0;
    }
}
