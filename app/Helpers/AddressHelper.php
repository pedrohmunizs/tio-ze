<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AddressHelper
{
    public static function getAddressWithCoordinates(string $cep, ?string $number = null): ?array 
    {
        $cleanCep = preg_replace('/[^0-9]/', '', $cep);
        
        if (strlen($cleanCep) !== 8) {
            throw new \InvalidArgumentException('CEP inválido. Deve conter 8 dígitos.');
        }

        $cacheKey = 'address_' . $cleanCep . '_' . ($number ?? 's/n') . '_';
        
        return Cache::remember($cacheKey, 86400, function () use ($cleanCep, $number) {
            $addressData = self::getAddressFromViaCep($cleanCep);
            
            if (!$addressData) {
                return null;
            }

            $fullAddress = self::buildFullAddress($addressData, $number);
            
            $coordinates = self::getCoordinatesFromGoogle($fullAddress);            
            
            if (!$coordinates) {
                return null;
            }

            return array_merge($addressData, $coordinates, [
                'number' => $number,
            ]);
        });
    }

    private static function getAddressFromViaCep(string $cep): ?array
    {
        try {
            $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");
            
            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            if (isset($data['erro']) && $data['erro'] === true) {
                return null;
            }

            return [
                'zip_code' => $data['cep'] ?? null,
                'street' => $data['logradouro'] ?? null,
                'neighborhood' => $data['bairro'] ?? null,
                'city' => $data['localidade'] ?? null,
                'state' => $data['uf'] ?? null,
                'complement_cep' => $data['complemento'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar CEP no ViaCEP: ' . $e->getMessage());
            return null;
        }
    }

    private static function buildFullAddress(array $addressData, ?string $number = null): string 
    {
        $parts = [];

        if (!empty($addressData['street'])) {
            $street = $addressData['street'];
            if ($number && $number !== '' && $number !== 's/n') {
                $street .= ', ' . $number;
            }
            $parts[] = $street;
        }

        if (!empty($addressData['neighborhood'])) {
            $parts[] = $addressData['neighborhood'];
        }

        $cityState = '';
        if (!empty($addressData['city'])) {
            $cityState .= $addressData['city'];
        }
        if (!empty($addressData['state'])) {
            $cityState .= ' - ' . $addressData['state'];
        }
        if (!empty($cityState)) {
            $parts[] = $cityState;
        }

        if (!empty($addressData['zip_code'])) {
            $parts[] = $addressData['zip_code'];
        }

        $parts[] = 'Brasil';

        $fullAddress = implode(', ', $parts);
        $fullAddress = preg_replace('/,\s*,/', ',', $fullAddress);
        $fullAddress = trim($fullAddress, ', ');

        return $fullAddress;
    }

    private static function getCoordinatesFromGoogle(string $address): ?array
    {
        try {
            $apiKey = config('services.google.maps_api_key');
            
            if (!$apiKey) {
                Log::error('Google Maps API Key não configurada');
                return null;
            }

            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => $apiKey,
            ]);
    
            if ($response->failed()) {
                return null;
            }
    
            $data = $response->json();
    
            if ($data['status'] !== 'OK') {
                Log::warning('Google Maps retornou status: ' . $data['status']);
                return null;
            }

            $results = $data['results'] ?? [];
            $selectedResult = null;

            foreach ($results as $result) {
                $locationType = $result['geometry']['location_type'] ?? null;
                if ($locationType === 'ROOFTOP') {
                    $selectedResult = $result;
                    break;
                }
            }

            if (!$selectedResult) {
                $selectedResult = $results[0] ?? null;
            }

            if (!$selectedResult) {
                return null;
            }

            $location = $selectedResult['geometry']['location'];
            $locationType = $selectedResult['geometry']['location_type'] ?? null;

            return [
                'latitude' => $location['lat'],
                'longitude' => $location['lng'],
                'formatted_address' => $selectedResult['formatted_address'] ?? $address,
                'place_id' => $selectedResult['place_id'] ?? null,
                'location_type' => $locationType,
                'is_precise' => $locationType === 'ROOFTOP',
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao buscar coordenadas no Google: ' . $e->getMessage());
            return null;
        }
    }
}