# Contributing

This document describes how to contribute to the project. Please refer to the [`README.md`](./README.md)
file for information on how to build and setup the development environment.

## Process/Workflow

The project's [source code](https://github.com/fluid-project/lcil) is hosted on GitHub. In development work is committed
to the [dev branch](https://github.com/fluid-project/lcil/tree/dev), and merged into main when it is ready for more
production use.

### Code style and linting

Standard Laravel practices should be followed, and more specifically [Spatie's style guidelines](https://spatie.be/guidelines/laravel-php)
have been adopted into the codebase where possible.

Additionally tools have been provided to help manage the code styling. Before submitting a PR make sure to run the all
of the linting and formatting tasks as defined in the [Linting](https://github.com/fluid-project/lcil/blob/main/README.md#linting)
section of the [README](https://github.com/fluid-project/lcil/blob/main/README.md).

### Unit Tests

New features and bug fixes should be accompanied by tests where possible. Where possible tests should be written using
[PEST](https://pestphp.com).

### Commit Logs

Commit logs should follow the [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/) spec.

### Pull Requests

Contributions should be submitted by a Pull Request (PR) to the dev branch, and associated with a related
[issue](https://github.com/fluid-project/lcil/issues). After being approved by a maintainer it will be squash merged
into the dev branch.
