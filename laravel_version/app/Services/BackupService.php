<?php

namespace App\Services;

use App\Models\SiteSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use ZipArchive;

class BackupService
{
    const BACKUP_DISK = 'local';
    const BACKUP_DIRECTORY = 'backups';
    const MAX_BACKUPS = 30; // Keep last 30 backups

    /**
     * Create a full system backup
     */
    public function createBackup(array $options = []): array
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupName = $options['name'] ?? "backup_{$timestamp}";
            $backupPath = storage_path('app/' . self::BACKUP_DIRECTORY);
            
            // Ensure backup directory exists
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $backupFile = "{$backupPath}/{$backupName}.zip";
            
            Log::info('Starting system backup', ['backup_file' => $backupFile]);

            $zip = new ZipArchive();
            if ($zip->open($backupFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('Cannot create backup zip file');
            }

            $backup = [
                'filename' => "{$backupName}.zip",
                'created_at' => now(),
                'type' => $options['type'] ?? 'full',
                'size' => 0,
                'components' => []
            ];

            // Backup database
            if (!isset($options['skip_database']) || !$options['skip_database']) {
                $this->backupDatabase($zip, $backup);
            }

            // Backup application files (excluding vendor and node_modules)
            if (!isset($options['skip_files']) || !$options['skip_files']) {
                $this->backupApplicationFiles($zip, $backup);
            }

            // Backup storage files
            if (!isset($options['skip_storage']) || !$options['skip_storage']) {
                $this->backupStorageFiles($zip, $backup);
            }

            // Backup configuration
            if (!isset($options['skip_config']) || !$options['skip_config']) {
                $this->backupConfiguration($zip, $backup);
            }

            // Add backup metadata
            $metadata = [
                'backup_info' => $backup,
                'system_info' => [
                    'php_version' => phpversion(),
                    'laravel_version' => app()->version(),
                    'environment' => app()->environment(),
                    'created_by' => auth()->guard('admin')->id() ?? 'system',
                    'server_name' => gethostname(),
                    'backup_version' => '1.0'
                ]
            ];
            
            $zip->addFromString('backup_metadata.json', json_encode($metadata, JSON_PRETTY_PRINT));

            $zip->close();

            // Get final file size
            $backup['size'] = File::size($backupFile);
            $backup['size_formatted'] = $this->formatBytes($backup['size']);

            // Clean old backups
            $this->cleanOldBackups();

            // Update last backup date
            SiteSettings::updateSetting('last_backup_date', now());

            Log::info('System backup completed successfully', $backup);

            return [
                'success' => true,
                'backup' => $backup,
                'message' => 'Backup created successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Backup creation failed: ' . $e->getMessage());
            
            // Clean up partial backup file
            if (isset($backupFile) && File::exists($backupFile)) {
                File::delete($backupFile);
            }

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Backup creation failed'
            ];
        }
    }

    /**
     * Restore system from backup
     */
    public function restoreBackup(string $backupFile, array $options = []): array
    {
        try {
            $backupPath = storage_path('app/' . self::BACKUP_DIRECTORY . '/' . $backupFile);
            
            if (!File::exists($backupPath)) {
                throw new \Exception('Backup file not found');
            }

            Log::info('Starting system restore', ['backup_file' => $backupFile]);

            $zip = new ZipArchive();
            if ($zip->open($backupPath) !== TRUE) {
                throw new \Exception('Cannot open backup file');
            }

            // Extract to temporary directory
            $tempDir = storage_path('app/temp/restore_' . time());
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            $zip->extractTo($tempDir);
            $zip->close();

            // Read backup metadata
            $metadataFile = $tempDir . '/backup_metadata.json';
            if (File::exists($metadataFile)) {
                $metadata = json_decode(File::get($metadataFile), true);
                Log::info('Backup metadata loaded', ['metadata' => $metadata]);
            }

            $restored = [
                'components' => [],
                'restored_at' => now()
            ];

            // Restore database
            if (!isset($options['skip_database']) || !$options['skip_database']) {
                $this->restoreDatabase($tempDir, $restored);
            }

            // Restore application files
            if (!isset($options['skip_files']) || !$options['skip_files']) {
                $this->restoreApplicationFiles($tempDir, $restored);
            }

            // Restore storage files
            if (!isset($options['skip_storage']) || !$options['skip_storage']) {
                $this->restoreStorageFiles($tempDir, $restored);
            }

            // Restore configuration
            if (!isset($options['skip_config']) || !$options['skip_config']) {
                $this->restoreConfiguration($tempDir, $restored);
            }

            // Clean up temporary directory
            File::deleteDirectory($tempDir);

            Log::info('System restore completed successfully', $restored);

            return [
                'success' => true,
                'restored' => $restored,
                'message' => 'System restored successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Restore operation failed: ' . $e->getMessage());

            // Clean up temporary directory
            if (isset($tempDir) && File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Restore operation failed'
            ];
        }
    }

    /**
     * Get list of available backups
     */
    public function getBackupsList(): array
    {
        try {
            $backupPath = storage_path('app/' . self::BACKUP_DIRECTORY);
            
            if (!File::exists($backupPath)) {
                return [];
            }

            $backups = [];
            $files = File::files($backupPath);

            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                    $filename = $file->getFilename();
                    $metadata = $this->getBackupMetadata($filename);
                    
                    $backups[] = [
                        'filename' => $filename,
                        'size' => $file->getSize(),
                        'size_formatted' => $this->formatBytes($file->getSize()),
                        'created_at' => Carbon::createFromTimestamp($file->getMTime()),
                        'metadata' => $metadata
                    ];
                }
            }

            // Sort by creation date (newest first)
            usort($backups, function ($a, $b) {
                return $b['created_at']->timestamp - $a['created_at']->timestamp;
            });

            return $backups;

        } catch (\Exception $e) {
            Log::error('Failed to get backups list: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete a backup file
     */
    public function deleteBackup(string $filename): bool
    {
        try {
            $backupPath = storage_path('app/' . self::BACKUP_DIRECTORY . '/' . $filename);
            
            if (!File::exists($backupPath)) {
                throw new \Exception('Backup file not found');
            }

            File::delete($backupPath);

            Log::info('Backup deleted', ['filename' => $filename]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to delete backup: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get backup file for download
     */
    public function downloadBackup(string $filename)
    {
        $backupPath = storage_path('app/' . self::BACKUP_DIRECTORY . '/' . $filename);
        
        if (!File::exists($backupPath)) {
            abort(404, 'Backup file not found');
        }

        return response()->download($backupPath);
    }

    /**
     * Schedule automatic backup
     */
    public function scheduleBackup(string $frequency = 'daily'): bool
    {
        try {
            SiteSettings::updateSetting('auto_backup_enabled', true);
            SiteSettings::updateSetting('backup_frequency', $frequency);

            Log::info('Automatic backup scheduled', ['frequency' => $frequency]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to schedule backup: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Backup database
     */
    private function backupDatabase(ZipArchive $zip, array &$backup): void
    {
        try {
            $dbPath = database_path('database.sqlite');
            
            if (File::exists($dbPath)) {
                $zip->addFile($dbPath, 'database/database.sqlite');
                $backup['components'][] = 'database';
                Log::info('Database backed up successfully');
            } else {
                Log::warning('Database file not found for backup');
            }

            // Export database as SQL dump for additional safety
            $sqlDump = $this->createSqlDump();
            if ($sqlDump) {
                $zip->addFromString('database/dump.sql', $sqlDump);
                Log::info('SQL dump created successfully');
            }

        } catch (\Exception $e) {
            Log::error('Database backup failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Backup application files
     */
    private function backupApplicationFiles(ZipArchive $zip, array &$backup): void
    {
        try {
            $appPath = base_path();
            $excludePaths = [
                'vendor',
                'node_modules',
                'storage/app/backups',
                'storage/logs',
                'storage/framework/cache',
                'storage/framework/sessions',
                'storage/framework/views',
                '.git',
                '.env'
            ];

            $this->addDirectoryToZip($zip, $appPath, 'application/', $excludePaths);
            $backup['components'][] = 'application_files';
            Log::info('Application files backed up successfully');

        } catch (\Exception $e) {
            Log::error('Application files backup failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Backup storage files
     */
    private function backupStorageFiles(ZipArchive $zip, array &$backup): void
    {
        try {
            $storagePath = storage_path('app');
            $excludePaths = ['backups', 'temp'];

            $this->addDirectoryToZip($zip, $storagePath, 'storage/', $excludePaths);
            $backup['components'][] = 'storage_files';
            Log::info('Storage files backed up successfully');

        } catch (\Exception $e) {
            Log::error('Storage files backup failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Backup configuration
     */
    private function backupConfiguration(ZipArchive $zip, array &$backup): void
    {
        try {
            // Backup .env file (sanitized)
            $envContent = $this->getSanitizedEnvContent();
            $zip->addFromString('config/env_backup.txt', $envContent);

            // Backup configuration files
            $configPath = config_path();
            $this->addDirectoryToZip($zip, $configPath, 'config/');

            $backup['components'][] = 'configuration';
            Log::info('Configuration backed up successfully');

        } catch (\Exception $e) {
            Log::error('Configuration backup failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Add directory to zip with exclusions
     */
    private function addDirectoryToZip(ZipArchive $zip, string $sourcePath, string $zipPath = '', array $excludePaths = []): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourcePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $relativePath = str_replace($sourcePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $relativePath = str_replace('\\', '/', $relativePath);

            // Check if path should be excluded
            $shouldExclude = false;
            foreach ($excludePaths as $excludePath) {
                if (strpos($relativePath, $excludePath) === 0) {
                    $shouldExclude = true;
                    break;
                }
            }

            if ($shouldExclude) {
                continue;
            }

            $zipFilePath = $zipPath . $relativePath;

            if ($file->isDir()) {
                $zip->addEmptyDir($zipFilePath);
            } else {
                $zip->addFile($file->getPathname(), $zipFilePath);
            }
        }
    }

    /**
     * Create SQL dump of database
     */
    private function createSqlDump(): string
    {
        try {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $dump = "-- Database Dump Generated on " . now() . "\n\n";

            foreach ($tables as $table) {
                $tableName = $table->name;
                
                // Get table structure
                $createTable = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$tableName]);
                if (!empty($createTable)) {
                    $dump .= $createTable[0]->sql . ";\n\n";
                }

                // Get table data
                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        return is_null($value) ? 'NULL' : "'" . str_replace("'", "''", $value) . "'";
                    }, (array) $row);
                    
                    $dump .= "INSERT INTO {$tableName} VALUES (" . implode(', ', $values) . ");\n";
                }
                
                $dump .= "\n";
            }

            return $dump;

        } catch (\Exception $e) {
            Log::error('SQL dump creation failed: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Get sanitized .env content (remove sensitive data)
     */
    private function getSanitizedEnvContent(): string
    {
        $envPath = base_path('.env');
        
        if (!File::exists($envPath)) {
            return '';
        }

        $content = File::get($envPath);
        $lines = explode("\n", $content);
        $sanitized = [];

        $sensitiveKeys = [
            'DB_PASSWORD',
            'APP_KEY',
            'JWT_SECRET',
            'MAIL_PASSWORD',
            'AWS_SECRET_ACCESS_KEY',
            'STRIPE_SECRET',
            'PAYSTACK_SECRET_KEY'
        ];

        foreach ($lines as $line) {
            $isSensitive = false;
            foreach ($sensitiveKeys as $key) {
                if (strpos($line, $key . '=') === 0) {
                    $sanitized[] = $key . '=***REDACTED***';
                    $isSensitive = true;
                    break;
                }
            }
            
            if (!$isSensitive) {
                $sanitized[] = $line;
            }
        }

        return implode("\n", $sanitized);
    }

    /**
     * Clean old backups (keep only last MAX_BACKUPS)
     */
    private function cleanOldBackups(): void
    {
        try {
            $backups = $this->getBackupsList();
            
            if (count($backups) > self::MAX_BACKUPS) {
                $toDelete = array_slice($backups, self::MAX_BACKUPS);
                
                foreach ($toDelete as $backup) {
                    $this->deleteBackup($backup['filename']);
                }
                
                Log::info('Cleaned old backups', ['deleted_count' => count($toDelete)]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to clean old backups: ' . $e->getMessage());
        }
    }

    /**
     * Get backup metadata
     */
    private function getBackupMetadata(string $filename): ?array
    {
        try {
            $backupPath = storage_path('app/' . self::BACKUP_DIRECTORY . '/' . $filename);
            
            $zip = new ZipArchive();
            if ($zip->open($backupPath) !== TRUE) {
                return null;
            }

            $metadataContent = $zip->getFromName('backup_metadata.json');
            $zip->close();

            return $metadataContent ? json_decode($metadataContent, true) : null;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    // Additional restore methods would be implemented here:
    // - restoreDatabase()
    // - restoreApplicationFiles()
    // - restoreStorageFiles()
    // - restoreConfiguration()
}