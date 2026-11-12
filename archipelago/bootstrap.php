<?php

// Messages
require dirname(__FILE__) . '/src/Archipelago/Island/Inner/Request.php';
require dirname(__FILE__) . '/src/Archipelago/Island/Inner/Response.php';
require dirname(__FILE__) . '/src/Archipelago/Island/Outer/Request.php';
require dirname(__FILE__) . '/src/Archipelago/Island/Outer/Response.php';

// Ingress
require dirname(__FILE__) . '/src/Archipelago/Island/Inner/Ingress.php';

// Boundary
require dirname(__FILE__) . '/src/Archipelago/Island/Boundary/Bootstrapper.php';
require dirname(__FILE__) . '/src/Archipelago/Island/Boundary/Configurator.php';
require dirname(__FILE__) . '/src/Archipelago/Island/Boundary/Adapter.php';

// Island
require dirname(__FILE__) . '/src/Archipelago/Island.php';

// Archipelago
require dirname(__FILE__) . '/src/Archipelago.php';

// Exceptions
require dirname(__FILE__) . '/src/Archipelago/Exception.php';
require dirname(__FILE__) . '/src/Archipelago/BadValueException.php';
require dirname(__FILE__) . '/src/Archipelago/TypeException.php';

// Null boundaries
require dirname(__FILE__) . '/src/Archipelago/Internal/Boundary/NullBootstrapper.php';
require dirname(__FILE__) . '/src/Archipelago/Internal/Boundary/NullConfigurator.php';
require dirname(__FILE__) . '/src/Archipelago/Internal/Boundary/NullAdapter.php';

// Internals
require dirname(__FILE__) . '/src/Archipelago/Internal/Root.php';
require dirname(__FILE__) . '/src/Archipelago/Internal/TypeFormatter.php';
require dirname(__FILE__) . '/src/Archipelago/Internal/TypeExceptionFactory.php';

// Validators
require dirname(__FILE__) . '/src/Archipelago/Internal/Validator/AdapterValidator.php';
require dirname(__FILE__) . '/src/Archipelago/Internal/Validator/BootstrapperValidator.php';
require dirname(__FILE__) . '/src/Archipelago/Internal/Validator/ConfiguratorValidator.php';
require dirname(__FILE__) . '/src/Archipelago/Internal/Validator/IngressValidator.php';
