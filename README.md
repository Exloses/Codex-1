# Codex-1

Codex-1 is a minimal Python CLI starter project. It provides a small package, a command-line entry point, and a basic test suite so future work can grow from a working foundation.

## Project Status

- Language: Python 3.10+
- Framework: none
- Package manager: pip
- Test runner: pytest

## Project Structure

```text
.
|-- AGENTS.md
|-- PROJECT_PLAN.md
|-- README.md
|-- pyproject.toml
|-- src/
|   `-- codex_starter/
|       |-- __init__.py
|       |-- __main__.py
|       `-- cli.py
`-- tests/
    `-- test_cli.py
```

## Development

Install the project with development dependencies:

```sh
python -m pip install -e ".[dev]"
```

Run the CLI:

```sh
python -m codex_starter
python -m codex_starter --name "Your Project"
codex-1
codex-1 --name "Your Project"
```

Run tests:

```sh
python -m pytest
```

There is no separate lint or build command yet.

## Maintenance

- Keep changes small and easy to review.
- Do not commit secrets, API keys, tokens, wallets, private keys, cookies, or production credentials.
- Document new setup and validation commands in the same PR that introduces them.
