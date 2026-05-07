from codex_starter.cli import build_message, main


def test_build_message_uses_default_name() -> None:
    assert build_message() == "Hello from Codex-1."


def test_build_message_strips_blank_name() -> None:
    assert build_message("   ") == "Hello from Codex-1."


def test_main_prints_custom_name(capsys) -> None:
    exit_code = main(["--name", "Starter"])

    assert exit_code == 0
    assert capsys.readouterr().out == "Hello from Starter.\n"
