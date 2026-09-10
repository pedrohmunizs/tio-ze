<?php
// app/Services/S3Service.php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class S3Service
{
    private string $disk = 's3';

    /**
     * Upload de arquivo para o S3
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder Pasta onde o arquivo será salvo
     * @param string|null $filename Nome do arquivo (opcional)
     * @return string Caminho do arquivo salvo
     */
    public function upload($file, string $folder = '', ?string $filename = null): string
    {
        if (!$filename) {
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        }

        $path = $folder ? $folder . '/' . $filename : $filename;

        Storage::disk($this->disk)->put($path, file_get_contents($file->getRealPath()));

        return $path;
    }

    /**
     * Download de arquivo do S3
     */
    public function download(string $path): ?string
    {
        if (!Storage::disk($this->disk)->exists($path)) {
            return null;
        }

        return Storage::disk($this->disk)->get($path);
    }

    /**
     * Obter URL pública do arquivo
     */
    public function getUrl(string $path): string
    {
        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Obter URL temporária (assinada) para acesso privado
     */
    public function getTemporaryUrl(string $path, int $minutes = 5): string
    {
        return Storage::disk($this->disk)->temporaryUrl($path, now()->addMinutes($minutes));
    }

    /**
     * Deletar arquivo do S3
     */
    public function delete(string $path): bool
    {
        if (!Storage::disk($this->disk)->exists($path)) {
            return false;
        }

        return Storage::disk($this->disk)->delete($path);
    }

    /**
     * Gerar nome único para o arquivo
     */
    private function generateFileName($file): string
    {
        return Str::uuid() . '.' . $file->getClientOriginalExtension();
    }

    /**
     * Listar arquivos de uma pasta
     */
    public function listFiles(string $folder = ''): array
    {
        return Storage::disk($this->disk)->files($folder);
    }

    /**
     * Verificar se arquivo existe
     */
    public function exists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }

    /**
     * Obter tamanho do arquivo em bytes
     */
    public function getSize(string $path): int
    {
        return Storage::disk($this->disk)->size($path);
    }

    /**
     * Obter tipo MIME do arquivo
     */
    public function getMimeType(string $path): string
    {
        return Storage::disk($this->disk)->mimeType($path);
    }

    /**
     * Copiar arquivo para outra pasta
     */
    public function copy(string $from, string $to): bool
    {
        return Storage::disk($this->disk)->copy($from, $to);
    }

    /**
     * Mover arquivo para outra pasta
     */
    public function move(string $from, string $to): bool
    {
        return Storage::disk($this->disk)->move($from, $to);
    }
}