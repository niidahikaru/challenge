<?php

namespace App;

use GuzzleHttp\Client;

class Challenge
{
  private $client;

  const BASE_URL = 'http://challenge.z2o.cloud/';

  public function __construct() {
    $this->client = new Client();
  }

  public function exec(): void
  {
    $result = $this->createChallenge();
    $activesAt = $result['actives_at'];

    $result = $this->tryChallenge($result['id'], $activesAt);
    print_r($result);
    return;
  }

  private function createChallenge(): array
  {
    $url = self::BASE_URL . 'challenges/?nickname=niida';
    $res = $this->client->request('POST', $url);

    return $this->serializeToArray($res->getBody());
  }

  private function tryChallenge(string $challengeId, int $activesAt): array
  {
    $url = self::BASE_URL . 'challenges';
    $options = [
      'headers' => [
          'X-Challenge-Id' => $challengeId,
      ],
    ];

    $result = [];
    while (true) {
      if ($activesAt > $this->now()) {
        continue;
      }

      $res = $this->client->request('PUT', $url, $options);
      $result = $this->serializeToArray($res->getBody());

      if ($this->isSuccess($result)) {
        break;
      }

      $activesAt = $result['actives_at'];
    }

    return $result;
  }

  private function isSuccess($result): bool
  {
    return isset($result['result']['url']);
  }

  private function serializeToArray(string $json): array
  {
    return json_decode($json, true);
  }

  private function now(): int
  {
    return ceil(microtime(true)*1000);
  }
}
