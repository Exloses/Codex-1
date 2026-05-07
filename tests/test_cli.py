from pathlib import Path

from codex_starter.cli import build_message, build_report, detect_project_type, main


def test_build_message_uses_default_name() -> None:
    assert build_message() == "Hello from Codex-1."


def test_build_message_strips_blank_name() -> None:
    assert build_message("   ") == "Hello from Codex-1."


def test_detect_project_type_for_python_project(tmp_path: Path) -> None:
    (tmp_path / "pyproject.toml").write_text("[project]\nname = 'demo'\n", encoding="utf-8")

    assert detect_project_type(tmp_path) == "Python CLI project"


def test_build_report_includes_required_sections(tmp_path: Path) -> None:
    (tmp_path / "README.md").write_text("# Demo\n", encoding="utf-8")
    (tmp_path / "pyproject.toml").write_text("[project]\nname = 'demo'\n", encoding="utf-8")

    report = build_report(tmp_path, "Demo")

    assert "Repository summary" in report
    assert "Detected project type" in report
    assert "Important files found" in report
    assert "Missing recommended files" in report
    assert "Suggested next actions" in report
    assert "- README.md" in report
    assert "- AGENTS.md" in report


def test_main_prints_report(capsys, tmp_path: Path) -> None:
    (tmp_path / "pyproject.toml").write_text("[project]\nname = 'starter'\n", encoding="utf-8")

    exit_code = main(["--name", "Starter", "--path", str(tmp_path)])

    assert exit_code == 0
    output = capsys.readouterr().out
    assert "Starter project readiness report" in output
    assert "Python CLI project" in output
