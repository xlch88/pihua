set -eux

cd "$(git rev-parse --show-toplevel)"
cat <<'EOF' >.git/hooks/post-merge
#!/bin/sh
set -e

REPO_ROOT="$(git rev-parse --show-toplevel)"

chmod +x "$REPO_ROOT/builder/build.sh"
chmod +x "$REPO_ROOT/builder/cron.sh"

bash "$REPO_ROOT/builder/build.sh"
EOF
chmod +x .git/hooks/post-merge
