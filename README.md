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

Inspect a different project directory:

```sh
python -m codex_starter --path /path/to/project
```

Run tests:

```sh
python -m pytest
```

There is no separate lint or build command yet.

## Example Output

```text
Codex-1 project readiness report

Repository summary
- Name: Codex-1
- Path checked: /path/to/Codex-1
- Purpose: Minimal starter project for a small command-line tool.

Detected project type
- Python CLI project

Important files found
- README.md
- AGENTS.md
- PROJECT_PLAN.md
- pyproject.toml
- src/codex_starter/cli.py
- tests/test_cli.py

Missing recommended files
- None. The recommended starter files are present.

Suggested next actions
- Run tests with: python -m pytest
- Update PROJECT_PLAN.md when the project purpose becomes clearer.
- Add a formatter or linter when coding conventions are chosen.
- Keep secrets and production credentials out of the repository.
```

## Maintenance

- Keep changes small and easy to review.
- Do not commit secrets, API keys, tokens, wallets, private keys, cookies, or production credentials.
- Document new setup and validation commands in the same PR that introduces them.
