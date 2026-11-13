<?php
declare(strict_types=1);

namespace Examples\Simple\Islands\Templating;

use Examples\Simple\Islands\Templating\Response\RenderedResponse;
use Examples\Simple\Islands\Templating\Response\ErrorResponse;

use Archipelago_Island_Inner_Request;
use Archipelago_Island_Inner_Ingress;

use Throwable;

use function sprintf;

class Ingress implements Archipelago_Island_Inner_Ingress
{
    public function invoke(Archipelago_Island_Inner_Request $request)
    {
        if (!$request instanceof Request) {
            return new ErrorResponse(sprintf('Invalid request type: %s', $request::class));
        }

        $path = $this->getTemplatePath($request->template);

        if (!is_file($path)) {
            return new ErrorResponse(sprintf('Template not found: %s', $request->template));
        }

        try {
            return new RenderedResponse($this->render($request));
        } catch (Throwable $throwable) {
            return new ErrorResponse($throwable->getMessage());
        }
    }

    private function getTemplatePath(string $template): string
    {
        return __DIR__ . '/../templates/' . $template . '.php';
    }

    /**
     * @param array<string, mixed> $vars
     */
    private function render(string $path, array $vars): string
    {
        $vars = $request->vars;
        extract($vars);

        ob_start();

        try {
            require $path;

            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}
