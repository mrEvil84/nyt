<?php

namespace App\Articles\SharedKernel\Articles;

interface Article
{
    public function fromRawData(array $rawData): self;
    public function toArray(): array;
}