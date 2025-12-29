<?php

declare(strict_types=1);

namespace App\Service;

class OrdioApiService
{
    private const BASE_URL = 'https://public.ordio.com/api/v1';

    public function __construct(
        private readonly string $apiKey,
    ) {
    }

    /**
     * Holt alle Schichten für einen bestimmten Zeitraum.
     *
     * @return array<mixed>
     */
    public function getShifts(
        \DateTimeInterface $start,
        \DateTimeInterface $end,
        array $options = [],
    ): array {
        $params = [
            'startTz' => $start->format('Y-m-d\TH:i:s\Z'),
            'endTz' => $end->format('Y-m-d\TH:i:s\Z'),
        ];

        if (isset($options['workingArea'])) {
            $params['workingArea'] = $options['workingArea'];
        }
        if (isset($options['branch'])) {
            $params['branch'] = $options['branch'];
        }
        if (isset($options['employees'])) {
            $params['employees'] = $options['employees'];
        }
        if (isset($options['onlyPublish'])) {
            $params['onlyPublish'] = $options['onlyPublish'];
        }
        if (isset($options['limit'])) {
            $params['limit'] = $options['limit'];
        }
        if (isset($options['offset'])) {
            $params['offset'] = $options['offset'];
        }

        return $this->request('GET', '/shifts', $params);
    }

    /**
     * Holt alle Schichten für Dezember des aktuellen Jahres.
     *
     * @return array<mixed>
     */
    public function getShiftsForDecember(?int $year = null): array
    {
        $year = $year ?? (int) date('Y');

        $start = new \DateTimeImmutable("{$year}-12-01T00:00:00Z");
        $end = new \DateTimeImmutable("{$year}-12-31T23:59:59Z");

        return $this->getShifts($start, $end);
    }

    /**
     * Holt einen einzelnen Shift anhand der ID.
     *
     * @return array<mixed>
     */
    public function getShift(int $id): array
    {
        return $this->request('GET', "/shifts/{$id}");
    }

    /**
     * @return array<mixed>
     */
    private function request(string $method, string $endpoint, array $params = []): array
    {
        $url = self::BASE_URL . $endpoint;

        if ($method === 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => [
                    'Authorization: Bearer ' . $this->apiKey,
                    'Accept: application/json',
                    'Content-Type: application/json',
                ],
                'ignore_errors' => true,
                'timeout' => 30,
            ],
        ]);

        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            throw new \RuntimeException("API request failed: {$url}");
        }

        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        // Check for HTTP errors via response headers
        $statusCode = $this->getStatusCodeFromHeaders($http_response_header ?? []);
        if ($statusCode >= 400) {
            throw new \RuntimeException(
                sprintf('API error %d: %s', $statusCode, $data['message'] ?? 'Unknown error')
            );
        }

        return $data;
    }

    private function getStatusCodeFromHeaders(array $headers): int
    {
        foreach ($headers as $header) {
            if (preg_match('/^HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                return (int) $matches[1];
            }
        }

        return 200;
    }
}