"""Command-line entry point for Codex-1."""

from __future__ import annotations

import argparse


def build_message(name: str = "Codex-1") -> str:
    """Return the greeting shown by the CLI."""
    cleaned_name = name.strip() or "Codex-1"
    return f"Hello from {cleaned_name}."


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(
        prog="codex-1",
        description="Run the Codex-1 starter CLI.",
    )
    parser.add_argument(
        "--name",
        default="Codex-1",
        help="Name to include in the greeting.",
    )
    return parser


def main(argv: list[str] | None = None) -> int:
    parser = build_parser()
    args = parser.parse_args(argv)
    print(build_message(args.name))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
