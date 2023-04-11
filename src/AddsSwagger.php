<?php
declare(strict_types=1);

namespace Elephox\Builder\Swagger;

use Elephox\DI\Contract\ServiceCollection;
use Elephox\Web\Routing\Contract\Router;

trait AddsSwagger {
	abstract public function getServices(): ServiceCollection;

	public function addSwagger(?OpenApiInfo $info = null): void
	{
		$this->getServices()->addSingleton(OpenApiSpecGenerator::class, factory: function (Router $router) use ($info): OpenApiSpecGenerator {
			return new OpenApiSpecGenerator($router, $info);
		});

		$this->getServices()->addSingleton(SwaggerController::class);

		$swaggerLoader = $this->getServices()
			->resolver()
			->instantiate(SwaggerRouteLoader::class);

		$router = $this->getServices()->requireService(Router::class);
		$router->addLoader($swaggerLoader);
	}
}
