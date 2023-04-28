<?php
declare(strict_types=1);

namespace Elephox\Builder\Swagger;

use Elephox\DI\Contract\Resolver;
use Elephox\Web\Routing\ClassRouteLoader;

readonly class SwaggerRouteLoader extends ClassRouteLoader {
	public function __construct() {
		parent::__construct(SwaggerController::class);
	}
}
