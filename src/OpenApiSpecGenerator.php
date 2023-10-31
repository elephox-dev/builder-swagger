<?php

namespace Elephox\Builder\Swagger;

use Elephox\Collection\Contract\GenericEnumerable;
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
		$routes = $this->router->getRoutes();

		$spec = [
			"openapi" => "3.0.0",
			"paths" => self::generatePaths($routes),
		];

		$info = $this->info?->toArray();
		if (!empty($info)) {
			$spec['info'] = $info;
		}

		return $spec;
	}

	/**
	 * @param \Elephox\Collection\Contract\GenericEnumerable<RouteData> $routes
	 * @return array
	 */
	private static function generatePaths(GenericEnumerable $routes): array {
		$paths = [];

		/** @var RouteData $routeData */
		foreach ($routes as $routeData) {
			$path = $routeData->getTemplate()->getSource();
			if (str_starts_with($path, '/swagger')) {
				continue;
			}

			$paths[$path] = self::generatePathItem($routeData);
		}

		return $paths;
	}

	private static function generatePathItem(RouteData $routeData): array {
		$methods = [
			"summary" => $routeData->getHandlerName(),
		];

		foreach ($routeData->getMethods() as $method) {
			$methods[mb_strtolower($method)] = self::generateOption($method, $routeData);
		}

		return $methods;
	}

	private static function generateOption(string $method, RouteData $routeData): array {
		$option = [
			'operationId' => $method . " " . $routeData->getTemplate()->getSource(),
		];

		$parameters = self::generateParameters($routeData);
		if (count($parameters) > 0) {
			$option['parameters'] = $parameters;
		}

		return $option;
	}

	private static function generateParameters(RouteData $routeData): array {
		$parameters = [];

		/**
		 * @var \Elephox\Web\Routing\RouteTemplateVariable $variable
		 */
		foreach ($routeData->getTemplate()->getVariables() as $variable) {
			$parameters[] = [
				'name' => $variable->name,
				'in' => 'path',
				'required' => true,
				'schema' => [
					'type' => self::translateParameterType($variable->type),
				],
			];
		}

		return $parameters;
	}

	private static function translateParameterType(string $variableType): string {
		return match ($variableType) {
			'int' => 'integer',
			default => 'string',
		};
	}
}
