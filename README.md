# Archipelago

**A legacy PHP refactoring framework. Compatible with PHP 5.0.0 through to current PHP versions.**

> [!WARNING]
> **Work in Progress:** This package is currently in active development ahead of its initial `1.0.0` release. The API is subject to change without notice, and it is **not recommended for production use** yet.

Archipelago is a small framework that helps developers incrementally refactor legacy/spaghetti PHP applications.

It provides a structured approach to extracting functionality from an existing application, isolating it behind well-defined boundaries, and progressively replacing the original implementation without requiring a complete rewrite.

> Archipelago is designed for refactoring legacy applications, not as a recommendation for developing new applications on older versions of PHP. PHP 5.0.0 compatibility is intentional: legacy applications cannot always be upgraded before they can be refactored.

## The problem

Refactoring spaghetti code is difficult.

In a legacy application, functionality is often tightly coupled. Responsibilities are unclear, dependencies are implicit, and seemingly minor changes can have unexpected consequences elsewhere.

A common approach to this problem is to wrap your legacy code in a newer application, and gradually replace calls to the old logic with calls to the new logic. The idea being that one day there will be no more calls to the old logic and it can be deleted. Usually, that day never comes. Instead, the engineers who built that logic move on, and anybody who touches the old system breaks it. A situation emerges where everybody wants the old code gone, but the organisation is reluctant to change it. Because of that it never goes. The old system is not strangled, but buried.

**Archipelago takes a different approach.**

Instead of refactoring legacy code from the outside-in, Archipelago provides a clean mechanism for refactoring legacy applications inside-out. By gradually moving existing logic into isolated pockets, the legacy code will gradually shrink in a low-risk and easily reversible way. Those same isolated pockets will over time be replaced by larger isolated pockets as the original logic is rediscovered. Eventually, your system will be totally refactored without ever needing to build a replacement.

## How it works

Archipelago works by gradually moving your code into **islands**.

An island is a mostly separate codebase within the larger codebase of your application. It can commuincate with your application through requests and responses. It should **never** send/receive messages to/from other islands.

You start by going through your legacy codebase and identifying any code that could be made into an island. The islands do not need to be large, 1-3 lines of code would be enough. Since legacy code often repeats the same code in multiple places, those are good candidates if you can find them.

Convert those candidates into islands, and replace the sections of the legacy code where the logic was extracted from with calls to the islands instead.

Keep moving small pieces of code into islands, eventually you will find 2 islands that need to send messages to each other, which is not allowed. That's okay. In fact, that's the point.

When you find 2 islands that need to send messages to each other, that's a sign that those 2 concerns might not be as isolated as you first thought. You will need to consider building larger islands to replace 2 or more smaller islands.

Keep going, and whenever you find 2 or more islands that need to send messages to one another, take that as a hint that there is a larger island that should be built to replace those smaller islands.

Eventually, you will end up with one huge island that is in effect the entire application, probably with a thin layer of spaghetti stretched around it. When you get to this point, you're ready for the final refactor. You need to take the logic that has been revealed through Archipelago, and build a new version without Archipelago. Once you've done that, your refactor is complete.

So essentially, you carve out small islands, replace them with larger islands, and keep going until you have one huge island.

## Island architecture

An island consists of three logical parts:

- Inner - May not reference classes or contain objects outside itself.
- Outer - May not reference classes or contain objects outside from the inner, or from other islands.
- Boundary - May contain references to classes or contain objects from anywhere except other islands.

## Requests and responses

Requests and responses are the communication mechanism between the inner and outer parts of an island.

They establish a clear contract between the two sides without requiring either side to understand the implementation of the other.

The following restrictions apply to all requests and responses:

1. They must not contain references to prohibited classes.
2. Anonymous functions are forbidden.
3. Closures are forbidden.
4. Stream handles are forbidden.

These restrictions apply to the contents of requests and responses, including nested data structures.

For example, placing a closure inside an array does not make it an acceptable request value.

## Ingress

The ingress is the part of the inner island that accepts an inner request and returns an inner response.

## Boundary Adapter

One important (and optional) part of the boundary is the adapter. It converts outer requests into inner requests, and it converts inner reaponses to outer responses. It sits in front of the ingress protecting it from the rest of the application.

If an adapter is not specified, then all all requests sent to the island must be inner and outer requests at the same time, and all responses must be inner and outer responses at the same time. This means that as per the rules on what requests and responses are allowed to reference, that a class that is inner and outer at once may reference **no classes at all**. This means that for very simple universes you don't need to bother with inner/outer layers or requests and responoses, but you must keep them simple enough to ensure that the inner island remains properly isolated.
