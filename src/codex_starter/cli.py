"""Command-line entry point for Codex-1."""

from __future__ import annotations

import argparse
from pathlib import Path


IMPORTANT_FILES = (
    "README.md",
    "AGENTS.md",
    "PROJECT_PLAN.md",
    "pyproject.toml",
    "src/codex_starter/cli.py",
    "tests/test_cli.py",
)

RECOMMENDED_FILES = (
    ".gitignore",
    "README.md",
    "AGENTS.md",
    "PROJECT_PLAN.md",
    "pyproject.toml",
    "tests/test_cli.py",
)


def build_message(name: str = "Codex-1") -> str:
    """Return a compact greeting for callers that need one line of text."""
    cleaned_name = name.strip() or "Codex-1"
    return f"Hello from {cleaned_name}."


def detect_project_type(root: Path) -> str:
    """Return a beginner-friendly project type summary for the path."""
    if (root / "pyproject.toml").exists():
        return "Python CLI project"
    if (root / "package.json").exists():
        return "JavaScript or TypeScript project"
    return "Project type not detected yet"


def find_existing_files(root: Path, candidates: tuple[str, ...]) -> list[str]:
    """Return candidate files that exist under root."""
    return [path for path in candidates if (root / path).exists()]


def find_missing_files(root: Path, candidates: tuple[str, ...]) -> list[str]:
    """Return candidate files that do not exist under root."""
    return [path for path in candidates if not (root / path).exists()]


def build_report(root: Path, name: str = "Codex-1") -> str:
    """Build a readable project readiness report."""
    cleaned_name = name.strip() or "Codex-1"
    project_root = root.resolve()
    found_files = find_existing_files(project_root, IMPORTANT_FILES)
    missing_files = find_missing_files(project_root, RECOMMENDED_FILES)

    lines = [
        f"{cleaned_name} project readiness report",
        "",
        "Repository summary",
        f"- Name: {cleaned_name}",
        f"- Path checked: {project_root}",
        "- Purpose: Minimal starter project for a small command-line tool.",
        "",
        "Detected project type",
        f"- {detect_project_type(project_root)}",
        "",
        "Important files found",
    ]

    if found_files:
        lines.extend(f"- {path}" for path in found_files)
    else:
        lines.append("- None of the expected starter files were found.")

    lines.extend(["", "Missing recommended files"])
    if missing_files:
        lines.extend(f"- {path}" for path in missing_files)
    else:
        lines.append("- None. The recommended starter files are present.")

    lines.extend(
        [
            "",
            "Suggested next actions",
            "- Run tests with: python -m pytest",
            "- Update PROJECT_PLAN.md when the project purpose becomes clearer.",
            "- Add a formatter or linter when coding conventions are chosen.",
            "- Keep secrets and production credentials out of the repository.",
        ]
    )

    return "\n".join(lines)


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(
        prog="codex-1",
        description="Show a beginner-friendly project readiness report.",
    )
    parser.add_argument(
        "--name",
        default="Codex-1",
        help="Repository name to include in the report.",
    )
    parser.add_argument(
        "--path",
        default=".",
        help="Project directory to inspect. Defaults to the current directory.",
    )
    return parser


def main(argv: list[str] | None = None) -> int:
    parser = build_parser()
    args = parser.parse_args(argv)
    print(build_report(Path(args.path), args.name))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
