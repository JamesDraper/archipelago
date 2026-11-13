# Simple Templating Island

We've all worked on an application where the view logic is not separated from everything else.

This is an example how you could use Archipelago to separate the view layer into its own island over time.

It's a simple templating island, the request takes a template path, and an associative array of template variables.

You would start by finding 2 or 3 lines of view code which you could safely move into the island. Then, as you refactor and gradually find more and more of the surrounding template, you can move that into the templating island too.

As it grows, this island would go from a small group of partial templates based on what you were able to identify and move out of the app, into a complete (albeit simple) templating engine.

If later on you wanted to build/use a different/more complex templating engine, you could base that on the templates that you were able to extract here.
