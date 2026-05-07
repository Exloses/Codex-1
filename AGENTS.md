# AGENTS.md

## Repository Layout

This repository is a minimal Python CLI starter project.

- `README.md` - project overview, setup, run, and test instructions.
- `PROJECT_PLAN.md` - proposed lightweight project direction.
- `pyproject.toml` - Python package metadata, console script, and pytest configuration.
- `src/codex_starter/` - application package.
- `src/codex_starter/cli.py` - CLI parser and entry point.
- `src/codex_starter/__main__.py` - module execution entry point for `python -m codex_starter`.
- `tests/` - pytest tests.

## Detected Stack

- Language: Python 3.10+
- Framework: none
- Package manager: pip
- Test runner: pytest

## Commands

Install dependencies:

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

Lint command: not defined yet.

Build command: not defined yet.

## Coding Rules

- Keep changes small, focused, and easy to review.
- Prefer standard-library Python unless a dependency clearly pays for itself.
- Keep CLI behavior deterministic and easy to test.
- Add or update tests when behavior is added or changed.
- Document new setup, run, validation, and build commands as part of the same change that introduces them.

## Pull Request Rules

- Create feature or maintenance branches; do not push directly to `main`.
- Include a clear summary, files changed, commands run, validation results, and remaining follow-up work.
- Keep PRs scoped to one purpose.
- Do not merge your own PR unless explicitly instructed by the repository owner.

## Do Not Touch

Do not add, edit, expose, move, or delete:

- Secrets, API keys, tokens, cookies, passwords, private keys, wallet files, seed phrases, or credentials.
- Production deployment settings, infrastructure, DNS, billing, or live service configuration.
- Legal or license files unless explicitly requested.
- Crypto, mining, trading, or automation behavior unless the repository explicitly requests it.
- Important project files without a clear reason documented in the PR.
