<?php
declare(strict_types=1);

namespace Elephox\Builder\Swagger;

use Elephox\DI\Contract\ServiceCollection;
use Elephox\Web\Routing\Contract\RouterBuilder;

trait AddsSwagger {
	abstract public function getServices(): ServiceCollection;

	abstract public function getRouter(): RouterBuilder;

	public function addSwagger(?OpenApiInfo $info = null): void
	{
		$this->getServices()->addSingleton(SwaggerController::class);
		$this->getServices()->addSingleton(OpenApiSpecGenerator::class);

		if ($info !== null) {
			$this->getServices()->addSingleton(OpenApiInfo::class, instance: $info);
		}

		$this->getRouter()->addLoader(new SwaggerRouteLoader());
	}
}
