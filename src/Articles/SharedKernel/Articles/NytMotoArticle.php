<?php

declare(strict_types=1);

namespace App\Articles\SharedKernel\Articles;

class NytMotoArticle implements Article
{
    private string $title;
    private string $publicationDate;
    private string $lead;
    private string $image;
    private string $url;

    public function fromRawData(array $rawData): self
    {
        $instance = new self();
        $instance->setData(
            $rawData['headline']['main'],
            $rawData['pub_date'],
            $rawData['lead_paragraph'],
            $this->getJumboImageUrl($rawData),
            $rawData['web_url']
        );
        return $instance;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'publicationDate' => $this->publicationDate,
            'lead' => $this->lead,
            'image' => $this->image,
            'url' => $this->url,
        ];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    private function setData(string $title, string $publicationDate, string $lead, string $image, string $url): void
    {
        $this->title = $title;
        $this->publicationDate = $publicationDate;
        $this->lead = $lead;
        $this->image = $image;
        $this->url = $url;
    }

    private function getJumboImageUrl(array $rawData): string
    {
        if (empty($rawData['multimedia'])) {
            return 'superJumbo-image-not-found';
        }

        $multimedia = $rawData['multimedia'];
        $jumbo = array_filter($multimedia, function ($item) {
            return $item['subtype'] === 'superJumbo' ? $item : [];
        });

        if (empty($jumbo)) {
            $jumboUrl = 'superJumbo-image-not-found';
        } else {
            $jumboUrl = array_values($jumbo)[0]['url'];
        }

        return $jumboUrl;
    }
}