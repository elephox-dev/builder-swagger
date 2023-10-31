<?php
declare(strict_types=1);

namespace Elephox\Builder\Swagger;

use Elephox\DI\Contract\ServiceCollection;
use Elephox\Web\Routing\Contract\RouterBuilder as RouterBuilderContract;

trait AddsSwagger {
	abstract protected function getServices(): ServiceCollection;

	abstract public function getRouter(): RouterBuilderContract;

	public function addSwagger(?OpenApiInfo $info = null): void
	{
		$this->getServices()->addSingleton(OpenApiInfo::class, instance: $info);
		$this->getServices()->addSingleton(OpenApiSpecGenerator::class);
		$this->getRouter()->addLoader(new SwaggerRouteLoader());
	}
}
