<?php

namespace Tygh\Addons\CompetitiveMap\Service;

class RefererParser
{
    protected $queryParams = [];

    public function __construct(string $referer)
    {
        $parsed = parse_url($referer);
        parse_str($parsed['query'] ?? '', $this->queryParams);
    }

    public function getCategoryId(): ?int
    {
        return isset($this->queryParams['category_id']) ? (int) $this->queryParams['category_id'] : null;
    }

    public function getFeaturesHash(): ?string
    {
        return $this->queryParams['features_hash'] ?? null;
    }
}
