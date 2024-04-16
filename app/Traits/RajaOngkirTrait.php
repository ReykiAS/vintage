<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait RajaOngkirTrait
{
    public function getShippingCost($origin, $destination, $weight, $courier)
    {
        $apiKey = config('services.rajaongkir.api_key');

        $client = new Client();

        try {
            $response = $client->post('https://api.rajaongkir.com/starter/cost', [
                'headers' => [
                    'content-type' => 'application/x-www-form-urlencoded',
                    'key' => $apiKey
                ],
                'form_params' => [
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => $courier
                ]
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            return $data['rajaongkir']['results'][0]['costs'][0]['cost'][0]['value'] ?? null;

        } catch (\Exception $e) {
            return null;
        }
    }
}
