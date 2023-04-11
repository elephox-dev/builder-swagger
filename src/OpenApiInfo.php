<?php

namespace Elephox\Builder\Swagger;

readonly class OpenApiInfo
{
	public function __construct(
		public ?string $title = null,
		public ?string $summary = null,
		public ?string $description = null,
		public ?string $version = null,
	)
	{
	}

	public function toArray(): array {
		$info = [];

		if ($this->title !== null) {
			$info['title'] = $this->title;
		}

		if ($this->summary !== null) {
			$info['summary'] = $this->summary;
		}

		if ($this->description !== null) {
			$info['description'] = $this->description;
		}

		if ($this->version !== null) {
			$info['version'] = $this->version;
		}

		return $info;
	}
}
