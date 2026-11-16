# Archipelago

**A legacy PHP refactoring framework compatible with PHP 5.0.0 through to current PHP versions.**

> [!WARNING]
> **Work in Progress:** This package is currently in active development ahead of its initial `1.0.0` release. The API is subject to change without notice, and it is **not recommended for production use** yet.

Archipelago is a small framework that helps developers incrementally refactor legacy/spaghetti PHP applications.

It provides a structured approach to extracting functionality from an existing application, moving it into isolated self-contained areas called **islands**, and progressively replacing those islands as the original logic of the application is rediscovered. All without requiring an application rewrite.

```mermaid
flowchart TB
    subgraph ISLANDS["Islands"]
        direction LR

        PRODUCT["product"]
        ORDER["order"]
        VIEW["view.templating"]
        USER["user"]
    end

    LEGACY["Legacy Application"]

    PRODUCT <--> LEGACY
    ORDER <--> LEGACY
    VIEW <--> LEGACY
    USER <--> LEGACY
```

## The process

Let's say you have a legacy e-commerce application. It's currently spaghetti code and badly needs to be untangled.

Once Archipelago is installed, you would start by identifying areas of your codebase that could be extracted into islands. An island need not be large at this point, 2 or 3 lines of extracted code would do. If you see the same code repeated in several places in the spaghetti, that would also be a good candidate. You don't need to have a plan yet, in fact you shouldn't. But you do need to remember the golden rule: **no island may ever send messages to another island**.

Let's say that after some time, you ended up with the following islands:

| Island                   | Responsibility                   |
|--------------------------|----------------------------------|
| `price_formatter`        | Formats prices for display.      |
| `currency_converter`     | Converts between currencies.     |
| `product_lookup`         | Retrieves product information.   |
| `stock_checker`          | Checks product availability.     |
| `customer_lookup`        | Retrieves customer information.  |
| `email_validator`        | Validates email addresses.       |
| `address_validator`      | Validates postal addresses.      |
| `cart_calculator`        | Calculates shopping cart totals. |
| `discount_calculator`    | Calculates applicable discounts. |
| `tax_calculator`         | Calculates tax.                  |
| `shipping_calculator`    | Calculates shipping costs.       |
| `order_number_generator` | Generates unique order numbers.  |

Since no island may ever send messages to another island, you likely would have found that a lot of the islands you created were using the legacy application to send messages to one another. That's okay. What that is telling you is that those inter-related concerns are likely part of larger domains. You're starting to rediscover the initial application logic lost in the spaghetti.

The next step is to start thinking about replacing some of those smaller islands with larger islands. Let's say that you were able to create the following replacements:

| Island                  | Replaces                                                                           | Responsibility                              |
|-------------------------|------------------------------------------------------------------------------------|---------------------------------------------|
| `product`               | `product_lookup`, `stock_checker`                                                  | Product information and availability.       |
| `customer`              | `customer_lookup`, `email_validator`                                               | Customer records and contact details.       |
| `checkout`              | `cart_calculator`, `discount_calculator`, `tax_calculator`, `shipping_calculator`  | Cart totals, discounts, taxes and shipping. |
| `order`                 | `order_number_generator`                                                           | Order creation and identification.          |

That gives you this:

| Island                  | Responsibility                              |
|-------------------------|---------------------------------------------|
| `product`               | Product information and availability.       |
| `customer`              | Customer records and contact details.       |
| `checkout`              | Cart totals, discounts, taxes and shipping. |
| `order`                 | Order creation and identification.          |
| `price_formatter`       | Formats prices for display.                 |
| `currency_converter`    | Converts between currencies.                |
| `address_validator`     | Validates postal addresses.                 |

At this point you keep going, and whenever the friction between 2 or more islands becomes inconvenient, you think about how you might replace them with a larger island handling the same responsibilities. If you keep going long enough you will get this:

| Island                  | Responsibility                              |
|-------------------------|---------------------------------------------|
| `application`           | Handles all application responsibilities.   |

At this point, you've done it! All that remains to do now is to remove Archipelago. Congratulations!

The first changes you made were very low-risk and easily reversible. Every change you made shrunk the legacy codebase slightly. You never needed to plan in advance, and instead you let the logic buried in the spaghetti code reveal itself to you over time. Most importantly, **you never needed to build a replacement application**.

## Island Architecture

An island is composed of three logical areas: Inner, Outer, and Boundary. Each has a distinct responsibility and a set of restrictions governing its dependencies.

### Inner

The Inner contains the actual logic extracted from the legacy application.

It must not reference classes or contain objects defined outside the Inner.

### Outer

The Outer provides the integration point between the island and the surrounding application.

It must not reference classes or contain objects defined inside the Inner, or defined in other Islands.

### Boundary

The Boundary connects the Inner and the Outer.

It may not reference classes or contain objects defined inside other islands, but otherwise has no restrictions.

## Installation

Archipelago can be installed in one of two ways:

### Using Composer

Install Archipelago through Composer:

```bash
composer require james-draper/archipelago
```

### Manual Installation

Alternatively, copy the `archipelago` directory into your project in a location accessible to your application.

Then include the bootstrap file:

```php
require '/path/to/archipelago/bootstrap.php';
```
