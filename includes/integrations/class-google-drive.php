<?php
// Google Drive integration for Administration Plugin
require_once __DIR__ . '/../../vendor/autoload.php'; // Google API PHP Client

class Administration_Google_Drive {
    private $client;
    private $service;
    private $parent_folder_id;

    public function __construct($credentials_path, $parent_folder_id) {
        $this->parent_folder_id = $parent_folder_id;
        $this->client = new Google_Client();
        $this->client->setAuthConfig($credentials_path);
        $this->client->addScope(Google_Service_Drive::DRIVE);
        $this->client->setAccessType('offline');
        $this->service = new Google_Service_Drive($this->client);
    }

    // Create a folder in Drive (returns folder ID)
    public function createFolder($name, $parentId = null) {
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentId ? $parentId : $this->parent_folder_id]
        ]);
        $folder = $this->service->files->create($fileMetadata, [
            'fields' => 'id',
            'supportsAllDrives' => true
        ]);
        return $folder->id;
    }

    // Upload a file to a folder (returns file ID)
    public function uploadFile($filePath, $fileName, $folderId) {
        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $fileName,
            'parents' => [$folderId]
        ]);
        $content = file_get_contents($filePath);
        $file = $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => mime_content_type($filePath),
            'uploadType' => 'multipart',
            'fields' => 'id',
            'supportsAllDrives' => true
        ]);
        return $file->id;
    }

    // Get a shareable URL for a file (sets permission if needed)
    public function getFileUrl($fileId) {
        // Set file to anyone with the link can view
        $permission = new Google_Service_Drive_Permission([
            'type' => 'anyone',
            'role' => 'reader',
        ]);
        $this->service->permissions->create($fileId, $permission, ['supportsAllDrives' => true]);
        $file = $this->service->files->get($fileId, [
            'fields' => 'webViewLink',
            'supportsAllDrives' => true
        ]);
        return $file->webViewLink;
    }
}

// Usage example (set these in your plugin config):
// $credentials_path = '/absolute/path/to/credentials.json';
// $parent_folder_id = '15uxOSGKsmbEh1ojQZTADpGZ10grYs4LB';
// $drive = new Administration_Google_Drive($credentials_path, $parent_folder_id); 