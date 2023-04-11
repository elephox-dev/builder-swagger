<?php

namespace Elephox\Builder\Swagger;

use Elephox\Web\Routing\Contract\RouteData;
use Elephox\Web\Routing\Contract\Router;

readonly class OpenApiSpecGenerator
{
	public function __construct(
		private Router $router,
		private ?OpenApiInfo $info,
	)
	{
	}

	public function getSpec(): array {
		$spec = [
			"openapi" => "3.0.0",
			"paths" => $this->generatePaths(),
		];

		$info = $this->info?->toArray();
		if (!empty($info)) {
			$spec['info'] = $info;
		}

		return $spec;
	}

	private function generateInfo(): array {
		return [
			"title" => "My API",
			"summary" => "My Summary",
			"description" => "My Description",
			"version" => "1.0"
		];
	}

	private function generatePaths(): array {
		$this->router->loadRoutes();

		$paths = [];

		/** @var RouteData $routeData */
		foreach ($this->router->getLoadedRoutes() as $routeData) {
			$path = $routeData->getTemplate()->getSource();
			if (str_starts_with($path, '/swagger')) {
				continue;
			}

			$paths[$path] = $this->generatePathItem($routeData);
		}

		return $paths;
	}

	private function generatePathItem(RouteData $routeData): array {
		$methods = [
			"summary" => $routeData->getHandlerName(),
		];

		foreach ($routeData->getMethods() as $method) {
			$methods[mb_strtolower($method)] = $this->generateOption($method, $routeData);
		}

		return $methods;
	}

	private function generateOption(string $method, RouteData $routeData): array {
		$option = [
			'operationId' => $method . " " . $routeData->getTemplate()->getSource(),
		];

		$parameters = $this->generateParameters($routeData);
		if (count($parameters) > 0) {
			$option['parameters'] = $parameters;
		}

		return $option;
	}

	private function generateParameters(RouteData $routeData): array {
		$parameters = [];

		foreach ($routeData->getTemplate()->getVariableNames() as $variableName) {
			$parameters[] = [
				'name' => $variableName,
				'in' => 'path',
				'required' => true,
				'schema' => [
					'type' => 'string',
				],
			];
		}

		return $parameters;
	}
}
