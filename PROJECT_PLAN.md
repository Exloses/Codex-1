# Project Plan

## Direction

Codex-1 should start as a small Python CLI project that can be expanded gradually. The immediate goal is to provide a working command-line entry point, clear documentation, and a test setup without committing to a larger application architecture too early.

## Why This Direction

- The repository name is general and does not define a product domain yet.
- Python keeps the starter project simple and maintainable.
- A CLI is useful as a foundation for future utilities, experiments, or workflow tools without requiring web hosting, paid APIs, credentials, or production deployment.

## Initial Scope

- Package source under `src/codex_starter/`.
- A `codex-1` console command.
- Basic pytest coverage for CLI behavior.
- README and AGENTS instructions that reflect the actual project structure.

## Out of Scope for Now

- Web application framework setup.
- Database, queue, cloud, or deployment configuration.
- Paid API integrations.
- Secrets, credentials, wallets, crypto, mining, trading, or automation behavior.

## Recommended Next Steps

1. Decide the real project purpose and update this plan.
2. Add linting and formatting once coding conventions are chosen.
3. Expand the CLI with one useful command that matches the chosen purpose.
