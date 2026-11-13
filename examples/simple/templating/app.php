<?php
declare(strict_types=1);

/**
 * @throws LogicException
 */
function renderTemplate(string $template, array $vars = [])
{
    $request = new Examples\Simple\Islands\Templating\Request($template, $vars);

    $response = Archipelago::run('simple.templating', $request);

    if ($response instanceof Examples\Simple\Islands\Templating\Response\ErrorResponse) {
        throw new LogicException($response->message);
    }

    return $response->rendered;
}

Archipelago::register(new Examples\Simple\Islands\Templating\Island());
