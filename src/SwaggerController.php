<?php
declare(strict_types=1);

namespace Elephox\Builder\Swagger;

use Elephox\Files\Directory;
use Elephox\Files\Path;
use Elephox\Http\Contract\ResponseBuilder;
use Elephox\Http\Response;
use Elephox\Mimey\MimeType;
use Elephox\Web\Routing\Attribute\Controller;
use Elephox\Web\Routing\Attribute\Http\Get;
use JsonException;

#[Controller("swagger")]
readonly class SwaggerController {
	private Directory $distDirectory;

	public function __construct(
		private OpenApiSpecGenerator $specGenerator,
	) {
		$this->distDirectory = new Directory(Path::join(dirname(__DIR__), "static", "swagger-ui-4.18.2", "dist"));
	}

	#[Get, Get("/")]
	public function index(): ResponseBuilder
	{
		return Response::build()->redirect("/swagger/index.html", true);
	}

	#[Get("{filename}")]
	public function file(string $filename): ResponseBuilder
	{
		$response = Response::build()->ok();
		if ($filename === 'swagger-initializer.js') {
			return $response->textBody($this->generateInitializerScript(), MimeType::TextJavascript);
		}

		$file = $this->distDirectory->file($filename);
		if (!$file->exists()) {
			return $response->notFound();
		}

		return $response->fileBody($file);
	}

	/**
	 * @throws JsonException
	 */
	#[Get("spec.json")]
	public function spec(): ResponseBuilder
	{
		return Response::build()
			->ok()
			->jsonBody($this->specGenerator->getSpec());
	}

	private function getSwaggerSpecUrl(): string {
		return "/swagger/spec.json";
	}

	private function generateInitializerScript(): string {
		$specUrl = $this->getSwaggerSpecUrl();

		return <<<JS
window.onload = function() {
  //<editor-fold desc="Changeable Configuration Block">

  // the following lines will be replaced by docker/configurator, when it runs in a docker-container
  window.ui = SwaggerUIBundle({
    url: "{$specUrl}",
    dom_id: '#swagger-ui',
    deepLinking: true,
    presets: [
      SwaggerUIBundle.presets.apis,
      SwaggerUIStandalonePreset
    ],
    plugins: [
      SwaggerUIBundle.plugins.DownloadUrl
    ],
    layout: "StandaloneLayout"
  });

  //</editor-fold>
};
JS;
	}
}
