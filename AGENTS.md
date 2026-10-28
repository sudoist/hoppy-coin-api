# Hoppy Coin API — Agent Guidance

Laravel leaderboard backend for a free experimental game. Keep implementation straightforward, secure, and compatible with the existing Phaser client.

- Work in small branches and PRs targeting `master`. Do not merge into `master` without owner approval.
- No permanent `develop` branch, unnecessary infrastructure, or dependency upgrades just to be current.
- Preserve `/api/v1/scores` behavior unless a coordinated game-client change is approved.
- Treat `x-api-key` as public when embedded in browser JavaScript; it is not a meaningful anti-cheat secret.
- Prioritize score validation, abuse controls, leaderboard correctness (including tied scores), bounded responses, and safe error handling.
- Add or update targeted Laravel tests for API changes. Clearly report tests actually run; never assert unverified passing results.
- Never commit secrets, alter production configuration, or deploy without approval.
- Preserve third-party licenses. Do not assume the Laravel framework's MIT license automatically licenses original application code.
- Avoid destructive git operations and keep owner review manageable.
